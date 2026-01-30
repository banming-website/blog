<?php
/**
 * JSON 数据库操作类
 * 提供类似数据库的 CRUD 操作，但使用 JSON 文件存储
 */

class JsonDB {
    private $dataDir;
    private $cache = [];
    private $lockTimeout = 5; // 文件锁超时时间（秒）

    /**
     * 构造函数
     * @param string $dataDir 数据目录路径
     */
    public function __construct($dataDir = null) {
        $this->dataDir = $dataDir ?? DATA_DIR;

        // 确保数据目录存在
        if (!is_dir($this->dataDir)) {
            mkdir($this->dataDir, 0755, true);
        }
    }

    /**
     * 获取所有文章
     * @param array $filters 筛选条件
     * @return array
     */
    public function getPosts($filters = []) {
        $data = $this->loadJson('posts.json');
        $posts = $data['posts'] ?? [];

        // 应用筛选条件
        if (!empty($filters)) {
            $posts = $this->filterPosts($posts, $filters);
        }

        // 排序
        $sort = $filters['sort'] ?? 'date';
        $order = $filters['order'] ?? 'DESC';
        $posts = $this->sortPosts($posts, $sort, $order);

        // 限制数量
        if (isset($filters['limit'])) {
            $posts = array_slice($posts, 0, $filters['limit']);
        }

        // 分页
        if (isset($filters['page']) && isset($filters['perPage'])) {
            $offset = ($filters['page'] - 1) * $filters['perPage'];
            $posts = array_slice($posts, $offset, $filters['perPage']);
        }

        return array_values($posts);
    }

    /**
     * 根据 slug 获取单篇文章
     * @param string $slug 文章 slug
     * @return array|null
     */
    public function getPost($slug) {
        $posts = $this->getPosts();
        foreach ($posts as $post) {
            if ($post['id'] === $slug) {
                return $post;
            }
        }
        return null;
    }

    /**
     * 保存文章
     * @param array $post 文章数据
     * @return bool
     */
    public function savePost($post) {
        $data = $this->loadJson('posts.json');
        $posts = &$data['posts'];

        // 确保文章有 ID
        if (empty($post['id'])) {
            $post['id'] = $this->generateSlug($post['title']);
        }

        // 更新时间戳
        $post['updated'] = date('c');

        // 查找是否已存在
        $found = false;
        foreach ($posts as $key => $p) {
            if ($p['id'] === $post['id']) {
                $posts[$key] = $post;
                $found = true;
                break;
            }
        }

        // 如果不存在，添加到数组
        if (!$found) {
            $posts[] = $post;
        }

        $data['lastUpdated'] = date('c');
        return $this->saveJson('posts.json', $data);
    }

    /**
     * 删除文章
     * @param string $slug 文章 slug
     * @return bool
     */
    public function deletePost($slug) {
        $data = $this->loadJson('posts.json');
        $posts = &$data['posts'];

        foreach ($posts as $key => $post) {
            if ($post['id'] === $slug) {
                unset($posts[$key]);
                $posts = array_values($posts); // 重新索引
                $data['lastUpdated'] = date('c');
                return $this->saveJson('posts.json', $data);
            }
        }

        return false;
    }

    /**
     * 获取相邻文章
     * @param string $slug 当前文章 slug
     * @param string $direction 'prev' 或 'next'
     * @return array|null
     */
    public function getAdjacentPost($slug, $direction = 'next') {
        $posts = $this->getPosts(['publish' => true]);

        foreach ($posts as $key => $post) {
            if ($post['id'] === $slug) {
                if ($direction === 'next') {
                    return $posts[$key - 1] ?? null;
                } else {
                    return $posts[$key + 1] ?? null;
                }
            }
        }

        return null;
    }

    /**
     * 获取所有分类
     * @return array
     */
    public function getCategories() {
        $posts = $this->getPosts();
        $categories = [];

        foreach ($posts as $post) {
            $category = $post['category'] ?? '未分类';
            if (!isset($categories[$category])) {
                $categories[$category] = 0;
            }
            $categories[$category]++;
        }

        return $categories;
    }

    /**
     * 获取所有标签
     * @return array
     */
    public function getTags() {
        $posts = $this->getPosts();
        $tags = [];

        foreach ($posts as $post) {
            $postTags = $post['tags'] ?? [];
            foreach ($postTags as $tag) {
                if (!isset($tags[$tag])) {
                    $tags[$tag] = 0;
                }
                $tags[$tag]++;
            }
        }

        arsort($tags);
        return $tags;
    }

    /**
     * 搜索文章
     * @param string $query 搜索关键词
     * @param int $limit 返回数量限制
     * @return array
     */
    public function searchPosts($query, $limit = 30) {
        $posts = $this->getPosts(['publish' => true]);
        $results = [];
        $query = strtolower($query);

        foreach ($posts as $post) {
            $score = 0;

            // 标题匹配（权重最高）
            if (strpos(strtolower($post['title']), $query) !== false) {
                $score += 10;
            }

            // 摘要匹配
            if (strpos(strtolower($post['excerpt'] ?? ''), $query) !== false) {
                $score += 5;
            }

            // 内容匹配
            if (strpos(strtolower(strip_tags($post['content'] ?? '')), $query) !== false) {
                $score += 2;
            }

            // 标签匹配
            foreach ($post['tags'] ?? [] as $tag) {
                if (strpos(strtolower($tag), $query) !== false) {
                    $score += 3;
                }
            }

            // 分类匹配
            if (strpos(strtolower($post['category'] ?? ''), $query) !== false) {
                $score += 3;
            }

            if ($score > 0) {
                $post['score'] = $score;
                $results[] = $post;
            }
        }

        // 按分数排序
        usort($results, function($a, $b) {
            return $b['score'] - $a['score'];
        });

        return array_slice($results, 0, $limit);
    }

    /**
     * 获取统计数据
     * @return array
     */
    public function getStats() {
        $posts = $this->getPosts(['publish' => true]);
        $totalWords = 0;
        $thisYear = date('Y');
        $thisYearCount = 0;

        foreach ($posts as $post) {
            $totalWords += $post['wordCount'] ?? 0;

            $postYear = date('Y', strtotime($post['date']));
            if ($postYear == $thisYear) {
                $thisYearCount++;
            }
        }

        return [
            'totalPosts' => count($posts),
            'totalWords' => $totalWords,
            'thisYearCount' => $thisYearCount,
            'categories' => count($this->getCategories()),
            'tags' => count($this->getTags())
        ];
    }

    /**
     * 筛选文章
     * @param array $posts 文章数组
     * @param array $filters 筛选条件
     * @return array
     */
    private function filterPosts($posts, $filters) {
        foreach ($filters as $key => $value) {
            switch ($key) {
                case 'publish':
                    $posts = array_filter($posts, function($post) use ($value) {
                        return ($post['publish'] ?? true) === $value;
                    });
                    break;

                case 'resource':
                    $posts = array_filter($posts, function($post) use ($value) {
                        return ($post['resource'] ?? false) === $value;
                    });
                    break;

                case 'category':
                    $posts = array_filter($posts, function($post) use ($value) {
                        return ($post['category'] ?? '') === $value;
                    });
                    break;

                case 'tag':
                    $posts = array_filter($posts, function($post) use ($value) {
                        return in_array($value, $post['tags'] ?? []);
                    });
                    break;

                case 'year':
                    $posts = array_filter($posts, function($post) use ($value) {
                        return date('Y', strtotime($post['date'])) == $value;
                    });
                    break;
            }
        }

        return array_values($posts);
    }

    /**
     * 排序文章
     * @param array $posts 文章数组
     * @param string $field 排序字段
     * @param string $order 排序方向
     * @return array
     */
    private function sortPosts($posts, $field, $order) {
        usort($posts, function($a, $b) use ($field, $order) {
            $aVal = $a[$field] ?? '';
            $bVal = $b[$field] ?? '';

            if ($field === 'date') {
                $aVal = strtotime($aVal);
                $bVal = strtotime($bVal);
            }

            if ($order === 'DESC') {
                return $bVal <=> $aVal;
            } else {
                return $aVal <=> $bVal;
            }
        });

        return $posts;
    }

    /**
     * 加载 JSON 文件
     * @param string $filename 文件名
     * @return array
     */
    private function loadJson($filename) {
        $path = $this->dataDir . '/' . $filename;

        // 检查缓存
        if (isset($this->cache[$filename])) {
            return $this->cache[$filename];
        }

        // 文件不存在，返回空结构
        if (!file_exists($path)) {
            $this->cache[$filename] = [];
            return [];
        }

        // 读取文件
        $content = file_get_contents($path);
        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("JSON 解析错误: " . json_last_error_msg() . " in " . $path);
            $this->cache[$filename] = [];
            return [];
        }

        $this->cache[$filename] = $data;
        return $data;
    }

    /**
     * 保存 JSON 文件（带文件锁）
     * @param string $filename 文件名
     * @param array $data 数据
     * @return bool
     */
    private function saveJson($filename, $data) {
        $path = $this->dataDir . '/' . $filename;
        $tempPath = $path . '.tmp';
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        if ($json === false) {
            error_log("JSON 编码错误: " . json_last_error_msg());
            return false;
        }

        // 使用临时文件写入，防止写入失败导致数据丢失
        if (file_put_contents($tempPath, $json, LOCK_EX) === false) {
            error_log("无法写入临时文件: " . $tempPath);
            return false;
        }

        // 原子性重命名
        if (!rename($tempPath, $path)) {
            @unlink($tempPath);
            return false;
        }

        // 更新缓存
        $this->cache[$filename] = $data;

        return true;
    }

    /**
     * 生成 URL 友好的 slug
     * @param string $text 文本
     * @return string
     */
    private function generateSlug($text) {
        $slug = strtolower($text);
        $slug = preg_replace('/[^\w\x{4e00}-\x{9fa5}]+/u', '-', $slug);
        $slug = trim($slug, '-');

        // 如果 slug 已存在，添加数字后缀
        $originalSlug = $slug;
        $counter = 1;
        while ($this->getPost($slug)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * 清除缓存
     */
    public function clearCache() {
        $this->cache = [];
    }
}

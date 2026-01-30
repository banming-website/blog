<?php
/**
 * Markdown 解析器
 * 支持 GitHub Flavored Markdown 和代码高亮
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Parsedown;
use ParsedownExtra;

class MarkdownParser {
    private $parsedown;
    private $extra;

    public function __construct() {
        $this->parsedown = new Parsedown();
        $this->extra = new ParsedownExtra();
        $this->extra->setSafeMode(false); // 允许 HTML
    }

    /**
     * 解析 Markdown 为 HTML
     * @param string $markdown Markdown 文本
     * @return string HTML
     */
    public function parse($markdown) {
        // 1. 转换 Obsidian 图片格式 [[image.png]]
        $markdown = $this->convertObsidianImages($markdown);

        // 2. 转换 Markdown → HTML
        $html = $this->extra->text($markdown);

        // 3. 处理代码块（添加 Prism.js 类）
        $html = $this->addPrismClasses($html);

        // 4. 生成标题 ID
        $html = $this->addHeadingIds($html);

        // 5. 处理自定义语法
        $html = $this->processCustomSyntax($html);

        return $html;
    }

    /**
     * 转换 Obsidian 图片格式
     * [[image.png]] -> ![image](/assets/images/image.png)
     */
    private function convertObsidianImages($markdown) {
        // 匹配 [[image.png]] 或 [[image.png|alt]]
        return preg_replace_callback(
            '/\[\[([^\]|]+)(?:\|([^\]]+))?\]\]/',
            function($matches) {
                $filename = $matches[1];
                $alt = $matches[2] ?? $filename;
                return "![{$alt}]({$filename})";
            },
            $markdown
        );
    }

    /**
     * 为代码块添加 Prism.js 类
     */
    private function addPrismClasses($html) {
        // 匹配 <pre><code class="language-xxx">
        $html = preg_replace_callback(
            '/<pre><code class="language-(\w+)">/',
            function($matches) {
                $lang = $matches[1];
                return '<pre class="line-numbers"><code class="language-' . $lang . '">';
            },
            $html
        );

        // 匹配没有 language 类的代码块
        $html = preg_replace(
            '/<pre><code>(?!.*class=)/',
            '<pre class="line-numbers"><code class="language-plaintext">',
            $html
        );

        return $html;
    }

    /**
     * 为标题添加 ID
     */
    private function addHeadingIds($html) {
        return preg_replace_callback(
            '/<h([1-6])([^>]*)>(.*?)<\/h\1>/s',
            function($matches) {
                $level = $matches[1];
                $attrs = $matches[2];
                $text = $matches[3];

                // 如果已经有 ID，不重复添加
                if (preg_match('/id=/i', $attrs)) {
                    return "<h{$level}{$attrs}>{$text}</h{$level}>";
                }

                // 生成 ID
                $id = $this->generateId($text);

                return "<h{$level} id=\"{$id}\">{$text}</h{$level}>";
            },
            $html
        );
    }

    /**
     * 生成 URL 友好的 ID
     */
    private function generateId($text) {
        // 移除 HTML 标签
        $text = strip_tags($text);

        // 转换为小写
        $text = strtolower($text);

        // 替换非字母数字字符为连字符
        $text = preg_replace('/[^\w\x{4e00}-\x{9fa5}]+/u', '-', $text);

        // 移除首尾连字符
        $text = trim($text, '-');

        // 如果为空，使用默认 ID
        if (empty($text)) {
            return 'heading-' . uniqid();
        }

        return $text;
    }

    /**
     * 处理自定义语法
     * 例如：<!-- ProjectCard ... -->
     */
    private function processCustomSyntax($html) {
        // 处理项目卡片
        $html = preg_replace_callback(
            '/<!--\s*ProjectCard:\s*(.+?)\s*-->/',
            function($matches) {
                $data = json_decode($matches[1], true);
                if (!$data) {
                    return '';
                }

                $name = htmlspecialchars($data['name'] ?? '');
                $description = htmlspecialchars($data['description'] ?? '');
                $url = htmlspecialchars($data['url'] ?? '#');
                $image = htmlspecialchars($data['image'] ?? '');

                return <<<HTML
<div class="project-card">
    <a href="{$url}" target="_blank" rel="noopener">
        <img src="{$image}" alt="{$name}" class="project-image">
        <div class="project-info">
            <h3 class="project-name">{$name}</h3>
            <p class="project-description">{$description}</p>
        </div>
    </a>
</div>
HTML;
            },
            $html
        );

        return $html;
    }

    /**
     * 从 HTML 中提取目录
     * @param string $html HTML 内容
     * @return array
     */
    public function extractTOC($html) {
        $toc = [];

        preg_match_all('/<h([1-6])[^>]*id="([^"]*)"[^>]*>(.*?)<\/h\1>/i', $html, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $level = (int)$match[1];
            $id = $match[2];
            $text = strip_tags($match[3]);

            $toc[] = [
                'level' => $level,
                'id' => $id,
                'text' => $text
            ];
        }

        return $toc;
    }

    /**
     * 计算字数
     * @param string $markdown Markdown 文本
     * @return int
     */
    public function countWords($markdown) {
        // 移除代码块
        $text = preg_replace('/```[\s\S]*?```/', '', $markdown);

        // 移除行内代码
        $text = preg_replace('/`[^`]+`/', '', $text);

        // 移除链接语法
        $text = preg_replace('/\[([^\]]+)\]\([^)]+\)/', '$1', $text);

        // 移除图片语法
        $text = preg_replace('/!\[([^\]]*)\]\([^)]+\)/', '', $text);

        // 移除 Markdown 标记
        $text = preg_replace('/#{1,6}\s/', '', $text);
        $text = preg_replace('/\*{1,2}([^*]+)\*{1,2}/', '$1', $text);
        $text = preg_replace('/_{1,2}([^_]+)_{1,2}/', '$1', $text);

        // 统计中文字符
        $chineseCount = preg_match_all('/[\x{4e00}-\x{9fa5}]/u', $text, $matches);

        // 统计英文单词
        $englishCount = preg_match_all('/[a-zA-Z]+/', $text, $matches);

        return $chineseCount + $englishCount;
    }

    /**
     * 计算阅读时间（分钟）
     * @param int $wordCount 字数
     * @return int
     */
    public function calculateReadingTime($wordCount) {
        // 中文：350 字/分钟，英文：200 词/分钟
        $chineseCount = preg_match_all('/[\x{4e00}-\x{9fa5}]/u', $wordCount > 0 ? str_repeat('中', $wordCount) : '');
        $englishCount = $wordCount - $chineseCount;

        $totalMinutes = ($chineseCount / 350) + ($englishCount / 200);

        return max(1, (int)ceil($totalMinutes));
    }

    /**
     * 生成摘要
     * @param string $markdown Markdown 文本
     * @param int $maxLength 最大长度
     * @return string
     */
    public function generateExcerpt($markdown, $maxLength = 200) {
        // 移除代码块
        $text = preg_replace('/```[\s\S]*?```/', '', $markdown);

        // 移除 HTML 标签
        $text = strip_tags($text);

        // 移除行内元素
        $text = preg_replace('/\[([^\]]+)\]\([^)]+\)/', '', $text);
        $text = preg_replace('/`[^`]+`/', '', $text);
        $text = preg_replace('/!\[([^\]]*)\]\([^)]+\)/', '', $text);

        // 移除 Markdown 标记
        $text = preg_replace('/#{1,6}\s/', '', $text);
        $text = preg_replace('/\*{1,2}/', '', $text);
        $text = preg_replace('/_{1,2}/', '', $text);

        // 标准化空白字符
        $text = preg_replace('/\s+/', ' ', $text);
        $text = trim($text);

        // 截断
        if (mb_strlen($text) <= $maxLength) {
            return $text;
        }

        // 尝试在标点符号处截断
        $punctuations = ['。', '！', '？', '.', '!', '?', ' ', '  '];
        foreach ($punctuations as $punctuation) {
            $pos = mb_strpos($text, $punctuation, $maxLength - 50);
            if ($pos !== false && $pos <= $maxLength + 50) {
                return mb_substr($text, 0, $pos + 1) . '...';
            }
        }

        // 直接截断
        return mb_substr($text, 0, $maxLength) . '...';
    }
}

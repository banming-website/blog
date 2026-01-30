<?php
require_once 'config.php';
require_once 'includes/database.php';

$db = new JsonDB();
$posts = $db->getPosts([
    'publish' => true,
    'resource' => false,
    'sort' => 'date',
    'order' => 'DESC',
    'limit' => 20
]);

$settings = json_decode(file_get_contents(SETTINGS_FILE), true)['site'] ?? [];

header('Content-Type: application/rss+xml; charset=utf-8');

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title><?= htmlspecialchars($settings['title'] ?? 'Blog') ?></title>
        <link><?= $settings['url'] ?? SITE_URL ?></link>
        <description><?= htmlspecialchars($settings['description'] ?? '') ?></description>
        <language>zh-CN</language>
        <atom:link href="<?= $settings['url'] ?? SITE_URL ?>/rss" rel="self" type="application/rss+xml"/>
        <lastBuildDate><?= date('r') ?></lastBuildDate>

        <?php foreach ($posts as $post): ?>
        <item>
            <title><?= htmlspecialchars($post['title']) ?></title>
            <link><?= ($settings['url'] ?? SITE_URL) ?>/post/<?= $post['id'] ?></link>
            <description><![CDATA[<?= $post['excerpt'] ?? strip_tags($post['content']) ?>]]></description>
            <pubDate><?= date('r', strtotime($post['date'])) ?></pubDate>
            <guid><?= ($settings['url'] ?? SITE_URL) ?>/post/<?= $post['id'] ?></guid>
            <?php if (!empty($post['category'])): ?>
            <category><?= htmlspecialchars($post['category']) ?></category>
            <?php endif; ?>
            <?php if (!empty($post['author'])): ?>
            <author><?= htmlspecialchars($post['author']) ?></author>
            <?php endif; ?>
        </item>
        <?php endforeach; ?>
    </channel>
</rss>

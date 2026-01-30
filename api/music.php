<?php
/**
 * 音乐 API
 * 代理网易云音乐的接口
 */

require_once '../config.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$action = $_GET['action'] ?? '';

$playlistId = NETEASE_PLAYLIST_ID;
$apiBase = NETEASE_API;

switch ($action) {
    case 'playlist':
        // 获取歌单歌曲
        $url = "{$apiBase}/playlist/track/all?id={$playlistId}&limit=100";
        $response = @file_get_contents($url);

        if ($response === false) {
            echo json_encode(['error' => 'Failed to fetch playlist']);
            exit;
        }

        $data = json_decode($response, true);

        if (!isset($data['songs'])) {
            echo json_encode(['error' => 'Invalid response']);
            exit;
        }

        $tracks = array_map(function($song) {
            return [
                'id' => $song['id'],
                'name' => $song['name'],
                'artist' => $song['ar'][0]['name'] ?? 'Unknown',
                'album' => $song['al']['name'] ?? 'Unknown',
                'cover' => $song['al']['picUrl'] ?? '',
                'duration' => $song['dt'] ?? 0
            ];
        }, $data['songs']);

        echo json_encode($tracks);
        break;

    case 'url':
        // 获取歌曲播放地址
        $songId = $_GET['id'] ?? '';
        if (empty($songId)) {
            echo json_encode(['error' => 'Song ID is required']);
            exit;
        }

        $url = "{$apiBase}/song/url?id={$songId}";
        $response = @file_get_contents($url);

        if ($response === false) {
            echo json_encode(['error' => 'Failed to fetch song URL']);
            exit;
        }

        $data = json_decode($response, true);

        echo json_encode([
            'url' => $data['data'][0]['url'] ?? null,
        ]);
        break;

    case 'detail':
        // 获取歌曲详情
        $songId = $_GET['id'] ?? '';
        if (empty($songId)) {
            echo json_encode(['error' => 'Song ID is required']);
            exit;
        }

        $url = "{$apiBase}/song/detail?ids={$songId}";
        $response = @file_get_contents($url);

        if ($response === false) {
            echo json_encode(['error' => 'Failed to fetch song detail']);
            exit;
        }

        $data = json_decode($response, true);
        $song = $data['songs'][0] ?? [];

        echo json_encode([
            'name' => $song['name'] ?? '',
            'artist' => $song['ar'][0]['name'] ?? 'Unknown',
            'album' => $song['al']['name'] ?? 'Unknown',
            'cover' => $song['al']['picUrl'] ?? '',
        ]);
        break;

    case 'lyric':
        // 获取歌词
        $songId = $_GET['id'] ?? '';
        if (empty($songId)) {
            echo json_encode(['error' => 'Song ID is required']);
            exit;
        }

        $url = "{$apiBase}/lyric?id={$songId}";
        $response = @file_get_contents($url);

        if ($response === false) {
            echo json_encode(['error' => 'Failed to fetch lyrics']);
            exit;
        }

        $data = json_decode($response, true);

        $lyric = $data['lrc']['lyric'] ?? '';
        // 解析歌词（LRC 格式）
        $lyricLines = [];
        preg_match_all('/\[(\d+):(\d+)\.(\d+)\](.+)/', $lyric, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $minutes = (int)$match[1];
            $seconds = (int)$match[2];
            $milliseconds = (int)$match[3];
            $time = $minutes * 60 + $seconds + $milliseconds / 1000;
            $text = trim($match[4]);

            $lyricLines[] = [
                'time' => $time,
                'text' => $text
            ];
        }

        echo json_encode($lyricLines);
        break;

    default:
        echo json_encode(['error' => 'Invalid action']);
        break;
}

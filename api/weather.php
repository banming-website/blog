<?php
/**
 * 天气 API
 * 代理和风天气接口
 */

require_once '../config.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$apiKey = QWEATHER_API_KEY;

// 缓存天气数据（30分钟）
$cacheFile = ROOT_PATH . '/cache/weather.json';
$cacheTime = 1800; // 30分钟

if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheTime) {
    echo file_get_contents($cacheFile);
    exit;
}

try {
    // 1. 获取用户位置（IP 定位）
    $location = getUserLocation();

    // 2. 获取城市 ID
    $cityName = $location['city'] ?? '北京';
    $cityApi = "https://geoapi.qweather.com/v2/city/lookup?location={$cityName}&key={$apiKey}";
    $cityResponse = @file_get_contents($cityApi);

    if ($cityResponse === false) {
        throw new Exception('Failed to fetch city info');
    }

    $cityData = json_decode($cityResponse, true);
    $locationId = $cityData['location'][0]['id'] ?? '101010100';

    // 3. 获取实时天气
    $weatherApi = "https://devapi.qweather.com/v7/weather/now?location={$locationId}&key={$apiKey}";
    $weatherResponse = @file_get_contents($weatherApi);

    if ($weatherResponse === false) {
        throw new Exception('Failed to fetch weather data');
    }

    $weatherData = json_decode($weatherResponse, true);

    // 4. 获取未来3天预报
    $forecastApi = "https://devapi.qweather.com/v7/weather/3d?location={$locationId}&key={$apiKey}";
    $forecastResponse = @file_get_contents($forecastApi);

    $forecastData = $forecastResponse ? json_decode($forecastResponse, true) : [];

    // 5. 获取生活指数
    $indicesApi = "https://devapi.qweather.com/v7/indices/1d?type=0&location={$locationId}&key={$apiKey}";
    $indicesResponse = @file_get_contents($indicesApi);

    $indicesData = $indicesResponse ? json_decode($indicesResponse, true) : [];

    // 组合数据
    $result = [
        'city' => $cityName,
        'location' => $location,
        'now' => [
            'temp' => $weatherData['now']['temp'] ?? '-',
            'feelsLike' => $weatherData['now']['feelsLike'] ?? '-',
            'text' => $weatherData['now']['text'] ?? '-',
            'icon' => $weatherData['now']['icon'] ?? '100',
            'humidity' => $weatherData['now']['humidity'] ?? '-',
            'windDir' => $weatherData['now']['windDir'] ?? '-',
            'windScale' => $weatherData['now']['windScale'] ?? '-',
        ],
        'forecast' => $forecastData['daily'] ?? [],
        'indices' => $indicesData['daily'] ?? [],
        'updated' => date('c')
    ];

    // 缓存结果
    if (!is_dir(ROOT_PATH . '/cache')) {
        mkdir(ROOT_PATH . '/cache', 0755, true);
    }
    file_put_contents($cacheFile, json_encode($result));

    echo json_encode($result);

} catch (Exception $e) {
    // 返回默认数据
    echo json_encode([
        'city' => '北京',
        'location' => ['city' => '北京', 'province' => '北京'],
        'now' => [
            'temp' => '-',
            'text' => '未知',
            'icon' => '999'
        ],
        'error' => $e->getMessage()
    ]);
}

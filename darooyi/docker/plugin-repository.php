<?php
$uri = $_SERVER['REQUEST_URI'] ?? '';
$method = $_SERVER['REQUEST_METHOD'] ?? '';
if (!in_array($method, ['GET', 'POST'], true)
    || !preg_match('~^/(api/(?=plugins/)|downloads/(?=(?:plugin|translation/plugin)/))(.+)$~D', $uri, $match)) {
    http_response_code(404);
    return;
}

$host = $match[1] === 'api/' ? 'api.wordpress.org' : 'downloads.wordpress.org';
$body = tmpfile();
$curl = curl_init('https://' . $host . '/' . $match[2]);
if (!$body || !$curl) {
    http_response_code(502);
    return;
}

$options = [
    CURLOPT_FILE => $body,
    CURLOPT_TIMEOUT => 90,
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'] ?? 'WordPress',
];
if ($method === 'POST') {
    $options[CURLOPT_POST] = true;
    $options[CURLOPT_POSTFIELDS] = file_get_contents('php://input');
    $options[CURLOPT_HTTPHEADER] = [
        'Content-Type: ' . ($_SERVER['CONTENT_TYPE'] ?? 'application/x-www-form-urlencoded'),
    ];
}

curl_setopt_array($curl, $options);
if (!curl_exec($curl)) {
    http_response_code(502);
    return;
}

$status = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
$type = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);
http_response_code($status);
if ($type) {
    header('Content-Type: ' . $type);
}
rewind($body);
fpassthru($body);

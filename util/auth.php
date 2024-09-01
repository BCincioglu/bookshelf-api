<?php

function validateApiKey($apiKey) {
    $validApiKey = $_ENV['API_KEY'] ?: '7c709488-ab18-405f-81c6-45e2c8e95a64';
    return $apiKey === $validApiKey;
}

function checkApiKey() {
    $headers = getallheaders();
    if (!isset($headers['X-API-Key']) || !validateApiKey($headers['X-API-Key'])) {
        error_log('Unauthorized access attempt with API key: ' . ($headers['X-API-Key'] ?? 'None'));
        header('HTTP/1.0 401 Unauthorized');
        echo json_encode(['error' => 'Invalid API key']);
        exit();
    }
}

?>

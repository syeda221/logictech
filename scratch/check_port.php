<?php
function testUrl($url) {
    $ctx = stream_context_create([
        'http' => [
            'timeout' => 3,
            'ignore_errors' => true,
            'max_redirects' => 0
        ]
    ]);
    $res = @file_get_contents($url, false, $ctx);
    echo "URL: $url\n";
    if (isset($http_response_header)) {
        foreach ($http_response_header as $header) {
            echo "  $header\n";
        }
    } else {
        echo "  No response\n";
    }
    echo "\n";
}

testUrl('http://127.0.0.1:8002/quotations');
testUrl('http://127.0.0.1:8002/sale');

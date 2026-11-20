<?php
$res = @file_get_contents('http://localhost/logictech/public/sale');
echo "Apache logictech/public/sale: \n";
if (isset($http_response_header)) {
    echo implode("\n", $http_response_header) . "\n";
} else {
    echo "Failed to connect to Apache localhost\n";
}

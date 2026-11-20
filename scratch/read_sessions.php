<?php
$dir = __DIR__ . '/../storage/framework/sessions';
$files = scandir($dir);
foreach ($files as $f) {
    if ($f === '.' || $f === '..' || $f === '.gitignore') continue;
    $path = "$dir/$f";
    $content = file_get_contents($path);
    $data = @unserialize($content);
    $mtime = date('Y-m-d H:i:s', filemtime($path));
    echo "File: $f ($mtime)\n";
    if (is_array($data)) {
        foreach ($data as $k => $v) {
            if (is_string($v) || is_numeric($v)) {
                echo "  $k => $v\n";
            } else {
                echo "  $k => " . json_encode($v) . "\n";
            }
        }
    } else {
        echo "  Raw: " . substr($content, 0, 100) . "\n";
    }
    echo "\n";
}

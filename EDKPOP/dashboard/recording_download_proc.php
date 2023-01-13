<?php
    include_once 'setting_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'db_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'file_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'const_info.php';

    if (isset($_REQUEST["file"])) {
        $file = urldecode($_REQUEST["file"]);

        $filepath = $fileinfo['recordings'].DIRECTORY_SEPARATOR.$file.$fileinfo['recording_ext'];

        if (file_exists($filepath)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($filepath).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: '.filesize($filepath));
            flush();
            readfile($filepath);
        } else {
            http_response_code(404);
        }
    }

    die();
?>

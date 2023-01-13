<?php
    include_once 'setting_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'db_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'file_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'const_info.php';
    include_once 'file_funcs.php';

    if (!isset($_SESSION)) {
        session_start();
    }

    $full_name = $fileinfo['video'].DIRECTORY_SEPARATOR.$_POST['lectureid'].DIRECTORY_SEPARATOR.$_POST['vid'].$fileinfo['archive_ext'];

    if ($_FILES["fileToUpload"]["error"] == UPLOAD_ERR_OK) {
        $temp_name = $_FILES["fileToUpload"]["tmp_name"];

        move_uploaded_file($temp_name, $full_name);

        $zipPath = pathinfo(realpath($full_name), PATHINFO_DIRNAME);
        $zip = new ZipArchive;
        $res = $zip->open($full_name);

        $zip->extractTo($zipPath);
        $zip->close();

        removeFile($full_name);

        $temp_name = $fileinfo['video'].DIRECTORY_SEPARATOR.$_POST['lectureid'].DIRECTORY_SEPARATOR.$_POST['vid'].$fileinfo['audio_ext'];
        $audio_name = $fileinfo['audio'].DIRECTORY_SEPARATOR.$_POST['vid'].$fileinfo['audio_ext'];

        moveObject($temp_name, $audio_name);
    }
?>

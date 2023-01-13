<?php
    include_once 'setting_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'db_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'file_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'const_info.php';

    if (!isset($_SESSION)) {
        session_start();
    }

    $full_name = $fileinfo['lecture_thumbnail'].DIRECTORY_SEPARATOR.$_POST['lectureid'].$fileinfo['thumbnail_ext'];

    if ($_FILES["fileToUpload"]["error"] == UPLOAD_ERR_OK) {
        $temp_name = $_FILES["fileToUpload"]["tmp_name"];

        move_uploaded_file($temp_name, $full_name);
    }
?>

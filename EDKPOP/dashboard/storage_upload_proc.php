<?php
    include_once 'setting_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'db_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'file_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'const_info.php';
    include_once 'file_funcs.php';

    if (!isset($_SESSION)) {
        session_start();
    }

    $valid_file = true;
    $original_name = pathinfo($_FILES["fileToUpload"]["name"], PATHINFO_BASENAME);
    $full_name = $fileinfo['storage'].DIRECTORY_SEPARATOR.$original_name;
    $ext_name = strtolower(pathinfo($full_name, PATHINFO_EXTENSION));

    if ($_FILES["fileToUpload"]["error"] == UPLOAD_ERR_OK) {
        $temp_name = $_FILES["fileToUpload"]["tmp_name"];

        if ($db_settings['upload_image_only'] === true) {
            $is_image = getimagesize($temp_name);

            if (($is_image !== false) || (($ext_name != "jpg") && ($ext_name != "jpeg") && ($ext_name != "png") && ($ext_name != "gif" ))) {
                $valid_file = false;
            }
        }

        if ($valid_file !== false) {
            if (file_exists($full_name)) {
                $full_name = $fileinfo['storage'].DIRECTORY_SEPARATOR.$original_name.'_'.date("YmdHis", time()).'.'.$ext_name;
            }

            move_uploaded_file($temp_name, $full_name);

            $file_size = filesize($full_name);
            $final_name = pathinfo($full_name, PATHINFO_BASENAME);

            $mysqli = mysqli_connect($db['host'], $db['user'], $db['pass'], $db['name']);
            $mysqli->query("SET NAMES utf8");

            $sql = 'INSERT INTO storage (filename, original, size) VALUES ("'.$final_name.'", "'.$original_name.'", '.$file_size.')';
            $res = $mysqli->query($sql);

            mysqli_close($mysqli);
        }
    }
?>

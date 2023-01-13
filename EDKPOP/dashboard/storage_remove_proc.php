<?php
    include_once 'setting_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'db_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'file_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'const_info.php';
    include_once 'file_funcs.php';

    $mysqli = mysqli_connect($db['host'], $db['user'], $db['pass'], $db['name']);
    $mysqli->query("SET NAMES utf8");
    $sql = 'DELETE FROM storage WHERE filename="'.$_POST['filename'].'"';
    $res = $mysqli->query($sql);
    mysqli_close($mysqli);

    removeFile($fileinfo['storage'].DIRECTORY_SEPARATOR.$_POST['filename']);
?>

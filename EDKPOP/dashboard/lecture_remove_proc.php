<?php
    include_once 'setting_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'db_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'file_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'const_info.php';
    include_once 'file_funcs.php';

    $mysqli = mysqli_connect($db['host'], $db['user'], $db['pass'], $db['name']);
    $mysqli->query("SET NAMES utf8");
    $sql = 'DELETE FROM lectures WHERE lectureid="'.$_POST['lectureid'].'"';
    $res = $mysqli->query($sql);
    mysqli_close($mysqli);

    rmdir($fileinfo['video'].DIRECTORY_SEPARATOR.$_POST['lectureid']);
    rmdir($fileinfo['lesson_thumbnail'].DIRECTORY_SEPARATOR.$_POST['lectureid']);
    removeFile($fileinfo['lecture_thumbnail'].DIRECTORY_SEPARATOR.$_POST['lectureid'].$fileinfo['thumbnail_ext']);
?>

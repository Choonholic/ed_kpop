<?php
    include_once 'setting_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'db_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'file_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'const_info.php';
    include_once 'file_funcs.php';

    $mysqli = mysqli_connect($db['host'], $db['user'], $db['pass'], $db['name']);
    $mysqli->query("SET NAMES utf8");
    $sql = 'DELETE FROM videos WHERE vid="'.$_POST['vid'].'"';
    $res = $mysqli->query($sql);
    $sql = 'SELECT * FROM videos WHERE lectureid="'.$_POST['lectureid'].'"';
    $res = $mysqli->query($sql);
    $cnt = mysqli_num_rows($res);
    $sql = 'UPDATE lectures SET lessons="'.$cnt.'" WHERE lectureid="'.$_POST['lectureid'].'"';
    $res = $mysqli->query($sql);
    mysqli_close($mysqli);

    removeFile($fileinfo['lesson_thumbnail'].DIRECTORY_SEPARATOR.$_POST['lectureid'].DIRECTORY_SEPARATOR.$_POST['vid'].$fileinfo['thumbnail_ext']);
    removeFile($fileinfo['audio'].DIRECTORY_SEPARATOR.$_POST['vid'].$fileinfo['audio_ext']);
    removeDirectory($fileinfo['video'].DIRECTORY_SEPARATOR.$_POST['lectureid'].DIRECTORY_SEPARATOR.$_POST['vid']);
?>

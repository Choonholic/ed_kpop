<?php
    include_once 'setting_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'db_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'file_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'const_info.php';

    $mysqli = mysqli_connect($db['host'], $db['user'], $db['pass'], $db['name']);
    $mysqli->query("SET NAMES utf8");
    $sql = 'DELETE FROM users WHERE email="'.$_POST['email'].'"';
    $res = $mysqli->query($sql);
    $sql = 'DELETE FROM progress WHERE email="'.$_POST['email'].'"';
    $res = $mysqli->query($sql);
    $sql = 'SELECT uniquekey FROM recordings WHERE email="'.$_POST['email'].'"';
    $res = $mysqli->query($sql);

    while ($row = $res->fetch_array(MYSQLI_ASSOC)) {
        $recording = $fileinfo['recordings'].DIRECTORY_SEPARATOR.$row['uniquekey'].$fileinfo['recording_ext'];
        unlink($recording);
    }

    $sql = 'DELETE FROM recordings WHERE email="'.$_POST['email'].'"';
    $res = $mysqli->query($sql);
    mysqli_close($mysqli);
?>

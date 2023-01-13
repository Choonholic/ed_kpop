<?php
    include_once 'setting_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'db_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'file_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'const_info.php';
    include_once 'file_funcs.php';

    $mysqli = mysqli_connect($db['host'], $db['user'], $db['pass'], $db['name']);
    $mysqli->query("SET NAMES utf8");

    $date = date("Y-m-d", strtotime("-".$_POST['days']." Days"));
    $sql = 'SELECT * FROM recordings WHERE created<"'.$date.'"';
    $res = $mysqli->query($sql);

    while ($row = $res->fetch_array(MYSQLI_ASSOC)) {
        $filepath = $fileinfo['recordings'].DIRECTORY_SEPARATOR.$row['uniquekey'].$fileinfo['recording_ext'];

        unlink($filepath);
    }

    $sql = 'DELETE FROM recordings WHERE created<"'.$date.'"';
    $res = $mysqli->query($sql);

    $sql = 'SELECT * FROM recordings';
    $res = $mysqli->query($sql);

    while ($row = $res->fetch_array(MYSQLI_ASSOC)) {
        $filepath = $fileinfo['recordings'].DIRECTORY_SEPARATOR.$row['uniquekey'].$fileinfo['recording_ext'];

        if (!file_exists($filepath)) {
            $sql_remove = 'DELETE FROM recordings WHERE uniquekey="'.$row['uniquekey'].'"';
            $res_remove = $mysqli->query($sql_remove);
        }
    }

    $files = scandir($fileinfo['recordings']);

    foreach ($files as $key => $value) {
        $filepath = realpath($fileinfo['recordings'].DIRECTORY_SEPARATOR.$value);

        if (!is_dir($filepath)) {
            $uniquekey = basename($filepath, $fileinfo['recording_ext']);
            $sql = 'SELECT * FROM recordings WHERE uniquekey="'.$uniquekey.'"';
            $res = $mysqli->query($sql);

            if (!mysqli_num_rows($res)) {
                unlink($filepath);
            }
        }
    }

    mysqli_close($mysqli);
?>

<?php
    include_once 'setting_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'db_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'file_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'const_info.php';
    include_once 'file_funcs.php';

    $mysqli = mysqli_connect($db['host'], $db['user'], $db['pass'], $db['name']);
    $mysqli->query("SET NAMES utf8");

    $sql = 'SELECT * FROM storage';
    $res = $mysqli->query($sql);

    while ($row = $res->fetch_array(MYSQLI_ASSOC)) {
        $filepath = $fileinfo['storage'].DIRECTORY_SEPARATOR.$row['filename'];

        if (!file_exists($filepath)) {
            $sql_remove = 'DELETE FROM storage WHERE filename="'.$row['filename'].'"';
            $res_remove = $mysqli->query($sql_remove);
        }
    }

    $files = scandir($fileinfo['storage']);

    foreach ($files as $key => $value) {
        $filepath = realpath($fileinfo['storage'].DIRECTORY_SEPARATOR.$value);

        if (!is_dir($filepath)) {
            $sql = 'SELECT * FROM storage WHERE filename="'.$value.'"';
            $res = $mysqli->query($sql);

            if (!mysqli_num_rows($res)) {
                unlink($filepath);
            }
        }
    }

    mysqli_close($mysqli);
?>

<?php
    include_once 'setting_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'db_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'file_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'const_info.php';

    if (!isset($_SESSION)) {
        session_start();
    }

    $full_name = '';

    if ($_FILES["fileToUpload"]["error"] == UPLOAD_ERR_OK) {
        $temp_name = $_FILES["fileToUpload"]["tmp_name"];
        $base_name = basename($_FILES["fileToUpload"]["name"]);
        $full_name = $fileinfo['uploads'].DIRECTORY_SEPARATOR.$base_name;

        move_uploaded_file($temp_name, $full_name);
    }

    $pwd_hash = hash('sha512', md5($db_settings['user_default_password']));
    $mysqli = mysqli_connect($db['host'], $db['user'], $db['pass'], $db['name']);
    $mysqli->query("SET NAMES utf8");

    $fp = fopen($full_name, "r");
    $success_count = 0;
    $total_count = 0;

    while (!feof($fp)) {
        $eml = trim(fgets($fp));

        if (strlen($eml) < 5) {
            continue;
        }

        $total_count++;

        $sql = 'SELECT * FROM users WHERE email="'.$eml.'"';
        $res = $mysqli->query($sql);

        if (mysqli_num_rows($res) == 1) {
            continue;
        }

        $sql = 'INSERT INTO users (email, nickname, password, active, created, activated, lastlogin, ip, location) VALUES ("'.$eml.'", "", "'.$pwd_hash.'", "'.USER_TRIAL_INACTIVE.'", "'.date("Y-m-d H:i:s").'", NULL, NULL, NULL, NULL)';
        $res = $mysqli->query($sql);

        if ($res != FALSE) {
            $success_count++;
        }
    }

    fclose($fp);
    mysqli_close($mysqli);
    sleep(3);
    unlink($full_name);
?>

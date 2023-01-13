<?php
    include_once 'setting_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'db_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'file_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'const_info.php';

    $uniquekey = $_POST['email'].'_'.$_POST['lectureid'].'_'.$_POST['vid'];
    $mysqli = mysqli_connect($db['host'], $db['user'], $db['pass'], $db['name']);
    $mysqli->query("SET NAMES utf8");
    $sql = 'SELECT * FROM progress WHERE uniquekey="'.$uniquekey.'"';
    $res = $mysqli->query($sql);
    $progress = ((int)$_POST['currentTime'] * 100 / (int)$_POST['duration']);

    if (mysqli_num_rows($res) == 1) {
        $row = $res->fetch_array(MYSQLI_ASSOC);

        if ($row['progress'] <= $progress) {
            $sql = 'UPDATE progress SET progress='.$progress.', current='.$_POST['currentTime'].', duration='.$_POST['duration'].' WHERE uniquekey="'.$uniquekey.'"';
            $res = $mysqli->query($sql);
        }
    } else {
        $sql = 'INSERT INTO progress (uniquekey, email, lectureid, vid, progress, current, duration) VALUES ("'.$uniquekey.'", "'.$_POST['email'].'", "'.$_POST['lectureid'].'", "'.$_POST['vid'].'", '.$progress.', '.$_POST['currentTime'].', '.$_POST['duration'].')';
        $res = $mysqli->query($sql);
    }

    $now = time();
    $date_short = date("YmdHis", $now);
    $date = date("Y-m-d H:i:s", $now);
    $uniquekey = $_POST['email'].'_'.$_POST['vid'].'_'.$date_short;
    $sql = 'INSERT INTO totalplayed (uniquekey, email, date, vid, total) VALUES ("'.$uniquekey.'", "'.$_POST['email'].'", "'.$date.'", "'.$_POST['vid'].'", '.$_POST['currentTime'].')';
    $res = $mysqli->query($sql);

    $uniquekey = $date_short.'_PROGRESS';
    $message = 'User ['.$_POST['email'].'] has played video ['.$_POST['vid'].'] during ['.$_POST['currentTime'].'] seconds.';
    $sql = 'INSERT INTO logs (uniquekey, type, date, email, message) VALUES ("'.$uniquekey.'", "PROGRESS", "'.$date.'", "'.$_POST['email'].'", "'.$message.'")';
    $res = $mysqli->query($sql);

    mysqli_close($mysqli);
?>

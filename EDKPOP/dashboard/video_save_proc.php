<?php
    include_once 'setting_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'db_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'file_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'const_info.php';
    include_once 'file_funcs.php';

    if (!isset($_SESSION)) {
        session_start();
    }

    $currentPage = $_POST['currentPage'];
    $mysqli = mysqli_connect($db['host'], $db['user'], $db['pass'], $db['name']);
    $mysqli->query("SET NAMES utf8");

    if ($_POST['method'] == 'add') {
        $sql = 'SELECT * FROM lectures WHERE lectureid="'.$_POST['lectureid'].'"';
        $res = $mysqli->query($sql);

        if (mysqli_num_rows($res) == 0) {
            mysqli_close($mysqli);
            echo '<!doctype html>'.PHP_EOL;
            echo '<html>'.PHP_EOL;
            echo '<body onload="document.post_form.submit();">'.PHP_EOL;
            echo '<form name="post_form" method="post" action="message.php">'.PHP_EOL;
            echo '<input type="hidden" name="title" value="Error">'.PHP_EOL;
            echo '<input type="hidden" name="message" value="Lecture '.$_POST['lectureid'].' does not exist.">'.PHP_EOL;
            echo '<input type="hidden" name="from" value="'.basename($_SERVER['PHP_SELF'], '.php').'">'.PHP_EOL;
            echo '</form>'.PHP_EOL;
            echo '</body>'.PHP_EOL;
            echo '</html>'.PHP_EOL;
            exit(1);
        }

        $sql = 'SELECT * FROM videos WHERE vid="'.$_POST['vid'].'"';
        $res = $mysqli->query($sql);

        if (mysqli_num_rows($res) == 1) {
            mysqli_close($mysqli);
            echo '<!doctype html>'.PHP_EOL;
            echo '<html>'.PHP_EOL;
            echo '<body onload="document.post_form.submit();">'.PHP_EOL;
            echo '<form name="post_form" method="post" action="message.php">'.PHP_EOL;
            echo '<input type="hidden" name="title" value="Error">'.PHP_EOL;
            echo '<input type="hidden" name="message" value="Video '.$_POST['vid'].' already exists.">'.PHP_EOL;
            echo '<input type="hidden" name="from" value="'.basename($_SERVER['PHP_SELF'], '.php').'">'.PHP_EOL;
            echo '</form>'.PHP_EOL;
            echo '</body>'.PHP_EOL;
            echo '</html>'.PHP_EOL;
            exit(1);
        }

        $sql = 'INSERT INTO videos (vid, lectureid, category, lesson, type, recordable, trial) VALUES ("'.$_POST['vid'].'", "'.$_POST['lectureid'].'", "'.$_POST['category'].'", "'.$_POST['lesson'].'", "'.$_POST['type'].'", "'.$_POST['recordable'].'", "'.$_POST['trial'].'")';
        $res = $mysqli->query($sql);
        $sql = 'SELECT * FROM videos WHERE lectureid="'.$_POST['lectureid'].'"';
        $res = $mysqli->query($sql);
        $cnt = mysqli_num_rows($res);
        $sql = 'UPDATE lectures SET lessons="'.$cnt.'" WHERE lectureid="'.$_POST['lectureid'].'"';
        $res = $mysqli->query($sql);
    } else {
        if ($_POST['oldlectureid'] != $_POST['lectureid']) {
            $sql = 'SELECT * FROM lectures WHERE lectureid="'.$_POST['lectureid'].'"';
            $res = $mysqli->query($sql);

            if (mysqli_num_rows($res) == 0) {
                mysqli_close($mysqli);
                echo '<!doctype html>'.PHP_EOL;
                echo '<html>'.PHP_EOL;
                echo '<body onload="document.post_form.submit();">'.PHP_EOL;
                echo '<form name="post_form" method="post" action="message.php">'.PHP_EOL;
                echo '<input type="hidden" name="title" value="Error">'.PHP_EOL;
                echo '<input type="hidden" name="message" value="Lecture '.$_POST['lectureid'].' does not exist.">'.PHP_EOL;
                echo '<input type="hidden" name="from" value="'.basename($_SERVER['PHP_SELF'], '.php').'">'.PHP_EOL;
                echo '</form>'.PHP_EOL;
                echo '</body>'.PHP_EOL;
                echo '</html>'.PHP_EOL;
                exit(1);
            }
        }

        if ($_POST['oldlectureid'] != $_POST['lectureid']) {
            $sql = 'SELECT * FROM progress WHERE vid="'.$_POST['vid'].'"';
            $res = $mysqli->query($sql);

            while ($row = $res->fetch_array(MYSQLI_ASSOC)) {
                $sql_update = 'UPDATE progress SET uniquekey="'.$row['email'].'_'.$_POST['lectureid'].'_'.$_POST['vid'].'", lectureid="'.$_POST['lectureid'].'" WHERE uniquekey="'.$row['uniquekey'].'"';
                $res_update = $mysqli->query($sql_update);
            }

            moveObject($fileinfo['lesson_thumbnail'].DIRECTORY_SEPARATOR.$_POST['oldlectureid'].DIRECTORY_SEPARATOR.$_POST['vid'].$fileinfo['thumbnail_ext'], $fileinfo['lesson_thumbnail'].DIRECTORY_SEPARATOR.$_POST['lectureid'].DIRECTORY_SEPARATOR.$_POST['vid'].$fileinfo['thumbnail_ext']);
            moveObject($fileinfo['video'].DIRECTORY_SEPARATOR.$_POST['oldlectureid'].DIRECTORY_SEPARATOR.$_POST['vid'], $fileinfo['video'].DIRECTORY_SEPARATOR.$_POST['lectureid'].DIRECTORY_SEPARATOR.$_POST['vid']);
        }

        $sql = 'UPDATE videos SET vid="'.$_POST['vid'].'", lectureid="'.$_POST['lectureid'].'", category="'.$_POST['category'].'", lesson="'.$_POST['lesson'].'", type="'.$_POST['type'].'", recordable="'.$_POST['recordable'].'", trial="'.$_POST['trial'].'" WHERE vid="'.$_POST['oldvid'].'"';
        $res = $mysqli->query($sql);
        $sql = 'SELECT * FROM videos WHERE lectureid="'.$_POST['lectureid'].'"';
        $res = $mysqli->query($sql);
        $cnt = mysqli_num_rows($res);
        $sql = 'UPDATE lectures SET lessons="'.$cnt.'" WHERE lectureid="'.$_POST['lectureid'].'"';
        $res = $mysqli->query($sql);
    }

    mysqli_close($mysqli);
    header('Location: video.php?currentPage='.$currentPage);
?>

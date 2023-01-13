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

        if (mysqli_num_rows($res) == 1) {
            mysqli_close($mysqli);
            echo '<!doctype html>'.PHP_EOL;
            echo '<html>'.PHP_EOL;
            echo '<body onload="document.post_form.submit();">'.PHP_EOL;
            echo '<form name="post_form" method="post" action="message.php">'.PHP_EOL;
            echo '<input type="hidden" name="title" value="Error">'.PHP_EOL;
            echo '<input type="hidden" name="message" value="Lecture '.$_POST['lectureid'].' already exists.">'.PHP_EOL;
            echo '<input type="hidden" name="from" value="'.basename($_SERVER['PHP_SELF'], '.php').'">'.PHP_EOL;
            echo '</form>'.PHP_EOL;
            echo '</body>'.PHP_EOL;
            echo '</html>'.PHP_EOL;
            exit(1);
        }

        createDirectory($fileinfo['video']);
        createDirectory($fileinfo['video'].DIRECTORY_SEPARATOR.$_POST['lectureid']);
        createDirectory($fileinfo['thumbnail']);
        createDirectory($fileinfo['lecture_thumbnail']);
        createDirectory($fileinfo['lesson_thumbnail']);
        createDirectory($fileinfo['lesson_thumbnail'].DIRECTORY_SEPARATOR.$_POST['lectureid']);

        $sql = 'INSERT INTO lectures (lectureid, title, difficulty, description, lessons, recordable, trial, server, status) VALUES ("'.$_POST['lectureid'].'", "'.$_POST['title'].'", "'.$_POST['difficulty'].'", "'.$_POST['description'].'", "0", "'.$_POST['recordable'].'", "'.$_POST['trial'].'", "'.$_POST['server'].'", "'.$_POST['status'].'")';
        $res = $mysqli->query($sql);
    } else {
        if (strcmp($_POST['oldlectureid'], $_POST['lectureid'])) {
            $sql = 'SELECT * FROM lectures WHERE lectureid="'.$_POST['lectureid'].'"';
            $res = $mysqli->query($sql);

            if (mysqli_num_rows($res) == 1) {
                mysqli_close($mysqli);
                echo '<!doctype html>'.PHP_EOL;
                echo '<html>'.PHP_EOL;
                echo '<body onload="document.post_form.submit();">'.PHP_EOL;
                echo '<form name="post_form" method="post" action="message.php">'.PHP_EOL;
                echo '<input type="hidden" name="title" value="Error">'.PHP_EOL;
                echo '<input type="hidden" name="message" value="Lecture '.$_POST['lectureid'].' already exists.">'.PHP_EOL;
                echo '<input type="hidden" name="from" value="'.basename($_SERVER['PHP_SELF'], '.php').'">'.PHP_EOL;
                echo '</form>'.PHP_EOL;
                echo '</body>'.PHP_EOL;
                echo '</html>'.PHP_EOL;
                exit(1);
            }

            $sql = 'SELECT * FROM progress WHERE lectureid="'.$_POST['oldlectureid'].'"';
            $res = $mysqli->query($sql);

            while ($row = $res->fetch_array(MYSQLI_ASSOC)) {
                $sql_update = 'UPDATE progress SET uniquekey="'.$row['email'].'_'.$_POST['lectureid'].'_'.$row['vid'].'", lectureid="'.$_POST['lectureid'].'" WHERE uniquekey="'.$row['uniquekey'].'"';
                $res_update = $mysqli->query($sql_update);
            }

            $sql = 'UPDATE videos SET lectureid="'.$_POST['lectureid'].'" WHERE lectureid="'.$_POST['oldlectureid'].'"';
            $res = $mysqli->query($sql);

            moveObject($fileinfo['lecture_thumbnail'].DIRECTORY_SEPARATOR.$_POST['oldlectureid'].$fileinfo['thumbnail_ext'], $fileinfo['lecture_thumbnail'].DIRECTORY_SEPARATOR.$_POST['lectureid'].$fileinfo['thumbnail_ext']);
            moveObject($fileinfo['lesson_thumbnail'].DIRECTORY_SEPARATOR.$_POST['oldlectureid'], $fileinfo['lesson_thumbnail'].DIRECTORY_SEPARATOR.$_POST['lectureid']);
            moveObject($fileinfo['video'].DIRECTORY_SEPARATOR.$_POST['oldlectureid'], $fileinfo['video'].DIRECTORY_SEPARATOR.$_POST['lectureid']);
        }

        $sql = 'UPDATE lectures SET lectureid="'.$_POST['lectureid'].'", title="'.$_POST['title'].'", difficulty="'.$_POST['difficulty'].'", description="'.$_POST['description'].'", recordable="'.$_POST['recordable'].'", trial="'.$_POST['trial'].'", server="'.$_POST['server'].'", status="'.$_POST['status'].'" WHERE lectureid="'.$_POST['oldlectureid'].'"';
        $res = $mysqli->query($sql);
    }

    mysqli_close($mysqli);
    header('Location: lecture.php?currentPage='.$currentPage);
?>

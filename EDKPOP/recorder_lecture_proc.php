<?php
    include_once 'setting_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'db_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'file_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'const_info.php';

    $mysqli = mysqli_connect($db['host'], $db['user'], $db['pass'], $db['name']);
    $mysqli->query("SET NAMES utf8");

    if ($eds_settings['force_all_lectures'] == true) {
        if ($_POST['active'] == USER_TRIAL_ACTIVE) {
            $sql = 'SELECT * FROM lectures WHERE trial="'.TRIAL_OPENED.'" ORDER BY lectureid';
        } else {
            $sql = 'SELECT * FROM lectures WHERE 1 ORDER BY lectureid';
        }
    } else {
        if ($_POST['active'] == USER_TRIAL_ACTIVE) {
            $sql = 'SELECT * FROM lectures WHERE status="'.LECTURE_ACTIVE.'" AND recordable="'.LECTURE_RECORDABLE.'" AND trial="'.TRIAL_OPENED.'" ORDER BY lectureid';
        } else {
            $sql = 'SELECT * FROM lectures WHERE status="'.LECTURE_ACTIVE.'" AND recordable="'.LECTURE_RECORDABLE.'" ORDER BY lectureid';
        }
    }

    $res = $mysqli->query($sql);
    $count = mysqli_num_rows($res);

    if ($count == 0) {
        echo '<div class="lecture_none">No Lectures Available Now</div>'.PHP_EOL;
    } else {
        $progress_list = array();
        $progress_sql = 'SELECT * FROM progress WHERE email="'.$_POST['email'].'" ORDER BY uniquekey';
        $progress_res = $mysqli->query($progress_sql);

        while ($progress_row = $progress_res->fetch_array(MYSQLI_ASSOC)) {
            $progress_item = new stdClass();
            $progress_item->lectureid = $progress_row["lectureid"];
            $progress_item->vid = $progress_row["vid"];
            $progress_item->progress = $progress_row["progress"];
            $progress_list[] = $progress_item;
            unset($progress_item);
        };

        while ($row = $res->fetch_array(MYSQLI_ASSOC)) {
            $lecture_progress = 0;
            $lecture_percentage = 0;

            echo '<div class="lecture_item" id="lecture_'.$row["lectureid"].'" onclick="selectLesson(\''.$_POST["email"].'\', \''.$row["lectureid"].'\', \''.$_POST["active"].'\');" onmouseover="selectLectureItem(\''.$row["lectureid"].'\', true);" onmouseleave="selectLectureItem(\''.$row["lectureid"].'\', false);">'.PHP_EOL;
            echo '<div class="lecture_thumbnail"><img src="'.$webinfo['lecture_thumbnail'].DIRECTORY_SEPARATOR.$row["lectureid"].$fileinfo['thumbnail_ext'].'"></div>'.PHP_EOL;
            echo '<div class="lecture_info">'.PHP_EOL;
            echo '<div class="lecture_title" id="title_'.$row["lectureid"].'">'.htmlentities($row["title"]).'</div>'.PHP_EOL;
            echo '<div class="lecture_difficulty">'.htmlentities($row["difficulty"]).'</div>'.PHP_EOL;

            foreach ($progress_list as $progress) {
                if ($progress->lectureid == $row['lectureid']) {
                    $lecture_progress += (int)($progress->progress);
                }
            }

            $lecture_percentage = round($lecture_progress / (int)$row['lessons']);

            echo '<div class="lecture_charts charts"><div class="charts__chart chart--p100 chart--xs"><div class="charts__chart chart--ed chart--p'.$lecture_percentage.'"></div></div></div>'.PHP_EOL;
            echo '</div>'.PHP_EOL;
            echo '</div>'.PHP_EOL;
        }
    }

    mysqli_close($mysqli);
?>

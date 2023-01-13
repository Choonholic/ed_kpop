<?php
    include_once 'setting_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'db_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'file_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'const_info.php';

    $mysqli = mysqli_connect($db['host'], $db['user'], $db['pass'], $db['name']);
    $mysqli->query("SET NAMES utf8");

    if ($_POST['active'] == USER_TRIAL_ACTIVE) {
        $sql = 'SELECT * FROM videos WHERE lectureid="'.$_POST['lectureid'].'" AND trial="'.TRIAL_OPENED.'" ORDER BY vid';
    } else {
        $sql = 'SELECT * FROM videos WHERE lectureid="'.$_POST['lectureid'].'" ORDER BY vid';
    }

    $res = $mysqli->query($sql);
    $count = mysqli_num_rows($res);

    if ($count == 0) {
        echo '<div class="lesson_none">No Lessons Available Now</div>'.PHP_EOL;
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
        }

        while ($row = $res->fetch_array(MYSQLI_ASSOC)) {
            $progress_found = false;

            echo '<div class="lesson_item" id="lesson_'.$row["vid"].'" onclick="openLesson(\''.$row["lectureid"].'\', \''.$row["vid"].'\', \''.$row["type"].'\');" onmouseover="selectItem(\''.$row["vid"].'\', true);" onmouseleave="selectItem(\''.$row["vid"].'\', false);">'.PHP_EOL;
            echo '<div class="lesson_thumbnail"><img src="'.$webinfo['lesson_thumbnail'].DIRECTORY_SEPARATOR.$row["lectureid"].DIRECTORY_SEPARATOR.$row["vid"].$fileinfo['thumbnail_ext'].'"></div>'.PHP_EOL;
            echo '<div class="lesson_info">'.PHP_EOL;
            echo '<div class="lesson_title" id="title_'.$row["vid"].'">'.$row["lesson"].'</div>'.PHP_EOL;
            echo '<div class="lesson_category">'.$row["category"].'</div>'.PHP_EOL;

            foreach ($progress_list as $progress) {
                if (($progress->lectureid == $row["lectureid"]) && ($progress->vid == $row["vid"])) {
                    $progress_found = true;
                    echo '<div class="lesson_charts charts"><div class="charts__chart chart--p100 chart--xs"><div class="charts__chart chart--ed chart--p'.$progress->progress.'"></div></div></div>'.PHP_EOL;
                }
            }

            if ($progress_found == false) {
                echo '<div class="lesson_charts charts"><div class="charts__chart chart--p100 chart--xs"><div class="charts__chart chart--ed chart--p0"></div></div></div>'.PHP_EOL;
            }

            echo '</div>'.PHP_EOL;
            echo '<div class="lesson_play" id="play_'.$row["vid"].'"><img src="images/play.png"></div>'.PHP_EOL;
            echo '</div>'.PHP_EOL;
        }
    }

    mysqli_close($mysqli);
?>

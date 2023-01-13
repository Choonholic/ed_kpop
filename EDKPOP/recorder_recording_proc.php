<?php
    include_once 'setting_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'db_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'file_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'const_info.php';

    $mysqli = mysqli_connect($db['host'], $db['user'], $db['pass'], $db['name']);
    $mysqli->query("SET NAMES utf8");
    $sql = 'SELECT * FROM recordings WHERE email="'.$_POST['email'].'"';
    $res = $mysqli->query($sql);
    $count = mysqli_num_rows($res);

    if ($count == 0) {
        echo '<div class="recording_none">No Recordings</div>'.PHP_EOL;
    } else {
        while ($row = $res->fetch_array(MYSQLI_ASSOC)) {
            echo '<div class="recording_item" id="recording_'.$row["uniquekey"].'" onmouseover="selectRecordingItem(\''.$row["uniquekey"].'\', true);" onmouseleave="selectRecordingItem(\''.$row["uniquekey"].'\', false);">'.PHP_EOL;

            if ($row['mode'] == 0) {
                echo '<div class="recording_thumbnail" onclick="playRecording(\''.$row["uniquekey"].'\', \''.$row["mode"].'\', \''.$row["vid"].'\', \''.$row["begin"].'\', \''.$row["end"].'\');"><img src="images/recording_single.png"></div>'.PHP_EOL;
                echo '<div class="recording_info" onclick="playRecording(\''.$row["uniquekey"].'\', \''.$row["mode"].'\', \''.$row["vid"].'\', \''.$row["begin"].'\', \''.$row["end"].'\');">'.PHP_EOL;
                echo '<div class="recording_lesson" id="title_'.$row["uniquekey"].'">Single Recording</div>'.PHP_EOL;
                echo '<div class="recording_category">Recording Only</div>'.PHP_EOL;
                echo '<div class="recording_uniquekey">'.$row["uniquekey"].'</div>'.PHP_EOL;
            } else {
                $video_sql = 'SELECT * FROM videos WHERE vid="'.$row['vid'].'"'.PHP_EOL;
                $video_res = $mysqli->query($video_sql);
                $video_row = $video_res->fetch_array(MYSQLI_ASSOC);

                echo '<div class="recording_thumbnail" onclick="playRecording(\''.$row["uniquekey"].'\', \''.$row["mode"].'\', \''.$row["vid"].'\', \''.$row["begin"].'\', \''.$row["end"].'\');"><img src="images/recording_dual.png"></div>'.PHP_EOL;
                echo '<div class="recording_info" onclick="playRecording(\''.$row["uniquekey"].'\', \''.$row["mode"].'\', \''.$row["vid"].'\', \''.$row["begin"].'\', \''.$row["end"].'\');">'.PHP_EOL;
                echo '<div class="recording_lesson" id="title_'.$row["uniquekey"].'">'.$video_row["lesson"].'</div>'.PHP_EOL;
                echo '<div class="recording_category">'.$video_row["category"].'</div>'.PHP_EOL;
                echo '<div class="recording_uniquekey">'.$row["uniquekey"].'</div>'.PHP_EOL;
            }

            echo '</div>'.PHP_EOL;
            echo '<div class="recording_download" id="download_'.$row["uniquekey"].'" title="Download" onclick="downloadRecording(\''.$row["uniquekey"].'\');"></div>'.PHP_EOL;
            echo '<div class="recording_delete" id="delete_'.$row["uniquekey"].'" title="Delete" onclick="deleteRecording(\''.$row["uniquekey"].'\');"></div>'.PHP_EOL;
            echo '</div>'.PHP_EOL;
        }
    }

    mysqli_close($mysqli);
?>

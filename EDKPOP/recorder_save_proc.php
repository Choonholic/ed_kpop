<?php
    include_once 'setting_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'db_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'file_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'const_info.php';

    $begin = (!$_POST['begin']) ? 0 : ($_POST['begin'] + $eds_settings['record_sync']);
    $end = (!$_POST['end']) ? 0 : ($_POST['end'] + $eds_settings['record_sync']);
    $duration = $end - $begin;
    $filter = '-vf "crop=min(iw\,ih):min(iw\,ih):(iw-ow)/2:(ih-oh)/2" ';;

    if (($_POST['flip']) && ($_POST['rotate'])) {
        $filter = '-vf "crop=min(iw\,ih):min(iw\,ih):(iw-ow)/2:(ih-oh)/2,'.$_POST['flip'].','.$_POST['rotate'].'" ';
    }

    if (($_POST['flip']) && (!$_POST['rotate'])) {
        $filter = '-vf "crop=min(iw\,ih):min(iw\,ih):(iw-ow)/2:(ih-oh)/2,'.$_POST['flip'].'" ';
    }

    if ((!$_POST['flip']) && ($_POST['rotate'])) {
        $filter = '-vf "crop=min(iw\,ih):min(iw\,ih):(iw-ow)/2:(ih-oh)/2,'.$_POST['rotate'].'" ';
    }

    $mysqli = mysqli_connect($db['host'], $db['user'], $db['pass'], $db['name']);
    $mysqli->query("SET NAMES utf8");
    $sql = 'INSERT INTO recordings (uniquekey, email, mode, vid, begin, end, created) VALUES ("'.$_POST['uniquekey'].'", "'.$_POST['email'].'", "'.$_POST['mode'].'", "'.$_POST['vid'].'", '.$begin.', '.$end.', "'.$_POST['created'].'")';
    $res = $mysqli->query($sql);

    $now = time();
    $date_short = date("YmdHis", $now);
    $date = date("Y-m-d H:i:s", $now);
    $uniquekey = $date_short.'_RECORD';
    $message = 'User ['.$_POST['email'].'] has recorded video ['.$_POST['uniquekey'].'('.$duration.' seconds)] in ['.(($_POST['mode'] == SINGLE_RECORDING) ? 'Single' : 'Dual').'] mode.';
    $sql = 'INSERT INTO logs (uniquekey, type, date, email, message) VALUES ("'.$uniquekey.'", "RECORD", "'.$date.'", "'.$_POST['email'].'", "'.$message.'")';
    $res = $mysqli->query($sql);

    mysqli_close($mysqli);

    $full_name = $fileinfo['recordings'].DIRECTORY_SEPARATOR.$_POST['uniquekey']."_noaudio".$fileinfo['recording_ext'];
    $encd_name = $fileinfo['recordings'].DIRECTORY_SEPARATOR.$_POST['uniquekey'].$fileinfo['recording_ext'];
    $output = array();

    if ($_POST['vid']) {
        $aud_name = $fileinfo['audio'].DIRECTORY_SEPARATOR.$_POST['vid'].$fileinfo['audio_ext'];
        $cut_name = $fileinfo['recordings'].DIRECTORY_SEPARATOR.$_POST['uniquekey'].$fileinfo['audio_ext'];
        $encode_sh = 'ffmpeg -ss '.$begin.' -t '.$duration.' -i "'.$aud_name.'" -acodec copy "'.$cut_name.'"';
        exec($encode_sh, $output);

        $encode_sh = 'ffmpeg -i "'.$full_name.'" -i "'.$cut_name.'" '.$filter.' -c:a aac "'.$encd_name.'"';

        exec($encode_sh, $output);
        unlink($cut_name);
        unlink($full_name);
    } else {
        $encode_sh = 'ffmpeg -i "'.$full_name.'" '.$filter.' "'.$encd_name.'"';

        exec($encode_sh, $output);
        unlink($full_name);
    }

    print_r($output);
    unset($output);
?>

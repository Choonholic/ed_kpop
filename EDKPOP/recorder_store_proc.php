<?php
    include_once 'setting_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'db_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'file_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'const_info.php';

    $now = time();
    $uniquekey = $_POST['email'].'_'.$_POST['vid'].'_'.date("YmdHis", $now);
    $full_name = $fileinfo['recordings'].DIRECTORY_SEPARATOR.$uniquekey.$fileinfo['internal_recording_ext'];
    $encd_name = $fileinfo['recordings'].DIRECTORY_SEPARATOR.$uniquekey."_noaudio".$fileinfo['recording_ext'];
    $encode_sh = 'ffmpeg -i "'.$full_name.'" -preset veryfast "'.$encd_name.'"';

    echo '<!-- '.date("Y-m-d H:i:s", $now).' -->'.PHP_EOL;
    echo '<!-- '.$uniquekey.' -->'.PHP_EOL;

    if ($_FILES["file"]["error"] == UPLOAD_ERR_OK) {
        $temp_name = $_FILES["file"]["tmp_name"];

        move_uploaded_file($temp_name, $full_name);
        exec($encode_sh);
        sleep(3);
        unlink($full_name);
    }
?>

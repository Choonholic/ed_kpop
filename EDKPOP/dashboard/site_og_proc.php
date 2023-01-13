<?php
    include_once 'setting_info.php';
    include_once 'setting_func.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'db_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'file_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'const_info.php';

    if (!isset($_SESSION)) {
        session_start();
    }

    $og_inputId = array("og_title", "og_url", "og_image", "og_type", "og_description", "og_locale");
    $count = count($og_inputId);
    $og_content = array();

    for ($i = 0; $i < $count; $i++) {
        $og_content[] = $_POST[$og_inputId[$i]];
    }

    saveSettings($fileinfo['settings'].DIRECTORY_SEPARATOR.$fileinfo['open_graph'].$fileinfo['settings_ext'], $og_content);
    unset($og_content);
    header('Location: site.php');
?>

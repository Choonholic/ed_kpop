<!doctype html>
<html lang="en">
<head>
    <?php
        include_once 'setting_info.php';
        include_once 'setting_func.php';
        include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'db_info.php';
        include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'file_info.php';
        include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'const_info.php';

        if (!isset($_SESSION)) {
            session_start();
        }

        $head_tag = loadSetting($fileinfo['settings'].DIRECTORY_SEPARATOR.$fileinfo['head_tag'].$fileinfo['settings_ext']);

        if ($head_tag !== FALSE) {
            echo $head_tag.PHP_EOL;
        }

        $og_content = loadSettings($fileinfo['settings'].DIRECTORY_SEPARATOR.$fileinfo['open_graph'].$fileinfo['settings_ext']);

        if ($og_content !== FALSE) {
            $og_property = array("og:title", "og:url", "og:image", "og:type", "og:description", "og:locale");
            $og_count = count($og_property);

            for ($i = 0; $i < $og_count; $i++) {
                echo '    <meta property="'.$og_property[$i].'" content="'.$og_content[$i].'" />'.PHP_EOL;
            }
        }
    ?>
    <meta charset="UTF-8">
    <title>ED: Online Personal Studio</title>
    <?php
        $messageActions = array(
            "practice" => "location.href='lecture.php'",
            "recorder" => "location.href='mainmenu.php'",
            "mypage_proc" => "location.href='mypage.php'",
        );
    ?>
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="stylesheet" type="text/css" href="css/common.css">
    <link rel="stylesheet" type="text/css" href="css/message.css">
    <script type="text/javascript" src="js/common.js"></script>
</head>
<body>
    <div id="header">
        <div id="logo">
            <a href="index.php"><img src="images/logo.png"></a>
        </div>
    </div>
    <div id='container'>
        <div id="msg_box">
            <p id="msg_title"><?php echo $_POST['title']; ?></p>
            <p id="msg_text"><?php echo $_POST['message']; ?></p>
            <div id="msg_btn" onclick="<?php
                $found = false;

                foreach ($messageActions as $key => $value) {
                    if ($_POST['from'] == $key) {
                        echo $value;
                        $found = true;
                    }
                }

                if ($found == false) {
                    echo "javascript:history.back(-1);";
                }
            ?>">OK</div>
        </div>
    </div>
</body>
</html>

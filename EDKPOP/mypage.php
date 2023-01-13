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

        if (isset($_SESSION['loggedin'])) {
            switch ($_SESSION['active']) {
                case USER_ACTIVE:
                    break;
                case USER_PAUSED:
                    header('Location: signout.php');
                    break;
                case USER_TRIAL_ACTIVE:
                    $now = date("Y-m-d H:i:s");

                    if ($now > $_SESSION['expire']) {
                        header('Location: signout.php');
                    }
                    break;
                case USER_TRIAL_INACTIVE:
                case USER_INACTIVE:
                    header('Location: activation.php');
                    break;
                default:
                    header('Location: signout.php');
                    break;
            }
        } else {
            header('Location: index.php');
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
        $mysqli = mysqli_connect($db['host'], $db['user'], $db['pass'], $db['name']);
        $mysqli->query("SET NAMES utf8");
        $sql = 'SELECT * FROM users WHERE email="'.$_SESSION['loggedin'].'"';
        $res = $mysqli->query($sql);
        $row = $res->fetch_array(MYSQLI_ASSOC);

        mysqli_close($mysqli);
    ?>
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="stylesheet" type="text/css" href="css/common.css">
    <link rel="stylesheet" type="text/css" href="css/mypage.css">
    <script type="text/javascript" src="js/common.js"></script>
</head>
<body>
    <div id="header">
        <div id="nickname"><?php echo $_SESSION['nickname']; ?></div>
    </div>
    <div id='container'>
        <div id="mypage_box">
            <div id="mypage_area">
                <form id="mypage_box_form" name="mypage_form" action="mypage_proc.php" method="post">
                    <div id="mypage_title">My Page</div>
                    <div id="nickname_box">
                        <input id="nick" length="30" maxlength="20" type="text" name="nickname" placeholder="Nickname" onfocusin="focusNickname(true);" onfocusout="focusNickname(false);" value="<?php echo $row['nickname']; ?>" required>
                    </div>
                    <div id="current_password_box">
                        <input id="current_password" length="30" maxlength="100" type="password" name="current_pwd" style="ime-mode: disabled;" placeholder="Current Password" onfocusin="focusCurrentPassword(true);" onfocusout="focusCurrentPassword(false);">
                    </div>
                    <div id="new_password_box">
                        <input id="new_password" length="30" maxlength="100" type="password" name="new_pwd" style="ime-mode: disabled;" placeholder="New Password" onfocusin="focusPassword(true);" onfocusout="focusPassword(false);">
                    </div>
                    <div id="password_verify_box">
                        <input id="password_verify" length="30" maxlength="100" type="password" name="pwd_chk" style="ime-mode: disabled;" placeholder="Verify Password" onfocusin="focusPasswordVerify(true);" onfocusin="focusPasswordVerify(false);">
                    </div>
                    <div>
                        <input type="hidden" name="from" value="<?php echo $_GET['from']; ?>">
                        <input id="save_btn" type="submit" value="Save">
                    </div>
                </form>
            </div>
            <div id="mypage_version">Version <b><?php echo $eds_settings['version']; ?></b></div>
        </div>
    </div>
    <script>
        function focusNickname(active) {
            if (active) {
                document.getElementById("nickname_box").style.backgroundColor = "#E4E4E4";
            } else {
                document.getElementById("nickname_box").style.background = "none";
            }
        }

        function focusCurrentPassword(active) {
            if (active) {
                document.getElementById("current_password_box").style.backgroundColor = "#E4E4E4";
            } else {
                document.getElementById("current_password_box").style.background = "none";
            }
        }

        function focusNewPassword(active) {
            if (active) {
                document.getElementById("new_password_box").style.backgroundColor = "#E4E4E4";
            } else {
                document.getElementById("new_password_box").style.background = "none";
            }
        }

        function focusPasswordVerify(active) {
            if (active) {
                document.getElementById("password_verify_box").style.backgroundColor = "#E4E4E4";
            } else {
                document.getElementById("password_verify_box").style.background = "none";
            }
        }
    </script>
</body>
</html>

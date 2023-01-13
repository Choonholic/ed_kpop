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
                header('Location: mainmenu.php');
                break;
            case USER_PAUSED:
                header('Location: signout.php');
                break;
            case USER_TRIAL_ACTIVE:
                $now = date("Y-m-d H:i:s");

                if ($now > $_SESSION['expire']) {
                    header('Location: signout.php');
                } else {
                    header('Location: mainmenu.php');
                }
                break;
            case USER_TRIAL_INACTIVE:
            case USER_INACTIVE:
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
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="stylesheet" type="text/css" href="css/common.css">
    <link rel="stylesheet" type="text/css" href="css/activation.css">
    <script type="text/javascript" src="js/common.js"></script>
</head>
<body>
    <div id="header">
        <div id="logo">
            <a onclick="signOut();"><img src="images/logo.png"></a>
        </div>
    </div>
    <div id='container'>
        <div id="left">
            <div id="title_back">
                Online<br>Personal<br>Studio
            </div>
            <div id="title_front">
                Online<br>Personal<br>Studio
            </div>
        </div>
        <div id="right">
            <div id="activation_box">
                <form id="activation_box_form" name="activation_form" action="activation_proc.php" method="post">
                    <div id="activation_title">Activate your account</div>
                    <div id="nickname_box">
                        <input id="nick" length="30" maxlength="20" type="text" name="nickname" placeholder="Nickname" onfocusin="focusNickname(true);" onfocusout="focusNickname(false);" required>
                    </div>
                    <div id="password_box">
                        <input id="password" length="30" maxlength="100" type="password" name="pwd" style="ime-mode: disabled;" placeholder="Password" onfocusin="focusPassword(true);" onfocusout="focusPassword(false);" required>
                    </div>
                    <div id="password_verify_box">
                        <input id="password_verify" length="30" maxlength="100" type="password" name="pwd_chk" style="ime-mode: disabled;" placeholder="Verify Password" onfocusin="focusPasswordVerify(true);" onfocusin="focusPasswordVerify(false);" required>
                    </div>
                    <div>
                        <input id="activation_btn" type="submit" value="Activate">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        function focusNickname(active) {
            if (active) {
                document.getElementById("nickname_box").style.backgroundColor = "#F4F4F4";
            } else {
                document.getElementById("nickname_box").style.background = "none";
            }
        }

        function focusPassword(active) {
            if (active) {
                document.getElementById("password_box").style.backgroundColor = "#F4F4F4";
            } else {
                document.getElementById("password_box").style.background = "none";
            }
        }

        function focusPasswordVerify(active) {
            if (active) {
                document.getElementById("password_verify_box").style.backgroundColor = "#F4F4F4";
            } else {
                document.getElementById("password_verify_box").style.background = "none";
            }
        }
    </script>
</body>
</html>

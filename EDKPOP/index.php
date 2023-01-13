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
                header('Location: activation.php');
                break;
            default:
                header('Location: signout.php');
                break;
        }
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
    <link rel="stylesheet" type="text/css" href="css/index.css">
</head>
<body>
    <div id="header">
        <div id="logo">
            <a href="index.php"><img src="images/logo.png"></a>
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
            <div id="login_box">
                <form id="login_box_form" name="login_form" action="login_proc.php" onsubmit="return checkRememberme();" method="post">
                    <div id="email_box">
                        <input id="email" length="30" maxlength="100" type="email" name="emailaddress" style="ime-mode: inactive;" placeholder="Email" onfocusin="focusEmail(true);" onfocusout="focusEmail(false);" required>
                    </div>
                    <div id="password_box">
                        <input id="password" length="30" maxlength="100" type="password" name="pwd" style="ime-mode: disabled;" placeholder="Password" onfocusin="focusPassword(true);" onfocusout="focusPassword(false);" required>
                    </div>
                    <div>
                        <input id="signin_btn" type="submit" value="Sign in">
                    </div>
                </form>
                <div id="terms"><a href="https://bekpopidol.com/pages/terms" target="_blank">Service Terms</a></div>
            </div>
        </div>
    </div>
    <div id="screen_blind"></div>
    <div id="fullscreen_box">
        <div id="fullscreen_close" onclick="closeFSPopup();">&times;</div>
        <div id="fullscreen_title">Notice</div>
        <div id="fullscreen_lead_message">ED Studio works best in Fullscreen mode.</div>
        <div id="fullscreen_message">
            <p>To enter in Fullscreen Mode:</p>
            <p>For Windows: Press <b>&lt;F11&gt;</b><br>
            For Mac: Press <b>&lt;Cmd&gt;</b>+<b>&lt;Control&gt;</b>+<b>&lt;F&gt;</b> or <b>&lt;Cmd&gt;</b>+<b>&lt;Shift&gt;</b>+<b>&lt;F&gt;</b><br>
            If it does not work, please check <a href="https://support.apple.com/guide/mac-help/use-apps-in-full-screen-mchl9c21d2be/mac" target="_blank">here</a>.</p>
        </div>
        <div id="fullscreen_proceed" onclick="closeFSPopup();">I understand and proceed without entering Fullscreen mode</div>
    </div>
    <script type="text/javascript" src="js/common.js"></script>
    <script>
        window.onload = function(event) {
            if (window.innerWidth == screen.width && window.innerHeight == screen.height) {
                document.getElementById("screen_blind").style.display = "none";
                document.getElementById("fullscreen_box").style.display = "none";
            } else {
                document.getElementById("screen_blind").style.display = "block";
                document.getElementById("fullscreen_box").style.display = "block";
            }
        };

        window.onresize = function(event) {
            if (window.innerWidth == screen.width && window.innerHeight == screen.height) {
                document.getElementById("screen_blind").style.display = "none";
                document.getElementById("fullscreen_box").style.display = "none";
            }
        }

        function closeFSPopup() {
            document.getElementById("screen_blind").style.display = "none";
            document.getElementById("fullscreen_box").style.display = "none";
        }

        function focusEmail(active) {
            if (active) {
                document.getElementById("email_box").style.backgroundColor = "#F4F4F4";
            } else {
                document.getElementById("email_box").style.background = "none";
            }
        }

        function focusPassword(active) {
            if (active) {
                document.getElementById("password_box").style.backgroundColor = "#F4F4F4";
            } else {
                document.getElementById("password_box").style.background = "none";
            }
        }
    </script>
</body>
</html>

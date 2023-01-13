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
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Teko:100,200,300,400,400i,500,600,700,800,900">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Abel:300,400,400i,500,600,700,800,900">
    <link rel="stylesheet" type="text/css" href="css/common.css">
    <link rel="stylesheet" type="text/css" href="css/mainmenu.css">
    <link rel="stylesheet" type="text/css" href="css/popup.css">
    <script type="text/javascript" src="js/common.js"></script>
</head>
<body>
    <div id="side_panel">
        <div id="menu_close_btn" onclick="closeNav()">&times;</div>
        <div id="menu_lecture_practice" class="menu_top_item" onclick="openLecturePractice();">Lecture Practice</div>
        <div id="menu_self_recording" class="menu_top_item" onclick="openSelfRecording();">Self-Recording</div>
        <div id="menu_ask_to_teacher" class="menu_top_item" onclick="openAskToTeacher();">Ask To Teacher</div>
        <div id="menu_ed_community" class="menu_top_item" onclick="openEDCommunity();">ED Community</div>
        <div id="menu_mypage" class="menu_bottom_item" onclick="openMyPage();">My Page</div>
        <div id="menu_sign_out" class="menu_bottom_item" onclick="signOut();">Sign Out</div>
    </div>
    <div id="header">
        <div id="menu_btn" onclick="openNav();">&#9776;</div>
        <div id="nickname">
            <?php
                print($_SESSION['nickname']);
            ?>
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
            <div id="main_menu">
                <div class="menu_item" id="lecture_practice" onmouseover="selectItem('lecture_practice', true);" onmouseleave="selectItem('lecture_practice', false);" onclick="openLecturePractice();">
                    <div class="menu_id" id="lecture_practice_id">01</div>
                    <div class="menu_title" id="lecture_practice_title">Lecture Practice</div>
                    <div class="menu_arrow"><img src="images/menu_arrow.png"></div>
                </div>
                <div class="menu_item" id="self_recording" onmouseover="selectItem('self_recording', true);" onmouseleave="selectItem('self_recording', false);" onclick="openSelfRecording();">
                    <div class="menu_id" id="self_recording_id">02</div>
                    <div class="menu_title" id="self_recording_title">Self-Recording</div>
                    <div class="menu_arrow"><img src="images/menu_arrow.png"></div>
                </div>
                <div class="menu_item" id="ask_to_teacher" onmouseover="selectItem('ask_to_teacher', true);" onmouseleave="selectItem('ask_to_teacher', false);" onclick="openAskToTeacher();">
                    <div class="menu_id" id="ask_to_teacher_id">03</div>
                    <div class="menu_title" id="ask_to_teacher_title">Ask To Teacher</div>
                    <div class="menu_arrow"><img src="images/menu_arrow.png"></div>
                </div>
                <div class="menu_item" id="ed_community" onmouseover="selectItem('ed_community', true);" onmouseleave="selectItem('ed_community', false);" onclick="openEDCommunity();">
                    <div class="menu_id" id="ed_community_id">04</div>
                    <div class="menu_title" id="ed_community_title">ED Community</div>
                    <div class="menu_arrow"><img src="images/menu_arrow.png"></div>
                </div>
            </div>
        </div>
    </div>
    <div id="popup_area">
        <div id="popup_close" onclick="closePopup();">&times;</div>
        <div id="popup_header"><img id="popup_image" src="https://edkpop.co/studio/storage/Layer_558.png"></div>
        <div id="popup_message">
            <p>Start ED Daily Personal Coaching and earn 1 extra month for FREE!</p>
            <ul>
                <li>Period: Oct. 5th ~ Dec. 4th, 2020 (2 months)</li>
                <li>How It Works: A designated coach cares and helps your training everyday through ED livechat.</li>
                <li>Feature: assigned coach, daily checkup, tailored practice plans, professional advice and more</li>
            </ul>
            <p>See email for more details.</p>
            <p style="text-align: center; font-family: Teko; font-size: 1.2vw; font-weight: 600;">ONLY 20 SEATS OPEN</p>
        </div>
        <div id="popup_button" onclick="claimSeat();">CLAIM YOUR SEAT</div>
    </div>
    <div id="form_area">
        <form id="message_form" method="post" action="message.php">
        </form>
    </div>
    <div id="screen_blind" onclick="closeNav();"></div>
    <script>
        function closePopup() {
            document.getElementById('popup_area').style.display = 'none';
        }

        function claimSeat() {
            closePopup();

            var newTab = window.open('https://bekpopidol.samcart.com/products/ed-daily-personal-coach?utm_source=TS&utm_medium=popup', '_blank');

            newTab.focus();
        }

        function comingSoon() {
            var form = document.getElementById('message_form');
            var titleInput = document.createElement('input');
            var textInput = document.createElement('input');
            var fromInput = document.createElement('input');

            titleInput.setAttribute('type', 'hidden');
            titleInput.setAttribute('name', 'title');
            titleInput.setAttribute('value', "Notice");
            form.appendChild(titleInput);

            textInput.setAttribute('type', 'hidden');
            textInput.setAttribute('name', 'message');
            textInput.setAttribute('value', "Coming Soon");
            form.appendChild(textInput);

            fromInput.setAttribute('type', 'hidden');
            fromInput.setAttribute('name', 'from');
            fromInput.setAttribute('value', "<?php echo basename($_SERVER['PHP_SELF'], '.php'); ?>");
            form.appendChild(fromInput);
            form.submit();
        }

        function openLecturePractice() {
            location.href = "lecture.php";
        }

        function openSelfRecording() {
            location.href = "recorder.php";
        }

        function openAskToTeacher() {
            comingSoon();
        }

        function openEDCommunity() {
            var newTab = window.open('http://aminoapps.com/c/EDKPOP', '_blank');

            newTab.focus();
        }

        function openMyPage() {
            location.href = "mypage.php?from=mainmenu";
        }

        function selectItem(item, active) {
            if (active) {
                document.getElementById(item + "_id").style.color = "#161616";
                document.getElementById(item + "_title").style.color = "#161616";
            } else {
                document.getElementById(item + "_id").style.color = "#F4F4F4";
                document.getElementById(item + "_title").style.color = "#F4F4F4";
            }
        }
    </script>
</body>
</html>

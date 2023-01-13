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

        $mysqli = mysqli_connect($db['host'], $db['user'], $db['pass'], $db['name']);
        $mysqli->query("SET NAMES utf8");
        $now = time();
        $date_short = date("YmdHis", $now);
        $date = date("Y-m-d H:i:s", $now);
        $uniquekey = $date_short.'_LECTURE';
        $message = 'User ['.$_SESSION['loggedin'].'] has opened vid ['.$_POST['vid'].'] (lectureid ['.$_POST['lectureid'].']).';
        $sql = 'INSERT INTO logs (uniquekey, type, date, email, message) VALUES ("'.$uniquekey.'", "LECTURE", "'.$date.'", "'.$_SESSION['loggedin'].'", "'.$message.'")';
        $res = $mysqli->query($sql);

        mysqli_close($mysqli);
    ?>
    <meta charset="UTF-8">
    <title>ED: Online Personal Studio</title>
    <?php
        $src = $webinfo['video'].DIRECTORY_SEPARATOR.$_POST['lectureid'].DIRECTORY_SEPARATOR.$_POST['vid'].DIRECTORY_SEPARATOR.$_POST['vid'].$fileinfo['hls_ext'];
        $vidtype = "var vidtype = '".$_POST['vidtype']."';".PHP_EOL;
        $aspect_ratio_str = "width = length; height = length;".PHP_EOL;
        $webcam_position_str = "var wc_x = ((window.innerWidth / 2) - length) / 2; var wc_y = (window.innerHeight - length) / 2;".PHP_EOL;
        $lesson_position_str = "var ls_x = ((window.innerWidth / 2) - width) / 2; var ls_y = (window.innerHeight - height) / 2;".PHP_EOL;

        switch ($_POST['vidtype']) {
            case VIDEO_16_9:
                $aspect_ratio_str = "width = length; height = 9 * length / 16;".PHP_EOL;
                $lesson_position_str = "var ls_x = ((window.innerWidth / 2) - width) / 2; var ls_y = (window.innerHeight - height) / 2;".PHP_EOL;
                break;
            case VIDEO_4_3:
                $aspect_ratio_str = "width = length; height = 3 * length / 4;".PHP_EOL;
                $lesson_position_str = "var ls_x = ((window.innerWidth / 2) - width) / 2; var ls_y = (window.innerHeight - height) / 2;".PHP_EOL;
                break;
            case VIDEO_9_16:
                $aspect_ratio_str = "height = length; width = 9 * length / 16;".PHP_EOL;
                $lesson_position_str = "var ls_x = ((window.innerWidth / 2) - width); var ls_y = (window.innerHeight - height) / 2;".PHP_EOL;
                break;
            case VIDEO_3_4:
                $aspect_ratio_str = "height = length; width = 3 * length / 4;".PHP_EOL;
                $lesson_position_str = "var ls_x = ((window.innerWidth / 2) - width); var ls_y = (window.innerHeight - height) / 2;".PHP_EOL;
                break;
            case VIDEO_1_1:
                $aspect_ratio_str = "width = length; height = length;".PHP_EOL;
                $lesson_position_str = "var ls_x = ((window.innerWidth / 2) - width) / 2; var ls_y = (window.innerHeight - height) / 2;".PHP_EOL;
                break;
        }
    ?>
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="stylesheet" type="text/css" href="css/common.css">
    <link rel="stylesheet" type="text/css" href="css/practice.css">
    <link rel="stylesheet" href="https://vjs.zencdn.net/7.8.4/video-js.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
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
        <div id="webcam_settings" title="Webcam Settings" onclick="webcamSettings();">
            <img id="webcam_settings_img" src="images/webcam_settings.png">
        </div>
        <div id="nickname"><?php echo $_SESSION['nickname']; ?></div>
        <div id="finish_practice" title="Finish Practice" onclick="finishPractice();">&times;</div>
    </div>
    <div id='container'>
        <div id="left">
            <div id="webcam_video_box">
                <video id="webcam_video" playsinline autoplay crossorigin="anonymous"></video>
            </div>
        </div>
        <div id="right">
            <div id="lesson_video_box">
                <video-js id="lesson_video" class="vjs-default-skin vjs-big-play-centered" autoplay playsinline controls crossorigin="anonymous">
                    <source src="<?php echo $src; ?>" type="application/x-mpegURL">
                </video-js>
            </div>
        </div>
    </div>
    <div id="settings_background">
        <div id="settings_box">
            <div id="settings_title">Camera Settings</div>
            <div id="camera_selector" class="select">
                <select id="videoSource">
                </select>
            </div>
            <div id="flip_selector" class="select">
                <select id="videoFlip">
                    <option value="no_flip" selected>No Flip</option>
                    <option value="flip">Flip</option>
                </select>
            </div>
            <div id="rotate_selector" class="select">
                <select id="videoRotate">
                    <option value="0d" selected>Normal</option>
                    <option value="270d">Rotate&nbsp;90&deg;&nbsp;Counter&#8209;Clockwise</option>
                    <option value="90d">Rotate&nbsp;90&deg;&nbsp;Clockwise</option>
                </select>
            </div>
            <div id="settings_close_btn" onclick="document.getElementById('settings_background').style.display = 'none';">Close</div>
        </div>
    </div>
    <div id="screen_blind" onclick="closeNav();"></div>
    <div id="form_area">
        <form id="message_form" method="post" action="message.php">
        </form>
    </div>
    <script src="https://vjs.zencdn.net/7.8.4/video.js"></script>
    <script src="js/camera.js"></script>
    <script src="js/common.js"></script>
    <script>
        var webcamPlayer;
        var lessonPlayer;
        var width;
        var height;
        var length;
        var tucount = 0;

        <?php echo $vidtype; ?>

        window.onload = function(event) {
            recalcVideoSize();

            webcamPlayer = document.getElementById("webcam_video");
            lessonPlayer = videojs('lesson_video', {
                controlBar: {
                    pictureInPictureToggle: false
                }
            });

            lessonPlayer.on("timeupdate", function() {
                if (tucount == 1) {
                    alert("src: <?php echo $src; ?>, [" + lessonPlayer.duration() + "]");
                }

                tucount++;
            });

            lessonPlayer.on("ended", function() {
                var duration = lessonPlayer.duration();
                <?php echo 'saveProgress("'.$_SESSION['loggedin'].'", "'.$_POST['lectureid'].'", "'.$_POST['vid'].'", duration, duration);'.PHP_EOL ?>
            });
        };

        window.onresize = function(event) {
            recalcVideoSize();
        };

        function recalcVideoSize() {
            width = window.innerWidth / 2;
            height = window.innerHeight;
            length = (width < height) ? width : height;

            <?php echo $aspect_ratio_str; ?>
            <?php echo $webcam_position_str; ?>
            <?php echo $lesson_position_str; ?>

            document.getElementById("webcam_video_box").style.width = length.toString() + "px";
            document.getElementById("webcam_video_box").style.height = length.toString() + "px";
            document.getElementById("webcam_video_box").style.left = wc_x.toString() + "px";
            document.getElementById("webcam_video_box").style.top = wc_y.toString() + "px";
            document.getElementById("lesson_video_box").style.width = width.toString() + "px";
            document.getElementById("lesson_video_box").style.height = height.toString() + "px";
            document.getElementById("lesson_video_box").style.right = ls_x.toString() + "px";
            document.getElementById("lesson_video_box").style.top = ls_y.toString() + "px";
        }

        function saveProgress(email, lectureid, vid, currentTime, duration) {
            $.ajax({
                type: 'post',
                url: 'practice_progress_proc.php',
                data: {
                    email: email,
                    lectureid: lectureid,
                    vid: vid,
                    currentTime: currentTime,
                    duration: duration
                },
                success: function(data) {
                },
                error: function(request, status, error) {
                    console.log("code: " + request.status + "\n" + "message: " + request.responseText + "\n" + "error: " + error);
                }
            });
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
            location.href = "mypage.php?from=practice";
        }

        function webcamSettings() {
            document.getElementById("settings_background").style.display = "block";
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
            textInput.setAttribute('value', "Coming Soon - return to select lecture");
            form.appendChild(textInput);

            fromInput.setAttribute('type', 'hidden');
            fromInput.setAttribute('name', 'from');
            fromInput.setAttribute('value', "<?php echo basename($_SERVER['PHP_SELF'], '.php'); ?>");
            form.appendChild(fromInput);
            form.submit();
        }

        function cameraNotFound() {
            if (forceWebcam == false) {
                var form = document.getElementById('message_form');
                var titleInput = document.createElement('input');
                var textInput = document.createElement('input');
                var fromInput = document.createElement('input');

                titleInput.setAttribute('type', 'hidden');
                titleInput.setAttribute('name', 'title');
                titleInput.setAttribute('value', "Error");
                form.appendChild(titleInput);

                textInput.setAttribute('type', 'hidden');
                textInput.setAttribute('name', 'message');
                textInput.setAttribute('value', "Camera Not Found");
                form.appendChild(textInput);

                fromInput.setAttribute('type', 'hidden');
                fromInput.setAttribute('name', 'from');
                fromInput.setAttribute('value', "<?php echo basename($_SERVER['PHP_SELF'], '.php'); ?>");
                form.appendChild(fromInput);
                form.submit();
            }
        }

        function finishPractice() {
            var currentTime = lessonPlayer.currentTime();
            var duration = lessonPlayer.duration();

            if (!lessonPlayer.paused()) {
                lessonPlayer.pause();
            }

            <?php echo 'saveProgress("'.$_SESSION['loggedin'].'", "'.$_POST['lectureid'].'", "'.$_POST['vid'].'", currentTime, duration);'.PHP_EOL ?>

            location.href = "lecture.php";
        }

        function flip_handler() {
            var transformText = "initial";

            if ($(this).children("option:selected").val() == "no_flip") {
                if ($("select#videoRotate").children("option:selected").val() == "0d") {
                } else if ($("select#videoRotate").children("option:selected").val() == "90d") {
                    transformText = "rotate(90deg)";
                } else if ($("select#videoRotate").children("option:selected").val() == "270d") {
                    transformText = "rotate(-90deg)";
                }
            } else {
                if ($("select#videoRotate").children("option:selected").val() == "0d") {
                    transformText = "rotateY(180deg)";
                } else if ($("select#videoRotate").children("option:selected").val() == "90d") {
                    transformText = "rotateY(180deg) rotate(90deg)";
                } else if ($("select#videoRotate").children("option:selected").val() == "270d") {
                    transformText = "rotateY(180deg) rotate(-90deg)";
                }
            }

            $("#webcam_video").css("transform", transformText);
        }

        function rotate_handler() {
            var transformText = "initial";

            if ($(this).children("option:selected").val() == "0d") {
                if ($("select#videoFlip").children("option:selected").val() == "no_flip") {
                } else {
                    transformText = "rotateY(180deg)";
                }
            } else if ($(this).children("option:selected").val() == "90d") {
                if ($("select#videoFlip").children("option:selected").val() == "no_flip") {
                    transformText = "rotate(90deg)";
                } else {
                    transformText = "rotateY(180deg) rotate(90deg)";
                }
            } else if ($(this).children("option:selected").val() == "270d") {
                if ($("select#videoFlip").children("option:selected").val() == "no_flip") {
                    transformText = "rotate(-90deg)";
                } else {
                    transformText = "rotateY(180deg) rotate(-90deg)";
                }
            }

            $("#webcam_video").css("transform", transformText);
        }

        $(document).ready(function() {
            $("#videoFlip").change(flip_handler);
            $("#videoRotate").change(rotate_handler);
        });
    </script>
</body>
</html>

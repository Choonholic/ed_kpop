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
        $webcam_src = $webinfo['recordings'].DIRECTORY_SEPARATOR.$_POST['uniquekey'].$fileinfo['recording_ext'];

        if ($_POST['mode'] == 1) {
            $mysqli = mysqli_connect($db['host'], $db['user'], $db['pass'], $db['name']);
            $mysqli->query("SET NAMES utf8");
            $sql = 'SELECT * FROM videos WHERE vid="'.$_POST['vid'].'"';
            $res = $mysqli->query($sql);
            $row = $res->fetch_array(MYSQLI_ASSOC);
            mysqli_close($mysqli);

            $lesson_src = $webinfo['video'].DIRECTORY_SEPARATOR.$row['lectureid'].DIRECTORY_SEPARATOR.$row['vid'].DIRECTORY_SEPARATOR.$row['vid'].$fileinfo['hls_ext'];
            $lesson_type = "var vidType = ".$row['type'].";".PHP_EOL;
        }
    ?>
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="stylesheet" type="text/css" href="css/common.css">
    <link rel="stylesheet" type="text/css" href="css/recorder_view.css">
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
        <div id="nickname"><?php echo $_SESSION['nickname']; ?></div>
        <div id="close_viewer" onclick="closeViewer();">&times;</div>
    </div>
    <div id='container'>
        <div id="left">
            <div id="webcam_video_box">
                <video id="webcam_video" playsinline muted autoplay crossorigin="anonymous"></video>
            </div>
        </div>
        <div id="right">
            <div id="lesson_video_box">
                <video-js id="lesson_video" class="vjs-default-skin vjs-big-play-centered" autoplay playsinline controls crossorigin="anonymous"></video-js>
            </div>
        </div>
    </div>
    <div id="screen_blind" onclick="closeNav();"></div>
    <div id="form_area">
        <form id="message_form" method="post" action="message.php">
        </form>
    </div>
    <script src="https://vjs.zencdn.net/7.8.4/video.js"></script>
    <script src="js/common.js"></script>
    <script>
        var webcamPlayer;
        var lessonPlayer;
        var width;
        var height;
        var length;
        var recordMode = <?php echo $_POST['mode']; ?>;

        window.onload = function(event) {
            recalcVideoSize();

            webcamPlayer = document.getElementById("webcam_video");
            webcamPlayer.src = "<?php echo $webcam_src; ?>";

            if (recordMode == 1) {
                lessonPlayer = videojs('lesson_video', {
                    controlBar: {
                        progressControl: false,
                        pictureInPictureToggle: false
                    }
                });

                lessonPlayer.src({
                    type: "application/x-mpegURL",
                    src: "<?php echo $lesson_src; ?>"
                });

                lessonPlayer.currentTime(<?php echo $_POST['begin']; ?>);

                lessonPlayer.on("pause", function() {
                    webcamPlayer.pause();
                });

                lessonPlayer.on("play", function() {
                    webcamPlayer.play();
                });

                webcamPlayer.onended = function() {
                    lessonPlayer.pause();
                    lessonPlayer.currentTime(<?php echo $_POST['begin']; ?>);
                };

                lessonPlayer.play();
            } else {
                webcamPlayer.play();
            }
        };

        window.onresize = function(event) {
            recalcVideoSize();
        };

        function recalcVideoSize() {
            var wc_x;
            var wc_y;
            var ls_x;
            var ls_y;

            width = window.innerWidth / 2;
            height = window.innerHeight;
            length = (width < height) ? width : height;

            if (recordMode == 0) {
                /* SINGLE_RECORDING */
                width = length;
                height = length;
                wc_x = (window.innerWidth - length) / 2;
                wc_y = (window.innerHeight - length) / 2;

                document.getElementById("webcam_video_box").style.width = length.toString() + "px";
                document.getElementById("webcam_video_box").style.height = length.toString() + "px";
                document.getElementById("webcam_video_box").style.left = wc_x.toString() + "px";
                document.getElementById("webcam_video_box").style.top = wc_y.toString() + "px";
                document.getElementById("right").style.display = "none";
            } else {
                <?php echo $lesson_type; ?>

                /* DUAL_RECORDING */
                width = length;
                height = length;
                wc_x = ((window.innerWidth / 2) - length) / 2;
                wc_y = (window.innerHeight - length) / 2;

                document.getElementById("webcam_video_box").style.width = length.toString() + "px";
                document.getElementById("webcam_video_box").style.height = length.toString() + "px";
                document.getElementById("webcam_video_box").style.left = wc_x.toString() + "px";
                document.getElementById("webcam_video_box").style.top = wc_y.toString() + "px";

                switch (vidType) {
                    case 0: /* VIDEO_16_9 */
                        width = length;
                        height = 9 * length / 16;
                        ls_x = ((window.innerWidth / 2) - width) / 2;
                        ls_y = (window.innerHeight - height) / 2;
                        break;
                    case 1: /* VIDEO_4_3 */
                        width = length;
                        height = 3 * length / 4;
                        ls_x = ((window.innerWidth / 2) - width) / 2;
                        ls_y = (window.innerHeight - height) / 2;
                        break;
                    case 2: /* VIDEO_9_16 */
                        height = length;
                        width = 9 * length / 16;
                        ls_x = ((window.innerWidth / 2) - width);
                        ls_y = (window.innerHeight - height) / 2;
                        break;
                    case 3: /* VIDEO_3_4 */
                        height = length;
                        width = 3 * length / 4;
                        ls_x = ((window.innerWidth / 2) - width);
                        ls_y = (window.innerHeight - height) / 2;
                        break;
                    case 4: /* VIDEO_1_1 */
                    default:
                        width = length;
                        height = length;
                        ls_x = ((window.innerWidth / 2) - width) / 2;
                        ls_y = (window.innerHeight - height) / 2;
                       break;
                }

                document.getElementById("lesson_video_box").style.width = width.toString() + "px";
                document.getElementById("lesson_video_box").style.height = height.toString() + "px";
                document.getElementById("lesson_video_box").style.right = ls_x.toString() + "px";
                document.getElementById("lesson_video_box").style.top = ls_y.toString() + "px";
                document.getElementById("right").style.display = "block";
            }
        }

        function openLecturePractice() {
            location.href = "lecture.php";
        }

        function openSelfRecording() {
            closeViewer();
        }

        function openAskToTeacher() {
            comingSoon();
        }

        function openEDCommunity() {
            var newTab = window.open('http://aminoapps.com/c/EDKPOP', '_blank');

            newTab.focus();
        }

        function openMyPage() {
            location.href = "mypage.php?from=recorder_view";
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

        function closeViewer() {
            location.href = "recorder.php";
        }
    </script>
</body>
</html>

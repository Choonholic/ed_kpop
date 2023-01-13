<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ED: Online Personal Studio</title>
    <?php
        include_once 'setting_info.php';
        include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'db_info.php';
        include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'file_info.php';
        include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'const_info.php';

        if (!isset($_SESSION)) {
            session_start();
        }

        if (isset($_SESSION['db_loggedin'])) {
            if ($_SESSION['db_active'] != USER_ACTIVE) {
                header('Location: activation.php');
            }
        } else {
            header('Location: index.php');
        }

        $lesson_src = $webinfo['video'].DIRECTORY_SEPARATOR.$_POST['lectureid'].DIRECTORY_SEPARATOR.$_POST['vid'].DIRECTORY_SEPARATOR.$_POST['vid'].$fileinfo['hls_ext'];
        $lesson_type = "var vidType = ".$_POST['type'].";".PHP_EOL;
    ?>
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="stylesheet" type="text/css" href="css/common.css">
    <link rel="stylesheet" type="text/css" href="css/video_play.css">
    <link rel="stylesheet" href="https://vjs.zencdn.net/7.8.4/video-js.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
</head>
<body>
    <div id='container'>
        <div id="video_box">
            <div id="lesson_video_box">
                <video-js id="lesson_video" class="vjs-default-skin vjs-big-play-centered" autoplay playsinline controls crossorigin="anonymous"></video-js>
            </div>
        </div>
    </div>
    <script src="https://vjs.zencdn.net/7.8.4/video.js"></script>
    <script src="js/common.js"></script>
    <script>
        var lessonPlayer;
        var width;
        var height;
        var length;

        window.onload = function(event) {
            recalcVideoSize();

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

            lessonPlayer.play();
        };

        window.onresize = function(event) {
            recalcVideoSize();
        };

        function recalcVideoSize() {
            var ls_x;
            var ls_y;

            width = window.innerWidth / 2;
            height = window.innerHeight;
            length = (width < height) ? width : height;
            <?php echo $lesson_type; ?>

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
        }
    </script>
</body>
</html>

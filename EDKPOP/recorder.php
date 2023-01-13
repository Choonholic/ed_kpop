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
    <link rel="stylesheet" type="text/css" href="css/common.css">
    <link rel="stylesheet" type="text/css" href="css/chart.css">
    <link rel="stylesheet" type="text/css" href="css/recorder.css">
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
        <div id="record_list" title="Your Recording List" onclick="recordingList();">
            <img id="record_list_img" src="images/record_list.png">
        </div>
        <div id="record_start" title="Start Recording" onclick="recordInitiate();">
            <img id="record_start_img" src="images/record_start.png">
        </div>
        <div id="record_stop" title="Stop Recording" onclick="recordStop(true);">
            <img id="record_stop_img" src="images/record_stop.png">
        </div>
        <div id="single_recording" title="Single Recording Mode" onclick="singleRecording();">
            <img id="single_recording_img" src="images/single_rec_selected.png">
        </div>
        <div id="dual_recording" title="Dual Recording Mode" onclick="dualRecording();">
            <img id="dual_recording_img" src="images/dual_rec.png">
        </div>
        <div id="webcam_settings" title="Webcam Settings" onclick="webcamSettings();">
            <img id="webcam_settings_img" src="images/webcam_settings.png">
        </div>
        <div id="nickname"><?php echo $_SESSION['nickname']; ?></div>
        <div id="close_recorder" title="Close Recorder" onclick="closeRecorder();">&times;</div>
    </div>
    <div id='container'>
        <div id="left">
            <div id="webcam_video_box">
                <video id="webcam_video" playsinline autoplay crossorigin="anonymous"></video>
            </div>
        </div>
        <div id="right">
            <div id="lesson_video_box">
                <video-js id="lesson_video" class="vjs-default-skin vjs-big-play-centered" autoplay playsinline controls crossorigin="anonymous"></video-js>
            </div>
        </div>
    </div>
    <div id="record_countdown"></div>
    <div id="recording_exitor" onclick="exitSelectRecording();"></div>
    <div id="recording_container">
        <div id="recording_list_close" onclick="exitSelectRecording();">&times;</div>
        <div id="recording_list_title">Choose Recording</div>
        <div id="recording_scroll_up">
            <img src="images/scroll_up.png">
        </div>
        <div id="recording_list">
        </div>
        <div id="recording_scroll_down">
            <img src="images/scroll_down.png">
        </div>
    </div>
    <div id="lecture_exitor" onclick="exitSelectLecture(true);"></div>
    <div id="lecture_container">
        <div id="lecture_list_close" onclick="exitSelectLecture(true);">&times;</div>
        <div id="lecture_list_title">Choose Lecture</div>
        <div id="lecture_scroll_up">
            <img src="images/scroll_up.png">
        </div>
        <div id="lecture_list">
        </div>
        <div id="lecture_scroll_down">
            <img src="images/scroll_down.png">
        </div>
    </div>
    <div id="lesson_exitor" onclick="exitSelectLesson(true);"></div>
    <div id="lesson_container">
        <div id="lesson_list_close" onclick="exitSelectLesson(true);">&times;</div>
        <div id="lesson_list_title">Choose Lesson</div>
        <div id="lesson_scroll_up">
            <img src="images/scroll_up.png">
        </div>
        <div id="lesson_list">
        </div>
        <div id="lesson_scroll_down">
            <img src="images/scroll_down.png">
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
    <div id="process_box">
        <div id="process_message">
            <p id="process_message">Processing Recorded Video... Please Wait...</p>
            <p id="process_description"><b>DO NOT</b> close this window or navigate to the other pages,<br>or the tasks will be cancelled unexpectedly.</p>
        </div>
    </div>
    <div id="form_area">
        <form id="message_form" method="post" action="message.php">
        </form>
        <form id="play_form" method="post" action="recorder_view.php">
        </form>
    </div>
    <script src="https://vjs.zencdn.net/7.8.4/video.js"></script>
    <script src="js/camera.js"></script>
    <script src="js/common.js"></script>
    <script>
        var email = "<?php echo $_SESSION['loggedin']; ?>";
        var active = "<?php echo $_SESSION['active']; ?>";
        var recordPeriod = 1000 * <?php echo $eds_settings['record_maxsec']; ?>;
        var recordHandler;
        var recordMode;
        var recordStatus;
        var recordVid;
        var recordBegin;
        var recordEnd;
        var recordBlobs;
        var recordSave;
        var recordCreated;
        var recordUniquekey;
        var recordFlip;
        var recordRotate;
        var lessonCount;
        var lessonLectureid;
        var lessonVid;
        var lessonType;
        var mediaRecorder;
        var vidType;
        var webcamPlayer;
        var lessonPlayer;
        var width;
        var height;
        var length;
        var countHandler;
        var countRemain = <?php echo $eds_settings['record_countdown']; ?>;

        window.onload = function(event) {
            recordMode = 0; /* SINGLE_RECORDING */
            recordStatus = 0; /* RECORD_STOPPED */

            recalcVideoSize();

            webcamPlayer = document.getElementById("webcam_video");
            lessonPlayer = null;
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

        function parseCreated(html) {
            var length1;
            var length2;

            length1 = html.indexOf(" -->", 0);
            recordCreated = html.substr(5, length1 - 5);
            length2 = html.indexOf(" -->", length1 + 4);
            recordUniquekey = html.substr(length1 + 10, length2 - length1 - 10);
        }

        function handleDataAvailable(event) {
            if (event.data && event.data.size > 0 && recordSave) {
                var filename = email + '_' + recordVid + '.webm';
                var data = new FormData();

                recordBlobs.push(event.data);

                recordSave = false;

                const blob = new Blob(recordBlobs);
                const url = window.URL.createObjectURL(blob);
                var fileObject = new File([blob], filename);

                data.append('file', fileObject);
                data.append('email', email);
                data.append('vid', recordVid);

                document.getElementById("process_box").style.display = "block";

                $.ajax({
                    url: "recorder_store_proc.php",
                    type: 'POST',
                    data: data,
                    contentType: false,
                    processData: false,
                    success: function(data) {
                        parseCreated(data);

                        if (recordMode == 0) {
                            saveVideo(recordUniquekey, email, recordMode, null, null, null, recordCreated, recordFlip, recordRotate);
                        } else {
                            saveVideo(recordUniquekey, email, recordMode, recordVid, recordBegin, recordEnd, recordCreated, recordFlip, recordRotate);
                        }
                        document.getElementById("process_box").style.display = "none";
                    },
                    error: function(request, status, error) {
                        console.log("code: " + request.status + "\n" + "message: " + request.responseText + "\n" + "error: " + error);
                        document.getElementById("process_box").style.display = "none";
                    }
                });

            }
        }

        function recordingList() {
            recordStop(false);
            selectRecording(email);
        }

        function recordInitiate() {
            if (window.stream != null) {
                countHandler = setInterval(function() {
                    if (countRemain > 0) {
                        document.getElementById("record_countdown").innerHTML = countRemain;
                        document.getElementById('record_countdown').style.display = 'block';

                        countRemain--;
                    } else {
                        document.getElementById("record_countdown").innerHTML = '';
                        document.getElementById('record_countdown').style.display = 'none';
                        clearInterval(countHandler);

                        countRemain = <?php echo $eds_settings['record_countdown']; ?>;

                        recordStart();
                    }
                }, 1000);
            }
        }

        function recordStart() {
            if (recordMode == 0) {
                recordVid = null;
                recordBegin = null;
                recordEnd = null;
            } else {
                lessonPlayer.pause();
                recordBegin = lessonPlayer.currentTime();
                recordEnd = null;
            }

            var recordOptions = {
                audioBitsPerSecond: 128000,
                videoBitsPerSecond: 2500000
            };

            recordSave = false;
            recordBlobs = [];

            mediaRecorder = new MediaRecorder(window.stream, recordOptions);
            mediaRecorder.ondataavailable = handleDataAvailable;

            mediaRecorder.start();

            if (recordMode == 0) {
                recordHandler = setTimeout(function() {
                    recordStop(true);
                }, recordPeriod);
            }

            recordStatus = 1; /* RECORD_STARTED */

            document.getElementById("record_start").style.display = "none";
            document.getElementById("record_stop").style.display = "block";
            document.getElementById("webcam_settings_img").src = "images/webcam_settings_disabled.png";

            if (recordMode == 1) {
                lessonPlayer.controls(false);
                lessonPlayer.play();
            }
        }

        function recordStop(save) {
            if (recordStatus == 1) {
                clearTimeout(recordHandler);
                mediaRecorder.stop();

                if (save) {
                    recordSave = true;

                    if (recordMode == 1) {
                        lessonPlayer.pause();
                        lessonPlayer.controls(true);
                        recordEnd = lessonPlayer.currentTime();
                    }
                }

                if (recordMode == 1) {
                    lessonPlayer.pause();
                    lessonPlayer.currentTime(0);
                }

                recordStatus = 0; /* RECORD_STOPPED */

                document.getElementById("record_start").style.display = "block";
                document.getElementById("record_stop").style.display = "none";
                document.getElementById("webcam_settings_img").src = "images/webcam_settings.png";
            }
        }

        function singleRecording() {
            if (recordMode != 0) {
                recordStop(false);

                recordMode = 0; /* SINGLE_RECORDING */
                document.getElementById("single_recording_img").src = "images/single_rec_selected.png";
                document.getElementById("dual_recording_img").src = "images/dual_rec.png";

                if (lessonPlayer != null) {
                    lessonPlayer.pause();
                    lessonPlayer.reset();
                    lessonPlayer = null;
                }

                recalcVideoSize();
            }
        }

        function dualRecording() {
            if (recordMode != 1) {
                recordStop(false);

                recordMode = 1; /* DUAL_RECORDING */
                document.getElementById("single_recording_img").src = "images/single_rec.png";
                document.getElementById("dual_recording_img").src = "images/dual_rec_selected.png";

                selectLecture(email, active);
                recalcVideoSize();
            }
        }

        function selectRecordingItem(id, active) {
            if (active) {
                document.getElementById("title_" + id).style.color = "#161616";
            } else {
                document.getElementById("title_" + id).style.color = "#F4F4F4";
            }
        }

        function selectRecording(email) {
            $.ajax({
                type: 'post',
                url: 'recorder_recording_proc.php',
                data: {
                    email: email
                },
                success: function(data) {
                    $("#recording_list").html(data);
                    enterSelectRecording();
                },
                error: function(request, status, error) {
                    console.log("code: " + request.status + "\n" + "message: " + request.responseText + "\n" + "error: " + error);
                }
            });
        }

        function enterSelectRecording() {
            $("#recording_exitor").css("display", "block");
            $("#recording_container").animate({ right: '0' }, "fast", "linear");
        }

        function exitSelectRecording() {
            $("#recording_list").html(null);
            $("#recording_exitor").css("display", "none");
            $("#recording_container").animate({ right: '-50vw' }, "fast", "linear");
        }

        function selectLectureItem(id, active) {
            if (active) {
                document.getElementById("title_" + id).style.color = "#161616";
            } else {
                document.getElementById("title_" + id).style.color = "#F4F4F4";
            }
        }

        function selectLecture(email, active) {
            $.ajax({
                type: 'post',
                url: 'recorder_lecture_proc.php',
                data: {
                    email: email,
                    active: active
                },
                success: function(data) {
                    $("#lecture_list").html(data);
                    enterSelectLecture();
                },
                error: function(request, status, error) {
                    console.log("code: " + request.status + "\n" + "message: " + request.responseText + "\n" + "error: " + error);
                }
            });
        }

        function enterSelectLecture() {
            $("#lecture_exitor").css("display", "block");
            $("#lecture_container").animate({ right: '0' }, "fast", "linear");
        }

        function exitSelectLecture(force) {
            $("#lecture_list").html(null);
            $("#lecture_exitor").css("display", "none");
            $("#lecture_container").animate({ right: '-50vw' }, "fast", "linear");

            if (force) {
                singleRecording();
            }
        }

        function parseLesson(html) {
            var length;

            length = html.indexOf(" -->", 0);
            lessonCount = parseInt(html.substr(5, length - 5));

            if (lessonCount == 1) {
                var lengthParse;

                lengthParse = html.indexOf(" -->", length + 4);
                lessonLectureid = html.substr(length + 10, lengthParse - length - 10);

                length = lengthParse;
                lengthParse = html.indexOf(" -->", length + 4);
                lessonVid = html.substr(length + 10, lengthParse - length - 10);

                length = lengthParse;
                lengthParse = html.indexOf(" -->", length + 4);
                lessonType = html.substr(length + 10, lengthParse - length - 10);
            }
        }

        function selectLessonItem(id, active) {
            if (active) {
                document.getElementById("title_" + id).style.color = "#161616";
                document.getElementById("play_" + id).style.display = "block";
            } else {
                document.getElementById("title_" + id).style.color = "#F4F4F4";
                document.getElementById("play_" + id).style.display = "none";
            }
        }

        function selectLesson(email, lectureid, active) {
            $.ajax({
                type: 'post',
                url: 'recorder_lesson_proc.php',
                data: {
                    email: email,
                    lectureid: lectureid,
                    active: active
                },
                success: function(data) {
                    parseLesson(data);

                    if (lessonCount == 1) {
                        exitSelectLecture(false);
                        openLessonVideo(lessonLectureid, lessonVid, lessonType);
                    } else {
                        $("#lesson_list").html(data);
                        exitSelectLecture(false);
                        enterSelectLesson();
                    }
                },
                error: function(request, status, error) {
                    console.log("code: " + request.status + "\n" + "message: " + request.responseText + "\n" + "error: " + error);
                }
            });
        }

        function enterSelectLesson() {
            $("#lesson_exitor").css("display", "block");
            $("#lesson_container").animate({ right: '0' }, "fast", "linear");
        }

        function exitSelectLesson(force) {
            $("#lesson_list").html(null);
            $("#lesson_exitor").css("display", "none");
            $("#lesson_container").animate({ right: '-50vw' }, "fast", "linear");

            if (force) {
                singleRecording();
            }
        }

        function openLessonVideo(lectureid, vid, type) {
            exitSelectLesson(false);

            recordVid = vid;

            <?php echo 'var src = "'.$webinfo['video'].DIRECTORY_SEPARATOR.'" + lectureid + "'.DIRECTORY_SEPARATOR.'" + vid + "'.DIRECTORY_SEPARATOR.'" + vid + "'.$fileinfo['hls_ext'].'";'.PHP_EOL; ?>

            lessonPlayer = videojs('lesson_video', {
                controlBar: {
                    pictureInPictureToggle: false
                }
            });

            lessonPlayer.src({
                type: "application/x-mpegURL",
                src: src
            });

            lessonPlayer.on("ended", function() {
                recordStop(true);
            });
        }

        function saveVideo(uniquekey, email, mode, vid, begin, end, created, flip, rotate) {
            $.ajax({
                type: 'post',
                url: 'recorder_save_proc.php',
                data: {
                    uniquekey: uniquekey,
                    email: email,
                    mode: mode,
                    vid: vid,
                    begin: begin,
                    end: end,
                    created: created,
                    flip: flip,
                    rotate: rotate
                },
                success: function(data) {
                },
                error: function(request, status, error) {
                }
            });
        }

        function playRecording(uniquekey, mode, vid, begin, end) {
            exitSelectRecording();

            var form = document.getElementById('play_form');
            var uniquekeyInput = document.createElement('input');
            var modeInput = document.createElement('input');
            var vidInput = document.createElement("input");
            var beginInput = document.createElement('input');
            var endInput = document.createElement('input');

            uniquekeyInput.setAttribute('type', 'hidden');
            uniquekeyInput.setAttribute('name', 'uniquekey');
            uniquekeyInput.setAttribute('value', uniquekey);
            form.appendChild(uniquekeyInput);

            modeInput.setAttribute('type', 'hidden');
            modeInput.setAttribute('name', 'mode');
            modeInput.setAttribute('value', mode);
            form.appendChild(modeInput);

            vidInput.setAttribute('type', 'hidden');
            vidInput.setAttribute('name', 'vid');
            vidInput.setAttribute('value', vid);
            form.appendChild(vidInput);

            beginInput.setAttribute('type', 'hidden');
            beginInput.setAttribute('name', 'begin');
            beginInput.setAttribute('value', begin);
            form.appendChild(beginInput);

            endInput.setAttribute('type', 'hidden');
            endInput.setAttribute('name', 'end');
            endInput.setAttribute('value', end);
            form.appendChild(endInput);
            form.submit();
        }

        function downloadRecording(uniquekey) {
            exitSelectRecording();
            location.href = "recorder_download_proc.php?file=" + encodeURIComponent(uniquekey);
        }

        function deleteRecording(uniquekey) {
            $.ajax({
                type: 'post',
                url: 'recorder_delete_proc.php',
                data: {
                    uniquekey: uniquekey
                },
                success: function() {
                    exitSelectRecording();
                },
                error: function(request, status, error) {
                    console.log("code: " + request.status + "\n" + "message: " + request.responseText + "\n" + "error: " + error);
                }
            });
        }

        function openLecturePractice() {
            recordStop(false);
            location.href = "lecture.php";
        }

        function openSelfRecording() {
        }

        function openAskToTeacher() {
            recordStop(false);
            comingSoon();
        }

        function openEDCommunity() {
            recordStop(false);

            var newTab = window.open('http://aminoapps.com/c/EDKPOP', '_blank');

            newTab.focus();
        }

        function openMyPage() {
            recordStop(false);
            location.href = "mypage.php?from=recorder";
        }

        function webcamSettings() {
            if (recordStatus == 0) {
                document.getElementById("settings_background").style.display = "block";
            }
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

        function closeRecorder() {
            recordStop(false);
            location.href = "mainmenu.php";
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

                recordFlip = null;
            } else {
                if ($("select#videoRotate").children("option:selected").val() == "0d") {
                    transformText = "rotateY(180deg)";
                } else if ($("select#videoRotate").children("option:selected").val() == "90d") {
                    transformText = "rotateY(180deg) rotate(90deg)";
                } else if ($("select#videoRotate").children("option:selected").val() == "270d") {
                    transformText = "rotateY(180deg) rotate(-90deg)";
                }

                recordFlip = "hflip";
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

                recordRotate = null;
            } else if ($(this).children("option:selected").val() == "90d") {
                if ($("select#videoFlip").children("option:selected").val() == "no_flip") {
                    transformText = "rotate(90deg)";
                } else {
                    transformText = "rotateY(180deg) rotate(90deg)";
                }

                recordRotate = "transpose=2";
            } else if ($(this).children("option:selected").val() == "270d") {
                if ($("select#videoFlip").children("option:selected").val() == "no_flip") {
                    transformText = "rotate(-90deg)";
                } else {
                    transformText = "rotateY(180deg) rotate(-90deg)";
                }

                recordRotate = "transpose=1";
            }

            $("#webcam_video").css("transform", transformText);
        }

        $(document).ready(function() {
            $("#videoFlip").change(flip_handler);
            $("#videoRotate").change(rotate_handler);

            $('#lecture_scroll_up').click(function() {
                $('#lecture_list').animate({
                    scrollTop: '-=400px'
                }, 500);
            });

            $('#lecture_scroll_down').click(function() {
                $('#lecture_list').animate({
                    scrollTop: '+=400px'
                }, 500);
            });

            $('#lesson_scroll_up').click(function() {
                $('#lesson_list').animate({
                    scrollTop: '-=400px'
                }, 500);
            });

            $('#lesson_scroll_down').click(function() {
                $('#lesson_list').animate({
                    scrollTop: '+=400px'
                }, 500);
            });
        });
    </script>
</body>
</html>

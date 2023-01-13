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

        switch ($_SESSION['active']) {
            case USER_TRIAL_ACTIVE:
                $sql = 'SELECT * FROM lectures WHERE status="'.LECTURE_ACTIVE.'" AND trial="'.TRIAL_OPENED.'" ORDER BY lectureid LIMIT 1';
                break;
            default:
                $sql = 'SELECT * FROM lectures WHERE status="'.LECTURE_ACTIVE.'" ORDER BY lectureid LIMIT 1';
                break;
        }

        $res = $mysqli->query($sql);
        $row = $res->fetch_array(MYSQLI_ASSOC);
        $lastlectureid = $row['lectureid'];

        switch ($_SESSION['active']) {
            case USER_TRIAL_ACTIVE:
                $sql = 'SELECT * FROM videos WHERE lectureid="'.$lastlectureid.'" AND trial="'.TRIAL_OPENED.'" ORDER BY vid LIMIT 1';
                break;
            default:
                $sql = 'SELECT * FROM videos WHERE lectureid="'.$lastlectureid.'" ORDER BY vid LIMIT 1';
                break;
        }

        $res = $mysqli->query($sql);
        $row = $res->fetch_array(MYSQLI_ASSOC);
        $lastpos = 0;

        if (strlen($_COOKIE['lastlectureid']) && strlen($_COOKIE['lastpos'])) {
            $lastlectureid = $_COOKIE['lastlectureid'];
            $lastpos = intval($_COOKIE['lastpos']);
        }
    ?>
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="stylesheet" type="text/css" href="css/common.css">
    <link rel="stylesheet" type="text/css" href="css/lecture.css">
    <link rel="stylesheet" type="text/css" href="css/chart.css">
    <link rel="stylesheet" type="text/css" href="js/assets/owl.carousel.min.css">
    <link rel="stylesheet" type="text/css" href="js/assets/owl.theme.default.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script src="js/owl.carousel.min.js"></script>
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
        <div id="nickname"><?php echo $_SESSION['nickname']; ?></div>
    </div>
    <?php
        $lecture_list = array();

        $mysqli = mysqli_connect($db['host'], $db['user'], $db['pass'], $db['name']);
        $mysqli->query("SET NAMES utf8");

        if ($eds_settings['force_all_lectures'] == true) {
            switch ($_SESSION['active']) {
                case USER_TRIAL_ACTIVE:
                    $sql = 'SELECT * FROM lectures WHERE trial="'.TRIAL_OPENED.'" ORDER BY lectureid';
                    break;
                default:
                    $sql = 'SELECT * FROM lectures WHERE 1 ORDER BY lectureid';
                    break;
            }
        } else {
            switch ($_SESSION['active']) {
                case USER_TRIAL_ACTIVE:
                    $sql = 'SELECT * FROM lectures WHERE status="'.LECTURE_ACTIVE.'" AND trial="'.TRIAL_OPENED.'" ORDER BY lectureid';
                    break;
                default:
                    $sql = 'SELECT * FROM lectures WHERE status="'.LECTURE_ACTIVE.'" ORDER BY lectureid';
                    break;
            }
        }

        $res = $mysqli->query($sql);

        while ($row = $res->fetch_array(MYSQLI_ASSOC)) {
            $lecture_item = new stdClass();
            $lecture_item->lectureid = $row["lectureid"];
            $lecture_item->title = $row["title"];
            $lecture_item->difficulty = $row["difficulty"];
            $lecture_item->description = $row["description"];
            $lecture_item->lessons = $row["lessons"];
            $lecture_item->server = $row["server"];
            $lecture_list[] = $lecture_item;
            unset($lecture_item);
        }

        $progress_list = array();

        $sql = 'SELECT * FROM progress WHERE email="'.$_SESSION['loggedin'].'" ORDER BY uniquekey';
        $res = $mysqli->query($sql);

        mysqli_close($mysqli);

        while ($row = $res->fetch_array(MYSQLI_ASSOC)) {
            $progress_item = new stdClass();
            $progress_item->lectureid = $row["lectureid"];
            $progress_item->vid = $row["vid"];
            $progress_item->progress = $row["progress"];
            $progress_list[] = $progress_item;
            unset($progress_item);
        };
    ?>
    <div id="video_container">
        <div id="lecture_scroll_left">
            <img src="images/scroll_left.png">
        </div>
        <div id="videos" class="owl-carousel owl-theme">
            <?php
                $pos = 0;

                foreach ($lecture_list as $item) {
                    $lecture_progress = 0;
                    $lecture_percentage = 0;

                    echo '<div class="lecture_item" id="'.$item->lectureid.'" onclick="preview(\''.$item->lectureid.'\', \''.$pos.'\');">'.PHP_EOL;
                    echo '<div class="lecture_item_inner">'.PHP_EOL;
                    echo '<div class="thumbnail"><img src="'.$webinfo['lecture_thumbnail'].DIRECTORY_SEPARATOR.$item->lectureid.$fileinfo['thumbnail_ext'].'"></div>'.PHP_EOL;

                    foreach ($progress_list as $progress) {
                        if ($progress->lectureid == $item->lectureid) {
                            $lecture_progress += (int)($progress->progress);
                        }
                    }

                    $lecture_percentage = round($lecture_progress / $item->lessons);

                    echo '<div class="lecture_charts charts"><div class="charts__chart chart--p100 chart--xs"><div class="charts__chart chart--ed chart--p'.$lecture_percentage.'"></div></div></div>'.PHP_EOL;
                    echo '<div class="title">'.htmlentities($item->title).'</div>'.PHP_EOL;
                    echo '<div class="difficulty">'.htmlentities($item->difficulty).'</div>'.PHP_EOL;
                    echo '</div>'.PHP_EOL;
                    echo '</div>'.PHP_EOL;

                    $pos++;
                }
            ?>
        </div>
        <div id="lecture_scroll_right">
            <img src="images/scroll_right.png">
        </div>
    </div>
    <div id="separator"></div>
    <div id="preview_container">
        <?php
            foreach ($lecture_list as $item) {
                $lecture_progress = 0;
                $lecture_percentage = 0;

                echo '<div id="'.$item->lectureid.'_preview" class="preview_block">'.PHP_EOL;
                echo '<div class="preview_image">'.PHP_EOL;
                echo '<div class="preview_thumbnail"><img src="'.$webinfo['lecture_thumbnail'].DIRECTORY_SEPARATOR.$item->lectureid.$fileinfo['thumbnail_ext'].'"></div>'.PHP_EOL;
                echo '<div class="charts_area">'.PHP_EOL;

                foreach ($progress_list as $progress) {
                    if ($progress->lectureid == $item->lectureid) {
                        $lecture_progress += (int)($progress->progress);
                    }
                }

                $lecture_percentage = round($lecture_progress / $item->lessons);

                echo '<div class="preview_charts charts"><div class="charts__chart chart--p100 chart--xs"><div class="charts__chart chart--ed chart--p'.$lecture_percentage.'"></div></div></div>'.PHP_EOL;
                echo '<div class="preview_progress">'.$lecture_percentage.'%</div>'.PHP_EOL;
                echo '</div>'.PHP_EOL;
                echo '</div>'.PHP_EOL;
                echo '<div class="preview_info">'.PHP_EOL;
                echo '<div class="preview_title">'.htmlentities($item->title).'</div>'.PHP_EOL;
                echo '<div class="preview_difficulty">'.htmlentities($item->difficulty).'</div>'.PHP_EOL;
                echo '<div class="preview_description">'.htmlentities($item->description).'<br><br><b>[Lecture Videos in Mirror Mode]</b></div>'.PHP_EOL;
                echo '<div class="buttons">'.PHP_EOL;
                echo '<div class="lessons_button" onclick="selectLesson(\''.$_SESSION['loggedin'].'\', \''.$item->lectureid.'\', \''.$_SESSION['active'].'\');">Select Lecture</div>'.PHP_EOL;
                echo '</div>'.PHP_EOL;
                echo '</div>'.PHP_EOL;
                echo '</div>'.PHP_EOL;
            }
        ?>
    </div>
    <div id="lesson_exitor" onclick="exitSelectLesson();"></div>
    <div id="lesson_container">
        <div id="lesson_list_close" onclick="exitSelectLesson();">&times;</div>
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
    <div id="screen_blind" onclick="closeNav();"></div>
    <div id="form_area">
        <form id="message_form" method="post" action="message.php">
        </form>
        <form id="lesson_form" method="post" action="practice.php">
        </form>
    </div>
    <script>
        $(document).ready(function() {
            $('.owl-carousel').owlCarousel({
                slideBy: 3,
                margin: 2,
                autoWidth: true,
                dots: false,
                startPosition: <?php echo $lastpos; ?>
            });

            $('#lecture_scroll_left').click(function() {
                $('.owl-carousel').trigger('prev.owl');
            });

            $('#lecture_scroll_right').click(function() {
                $('.owl-carousel').trigger('next.owl');
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

        var previous_lectureid = '';

        function preview(lectureid, position) {
            if (previous_lectureid != '') {
                document.getElementById(previous_lectureid).style.border = "2px rgba(0, 0, 0, 0) solid";
                document.getElementById(previous_lectureid + "_preview").style.display = "none";
            }

            previous_lectureid = lectureid;
            document.getElementById(lectureid).style.border = "2px solid #F4F4F4";
            document.getElementById(lectureid + "_preview").style.display = "block";

            setWebCookie("lastlectureid", lectureid, 1);
            setWebCookie("lastpos", position, 1);
        }

        function selectItem(id, active) {
            if (active) {
                document.getElementById("title_" + id).style.color = "#161616";
                document.getElementById("play_" + id).style.display = "block";
            } else {
                document.getElementById("title_" + id).style.color = "#F4F4F4";
                document.getElementById("play_" + id).style.display = "none";
            }
        }

        function selectLesson(user, lectureid, active) {
            $.ajax({
                type: 'post',
                url: 'lecture_lesson_proc.php',
                data: {
                    email: user,
                    lectureid: lectureid,
                    active: active
                },
                success: function(data) {
                    $("#lesson_list").html(data);
                    enterSelectLesson();
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

        function exitSelectLesson() {
            $("#lesson_exitor").css("display", "none");
            $("#lesson_container").animate({ right: '-50vw' }, "fast", "linear");
        }

        function openLesson(lectureid, vid, type) {
            var form = document.getElementById('lesson_form');
            var lectureIdInput = document.createElement('input');
            var vidInput = document.createElement('input');
            var typeInput = document.createElement("input");

            lectureIdInput.setAttribute('type', 'hidden');
            lectureIdInput.setAttribute('name', 'lectureid');
            lectureIdInput.setAttribute('value', lectureid);
            form.appendChild(lectureIdInput);

            vidInput.setAttribute('type', 'hidden');
            vidInput.setAttribute('name', 'vid');
            vidInput.setAttribute('value', vid);
            form.appendChild(vidInput);

            typeInput.setAttribute('type', 'hidden');
            typeInput.setAttribute('name', 'vidtype');
            typeInput.setAttribute('value', type);
            form.appendChild(typeInput);
            form.submit();
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
            location.href = "mypage.php?from=lecture";
        }

        <?php echo '$("#'.$lastlectureid.'").trigger("click");'.PHP_EOL; ?>
    </script>
</body>
</html>

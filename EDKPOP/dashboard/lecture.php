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

            switch ($_SESSION['db_level']) {
                case MANAGER_INSTRUCTOR:
                    header('Location: recording.php');
                    break;
                case MANAGER_ADMIN:
                case MANAGER_MODERATOR:
                default:
                    break;
            }
        } else {
            header('Location: index.php');
        }
    ?>
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="stylesheet" type="text/css" href="css/common.css">
    <link rel="stylesheet" type="text/css" href="css/sidebar.css">
    <link rel="stylesheet" type="text/css" href="css/lecture.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script type="text/javascript" src="js/common.js"></script>
    <script>
        var currentPage = <?php echo (strlen($_GET['currentPage']) ? $_GET['currentPage'] : '0'); ?>;
        var search = "<?php echo $_POST['search']; ?>";
    </script>
</head>
<body>
    <div id="header">
        <div id="logo" onclick="location.href='index.php';">
            <img src="images/logo.png">
        </div>
        <div id="username"><?php echo $_SESSION['db_loggedin']; ?></div>
    </div>
    <div id='container'>
        <div id="sidebar">
            <?php
                switch ($_SESSION['db_level']) {
                    case MANAGER_INSTRUCTOR:
                        echo '<div class="sidebar_item" onclick="location.href=\'recording.php\';">Recordings</div>'.PHP_EOL;
                        echo '<div class="sidebar_item" onclick="signOut();">Sign Out</div>'.PHP_EOL;
                        break;
                    case MANAGER_ADMIN:
                        echo '<div class="sidebar_item" onclick="location.href=\'home.php\';">Home</div>'.PHP_EOL;
                        echo '<div class="sidebar_item" onclick="location.href=\'site.php\';">Site Settings</div>'.PHP_EOL;
                        echo '<div class="sidebar_item" onclick="location.href=\'manager.php\';">Managers</div>'.PHP_EOL;
                        echo '<div class="sidebar_item" onclick="location.href=\'user.php\';">Users</div>'.PHP_EOL;
                        echo '<div class="sidebar_item selected" onclick="location.href=\'lecture.php\';">Lectures</div>'.PHP_EOL;
                        echo '<div class="sidebar_item" onclick="location.href=\'video.php\';">Videos</div>'.PHP_EOL;
                        echo '<div class="sidebar_item" onclick="location.href=\'recording.php\';">Recordings</div>'.PHP_EOL;
                        echo '<div class="sidebar_item" onclick="location.href=\'log.php\';">Logs</div>'.PHP_EOL;
                        echo '<div class="sidebar_item" onclick="location.href=\'notification.php\';">Notifications</div>'.PHP_EOL;
                        echo '<div class="sidebar_item" onclick="location.href=\'storage.php\';">Storage</div>'.PHP_EOL;
                        echo '<div class="sidebar_item" onclick="signOut();">Sign Out</div>'.PHP_EOL;
                        break;
                    case MANAGER_MODERATOR:
                    default:
                        echo '<div class="sidebar_item" onclick="location.href=\'home.php\';">Home</div>'.PHP_EOL;
                        echo '<div class="sidebar_item" onclick="location.href=\'user.php\';">Users</div>'.PHP_EOL;
                        echo '<div class="sidebar_item selected" onclick="location.href=\'lecture.php\';">Lectures</div>'.PHP_EOL;
                        echo '<div class="sidebar_item" onclick="location.href=\'video.php\';">Videos</div>'.PHP_EOL;
                        echo '<div class="sidebar_item" onclick="location.href=\'recording.php\';">Recordings</div>'.PHP_EOL;
                        echo '<div class="sidebar_item" onclick="location.href=\'log.php\';">Logs</div>'.PHP_EOL;
                        echo '<div class="sidebar_item" onclick="location.href=\'notification.php\';">Notifications</div>'.PHP_EOL;
                        echo '<div class="sidebar_item" onclick="location.href=\'storage.php\';">Storage</div>'.PHP_EOL;
                        echo '<div class="sidebar_item" onclick="signOut();">Sign Out</div>'.PHP_EOL;
                        break;
                }
            ?>
        </div>
        <div id="content">
            <div id="lecture_title">Lectures<?php
                if (strlen($_POST['search'])) {
                    $prefix = strpos($_POST['search'], $db_settings['condition_search_prefix']);

                    if (($prefix === FALSE) || ($prefix != 0)) {
                        echo ': \''.$_POST['search'].'\'';
                    } else {
                        echo ': SQL \''.substr($_POST['search'], 1).'\'';
                    }
                }
            ?></div>
            <div id="lecture_table"></div>
            <div id="lecture_actions">
                <div id="lecture_actions_title">Actions</div>
                <div class="lecture_action" onclick="refresh();">Refresh</div>
                <div class="lecture_action" onclick="add();">Add</div>
            </div>
        </div>
    </div>
    <div id="form_area">
        <form id="edit_form" method="post" action="lecture_edit.php">
        </form>
        <form id="message_form" method="post" action="message.php">
        </form>
    </div>
    <script>
        function refresh() {
            location.href = "lecture.php";
        }

        function add() {
            addLecture();
        }

        function edit(lectureid) {
            editLecture(lectureid);
        }

        function remove(lectureid) {
            var message = "Are you sure you want to remove this lecture?\n" + lectureid;
            var response = confirm(message);

            if (response == true) {
                checkVideos(lectureid);
            }
        }

        function goPages(pages) {
            currentPage += pages;

            loadLectureList(currentPage, search);
        }

        function parseCurrent(html) {
            var length = html.indexOf(" -->");
            currentPage = parseInt(html.substr(5, length - 5), 10);
        }

        function addLecture() {
            var form = document.getElementById('edit_form');
            var methodInput = document.createElement('input');
            var currentInput = document.createElement('input');

            methodInput.setAttribute('type', 'hidden');
            methodInput.setAttribute('name', 'method');
            methodInput.setAttribute('value', 'add');
            form.appendChild(methodInput);

            currentInput.setAttribute('type', 'hidden');
            currentInput.setAttribute('name', 'currentPage');
            currentInput.setAttribute('value', currentPage);
            form.appendChild(currentInput);
            form.submit();
        }

        function editLecture(lectureid) {
            var form = document.getElementById('edit_form');
            var lectureIdInput = document.createElement('input');
            var methodInput = document.createElement('input');
            var currentInput = document.createElement('input');

            lectureIdInput.setAttribute('type', 'hidden');
            lectureIdInput.setAttribute('name', 'lectureid');
            lectureIdInput.setAttribute('value', lectureid);
            form.appendChild(lectureIdInput);

            methodInput.setAttribute('type', 'hidden');
            methodInput.setAttribute('name', 'method');
            methodInput.setAttribute('value', 'edit');
            form.appendChild(methodInput);

            currentInput.setAttribute('type', 'hidden');
            currentInput.setAttribute('name', 'currentPage');
            currentInput.setAttribute('value', currentPage);
            form.appendChild(currentInput);
            form.submit();
        }

        function checkVideoMessage(lectureid) {
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
            textInput.setAttribute('value', 'One or more videos for ' + lectureid + ' exist.');
            form.appendChild(textInput);

            fromInput.setAttribute('type', 'hidden');
            fromInput.setAttribute('name', 'from');
            fromInput.setAttribute('value', "<?php echo basename($_SERVER['PHP_SELF'], '.php'); ?>");
            form.appendChild(fromInput);
            form.submit();
        }

        function checkVideos(lectureid) {
            $.ajax({
                type: 'post',
                url: 'lecture_video_proc.php',
                data: {
                    lectureid: lectureid
                },
                success: function(result) {
                    if (result == '0') {
                        removeLecture(lectureid);
                    } else {
                        checkVideoMessage(lectureid);
                    }
                },
                error: function(request, status, error) {
                    console.log("code: " + request.status + "\n" + "message: " + request.responseText + "\n" + "error: " + error);
                }
            });
        }

        function removeLecture(lectureid) {
            $.ajax({
                type: 'post',
                url: 'lecture_remove_proc.php',
                data: {
                    lectureid: lectureid
                },
                success: function() {
                    loadLectureList(currentPage, search);
                },
                error: function(request, status, error) {
                    console.log("code: " + request.status + "\n" + "message: " + request.responseText + "\n" + "error: " + error);
                }
            });
        }

        function loadLectureList(current, search) {
            $.ajax({
                type: 'post',
                url: 'lecture_list_proc.php',
                data: {
                    current: current,
                    search: search
                },
                success: function(data) {
                    $("#lecture_table").html(data);
                    parseCurrent(data);
                },
                error: function(request, status, error) {
                    console.log("code: " + request.status + "\n" + "message: " + request.responseText + "\n" + "error: " + error);
                }
            });
        }

        loadLectureList(currentPage, search);
    </script>
</body>
</html>

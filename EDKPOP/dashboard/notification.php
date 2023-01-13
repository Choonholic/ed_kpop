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
    <link rel="stylesheet" type="text/css" href="css/video.css">
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
                        echo '<div class="sidebar_item" onclick="location.href=\'lecture.php\';">Lectures</div>'.PHP_EOL;
                        echo '<div class="sidebar_item" onclick="location.href=\'video.php\';">Videos</div>'.PHP_EOL;
                        echo '<div class="sidebar_item" onclick="location.href=\'recording.php\';">Recordings</div>'.PHP_EOL;
                        echo '<div class="sidebar_item" onclick="location.href=\'log.php\';">Logs</div>'.PHP_EOL;
                        echo '<div class="sidebar_item selected" onclick="location.href=\'notification.php\';">Notifications</div>'.PHP_EOL;
                        echo '<div class="sidebar_item" onclick="location.href=\'storage.php\';">Storage</div>'.PHP_EOL;
                        echo '<div class="sidebar_item" onclick="signOut();">Sign Out</div>'.PHP_EOL;
                        break;
                    case MANAGER_MODERATOR:
                    default:
                        echo '<div class="sidebar_item" onclick="location.href=\'home.php\';">Home</div>'.PHP_EOL;
                        echo '<div class="sidebar_item" onclick="location.href=\'user.php\';">Users</div>'.PHP_EOL;
                        echo '<div class="sidebar_item" onclick="location.href=\'lecture.php\';">Lectures</div>'.PHP_EOL;
                        echo '<div class="sidebar_item" onclick="location.href=\'video.php\';">Videos</div>'.PHP_EOL;
                        echo '<div class="sidebar_item" onclick="location.href=\'recording.php\';">Recordings</div>'.PHP_EOL;
                        echo '<div class="sidebar_item" onclick="location.href=\'log.php\';">Logs</div>'.PHP_EOL;
                        echo '<div class="sidebar_item selected" onclick="location.href=\'notification.php\';">Notifications</div>'.PHP_EOL;
                        echo '<div class="sidebar_item" onclick="location.href=\'storage.php\';">Storage</div>'.PHP_EOL;
                        echo '<div class="sidebar_item" onclick="signOut();">Sign Out</div>'.PHP_EOL;
                        break;
                }
            ?>
        </div>
        <div id="content">
            <div id="video_title">Videos<?php
                if (strlen($_POST['search'])) {
                    $prefix = strpos($_POST['search'], $db_settings['condition_search_prefix']);

                    if (($prefix === FALSE) || ($prefix != 0)) {
                        echo ': \''.$_POST['search'].'\'';
                    } else {
                        echo ': SQL \''.substr($_POST['search'], 1).'\'';
                    }
                }
            ?></div>
            <div id="video_table"></div>
            <div id="video_actions">
                <div id="video_actions_title">Actions</div>
                <div class="video_action" onclick="refresh();">Refresh</div>
                <div class="video_action" onclick="add();">Add</div>
            </div>
        </div>
    </div>
    <div id="form_area">
        <form id="edit_form" method="post" action="video_edit.php">
        </form>
        <form id="play_form" method="post" action="video_play.php" target="_blank">
        </form>
        <form id="upload_form" method="post" action="video_upload.php">
        </form>
    </div>
    <script>
        function refresh() {
            location.href = "video.php";
        }

        function add() {
            addVideo();
        }

        function edit(lectureid, vid) {
            editVideo(lectureid, vid);
        }

        function play(lectureid, vid, type) {
            playVideo(lectureid, vid, type);
        }

        function upload(lectureid, vid) {
            uploadVideo(lectureid, vid);
        }

        function remove(lectureid, vid) {
            var message = "Are you sure you want to remove this video?\n" + vid;
            var response = confirm(message);

            if (response == true) {
                removeVideo(lectureid, vid);
            }
        }

        function goPages(pages) {
            currentPage += pages;

            loadVideoList(currentPage, search);
        }

        function parseCurrent(html) {
            var length = html.indexOf(" -->");
            currentPage = parseInt(html.substr(5, length - 5), 10);
        }

        function addVideo() {
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

        function editVideo(lectureid, vid) {
            var form = document.getElementById('edit_form');
            var lectureIdInput = document.createElement('input');
            var vidInput = document.createElement('input');
            var methodInput = document.createElement('input');
            var currentInput = document.createElement('input');

            lectureIdInput.setAttribute('type', 'hidden');
            lectureIdInput.setAttribute('name', 'lectureid');
            lectureIdInput.setAttribute('value', lectureid);
            form.appendChild(lectureIdInput);

            vidInput.setAttribute('type', 'hidden');
            vidInput.setAttribute('name', 'vid');
            vidInput.setAttribute('value', vid);
            form.appendChild(vidInput);

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

        function playVideo(lectureid, vid, type) {
            var form = document.getElementById('play_form');
            var lectureidInput = document.createElement('input');
            var vidInput = document.createElement("input");
            var typeInput = document.createElement("input");

            lectureidInput.setAttribute('type', 'hidden');
            lectureidInput.setAttribute('name', 'lectureid');
            lectureidInput.setAttribute('value', lectureid);
            form.appendChild(lectureidInput);

            vidInput.setAttribute('type', 'hidden');
            vidInput.setAttribute('name', 'vid');
            vidInput.setAttribute('value', vid);
            form.appendChild(vidInput);

            typeInput.setAttribute('type', 'hidden');
            typeInput.setAttribute('name', 'type');
            typeInput.setAttribute('value', type);
            form.appendChild(typeInput);
            form.submit();
        }

        function uploadVideo(lectureid, vid) {
            var form = document.getElementById('upload_form');
            var lectureIdInput = document.createElement('input');
            var vidInput = document.createElement('input');
            var currentInput = document.createElement('input');

            lectureIdInput.setAttribute('type', 'hidden');
            lectureIdInput.setAttribute('name', 'lectureid');
            lectureIdInput.setAttribute('value', lectureid);
            form.appendChild(lectureIdInput);

            vidInput.setAttribute('type', 'hidden');
            vidInput.setAttribute('name', 'vid');
            vidInput.setAttribute('value', vid);
            form.appendChild(vidInput);

            currentInput.setAttribute('type', 'hidden');
            currentInput.setAttribute('name', 'currentPage');
            currentInput.setAttribute('value', currentPage);
            form.appendChild(currentInput);
            form.submit();
        }

        function removeVideo(lectureid, vid) {
            $.ajax({
                type: 'post',
                url: 'video_remove_proc.php',
                data: {
                    lectureid: lectureid,
                    vid: vid
                },
                success: function() {
                    loadVideoList(currentPage, search);
                },
                error: function(request, status, error) {
                    console.log("code: " + request.status + "\n" + "message: " + request.responseText + "\n" + "error: " + error);
                }
            });
        }

        function loadVideoList(current, search) {
            $.ajax({
                type: 'post',
                url: 'video_list_proc.php',
                data: {
                    current: current,
            search: search
                },
                success: function(data) {
                    $("#video_table").html(data);
                    parseCurrent(data);
                },
                error: function(request, status, error) {
                    console.log("code: " + request.status + "\n" + "message: " + request.responseText + "\n" + "error: " + error);
                }
            });
        }

        loadVideoList(currentPage, search);
    </script>
</body>
</html>

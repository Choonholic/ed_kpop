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
    <link rel="stylesheet" type="text/css" href="css/recording.css">
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
                        echo '<div class="sidebar_item selected" onclick="location.href=\'recording.php\';">Recordings</div>'.PHP_EOL;
                        echo '<div class="sidebar_item" onclick="signOut();">Sign Out</div>'.PHP_EOL;
                        break;
                    case MANAGER_ADMIN:
                        echo '<div class="sidebar_item" onclick="location.href=\'home.php\';">Home</div>'.PHP_EOL;
                        echo '<div class="sidebar_item" onclick="location.href=\'site.php\';">Site Settings</div>'.PHP_EOL;
                        echo '<div class="sidebar_item" onclick="location.href=\'manager.php\';">Managers</div>'.PHP_EOL;
                        echo '<div class="sidebar_item" onclick="location.href=\'user.php\';">Users</div>'.PHP_EOL;
                        echo '<div class="sidebar_item" onclick="location.href=\'lecture.php\';">Lectures</div>'.PHP_EOL;
                        echo '<div class="sidebar_item" onclick="location.href=\'video.php\';">Videos</div>'.PHP_EOL;
                        echo '<div class="sidebar_item selected" onclick="location.href=\'recording.php\';">Recordings</div>'.PHP_EOL;
                        echo '<div class="sidebar_item" onclick="location.href=\'log.php\';">Logs</div>'.PHP_EOL;
                        echo '<div class="sidebar_item" onclick="location.href=\'notification.php\';">Notifications</div>'.PHP_EOL;
                        echo '<div class="sidebar_item" onclick="location.href=\'storage.php\';">Storage</div>'.PHP_EOL;
                        echo '<div class="sidebar_item" onclick="signOut();">Sign Out</div>'.PHP_EOL;
                        break;
                    case MANAGER_MODERATOR:
                    default:
                        echo '<div class="sidebar_item" onclick="location.href=\'home.php\';">Home</div>'.PHP_EOL;
                        echo '<div class="sidebar_item" onclick="location.href=\'user.php\';">Users</div>'.PHP_EOL;
                        echo '<div class="sidebar_item" onclick="location.href=\'lecture.php\';">Lectures</div>'.PHP_EOL;
                        echo '<div class="sidebar_item" onclick="location.href=\'video.php\';">Videos</div>'.PHP_EOL;
                        echo '<div class="sidebar_item selected" onclick="location.href=\'recording.php\';">Recordings</div>'.PHP_EOL;
                        echo '<div class="sidebar_item" onclick="location.href=\'log.php\';">Logs</div>'.PHP_EOL;
                        echo '<div class="sidebar_item" onclick="location.href=\'notification.php\';">Notifications</div>'.PHP_EOL;
                        echo '<div class="sidebar_item" onclick="location.href=\'storage.php\';">Storage</div>'.PHP_EOL;
                        echo '<div class="sidebar_item" onclick="signOut();">Sign Out</div>'.PHP_EOL;
                        break;
                }
            ?>
        </div>
        <div id="content">
            <div id="recording_title">Recordings<?php
                if (strlen($_POST['search'])) {
                    $prefix = strpos($_POST['search'], $db_settings['condition_search_prefix']);

                    if (($prefix === FALSE) || ($prefix != 0)) {
                        echo ': \''.$_POST['search'].'\'';
                    } else {
                        echo ': SQL \''.substr($_POST['search'], 1).'\'';
                    }
                }
            ?></div>
            <div id="recording_table"></div>
            <div id="recording_actions">
                <div id="recording_actions_title">Actions</div>
                <div class="recording_action" onclick="refresh();">Refresh</div>
                <div class="recording_action" onclick="cleanup();">Cleanup</div>
            </div>
        </div>
    </div>
    <div id="form_area">
        <form id="play_form" method="post" action="recording_play.php" target="_blank">
        </form>
        <form id="edit_form" method="post" action="recording_edit.php">
        </form>
    </div>
    <script>
        function refresh() {
            location.href = "recording.php";
        }

        function cleanup() {
            var days = <?php echo $db_settings['recording_retention_days']; ?>;
            var message = "Are you sure you want to cleanup recordings older than " + days + " days?";
            var response = confirm(message);

            if (response == true) {
                cleanupRecording(days);
            }
        }

        function play(uniquekey, mode, vid, begin, end) {
            playRecording(uniquekey, mode, vid, begin, end);
        }

        function download(uniquekey) {
            location.href = "recording_download_proc.php?file=" + encodeURIComponent(uniquekey);
        }

        function remove(uniquekey) {
            var message = "Are you sure you want to remove this recording?\n" + uniquekey;
            var response = confirm(message);

            if (response == true) {
                removeRecording(uniquekey);
            }
        }

        function goPages(pages) {
            currentPage += pages;

            loadRecordingList(currentPage, search);
        }

        function parseCurrent(html) {
            var length = html.indexOf(" -->");
            currentPage = parseInt(html.substr(5, length - 5), 10);
        }

        function cleanupRecording(days) {
            $.ajax({
                type: 'post',
                url: 'recording_cleanup_proc.php',
                data: {
                    days: days
                },
                success: function() {
                    loadRecordingList(currentPage, search);
                },
                error: function(request, status, error) {
                    console.log("code: " + request.status + "\n" + "message: " + request.responseText + "\n" + "error: " + error);
                }
            });
        }

        function playRecording(uniquekey, mode, vid, begin, end) {
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

        function removeRecording(uniquekey) {
            $.ajax({
                type: 'post',
                url: 'recording_remove_proc.php',
                data: {
                    uniquekey: uniquekey
                },
                success: function() {
                    loadRecordingList(currentPage, search);
                },
                error: function(request, status, error) {
                    console.log("code: " + request.status + "\n" + "message: " + request.responseText + "\n" + "error: " + error);
                }
            });
        }

        function loadRecordingList(current, search) {
            $.ajax({
                type: 'post',
                url: 'recording_list_proc.php',
                data: {
                    current: current,
                    search: search
                },
                success: function(data) {
                    $("#recording_table").html(data);
                    parseCurrent(data);
                },
                error: function(request, status, error) {
                    console.log("code: " + request.status + "\n" + "message: " + request.responseText + "\n" + "error: " + error);
                }
            });
        }

        loadRecordingList(currentPage, search);
    </script>
</body>
</html>

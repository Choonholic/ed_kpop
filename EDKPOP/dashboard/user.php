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
    <link rel="stylesheet" type="text/css" href="css/user.css">
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
                        echo '<div class="sidebar_item selected" onclick="location.href=\'user.php\';">Users</div>'.PHP_EOL;
                        echo '<div class="sidebar_item" onclick="location.href=\'lecture.php\';">Lectures</div>'.PHP_EOL;
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
                        echo '<div class="sidebar_item selected" onclick="location.href=\'user.php\';">Users</div>'.PHP_EOL;
                        echo '<div class="sidebar_item" onclick="location.href=\'lecture.php\';">Lectures</div>'.PHP_EOL;
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
            <div id="user_title">Users<?php
                if (strlen($_POST['search'])) {
                    $prefix = strpos($_POST['search'], $db_settings['condition_search_prefix']);

                    if (($prefix === FALSE) || ($prefix != 0)) {
                        echo ': \''.$_POST['search'].'\'';
                    } else {
                        echo ': SQL \''.substr($_POST['search'], 1).'\'';
                    }
                }
            ?></div>
            <div id="user_table"></div>
            <div id="user_actions">
                <div id="user_actions_title">Actions</div>
                <div class="user_action" onclick="refresh();">Refresh</div>
                <div class="user_action" onclick="statsExport();">Export Statistics</div>
                <div class="user_action" onclick="add(0);">Add Regular</div>
                <div class="user_action" onclick="add(1);">Add Trial</div>
            </div>
        </div>
    </div>
    <div id="process_box">
        <div id="process_message">
            <p id="process_message">Processing Data to Export... Please Wait...</p>
            <p id="process_description"><b>DO NOT</b> close this window or navigate to the other pages,<br>or the tasks will be cancelled unexpectedly.</p>
        </div>
    </div>
    <div id="form_area">
        <form id="stats_form" method="post" action="user_stats.php">
        </form>
    </div>
    <script>
        function refresh() {
            location.href = "user.php";
        }

        function statsExport() {
            document.getElementById("process_box").style.display = "block";

            $.ajax({
                type: 'post',
                url: 'user_export_proc.php',
                data: {
                    id: 'all',
                },
                success: function() {
                    document.getElementById("process_box").style.display = "none";
                    downloadStats();
                },
                error: function(request, status, error) {
                    document.getElementById("process_box").style.display = "none";
                    console.log("code: " + request.status + "\n" + "message: " + request.responseText + "\n" + "error: " + error);
                }
            });
        }

        function downloadStats() {
            location.href = "user_download_proc.php";
        }

        function add(type) {
            var pageUrl;

            switch (type) {
                case 1:
                    pageUrl = 'user_add_trial.php?currentPage=' + currentPage;
                    break;
                case 0:
                default:
                    pageUrl = 'user_add.php?currentPage=' + currentPage;
                    break;
            }

            location.href = pageUrl;
        }

        function stats(email) {
            var form = document.getElementById('stats_form');
            var emailInput = document.createElement('input');
            var currentInput = document.createElement('input');

            emailInput.setAttribute('type', 'hidden');
            emailInput.setAttribute('name', 'email');
            emailInput.setAttribute('value', email);
            form.appendChild(emailInput);

            currentInput.setAttribute('type', 'hidden');
            currentInput.setAttribute('name', 'currentPage');
            currentInput.setAttribute('value', currentPage);
            form.appendChild(currentInput);
            form.submit();
        }

        function change(email, status) {
            var action = (status == 2) ? "pause" : "activate";
            var message = "Are you sure you want to " + action + " this user?\n" + email;
            var response = confirm(message);

            if (response == true) {
                changeUser(email, status);
            }
        }

        function reset(email) {
            var message = "Are you sure you want to reset password of this user?\n" + email;
            var response = confirm(message);

            if (response == true) {
                resetPassword(email);
            }
        }

        function remove(email) {
            var message = "Are you sure you want to remove this user?\n" + email;
            var response = confirm(message);

            if (response == true) {
                removeUser(email);
            }
        }

        function goPages(pages) {
            currentPage += pages;

            loadUserList(currentPage, search);
        }

        function parseCurrent(html) {
            var length = html.indexOf(" -->");
            currentPage = parseInt(html.substr(5, length - 5), 10);
        }

        function changeUser(email, status) {
            $.ajax({
                type: 'post',
                url: 'user_change_proc.php',
                data: {
                    email: email,
                    status: status
                },
                success: function() {
                    loadUserList(currentPage, search);
                },
                error: function(request, status, error) {
                    console.log("code: " + request.status + "\n" + "message: " + request.responseText + "\n" + "error: " + error);
                }
            });
        }

        function resetPassword(email) {
            $.ajax({
                type: 'post',
                url: 'user_password_proc.php',
                data: {
                    email: email
                },
                success: function() {
                    loadUserList(currentPage, search);
                },
                error: function(request, status, error) {
                    console.log("code: " + request.status + "\n" + "message: " + request.responseText + "\n" + "error: " + error);
                }
            });
        }

        function removeUser(email) {
            $.ajax({
                type: 'post',
                url: 'user_remove_proc.php',
                data: {
                    email: email
                },
                success: function() {
                    loadUserList(currentPage, search);
                },
                error: function(request, status, error) {
                    console.log("code: " + request.status + "\n" + "message: " + request.responseText + "\n" + "error: " + error);
                }
            });
        }

        function loadUserList(current, search) {
            $.ajax({
                type: 'post',
                url: 'user_list_proc.php',
                data: {
                    current: current,
                    search: search
                },
                success: function(data) {
                    $("#user_table").html(data);
                    parseCurrent(data);
                },
                error: function(request, status, error) {
                    console.log("code: " + request.status + "\n" + "message: " + request.responseText + "\n" + "error: " + error);
                }
            });
        }

        loadUserList(currentPage, search);
    </script>
</body>
</html>

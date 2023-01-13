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

    if (isset($_SESSION['db_loggedin'])) {
        if ($_SESSION['db_active'] != USER_ACTIVE) {
            header('Location: activation.php');
        }

        switch ($_SESSION['db_level']) {
            case MANAGER_INSTRUCTOR:
                header('Location: recording.php');
                break;
            case MANAGER_ADMIN:
                break;
            case MANAGER_MODERATOR:
            default:
                header('Location: home.php');
                break;
        }
    } else {
        header('Location: index.php');
    }
?>
    <meta charset="UTF-8">
    <title>ED: Online Personal Studio</title>
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="stylesheet" type="text/css" href="css/common.css">
    <link rel="stylesheet" type="text/css" href="css/sidebar.css">
    <link rel="stylesheet" type="text/css" href="css/site.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script type="text/javascript" src="js/common.js"></script>
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
            echo '<div class="sidebar_item selected" onclick="location.href=\'site.php\';">Site Settings</div>'.PHP_EOL;
            echo '<div class="sidebar_item" onclick="location.href=\'manager.php\';">Managers</div>'.PHP_EOL;
            echo '<div class="sidebar_item" onclick="location.href=\'user.php\';">Users</div>'.PHP_EOL;
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
            echo '<div class="sidebar_item" onclick="location.href=\'user.php\';">Users</div>'.PHP_EOL;
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
            <div id="site_title">Site Settings</div>
            <div id="site_table"></div>
            <div id="site_actions">
                <div id="site_actions_title">Actions</div>
                <div class="site_action" onclick="refresh();">Refresh</div>
                <div class="site_action" onclick="openGraphMetaTags();">Open Graph</div>
                <div class="site_action" onclick="headTag();">&lt;head&gt; Tag</div>
            </div>
        </div>
    </div>
    <script>
        function refresh() {
            location.href = "site.php";
        }

        function openGraphMetaTags() {
            location.href = 'site_og.php';
        }

        function headTag() {
            location.href = 'site_head.php';
        }

        function loadSiteSettings() {
            $.ajax({
                type: 'post',
                url: 'site_load_proc.php',
                success: function(data) {
                    $("#site_table").html(data);
                },
                error: function(request, status, error) {
                    console.log("code: " + request.status + "\n" + "message: " + request.responseText + "\n" + "error: " + error);
                }
            });
        }

        loadSiteSettings();
    </script>
</body>
</html>

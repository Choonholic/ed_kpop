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
    <link rel="stylesheet" type="text/css" href="css/storage.css">
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
                        echo '<div class="sidebar_item" onclick="location.href=\'notification.php\';">Notifications</div>'.PHP_EOL;
                        echo '<div class="sidebar_item selected" onclick="location.href=\'storage.php\';">Storage</div>'.PHP_EOL;
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
                        echo '<div class="sidebar_item selected" onclick="location.href=\'storage.php\';">Storage</div>'.PHP_EOL;
                        echo '<div class="sidebar_item" onclick="signOut();">Sign Out</div>'.PHP_EOL;
                        break;
                }
            ?>
        </div>
        <div id="content">
            <div id="storage_title">Storage<?php
                if (strlen($_POST['search'])) {
                    $prefix = strpos($_POST['search'], $db_settings['condition_search_prefix']);

                    if (($prefix === FALSE) || ($prefix != 0)) {
                        echo ': \''.$_POST['search'].'\'';
                    } else {
                        echo ': SQL \''.substr($_POST['search'], 1).'\'';
                    }
                }
            ?></div>
            <div id="storage_table"></div>
            <div id="storage_actions">
                <div id="storage_actions_title">Actions</div>
                <div class="storage_action" onclick="refresh();">Refresh</div>
                <div class="storage_action" onclick="upload();">Upload</div>
                <div class="storage_action" onclick="cleanup();">Cleanup</div>
            </div>
        </div>
    </div>
    <div id="form_area">
        <form id="copy_form">
        </form>
    </div>
    <script>
        function refresh() {
            location.href = "storage.php";
        }

        function upload() {
            var pageUrl = 'storage_upload.php?currentPage=' + currentPage;

            location.href = pageUrl;
        }

        function cleanup() {
            var message = "Are you sure you want to cleanup storage?";
            var response = confirm(message);

            if (response == true) {
                cleanupStorage();
            }
        }

        function copy(filename) {
            var url = '<?php echo 'https://'.$_SERVER["HTTP_HOST"].$webinfo['storage'].DIRECTORY_SEPARATOR; ?>' + filename;
            var form = document.getElementById('copy_form');
            var urlInput = document.createElement('input');

            urlInput.setAttribute('type', 'text');
            urlInput.setAttribute('name', 'url');
            urlInput.setAttribute('value', url);
            form.appendChild(urlInput);

            urlInput.select();
            urlInput.setSelectionRange(0, 512);
            document.execCommand("copy");
            form.removeChild(urlInput);

            alert('The following URL has been copied to Clipboard:\n' + url);
        }

        function remove(filename) {
            var message = "Are you sure you want to remove this file?\n" + filename;
            var response = confirm(message);

            if (response == true) {
                removeStorage(filename);
            }
        }

        function goPages(pages) {
            currentPage += pages;

            loadStorageList(currentPage, search);
        }

        function parseCurrent(html) {
            var length = html.indexOf(" -->");
            currentPage = parseInt(html.substr(5, length - 5), 10);
        }

        function removeStorage(filename) {
            $.ajax({
                type: 'post',
                url: 'storage_remove_proc.php',
                data: {
                    filename: filename
                },
                success: function() {
                    loadStorageList(currentPage, search);
                },
                error: function(request, status, error) {
                    console.log("code: " + request.status + "\n" + "message: " + request.responseText + "\n" + "error: " + error);
                }
            });
        }

        function loadStorageList(current, search) {
            $.ajax({
                type: 'post',
                url: 'storage_list_proc.php',
                data: {
                    current: current,
                    search: search
                },
                success: function(data) {
                    $("#storage_table").html(data);
                    parseCurrent(data);
                },
                error: function(request, status, error) {
                    console.log("code: " + request.status + "\n" + "message: " + request.responseText + "\n" + "error: " + error);
                }
            });
        }

        loadStorageList(currentPage, search);
    </script>
</body>
</html>

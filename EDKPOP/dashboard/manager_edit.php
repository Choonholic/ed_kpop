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
                    break;
                case MANAGER_MODERATOR:
                    header('Location: home.php');
                    break;
            }
        } else {
            header('Location: index.php');
        }

        if ($_POST['method'] != 'add') {
            $mysqli = mysqli_connect($db['host'], $db['user'], $db['pass'], $db['name']);
            $mysqli->query("SET NAMES utf8");
            $sql = 'SELECT * FROM managers WHERE email="'.$_POST['email'].'"';
            $res = $mysqli->query($sql);
            $row = $res->fetch_array(MYSQLI_ASSOC);
            mysqli_close($mysqli);
        }
    ?>
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="stylesheet" type="text/css" href="css/common.css">
    <link rel="stylesheet" type="text/css" href="css/sidebar.css">
    <link rel="stylesheet" type="text/css" href="css/manager_edit.css">
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
                        echo '<div class="sidebar_item" onclick="location.href=\'site.php\';">Site Settings</div>'.PHP_EOL;
                        echo '<div class="sidebar_item selected" onclick="location.href=\'manager.php\';">Managers</div>'.PHP_EOL;
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
            <div id="manager_title">Managers</div>
            <div id="manager_edit">
                <?php
                    if ($_POST['method'] != 'add') {
                        echo '<div id="manager_edit_title">Edit Manager: '.$_POST['email'].'</div>'.PHP_EOL;
                    } else {
                        echo '<div id="manager_edit_title">Add Manager</div>'.PHP_EOL;
                    }
                ?>
                <div id="manager_edit_box">
                    <form id="edit_form" name="edit_form" action="manager_save_proc.php" method="post">
                        <div>
                            <table id="manager_edit_table">
                                <tbody>
                                    <?php
                                        if ($_POST['method'] == 'add') {
                                            echo '<tr>'.PHP_EOL;
                                            echo '<td class="table_title">Email</td>'.PHP_EOL;
                                            echo '<td class="table_field"><input required type="text" name="email" id="email" length="50" maxlength="50" value=""></td>'.PHP_EOL;
                                            echo '</tr>'.PHP_EOL;
                                        }
                                    ?>
                                    <tr>
                                        <td class="table_title">Level</td>
                                        <td class="table_field"><input required type="number" name="level" id="level" min="0" max="99" value="<?php if ($_POST['method'] != 'add') { echo $row['level']; } ?>"></td>
                                    </tr>
                                </tbody>
                            </table>
                            <div>
                                <input type="hidden" name="method" value="<?php echo $_POST['method']; ?>">
                                <input type="hidden" name="oldemail" value="<?php echo $_POST['email']; ?>">
                                <input type="hidden" name="currentPage" value="<?php echo $_REQUEST['currentPage']; ?>">
                                <input id="save_btn" type="submit" value="Save">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php
        if ($_POST['method'] == 'add') {
            echo '<script>'.PHP_EOL;
            echo 'document.getElementById("manager_edit").style.height = "calc(calc(100vh - 8vw) * 0.27)";'.PHP_EOL;
            echo '</script>'.PHP_EOL;
        }
    ?>
</body>
</html>

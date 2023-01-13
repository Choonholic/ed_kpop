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

        if ($_POST['method'] != 'add') {
            $mysqli = mysqli_connect($db['host'], $db['user'], $db['pass'], $db['name']);
            $mysqli->query("SET NAMES utf8");
            $sql = 'SELECT * FROM lectures WHERE lectureid="'.$_POST['lectureid'].'"';
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
    <link rel="stylesheet" type="text/css" href="css/upload.css">
    <link rel="stylesheet" type="text/css" href="css/lecture_edit.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script type="text/javascript" src="js/jquery.form.min.js"></script>
    <script type="text/javascript" src="js/upload.js"></script>
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
            <div id="lecture_title">Lectures</div>
            <div id="lecture_edit">
                <?php
                    if ($_POST['method'] != 'add') {
                        echo '<div id="lecture_edit_title">Edit Lecture: '.$_POST['lectureid'].'</div>'.PHP_EOL;
                    } else {
                        echo '<div id="lecture_edit_title">Add Lecture</div>'.PHP_EOL;
                    }
                ?>
                <div id="lecture_edit_box">
                    <form id="edit_form" name="edit_form" action="lecture_save_proc.php" method="post">
                        <div>
                            <table id="lecture_edit_table">
                                <tbody>
                                    <tr>
                                        <td class="table_title">Lecture ID</td>
                                        <td class="table_field"><input required type="text" name="lectureid" id="lectureid" length="50" maxlength="20" value="<?php if ($_POST['method'] != 'add') { echo $row['lectureid']; } ?>"></td>
                                    </tr>
                                    <tr>
                                        <td class="table_title">Title</td>
                                        <td class="table_field"><input required type="text" name="title" id="title" length="50" maxlength="50" value="<?php if ($_POST['method'] != 'add') { echo $row['title']; } ?>"></td>
                                    </tr>
                                    <tr>
                                        <td class="table_title">Difficulty</td>
                                        <td class="table_field"><input required type="text" name="difficulty" id="difficulty" length="50" maxlength="20" value="<?php if ($_POST['method'] != 'add') { echo $row['difficulty']; } ?>"></td>
                                    </tr>
                                    <tr>
                                        <td class="table_title">Description</td>
                                        <td class="table_field"><textarea required name="description" id="description" row="5" col="100"><?php if ($_POST['method'] != 'add') { echo $row['description']; } ?></textarea></td>
                                    </tr>
                                    <tr>
                                        <td class="table_title">Recordable</td>
                                        <td class="table_field"><input required type="number" name="recordable" id="recordable" min="0" max="99" value="<?php if ($_POST['method'] != 'add') { echo $row['recordable']; } ?>"></td>
                                    </tr>
                                    <tr>
                                        <td class="table_title">Trial</td>
                                        <td class="table_field"><input required type="number" name="trial" id="trial" min="0" max="99" value="<?php if ($_POST['method'] != 'add') { echo $row['trial']; } ?>"></td>
                                    </tr>
                                    <tr>
                                        <td class="table_title">Server ID</td>
                                        <td class="table_field"><input required type="text" name="server" id="server" length="30" maxlength="20" value="<?php if ($_POST['method'] != 'add') { echo $row['server']; } ?>"></td>
                                    </tr>
                                    <tr>
                                        <td class="table_title">Status</td>
                                        <td class="table_field"><input required type="number" name="status" id="status" min="0" max="99" value="<?php if ($_POST['method'] != 'add') { echo $row['status']; } ?>"></td>
                                    </tr>
                                </tbody>
                            </table>
                            <div>
                                <input type="hidden" name="method" value="<?php echo $_POST['method']; ?>">
                                <input type="hidden" name="oldlectureid" value="<?php if ($_POST['method'] != 'add') { echo $_POST['lectureid']; } ?>">
                                <input type="hidden" name="currentPage" value="<?php echo $_REQUEST['currentPage']; ?>">
                                <input id="save_btn" type="submit" value="Save">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div id="upload_thumbnail">
                <div id="upload_thumbnail_title">Upload Thumbnail: <?php if ($_POST['method'] != 'add') { echo $_POST['lectureid']; } ?></div>
                <div id="upload_thumbnail_box">
                    <form id="upload_form" name="upload_form" action="lecture_thumbnail_proc.php" method="post" enctype="multipart/form-data">
                        <div id="upload_title">Select Thumbnail File to Upload:</div>
                        <div id="upload_box">
                            <input type="hidden" name="lectureid" value="<?php if ($_POST['method'] != 'add') { echo $_POST['lectureid']; } ?>">
                            <input type="file" name="fileToUpload" id="fileToUpload" class="inputfile">
                            <label for="fileToUpload"><span></span><strong>Choose a File&hellip;</strong></label>
                        </div>
                        <div class='progress_box' id="progress_div">
                            <div class='progress_bar' id='progress_bar'></div>
                            <div class='percent_text' id='percent_text'>0%</div>
                        </div>
                        <div>
                            <input id="upload_btn" name="upload_submit" type="submit" value="Upload" onclick="upload('lecture.php?currentPage=<?php echo $_REQUEST['currentPage']; ?>');">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        <?php
            if ($_POST['method'] != 'add') {
                echo 'document.getElementById("upload_thumbnail").style.display = "block";'.PHP_EOL;
            }
        ?>
    </script>
</body>
</html>

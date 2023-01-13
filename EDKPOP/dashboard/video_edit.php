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

        if ($_POST['method'] != 'add') {
            $mysqli = mysqli_connect($db['host'], $db['user'], $db['pass'], $db['name']);
            $mysqli->query("SET NAMES utf8");
            $sql = 'SELECT * FROM videos WHERE vid="'.$_POST['vid'].'"';
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
    <link rel="stylesheet" type="text/css" href="css/video_edit.css">
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
                        echo '<div class="sidebar_item" onclick="location.href=\'lecture.php\';">Lectures</div>'.PHP_EOL;
                        echo '<div class="sidebar_item selected" onclick="location.href=\'video.php\';">Videos</div>'.PHP_EOL;
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
                        echo '<div class="sidebar_item selected" onclick="location.href=\'video.php\';">Videos</div>'.PHP_EOL;
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
            <div id="video_title">Videos</div>
            <div id="video_edit">
                <?php
                    if ($_POST['method'] != 'add') {
                        echo '<div id="video_edit_title">Edit Video: '.$_POST['vid'].'</div>'.PHP_EOL;
                    } else {
                        echo '<div id="video_edit_title">Add Video</div>'.PHP_EOL;
                    }
                ?>
                <div id="video_edit_box">
                    <form id="edit_form" name="edit_form" action="video_save_proc.php" method="post">
                        <div>
                            <table id="video_edit_table">
                                <tbody>
                                    <?php
                                        if ($_POST['method'] == 'add') {
                                            echo '<tr>'.PHP_EOL;
                                            echo '<td class="table_title">Video ID</td>'.PHP_EOL;
                                            echo '<td class="table_field"><input required type="text" name="vid" id="vid" length="50" maxlength="20" value=""></td>'.PHP_EOL;
                                            echo '</tr>'.PHP_EOL;
                                        }
                                    ?>
                                    <tr>
                                        <td class="table_title">Lecture ID</td>
                                        <td class="table_field"><input required type="text" name="lectureid" id="lectureid" length="50" maxlength="20" value="<?php if ($_POST['method'] != 'add') { echo $row['lectureid']; } ?>"></td>
                                    </tr>
                                    <tr>
                                        <td class="table_title">Category</td>
                                        <td class="table_field"><textarea required name="category" id="category" row="5" col="100"><?php if ($_POST['method'] != 'add') { echo $row['category']; } ?></textarea></td>
                                    </tr>
                                    <tr>
                                        <td class="table_title">Lesson</td>
                                        <td class="table_field"><textarea required name="lesson" id="lesson" row="5" col="100"><?php if ($_POST['method'] != 'add') { echo $row['lesson']; } ?></textarea></td>
                                    </tr>
                                    <tr>
                                        <td class="table_title">Type</td>
                                        <td class="table_field"><input required type="number" name="type" id="type" min="0" max="99" value="<?php if ($_POST['method'] != 'add') { echo $row['type']; } ?>"></td>
                                    </tr>
                                    <tr>
                                        <td class="table_title">Recordable</td>
                                        <td class="table_field"><input required type="number" name="recordable" id="recordable" min="0" max="99" value="<?php if ($_POST['method'] != 'add') { echo $row['recordable']; } ?>"></td>
                                    </tr>
                                    <tr>
                                        <td class="table_title">Trial</td>
                                        <td class="table_field"><input required type="number" name="trial" id="trial" min="0" max="99" value="<?php if ($_POST['method'] != 'add') { echo $row['trial']; } ?>"></td>
                                    </tr>
                                </tbody>
                            </table>
                            <div>
                                <input type="hidden" name="method" value="<?php echo $_POST['method']; ?>">
                                <input type="hidden" name="oldlectureid" value="<?php if ($_POST['method'] != 'add') { echo $_POST['lectureid']; } ?>">
                                <?php
                                    if ($_POST['method'] != 'add') {
                                        echo '<input type="hidden" name="vid" value="'.$_POST['vid'].'">'.PHP_EOL;
                                    }
                                ?>
                                <input type="hidden" name="oldvid" value="<?php if ($_POST['method'] != 'add') { echo $_POST['vid']; } ?>">
                                <input type="hidden" name="currentPage" value="<?php echo $_REQUEST['currentPage']; ?>">
                                <input id="save_btn" type="submit" value="Save">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div id="upload_thumbnail">
                <div id="upload_thumbnail_title">Upload Thumbnail: <?php if ($_POST['method'] != 'add') { echo $_POST['vid']; } ?></div>
                <div id="upload_thumbnail_box">
                    <form id="upload_form" name="upload_form" action="video_thumbnail_proc.php" method="post" enctype="multipart/form-data">
                        <div id="upload_thumbnail_message">Select Thumbnail File to Upload:</div>
                        <div id="upload_thumbnail_inner_box">
                            <input type="hidden" name="lectureid" value="<?php if ($_POST['method'] != 'add') { echo $_POST['lectureid']; } ?>">
                            <input type="hidden" name="vid" value="<?php if ($_POST['method'] != 'add') { echo $_POST['vid']; } ?>">
                            <input type="file" name="fileToUpload" id="fileToUpload" class="inputfile">
                            <label for="fileToUpload"><span></span><strong>Choose a File&hellip;</strong></label>
                        </div>
                        <div class='progress_box' id="progress_div">
                            <div class='progress_bar' id='progress_bar'></div>
                            <div class='percent_text' id='percent_text'>0%</div>
                        </div>
                        <div>
                            <input id="upload_thumbnail_btn" name="upload_thumbnail_submit" type="submit" value="Upload" onclick="upload('video.php?currentPage=<?php echo $_REQUEST['currentPage']; ?>');">
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

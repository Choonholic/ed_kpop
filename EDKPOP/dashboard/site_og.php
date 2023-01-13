<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ED: Online Personal Studio</title>
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

        $og_settings = loadSettings($fileinfo['settings'].DIRECTORY_SEPARATOR.$fileinfo['open_graph'].$fileinfo['settings_ext']);
        $og_property = array("og:title", "og:url", "og:image", "og:type", "og:description", "og:locale");
        $og_inputId = array("og_title", "og_url", "og_image", "og_type", "og_description", "og_locale");
        $count = count($og_property);

        if (count($og_settings) != $count) {
            unset($og_settings);

            $og_settings[] = "ED Training Studio";
            $og_settings[] = "https://edkpop.co/";
            $og_settings[] = "";
            $og_settings[] = "Website";
            $og_settings[] = "Experience The Real KPOP Idol Curriculum​ Anywhere, at Anytime";
            $og_settings[] = "en-US";
        }
    ?>
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="stylesheet" type="text/css" href="css/common.css">
    <link rel="stylesheet" type="text/css" href="css/sidebar.css">
    <link rel="stylesheet" type="text/css" href="css/site_og.css">
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
            <div id="site_edit">
            <div id="site_edit_title">Open Graph Meta Tags</div>
            <div id="site_edit_box">
                <form id="edit_form" name="edit_form" action="site_og_proc.php" method="post">
                    <div>
                        <table id="site_edit_table">
                            <tbody>
                                <?php
                                    for ($i = 0; $i < $count; $i++) {
                                        echo '<tr>'.PHP_EOL;
                                        echo '<td class="table_title">'.$og_property[$i].'</td>'.PHP_EOL;
                                        echo '<td class="table_field"><input type="text" name="'.$og_inputId[$i].'" id="'.$og_inputId[$i].'" length="50" value="'.$og_settings[$i].'"></td>'.PHP_EOL;
                                        echo '</tr>'.PHP_EOL;
                                    }
                                ?>
                            </tbody>
                        </table>
                        <div>
                            <input id="save_btn" type="submit" value="Save">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

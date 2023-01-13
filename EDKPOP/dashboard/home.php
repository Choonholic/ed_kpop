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
        include_once 'file_funcs.php';

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

        $mysqli = mysqli_connect($db['host'], $db['user'], $db['pass'], $db['name']);
        $mysqli->query("SET NAMES utf8");
        $sql = 'SELECT email FROM users WHERE active="'.USER_ACTIVE.'" AND email NOT LIKE "%'.$db_settings['stakeholder_domain'].'"';
        $res = $mysqli->query($sql);
        $active = mysqli_num_rows($res);
        $sql = 'SELECT email FROM users WHERE active="'.USER_INACTIVE.'" AND email NOT LIKE "%'.$db_settings['stakeholder_domain'].'"';
        $res = $mysqli->query($sql);
        $inactive = mysqli_num_rows($res);
        $sql = 'SELECT email FROM users WHERE active="'.USER_PAUSED.'" AND email NOT LIKE "%'.$db_settings['stakeholder_domain'].'"';
        $res = $mysqli->query($sql);
        $paused = mysqli_num_rows($res);
        $sql = 'SELECT email FROM users WHERE email LIKE "%'.$db_settings['stakeholder_domain'].'"';
        $res = $mysqli->query($sql);
        $stakeholders = mysqli_num_rows($res);

        $date_str = array();
        $userR_cnt = array();
        $userT_cnt = array();

        for ($i = 0; $i < 21; $i++) {
            $date_str[$i] = date("Y-m-d", strtotime("-".(20 - $i)." Days"));
            $sql = 'SELECT uniquekey FROM logs WHERE type="LOGIN" AND email NOT LIKE "%'.$db_settings['stakeholder_domain'].'" AND date LIKE "'.$date_str[$i].'%"';
            $res = $mysqli->query($sql);
            $userR_cnt[$i] = mysqli_num_rows($res);
            $sql = 'SELECT uniquekey FROM logs WHERE type="TRIAL_LOGIN" AND email NOT LIKE "%'.$db_settings['stakeholder_domain'].'" AND date LIKE "'.$date_str[$i].'%"';
            $res = $mysqli->query($sql);
            $userT_cnt[$i] = mysqli_num_rows($res);
        }

        $lecture_list = array();
        $sql = 'SELECT * FROM lectures ORDER BY lectureid';
        $res = $mysqli->query($sql);

        while ($row = $res->fetch_array(MYSQLI_ASSOC)) {
            $lecture_item = new stdClass();
            $lecture_item->lectureid = $row["lectureid"];
            $lecture_item->title = $row["title"];
            $lecture_item->difficulty = $row["difficulty"];
            $lecture_item->description = $row["description"];
            $lecture_item->lessons = $row["lessons"];
            $lecture_item->server = $row["server"];
            $lecture_item->status = $row["status"];
            $lecture_list[] = $lecture_item;
            unset($lecture_item);
        }

        $progress_list = array();
        $sql = 'SELECT * FROM progress';
        $res = $mysqli->query($sql);

        while ($row = $res->fetch_array(MYSQLI_ASSOC)) {
            $progress_item = new stdClass();
            $progress_item->lectureid = $row["lectureid"];
            $progress_item->vid = $row["vid"];
            $progress_item->current = $row["current"];
            $progress_item->duration = $row["duration"];
            $progress_list[] = $progress_item;
            unset($progress_item);
        };

        mysqli_close($mysqli);
    ?>
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="stylesheet" type="text/css" href="css/common.css">
    <link rel="stylesheet" type="text/css" href="css/sidebar.css">
    <link rel="stylesheet" type="text/css" href="css/home.css">
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript" src="js/common.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawUsersChart);
        google.charts.setOnLoadCallback(drawLoginChart);
        google.charts.setOnLoadCallback(drawLectureChart);
        google.charts.setOnLoadCallback(drawDiskChart);

        function drawUsersChart() {
            var usersData = new google.visualization.DataTable();

            usersData.addColumn('string', 'Types');
            usersData.addColumn('number', 'Counts');
            usersData.addRows([
                ['Active', <?php echo $active; ?>],
                ['Inactive', <?php echo $inactive; ?>],
                ['Paused', <?php echo $paused; ?>],
                ['Stakeholders', <?php echo $stakeholders; ?>]
            ]);

            var usersOptions = {
                title: 'Total Users (including Stakeholders)',
                titleTextStyle: {
                    color: '#161616',
                    fontName: 'Noto Sans KR'
                },
                colors: ['Coral', 'Royalblue', 'Peachpuff', 'Mediumseagreen', 'Chocolate', 'Darkred', 'Navy', 'Sandybrown'],
                legend: {
                    textStyle: {
                        color: '#161616',
                        fontName: 'Noto Sans KR'
                    }
                },
                pieHole: 0.4
            };

            var usersChart = new google.visualization.PieChart(document.getElementById('users_chart_div'));

            usersChart.draw(usersData, usersOptions);
        }

        function drawLoginChart() {
            var loginData = new google.visualization.DataTable();

            loginData.addColumn('string', 'Date');
            loginData.addColumn('number', 'Login: Regular');
            loginData.addColumn('number', 'Login: Trial');
            loginData.addRows([
                <?php
                    for ($i = 0; $i < 21; $i++) {
                        echo '["'.substr($date_str[$i], 5).'", '.$userR_cnt[$i].', '.$userT_cnt[$i].'],';
                    }
                ?>
            ]);

            var loginOptions = {
                title: 'Daily Users\' Login Statistics',
                titleTextStyle: {
                    color: '#161616',
                    fontName: 'Noto Sans KR'
                },
                colors: ['Mediumseagreen', 'Sandybrown'],
                hAxis: {
                  title: 'Date'
                },
                vAxis: {
                   title: 'Users'
                },
                legend: {
                    position: 'bottom',
                    textStyle: {
                        color: '#161616',
                        fontName: 'Noto Sans KR'
                    }
                },
                isStacked: true
            };

            var loginChart = new google.visualization.ColumnChart(document.getElementById('login_chart_div'));

            loginChart.draw(loginData, loginOptions);
        }

        function drawLectureChart() {
            var lectureData = new google.visualization.DataTable();

            lectureData.addColumn('string', 'Lecture');
            lectureData.addColumn('number', 'Played Time (Hours)');
            lectureData.addRows([
                <?php
                    foreach ($lecture_list as $item) {
                        $lecture_played = 0;

                        foreach ($progress_list as $progress) {
                            if ($progress->lectureid == $item->lectureid) {
                                $lecture_played += (int)($progress->current);
                            }
                        }

                        echo '["['.$item->lectureid.'] '.$item->title.'", '.($lecture_played / 3600).'],';
                    }
                ?>
            ]);

            var lectureOptions = {
                title: 'Total Played Time by Lectures',
                titleTextStyle: {
                    color: '#161616',
                    fontName: 'Noto Sans KR'
                },
                colors: ['Slateblue'],
                vAxis: {
                    title: 'Played Time (Hours)',
                    textStyle: {
                        color: '#161616',
                        fontName: 'Noto Sans KR'
                    }
                },
                hAxis: {
                    title: 'Lectures',
                    textStyle: {
                        color: '#161616',
                        fontName: 'Noto Sans KR',
                        fontSize: (window.innerWidth * 0.001)
                    }
                },
                legend: {
                    position: 'bottom',
                    textStyle: {
                        color: '#161616',
                        fontName: 'Noto Sans KR'
                    }
                }
            };

            var lectureChart = new google.visualization.ColumnChart(document.getElementById('lecture_chart_div'));

            lectureChart.draw(lectureData, lectureOptions);
        }

        function drawDiskChart() {
            var diskData = new google.visualization.DataTable();

            <?php
                $studio_space = getDirectorySize($fileinfo['studio_root']);
                $video_space = getDirectorySize($fileinfo['video']);
                $audio_space = getDirectorySize($fileinfo['audio']);
                $thumbnail_space = getDirectorySize($fileinfo['thumbnail']);
                $recording_space = getDirectorySize($fileinfo['recordings']);
                $free_space = disk_free_space($fileinfo['studio_root']);
                $code_space = $studio_space - ($video_space + $audio_space + $thumbnail_space + $recording_space);
            ?>

            diskData.addColumn('string', 'Factors');
            diskData.addColumn('number', 'Sizes');
            diskData.addRows([
                ['Videos', <?php echo $video_space; ?>],
                ['Audios', <?php echo $audio_space; ?>],
                ['Codes', <?php echo $code_space; ?>],
                ['Thumbnails', <?php echo $thumbnail_space; ?>],
                ['User Recordings', <?php echo $recording_space; ?>],
                ['Free', <?php echo $free_space; ?>]
            ]);

            var diskOptions = {
                title: 'Disk Status',
                titleTextStyle: {
                    color: '#161616',
                    fontName: 'Noto Sans KR'
                },
                colors: ['Coral', 'Royalblue', 'Peachpuff', 'Sandybrown', 'Mediumseagreen', 'Violet', 'Chocolate', 'Navy'],
                legend: {
                    textStyle: {
                        color: '#161616',
                        fontName: 'Noto Sans KR'
                    }
                },
                pieHole: 0.4
            };

            var diskChart = new google.visualization.PieChart(document.getElementById('disk_chart_div'));

            diskChart.draw(diskData, diskOptions);
        }
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
                        echo '<div class="sidebar_item selected" onclick="location.href=\'home.php\';">Home</div>'.PHP_EOL;
                        echo '<div class="sidebar_item" onclick="location.href=\'site.php\';">Site Settings</div>'.PHP_EOL;
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
                        echo '<div class="sidebar_item selected" onclick="location.href=\'home.php\';">Home</div>'.PHP_EOL;
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
            <div id="users_chart_div"></div>
            <div id="login_chart_div"></div>
            <div id="lecture_chart_div"></div>
            <div id="disk_chart_div"></div>
        </div>
    </div>
</body>
</html>

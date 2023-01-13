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

        $mysqli = mysqli_connect($db['host'], $db['user'], $db['pass'], $db['name']);
        $mysqli->query("SET NAMES utf8");
        $sql = 'SELECT * FROM totalplayed WHERE email="'.$_POST['email'].'"';
        $res = $mysqli->query($sql);
        $total = 0;

        while ($row = $res->fetch_array(MYSQLI_ASSOC)) {
            $total += $row['total'];
        }

        $total_value = $total;
        $total_days = floor($total_value / 86400);
        $total_value -= ($total_days * 86400);
        $total_hours = floor($total_value / 3600);
        $total_value -= ($total_hours * 3600);
        $total_minutes = floor($total_value / 60);
        $total_seconds = $total_value - ($total_minutes * 60);

        $date_str = array();
        $date_total = array();

        for ($i = 0; $i < 12; $i++) {
            $date_str[$i] = date("Y-m", strtotime("-".(11 - $i)." Months"));
            $date_total[$i] = 0;
            $sql = 'SELECT * FROM totalplayed WHERE email="'.$_POST['email'].'" AND date BETWEEN DATE_FORMAT(DATE_ADD(DATE_SUB(NOW(), INTERVAL '.(11 - $i).' MONTH), INTERVAL -DAY(DATE_SUB(NOW(), INTERVAL '.(11 - $i).' MONTH)) + 1 DAY), "%y-%m-%d") AND DATE_FORMAT(DATE_ADD(DATE_SUB(NOW(), INTERVAL '.(10 - $i).' MONTH), INTERVAL -DAY(DATE_SUB(NOW(), INTERVAL '.(10 - $i).' MONTH)) + 1 DAY), "%y-%m-%d")';
            $res = $mysqli->query($sql);

            while ($row = $res->fetch_array(MYSQLI_ASSOC)) {
                $date_total[$i] += $row['total'];
            }
        }
    ?>
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="stylesheet" type="text/css" href="css/common.css">
    <link rel="stylesheet" type="text/css" href="css/sidebar.css">
    <link rel="stylesheet" type="text/css" href="css/user.css">
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script type="text/javascript" src="js/common.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var playData = new google.visualization.DataTable();

            playData.addColumn('string', 'Date');
            playData.addColumn('number', 'Played Time (Minutes)');
            playData.addRows([
                <?php
                    for ($i = 0; $i < 12; $i++) {
                        echo '["'.$date_str[$i].'", '.($date_total[$i] / 60).'],';
                    }
                ?>
            ]);

            var playOptions = {
                title: 'Total Played Time by Date',
                titleTextStyle: {
                    color: '#161616',
                    fontName: 'Noto Sans KR'
                },
                colors: ['#7F472C'],
                hAxis: {
                    title: 'Date',
                    textStyle: {
                        color: '#161616',
                        fontName: 'Noto Sans KR',
                        fontSize: '0.3vw'
                    }
                },
                vAxis: {
                    title: 'Played Time (Minutes)',
                    textStyle: {
                        color: '#161616',
                        fontName: 'Noto Sans KR',
                        fontSize: '0.3vw'
                    }
                },
                legend: {
                    position: 'right',
                    textStyle: {
                        color: '#161616',
                        fontName: 'Noto Sans KR'
                    }
                }
            };

            var playChart = new google.visualization.ColumnChart(document.getElementById('user_stats_graph'));

            playChart.draw(playData, playOptions);
        }
    </script>
    <?php
        unset($date_str);
        unset($date_cnt);
    ?>
    <script>
        var currentPage = 0;
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
            <div id="user_title">User Statistics: <?php echo $_POST['email']; ?></div>
            <div id="user_stats">
                <div id="user_stats_summary">
                    <div id="user_stats_summary_message">
                        <b>Total Played Time:</b> <?php echo number_format($total_days)." days ".$total_hours." hours ".$total_minutes." minutes ".$total_seconds." seconds (".number_format($total)." seconds)"; ?>
                    </div>
                </div>
                <div id="user_stats_graph">
                </div>
            </div>
            <div id="user_actions">
                <div id="user_actions_title">Actions</div>
                <div class="user_action" onclick="location.href='user.php?currentPage=<?php echo $_POST['currentPage']; ?>';">Return to User List</div>
            </div>
        </div>
    </div>
</body>
</html>

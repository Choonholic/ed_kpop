<?php
    include_once 'setting_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'db_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'file_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'const_info.php';

    $filename = $fileinfo['studio_root'].DIRECTORY_SEPARATOR.'user_stats_export.csv';
    $fileid = fopen($filename, 'w');

    fwrite($fileid, '"email","status","total",');

    for ($i = 0; $i < 12; $i++) {
        $date_str = date("Y-m", strtotime("-".(11 - $i)." Months"));

        fwrite($fileid, '"'.$date_str.'"');

        if ($i != 11) {
            fwrite($fileid, ',');
        } else {
            fwrite($fileid, "\r\n");
        }
    }

    $mysqli = mysqli_connect($db['host'], $db['user'], $db['pass'], $db['name']);
    $mysqli->query("SET NAMES utf8");

    $sql_user = 'SELECT * FROM users';
    $res_user = $mysqli->query($sql_user);

    while ($row_user = $res_user->fetch_array(MYSQLI_ASSOC)) {
        fwrite($fileid, '"'.$row_user['email'].'",');

        switch ($row_user['active']) {
            case 0:
                fwrite($fileid, '"Inactive",');
                break;
            case 2:
                fwrite($fileid, '"Paused",');
                break;
            case 10:
                fwrite($fileid, '"Trial Inactive",');
                break;
            case 11:
                fwrite($fileid, '"Trial Active",');
                break;
            case 1:
            default:
                fwrite($fileid, '"Active",');
                break;
        }

        $sql = 'SELECT * FROM totalplayed WHERE email="'.$row_user['email'].'"';
        $res = $mysqli->query($sql);
        $total = 0;

        while ($row = $res->fetch_array(MYSQLI_ASSOC)) {
            $total += $row['total'];
        }

        fwrite($fileid, '"'.$total.'",');

        for ($i = 0; $i < 12; $i++) {
            $total = 0;
            $sql = 'SELECT * FROM totalplayed WHERE email="'.$row_user['email'].'" AND date BETWEEN DATE_FORMAT(DATE_ADD(DATE_SUB(NOW(), INTERVAL '.(11 - $i).' MONTH), INTERVAL -DAY(DATE_SUB(NOW(), INTERVAL '.(11 - $i).' MONTH)) + 1 DAY), "%y-%m-%d") AND DATE_FORMAT(DATE_ADD(DATE_SUB(NOW(), INTERVAL '.(10 - $i).' MONTH), INTERVAL -DAY(DATE_SUB(NOW(), INTERVAL '.(10 - $i).' MONTH)) + 1 DAY), "%y-%m-%d")';
            $res = $mysqli->query($sql);

            while ($row = $res->fetch_array(MYSQLI_ASSOC)) {
                $total += $row['total'];
            }

            fwrite($fileid, '"'.$total.'"');

            if ($i != 11) {
                fwrite($fileid, ',');
            } else {
                fwrite($fileid, "\r\n");
            }
        }
    }

    fclose($fileid);
?>

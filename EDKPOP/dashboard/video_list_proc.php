<?php
    include_once 'setting_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'db_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'file_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'const_info.php';

    $search = $_POST['search'];

    $mysqli = mysqli_connect($db['host'], $db['user'], $db['pass'], $db['name']);
    $mysqli->query("SET NAMES utf8");

    if (strlen($search)) {
        $prefix = strpos($search, $db_settings['condition_search_prefix']);

        if (($prefix === FALSE) || ($prefix != 0)) {
            $sql = 'SELECT vid FROM videos WHERE vid LIKE "%'.$search.'%" OR lectureid LIKE "%'.$search.'%" OR category LIKE "%'.$search.'%" OR lesson LIKE "%'.$search.'%"';
        } else {
            $sql = 'SELECT vid FROM videos WHERE '.substr($search, 1);
        }
    } else {
        $sql = 'SELECT vid FROM videos';
    }

    $res = $mysqli->query($sql);
    $total_videos = mysqli_num_rows($res);
    $total_pages = ceil($total_videos / $db_settings['videos_per_page']);
    $current_page = $_POST['current'];

    if ($current_page >= $total_pages) {
        $current_page = $total_pages - 1;
    }

    if ($current_page < 0) {
        $current_page = 0;
    }

    $_REQUEST['currentPage'] = $current_page;

    if (strlen($search)) {
        $prefix = strpos($search, $db_settings['condition_search_prefix']);

        if (($prefix === FALSE) || ($prefix != 0)) {
            $sql = 'SELECT * FROM videos WHERE vid LIKE "%'.$search.'%" OR lectureid LIKE "%'.$search.'%" OR category LIKE "%'.$search.'%" OR lesson LIKE "%'.$search.'%" LIMIT '.($current_page * $db_settings['videos_per_page']).', '.$db_settings['videos_per_page'];
        } else {
            $sql = 'SELECT * FROM videos WHERE '.substr($search, 1).' LIMIT '.($current_page * $db_settings['videos_per_page']).', '.$db_settings['videos_per_page'];
        }
    } else {
        $sql = 'SELECT * FROM videos LIMIT '.($current_page * $db_settings['videos_per_page']).', '.$db_settings['videos_per_page'];
    }

    $res = $mysqli->query($sql);

    echo '<!-- '.$current_page.' -->'.PHP_EOL;
    echo '<table class="video_list_table">'.PHP_EOL;
    echo '<thead>'.PHP_EOL;
    echo '<tr class="video_list_header">'.PHP_EOL;
    echo '<th class="vid">Video</th>'.PHP_EOL;
    echo '<th class="lectureid">Lecture</th>'.PHP_EOL;
    echo '<th class="category">Category</th>'.PHP_EOL;
    echo '<th class="lesson">Lesson</th>'.PHP_EOL;
    echo '<th class="vidtype">Type</th>'.PHP_EOL;
    echo '<th class="recordable">Recordable</th>'.PHP_EOL;
    echo '<th class="trial">Trial</th>'.PHP_EOL;
    echo '<th class="thumbnail">Preview</th>'.PHP_EOL;
    echo '<th colspan="4" class="actions">Actions</th>'.PHP_EOL;
    echo '</tr>'.PHP_EOL;
    echo '</thead>'.PHP_EOL;
    echo '<tbody>'.PHP_EOL;

    if ($total_videos > 0) {
        $pos = 0;

        while ($row = $res->fetch_array(MYSQLI_ASSOC)) {
            if (($pos % 2) == 0) {
                echo '<tr class="video_list_even">'.PHP_EOL;
            } else {
                echo '<tr class="video_list_odd">'.PHP_EOL;
            }

            echo '<td class="vid">'.$row['vid'].'</td>'.PHP_EOL;
            echo '<td class="lectureid">'.$row['lectureid'].'</td>'.PHP_EOL;
            echo '<td class="category">'.htmlentities($row['category']).'</td>'.PHP_EOL;
            echo '<td class="lesson">'.mb_strimwidth(htmlentities($row['lesson']), 0, 75, "…", "utf-8").'</td>'.PHP_EOL;
            echo '<td class="vidtype">'.$row['type'].'</td>'.PHP_EOL;
            echo '<td class="recordable">'.$row['recordable'].'</td>'.PHP_EOL;
            echo '<td class="trial">'.$row['trial'].'</td>'.PHP_EOL;

            $thumbnail = $fileinfo['lesson_thumbnail'].DIRECTORY_SEPARATOR.$row['lectureid'].'/'.$row['vid'].$fileinfo['thumbnail_ext'];

            if (file_exists($thumbnail)) {
                echo '<td class="thumbnail">Normal</td>'.PHP_EOL;
            } else {
                echo '<td class="thumbnail">Missing</td>'.PHP_EOL;
            }

            echo '<td class="edit_actions"><span class="edit_video" onclick="edit(\''.$row['lectureid'].'\', \''.$row['vid'].'\');">Edit</span></td>'.PHP_EOL;
            echo '<td class="play_actions"><span class="play_video" onclick="play(\''.$row['lectureid'].'\', \''.$row['vid'].'\', \''.$row['type'].'\');">Play</span></td>'.PHP_EOL;
            echo '<td class="upload_actions"><span class="upload_video" onclick="upload(\''.$row['lectureid'].'\', \''.$row['vid'].'\');">Upload</span></td>'.PHP_EOL;
            echo '<td class="remove_actions"><span class="remove_video" onclick="remove(\''.$row['lectureid'].'\', \''.$row['vid'].'\');">Remove</span></td>'.PHP_EOL;
            echo '</tr>'.PHP_EOL;

            $pos++;
        }
    } else {
        echo '<tr class="video_list_even"><td colspan="12" class="nodata">No Videos</td></tr>'.PHP_EOL;
    }

    echo '</tbody>'.PHP_EOL;
    echo '</table>'.PHP_EOL;
    echo '<div id="video_list_nav">'.PHP_EOL;
    echo '<div id="video_list_nav_buttons">'.PHP_EOL;
    echo '<span id="video_list_first" onclick="goPages(-99999999);">&larrb;</span> <span id="video_list_prev_pages" onclick="goPages('.$db_settings['prev_pages'].');">&Larr;</span> <span id="video_list_prev" onclick="goPages(-1);">&lbarr;</span> <span id="pages">'.(($total_videos > 0) ? ($current_page + 1) : 0).' / <b>'.$total_pages.'</b> [<b>'.$total_videos.'</b>]</span> <span id="video_list_next" onclick="goPages(1);">&rbarr;</span> <span id="video_list_next_pages" onclick="goPages('.$db_settings['next_pages'].');">&Rarr;</span> <span id="video_list_last" onclick="goPages(99999999);">&rarrb;</span>'.PHP_EOL;
    echo '</div>'.PHP_EOL;
    echo '<div id="video_list_nav_search">'.PHP_EOL;
    echo '<form id="search_box_form" name="search_form" method="post">'.PHP_EOL;
    echo '<input id="search" length="30" maxlength="100" type="search" name="search" placeholder="Search">'.PHP_EOL;
    echo '<input id="search_btn" type="submit" value="Search">'.PHP_EOL;
    echo '</form>'.PHP_EOL;
    echo '</div>'.PHP_EOL;
    echo '</div>'.PHP_EOL;
?>

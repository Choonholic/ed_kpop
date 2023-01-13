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
            $sql = 'SELECT lectureid FROM lectures WHERE lectureid LIKE "%'.$search.'%" OR title LIKE "%'.$search.'%" OR difficulty LIKE "%'.$search.'%" OR description LIKE "%'.$search.'%"';
        } else {
            $sql = 'SELECT lectureid FROM lectures WHERE '.substr($search, 1);
        }
    } else {
        $sql = 'SELECT lectureid FROM lectures';
    }

    $res = $mysqli->query($sql);
    $total_lectures = mysqli_num_rows($res);
    $total_pages = ceil($total_lectures / $db_settings['lectures_per_page']);
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
            $sql = 'SELECT * FROM lectures WHERE lectureid LIKE "%'.$search.'%" OR title LIKE "%'.$search.'%" OR difficulty LIKE "%'.$search.'%" OR description LIKE "%'.$search.'%" LIMIT '.($current_page * $db_settings['lectures_per_page']).', '.$db_settings['lectures_per_page'];
        } else {
            $sql = 'SELECT * FROM lectures WHERE '.substr($search, 1).' LIMIT '.($current_page * $db_settings['lectures_per_page']).', '.$db_settings['lectures_per_page'];
        }
    } else {
        $sql = 'SELECT * FROM lectures LIMIT '.($current_page * $db_settings['lectures_per_page']).', '.$db_settings['lectures_per_page'];
    }

    $res = $mysqli->query($sql);
    mysqli_close($mysqli);

    echo '<!-- '.$current_page.' -->'.PHP_EOL;
    echo '<table class="lecture_list_table">'.PHP_EOL;
    echo '<thead>'.PHP_EOL;
    echo '<tr class="lecture_list_header">'.PHP_EOL;
    echo '<th class="lectureid">Lecture</th>'.PHP_EOL;
    echo '<th class="title">Title</th>'.PHP_EOL;
    echo '<th class="difficulty">Difficulty</th>'.PHP_EOL;
    echo '<th class="description">Description</th>'.PHP_EOL;
    echo '<th class="recordable">Recordable</th>'.PHP_EOL;
    echo '<th class="trial">Trial</th>'.PHP_EOL;
    echo '<th class="status">Status</th>'.PHP_EOL;
    echo '<th class="thumbnail">Preview</th>'.PHP_EOL;
    echo '<th colspan="2" class="actions">Actions</th>'.PHP_EOL;
    echo '</tr>'.PHP_EOL;
    echo '</thead>'.PHP_EOL;
    echo '<tbody>'.PHP_EOL;

    if ($total_lectures > 0) {
        $pos = 0;

        while ($row = $res->fetch_array(MYSQLI_ASSOC)) {
            if (($pos % 2) == 0) {
                echo '<tr class="lecture_list_even">'.PHP_EOL;
            } else {
                echo '<tr class="lecture_list_odd">'.PHP_EOL;
            }

            echo '<td class="lectureid">'.$row['lectureid'].'</td>'.PHP_EOL;
            echo '<td class="title">'.htmlentities($row['title']).'</td>'.PHP_EOL;
            echo '<td class="difficulty">'.htmlentities($row['difficulty']).'</td>'.PHP_EOL;
            echo '<td class="decription">'.mb_strimwidth(htmlentities($row['description']), 0, 84, "…", "utf-8").'</td>'.PHP_EOL;
            echo '<td class="recordable">'.$row['recordable'].'</td>'.PHP_EOL;
            echo '<td class="trial">'.$row['trial'].'</td>'.PHP_EOL;
            echo '<td class="status">'.$row['status'].'</td>'.PHP_EOL;

            if (file_exists($fileinfo['lecture_thumbnail'].DIRECTORY_SEPARATOR.$row['lectureid'].$fileinfo['thumbnail_ext'])) {
                echo '<td class="thumbnail">Normal</td>'.PHP_EOL;
            } else {
                echo '<td class="thumbnail">Missing</td>'.PHP_EOL;
            }

            echo '<td class="edit_actions"><span class="edit_lecture" onclick="edit(\''.$row['lectureid'].'\');">Edit</span></td>'.PHP_EOL;
            echo '<td class="remove_actions"><span class="remove_lecture" onclick="remove(\''.$row['lectureid'].'\');">Remove</span></td>'.PHP_EOL;
            echo '</tr>'.PHP_EOL;

            $pos++;
        }
    } else {
        echo '<tr class="lecture_list_even"><td colspan="10" class="nodata">No Lectures</td></tr>'.PHP_EOL;
    }

    echo '</tbody>'.PHP_EOL;
    echo '</table>'.PHP_EOL;
    echo '<div id="lecture_list_nav">'.PHP_EOL;
    echo '<div id="lecture_list_nav_buttons">'.PHP_EOL;
    echo '<span id="lecture_list_first" onclick="goPages(-99999999);">&larrb;</span> <span id="lecture_list_prev_pages" onclick="goPages('.$db_settings['prev_pages'].');">&Larr;</span> <span id="lecture_list_prev" onclick="goPages(-1);">&lbarr;</span> <span id="pages">'.(($total_lectures > 0) ? ($current_page + 1) : 0).' / <b>'.$total_pages.'</b> [<b>'.$total_lectures.'</b>]</span> <span id="lecture_list_next" onclick="goPages(1);">&rbarr;</span> <span id="lecture_list_next_pages" onclick="goPages('.$db_settings['next_pages'].');">&Rarr;</span> <span id="lecture_list_last" onclick="goPages(99999999);">&rarrb;</span>'.PHP_EOL;
    echo '</div>'.PHP_EOL;
    echo '<div id="lecture_list_nav_search">'.PHP_EOL;
    echo '<form id="search_box_form" name="search_form" method="post">'.PHP_EOL;
    echo '<input id="search" length="30" maxlength="100" type="search" name="search" placeholder="Search">'.PHP_EOL;
    echo '<input id="search_btn" type="submit" value="Search">'.PHP_EOL;
    echo '</form>'.PHP_EOL;
    echo '</div>'.PHP_EOL;
    echo '</div>'.PHP_EOL;
?>

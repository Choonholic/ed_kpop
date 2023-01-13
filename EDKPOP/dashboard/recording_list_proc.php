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
            $sql = 'SELECT uniquekey FROM recordings WHERE email LIKE "%'.$search.'%" OR vid LIKE "%'.$search.'%" OR created LIKE "%'.$search.'%"';
        } else {
            $sql = 'SELECT uniquekey FROM recordings WHERE '.substr($search, 1);
        }
    } else {
        $sql = 'SELECT uniquekey FROM recordings';
    }

    $res = $mysqli->query($sql);
    $total_recordings = mysqli_num_rows($res);
    $total_pages = ceil($total_recordings / $db_settings['recordings_per_page']);
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
            $sql = 'SELECT * FROM recordings WHERE email LIKE "%'.$search.'%" OR vid LIKE "%'.$search.'%" OR created LIKE "%'.$search.'%" LIMIT '.($current_page * $db_settings['recordings_per_page']).', '.$db_settings['recordings_per_page'];
        } else {
            $sql = 'SELECT * FROM recordings WHERE '.substr($search, 1).' LIMIT '.($current_page * $db_settings['recordings_per_page']).', '.$db_settings['recordings_per_page'];
        }
    } else {
        $sql = 'SELECT * FROM recordings LIMIT '.($current_page * $db_settings['recordings_per_page']).', '.$db_settings['recordings_per_page'];
    }

    $res = $mysqli->query($sql);
    mysqli_close($mysqli);

    echo '<!-- '.$current_page.' -->'.PHP_EOL;
    echo '<table class="recording_list_table">'.PHP_EOL;
    echo '<thead>'.PHP_EOL;
    echo '<tr class="recording_list_header">'.PHP_EOL;
    echo '<th class="uniquekey">Filename</th>'.PHP_EOL;
    echo '<th class="email">Email</th>'.PHP_EOL;
    echo '<th class="created">Created</th>'.PHP_EOL;
    echo '<th class="mode">Mode</th>'.PHP_EOL;
    echo '<th class="vid">Video</th>'.PHP_EOL;
    echo '<th colspan="3" class="actions">Actions</th>'.PHP_EOL;
    echo '</tr>'.PHP_EOL;
    echo '</thead>'.PHP_EOL;
    echo '<tbody>'.PHP_EOL;

    if ($total_recordings > 0) {
        $pos = 0;

        while ($row = $res->fetch_array(MYSQLI_ASSOC)) {
            if (($pos % 2) == 0) {
                echo '<tr class="recording_list_even">'.PHP_EOL;
            } else {
                echo '<tr class="recording_list_odd">'.PHP_EOL;
            }

            echo '<td class="uniquekey">'.$row['uniquekey'].'</td>'.PHP_EOL;
            echo '<td class="email">'.$row['email'].'</td>'.PHP_EOL;
            echo '<td class="created">'.$row['created'].'</td>'.PHP_EOL;

            if ($row['mode'] == 0) {
                echo '<td class="mode">Single</td>'.PHP_EOL;
            } else {
                echo '<td class="mode">Dual</td>'.PHP_EOL;
            }

            echo '<td class="vid">'.$row['vid'].'</td>'.PHP_EOL;

            echo '<td class="play_actions"><span class="play_recording" onclick="play(\''.$row['uniquekey'].'\', \''.$row['mode'].'\', \''.$row['vid'].'\', \''.$row['begin'].'\', \''.$row['end'].'\');">Play</span></td>'.PHP_EOL;
            echo '<td class="download_actions"><span class="download_recording" onclick="download(\''.$row['uniquekey'].'\');">Download</span></td>'.PHP_EOL;
            echo '<td class="remove_actions"><span class="remove_recording" onclick="remove(\''.$row['uniquekey'].'\');">Remove</span></td>'.PHP_EOL;
            echo '</tr>'.PHP_EOL;

            $pos++;
        }
    } else {
        echo '<tr class="recording_list_even"><td colspan="8" class="nodata">No Recordings</td></tr>'.PHP_EOL;
    }

    echo '</tbody>'.PHP_EOL;
    echo '</table>'.PHP_EOL;
    echo '<div id="recording_list_nav">'.PHP_EOL;
    echo '<div id="recording_list_nav_buttons">'.PHP_EOL;
    echo '<span id="recording_list_first" onclick="goPages(-99999999);">&larrb;</span> <span id="recording_list_prev_pages" onclick="goPages('.$db_settings['prev_pages'].');">&Larr;</span> <span id="recording_list_prev" onclick="goPages(-1);">&lbarr;</span> <span id="pages">'.(($total_recordings > 0) ? ($current_page + 1) : 0).' / <b>'.$total_pages.'</b> [<b>'.$total_recordings.'</b>]</span> <span id="recording_list_next" onclick="goPages(1);">&rbarr;</span> <span id="recording_list_next_pages" onclick="goPages('.$db_settings['next_pages'].');">&Rarr;</span> <span id="recording_list_last" onclick="goPages(99999999);">&rarrb;</span>'.PHP_EOL;
    echo '</div>'.PHP_EOL;
    echo '<div id="recording_list_nav_search">'.PHP_EOL;
    echo '<form id="search_box_form" name="search_form" method="post">'.PHP_EOL;
    echo '<input id="search" length="30" maxlength="100" type="search" name="search" placeholder="Search">'.PHP_EOL;
    echo '<input id="search_btn" type="submit" value="Search">'.PHP_EOL;
    echo '</form>'.PHP_EOL;
    echo '</div>'.PHP_EOL;
    echo '</div>'.PHP_EOL;
?>

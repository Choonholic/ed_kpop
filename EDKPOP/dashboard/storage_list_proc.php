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
            $sql = 'SELECT filename FROM storage WHERE filename LIKE "%'.$search.'%" OR original LIKE "%'.$search.'%"';
        } else {
            $sql = 'SELECT filename FROM storage WHERE '.substr($search, 1);
        }
    } else {
        $sql = 'SELECT filename FROM storage';
    }

    $res = $mysqli->query($sql);
    $total_files = mysqli_num_rows($res);
    $total_pages = ceil($total_files / $db_settings['files_per_page']);
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
            $sql = 'SELECT * FROM storage WHERE filename LIKE "%'.$search.'%" OR original LIKE "%'.$search.'%" LIMIT '.($current_page * $db_settings['files_per_page']).', '.$db_settings['files_per_page'];
        } else {
            $sql = 'SELECT * FROM storage WHERE '.substr($search, 1).' LIMIT '.($current_page * $db_settings['files_per_page']).', '.$db_settings['files_per_page'];
        }
    } else {
        $sql = 'SELECT * FROM storage LIMIT '.($current_page * $db_settings['files_per_page']).', '.$db_settings['files_per_page'];
    }

    $res = $mysqli->query($sql);
    mysqli_close($mysqli);

    echo '<!-- '.$current_page.' -->'.PHP_EOL;
    echo '<table class="storage_list_table">'.PHP_EOL;
    echo '<thead>'.PHP_EOL;
    echo '<tr class="storage_list_header">'.PHP_EOL;
    echo '<th class="filename">Stored Filename</th>'.PHP_EOL;
    echo '<th class="original">Original Filename</th>'.PHP_EOL;
    echo '<th class="size">Size</th>'.PHP_EOL;
    echo '<th colspan="3" class="actions">Actions</th>'.PHP_EOL;
    echo '</tr>'.PHP_EOL;
    echo '</thead>'.PHP_EOL;
    echo '<tbody>'.PHP_EOL;

    if ($total_files > 0) {
        $pos = 0;

        while ($row = $res->fetch_array(MYSQLI_ASSOC)) {
            if (($pos % 2) == 0) {
                echo '<tr class="storage_list_even">'.PHP_EOL;
            } else {
                echo '<tr class="storage_list_odd">'.PHP_EOL;
            }

            echo '<td class="filename">'.$row['filename'].'</td>'.PHP_EOL;
            echo '<td class="original">'.$row['original'].'</td>'.PHP_EOL;
            echo '<td class="size">'.number_format($row['size']).'</td>'.PHP_EOL;
            echo '<td class="copy_actions"><span class="copy_storage" onclick="copy(\''.$row['filename'].'\');">Copy URL</span></td>'.PHP_EOL;
            echo '<td class="remove_actions"><span class="remove_storage" onclick="remove(\''.$row['filename'].'\');">Remove</span></td>'.PHP_EOL;
            echo '</tr>'.PHP_EOL;

            $pos++;
        }
    } else {
        echo '<tr class="storage_list_even"><td colspan="5" class="nodata">No Files</td></tr>'.PHP_EOL;
    }

    echo '</tbody>'.PHP_EOL;
    echo '</table>'.PHP_EOL;
    echo '<div id="storage_list_nav">'.PHP_EOL;
    echo '<div id="storage_list_nav_buttons">'.PHP_EOL;
    echo '<span id="storage_list_first" onclick="goPages(-99999999);">&larrb;</span> <span id="storage_list_prev_pages" onclick="goPages('.$db_settings['prev_pages'].');">&Larr;</span> <span id="storage_list_prev" onclick="goPages(-1);">&lbarr;</span> <span id="pages">'.(($total_files > 0) ? ($current_page + 1) : 0).' / <b>'.$total_pages.'</b> [<b>'.$total_files.'</b>]</span> <span id="storage_list_next" onclick="goPages(1);">&rbarr;</span> <span id="storage_list_next_pages" onclick="goPages('.$db_settings['next_pages'].');">&Rarr;</span> <span id="storage_list_last" onclick="goPages(99999999);">&rarrb;</span>'.PHP_EOL;
    echo '</div>'.PHP_EOL;
    echo '<div id="storage_list_nav_search">'.PHP_EOL;
    echo '<form id="search_box_form" name="search_form" method="post">'.PHP_EOL;
    echo '<input id="search" length="30" maxlength="100" type="search" name="search" placeholder="Search">'.PHP_EOL;
    echo '<input id="search_btn" type="submit" value="Search">'.PHP_EOL;
    echo '</form>'.PHP_EOL;
    echo '</div>'.PHP_EOL;
    echo '</div>'.PHP_EOL;
?>

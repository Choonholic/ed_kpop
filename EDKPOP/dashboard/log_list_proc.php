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
            $sql = 'SELECT uniquekey FROM logs WHERE type LIKE "%'.$search.'%" OR date LIKE "%'.$search.'%" OR email LIKE "%'.$search.'%" OR message LIKE "%'.$search.'%"';
        } else {
            $sql = 'SELECT uniquekey FROM logs WHERE '.substr($search, 1);
        }
    } else {
        $sql = 'SELECT uniquekey FROM logs';
    }

    $res = $mysqli->query($sql);
    $total_logs = mysqli_num_rows($res);
    $total_pages = ceil($total_logs / $db_settings['logs_per_page']);
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
            $sql = 'SELECT * FROM logs WHERE type LIKE "%'.$search.'%" OR date LIKE "%'.$search.'%" OR email LIKE "%'.$search.'%" OR message LIKE "%'.$search.'%" ORDER BY uniquekey DESC LIMIT '.($current_page * $db_settings['logs_per_page']).', '.$db_settings['logs_per_page'];
        } else {
            $sql = 'SELECT * FROM logs WHERE '.substr($search, 1).' ORDER BY uniquekey DESC LIMIT '.($current_page * $db_settings['logs_per_page']).', '.$db_settings['logs_per_page'];
        }
    } else {
        $sql = 'SELECT * FROM logs ORDER BY uniquekey DESC LIMIT '.($current_page * $db_settings['logs_per_page']).', '.$db_settings['logs_per_page'];
    }

    $res = $mysqli->query($sql);

    echo '<!-- '.$current_page.' -->'.PHP_EOL;
    echo '<table class="log_list_table">'.PHP_EOL;
    echo '<thead>'.PHP_EOL;
    echo '<tr class="log_list_header">'.PHP_EOL;
    echo '<th class="type">Type</th>'.PHP_EOL;
    echo '<th class="date">Date</th>'.PHP_EOL;
    echo '<th class="email">Email</th>'.PHP_EOL;
    echo '<th class="message">Message</th>'.PHP_EOL;
    echo '<th class="actions">Actions</th>'.PHP_EOL;
    echo '</tr>'.PHP_EOL;
    echo '</thead>'.PHP_EOL;
    echo '<tbody>'.PHP_EOL;

    if ($total_logs > 0) {
        $pos = 0;

        while ($row = $res->fetch_array(MYSQLI_ASSOC)) {
            if (($pos % 2) == 0) {
                echo '<tr class="log_list_even">'.PHP_EOL;
            } else {
                echo '<tr class="log_list_odd">'.PHP_EOL;
            }

            echo '<td class="type">'.$row['type'].'</td>'.PHP_EOL;
            echo '<td class="date">'.$row['date'].'</td>'.PHP_EOL;
            echo '<td class="email">'.$row['email'].'</td>'.PHP_EOL;
            echo '<td class="message">'.$row['message'].'</td>'.PHP_EOL;
            echo '<td class="remove_actions"><span class="remove_log" onclick="remove(\''.$row['uniquekey'].'\');">Remove</span></td>'.PHP_EOL;
            echo '</tr>'.PHP_EOL;

            $pos++;
        }
    } else {
        echo '<tr class="log_list_even"><td colspan="4" class="nodata">No Logs</td></tr>'.PHP_EOL;
    }

    mysqli_close($mysqli);

    echo '</tbody>'.PHP_EOL;
    echo '</table>'.PHP_EOL;
    echo '<div id="log_list_nav">'.PHP_EOL;
    echo '<div id="log_list_nav_buttons">'.PHP_EOL;
    echo '<span id="log_list_first" onclick="goPages(-99999999);">&larrb;</span> <span id="log_list_prev_pages" onclick="goPages('.$db_settings['prev_pages'].');">&Larr;</span> <span id="log_list_prev" onclick="goPages(-1);">&lbarr;</span> <span id="pages">'.(($total_logs > 0) ? ($current_page + 1) : 0).' / <b>'.$total_pages.'</b> [<b>'.$total_logs.'</b>]</span> <span id="log_list_next" onclick="goPages(1);">&rbarr;</span> <span id="log_list_next_pages" onclick="goPages('.$db_settings['next_pages'].');">&Rarr;</span> <span id="log_list_last" onclick="goPages(99999999);">&rarrb;</span>'.PHP_EOL;
    echo '</div>'.PHP_EOL;
    echo '<div id="log_list_nav_search">'.PHP_EOL;
    echo '<form id="search_box_form" name="search_form" method="post">'.PHP_EOL;
    echo '<input id="search" length="30" maxlength="100" type="search" name="search" placeholder="Search">'.PHP_EOL;
    echo '<input id="search_btn" type="submit" value="Search">'.PHP_EOL;
    echo '</form>'.PHP_EOL;
    echo '</div>'.PHP_EOL;
    echo '</div>'.PHP_EOL;
?>

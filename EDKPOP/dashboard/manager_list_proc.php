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
            $sql = 'SELECT email FROM managers WHERE email LIKE "%'.$search.'%"';
        } else {
            $sql = 'SELECT email FROM managers WHERE '.substr($search, 1);
        }
    } else {
        $sql = 'SELECT email FROM managers';
    }

    $res = $mysqli->query($sql);
    $total_managers = mysqli_num_rows($res);
    $total_pages = ceil($total_managers / $db_settings['managers_per_page']);
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
            $sql = 'SELECT * FROM managers WHERE email LIKE "%'.$search.'%" LIMIT '.($current_page * $db_settings['managers_per_page']).', '.$db_settings['managers_per_page'];
        } else {
            $sql = 'SELECT * FROM managers WHERE '.substr($search, 1).' LIMIT '.($current_page * $db_settings['managers_per_page']).', '.$db_settings['managers_per_page'];
        }
    } else {
        $sql = 'SELECT * FROM managers LIMIT '.($current_page * $db_settings['managers_per_page']).', '.$db_settings['managers_per_page'];
    }

    $res = $mysqli->query($sql);

    echo '<!-- '.$current_page.' -->'.PHP_EOL;
    echo '<table class="manager_list_table">'.PHP_EOL;
    echo '<thead>'.PHP_EOL;
    echo '<tr class="manager_list_header">'.PHP_EOL;
    echo '<th class="email">Email</th>'.PHP_EOL;
    echo '<th class="level">Level</th>'.PHP_EOL;
    echo '<th class="status">Status</th>'.PHP_EOL;
    echo '<th colspan="3" class="actions">Actions</th>'.PHP_EOL;
    echo '</tr>'.PHP_EOL;
    echo '</thead>'.PHP_EOL;
    echo '<tbody>'.PHP_EOL;

    if ($total_managers > 0) {
        $pos = 0;

        while ($row = $res->fetch_array(MYSQLI_ASSOC)) {
            if (($pos % 2) == 0) {
                echo '<tr class="manager_list_even">'.PHP_EOL;
            } else {
                echo '<tr class="manager_list_odd">'.PHP_EOL;
            }

            echo '<td class="email">'.$row['email'].'</td>'.PHP_EOL;

            switch ($row['level']) {
                case MANAGER_ADMIN:
                    echo '<td class="level">Administrator</td>'.PHP_EOL;
                    break;
                case MANAGER_INSTRUCTOR:
                    echo '<td class="level">Instructor</td>'.PHP_EOL;
                    break;
                case MANAGER_MODERATOR:
                default:
                    echo '<td class="level">Moderator</td>'.PHP_EOL;
                    break;
            }

            switch ($row['active']) {
                case USER_ACTIVE:
                    echo '<td class="status">Active</td>'.PHP_EOL;
                    break;
                case USER_INACTIVE:
                default:
                    echo '<td class="status">Inactive</td>'.PHP_EOL;
                    break;
            }

            echo '<td class="edit_actions"><span class="edit_manager" onclick="edit(\''.$row['email'].'\');">Edit</span></td>'.PHP_EOL;
            echo '<td class="reset_actions"><span class="reset_password" onclick="reset(\''.$row['email'].'\');">Reset Password</span></td>'.PHP_EOL;
            echo '<td class="remove_actions"><span class="remove_manager" onclick="remove(\''.$row['email'].'\');">Remove</span></td>'.PHP_EOL;
            echo '</tr>';

            $pos++;
        }
    } else {
        echo '<tr class="manager_list_even"><td colspan="6" class="nodata">No Managers</td></tr>'.PHP_EOL;
    }

    mysqli_close($mysqli);

    echo '</tbody>'.PHP_EOL;
    echo '</table>'.PHP_EOL;
    echo '<div id="manager_list_nav">'.PHP_EOL;
    echo '<div id="manager_list_nav_buttons">'.PHP_EOL;
    echo '<span id="manager_list_first" onclick="goPages(-99999999);">&larrb;</span> <span id="manager_list_prev_pages" onclick="goPages('.$db_settings['prev_pages'].');">&Larr;</span> <span id="manager_list_prev" onclick="goPages(-1);">&lbarr;</span> <span id="pages">'.(($total_managers > 0) ? ($current_page + 1) : 0).' / <b>'.$total_pages.'</b> [<b>'.$total_managers.'</b>]</span> <span id="manager_list_next" onclick="goPages(1);">&rbarr;</span> <span id="manager_list_next_pages" onclick="goPages('.$db_settings['next_pages'].');">&Rarr;</span> <span id="manager_list_last" onclick="goPages(99999999);">&rarrb;</span>'.PHP_EOL;
    echo '</div>'.PHP_EOL;
    echo '<div id="manager_list_nav_search">'.PHP_EOL;
    echo '<form id="search_box_form" name="search_form" method="post">'.PHP_EOL;
    echo '<input id="search" length="30" maxlength="100" type="search" name="search" placeholder="Search">'.PHP_EOL;
    echo '<input id="search_btn" type="submit" value="Search">'.PHP_EOL;
    echo '</form>'.PHP_EOL;
    echo '</div>'.PHP_EOL;
    echo '</div>'.PHP_EOL;
?>

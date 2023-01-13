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

        if (($prefix === FALSE) || ($prefix !== 0)) {
            $sql = 'SELECT email FROM users WHERE email LIKE "%'.$search.'%" OR nickname LIKE "%'.$search.'%" OR location LIKE "%'.$search.'%"';
        } else {
            $sql = 'SELECT email FROM users WHERE '.substr($search, 1);
        }
    } else {
        $sql = 'SELECT email FROM users';
    }

    $res = $mysqli->query($sql);
    $total_users = mysqli_num_rows($res);
    $total_pages = ceil($total_users / $db_settings['users_per_page']);
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
            $sql = 'SELECT * FROM users WHERE email LIKE "%'.$search.'%" OR nickname LIKE "%'.$search.'%" OR location LIKE "%'.$search.'%" LIMIT '.($current_page * $db_settings['users_per_page']).', '.$db_settings['users_per_page'];
        } else {
            $sql = 'SELECT * FROM users WHERE '.substr($search, 1).' LIMIT '.($current_page * $db_settings['users_per_page']).', '.$db_settings['users_per_page'];
        }
    } else {
        $sql = 'SELECT * FROM users LIMIT '.($current_page * $db_settings['users_per_page']).', '.$db_settings['users_per_page'];
    }

    $res = $mysqli->query($sql);

    echo '<!-- '.$current_page.' -->'.PHP_EOL;
    echo '<table class="user_list_table">'.PHP_EOL;
    echo '<thead>'.PHP_EOL;
    echo '<tr class="user_list_header">'.PHP_EOL;
    echo '<th class="email">Email</th>'.PHP_EOL;
    echo '<th class="nickname">Nickname</th>'.PHP_EOL;
    echo '<th class="status">Status</th>'.PHP_EOL;
    echo '<th class="lastlogin">Last Login</th>'.PHP_EOL;
    echo '<th class="location">Location</th>'.PHP_EOL;
    echo '<th colspan="4" class="actions">Actions</th>'.PHP_EOL;
    echo '</tr>'.PHP_EOL;
    echo '</thead>'.PHP_EOL;
    echo '<tbody>'.PHP_EOL;

    if ($total_users > 0) {
        $pos = 0;

        while ($row = $res->fetch_array(MYSQLI_ASSOC)) {
            if (($pos % 2) == 0) {
                echo '<tr class="user_list_even">'.PHP_EOL;
            } else {
                echo '<tr class="user_list_odd">'.PHP_EOL;
            }

            echo '<td class="email">'.$row['email'].'</td>'.PHP_EOL;
            echo '<td class="nickname">'.$row['nickname'].'</td>'.PHP_EOL;

            switch ($row['active']) {
                case USER_ACTIVE:
                    echo '<td class="status">Active</td>'.PHP_EOL;
                    break;
                case USER_PAUSED:
                    echo '<td class="status">Paused</td>'.PHP_EOL;
                    break;
                case USER_TRIAL_ACTIVE:
                    echo '<td class="status">Trial Active</td>'.PHP_EOL;
                    break;
                case USER_TRIAL_INACTIVE:
                    echo '<td class="status">Trial</td>'.PHP_EOL;
                    break;
                case USER_INACTIVE:
                default:
                    echo '<td class="status">Inactive</td>'.PHP_EOL;
                    break;
            }

            echo '<td class="lastlogin">'.$row['lastlogin'].'</td>'.PHP_EOL;
            echo '<td class="location">'.mb_strimwidth(htmlentities($row['location']), 0, 30, "…", "utf-8").'</td>'.PHP_EOL;
            echo '<td class="stats_actions"><span class="user_stats" onclick="stats(\''.$row['email'].'\');">Statistics</span></td>'.PHP_EOL;

            switch ($row['active']) {
                case USER_ACTIVE:
                    echo '<td class="status_actions"><span class="pause" onclick="change(\''.$row['email'].'\', 2);">Pause</span></td>'.PHP_EOL;
                    break;
                case USER_PAUSED:
                    echo '<td class="status_actions"><span class="activate" onclick="change(\''.$row['email'].'\', 1);">Activate</span></td>'.PHP_EOL;
                    break;
                case USER_TRIAL_ACTIVE:
                    echo '<td class="status_actions"><span class="trial_active">Trial</span></td>'.PHP_EOL;
                    break;
                case USER_TRIAL_INACTIVE:
                    echo '<td class="status_actions"><span class="trial_inactive">Trial</span></td>'.PHP_EOL;
                    break;
                case USER_INACTIVE:
                default:
                    echo '<td class="status_actions"><span class="inactive">Inactive</span></td>'.PHP_EOL;
                    break;
            }

            echo '<td class="reset_actions"><span class="reset_password" onclick="reset(\''.$row['email'].'\');">Reset Password</span></td>'.PHP_EOL;
            echo '<td class="remove_actions"><span class="remove_user" onclick="remove(\''.$row['email'].'\');">Remove</span></td>'.PHP_EOL;
            echo '</tr>'.PHP_EOL;

            $pos++;
        }
    } else {
        echo '<tr class="user_list_even"><td colspan="9" class="nodata">No Users</td></tr>'.PHP_EOL;
    }

    mysqli_close($mysqli);

    echo '</tbody>'.PHP_EOL;
    echo '</table>'.PHP_EOL;
    echo '<div id="user_list_nav">'.PHP_EOL;
    echo '<div id="user_list_nav_buttons">'.PHP_EOL;
    echo '<span id="user_list_first" onclick="goPages(-99999999);">&larrb;</span> <span id="user_list_prev_pages" onclick="goPages('.$db_settings['prev_pages'].');">&Larr;</span> <span id="user_list_prev" onclick="goPages(-1);">&lbarr;</span> <span id="pages">'.(($total_users > 0) ? ($current_page + 1) : 0).' / <b>'.$total_pages.'</b> [<b>'.$total_users.'</b>]</span> <span id="user_list_next" onclick="goPages(1);">&rbarr;</span> <span id="user_list_next_pages" onclick="goPages('.$db_settings['next_pages'].');">&Rarr;</span> <span id="user_list_last" onclick="goPages(99999999);">&rarrb;</span>'.PHP_EOL;
    echo '</div>'.PHP_EOL;
    echo '<div id="user_list_nav_search">'.PHP_EOL;
    echo '<form id="search_box_form" name="search_form" method="post">'.PHP_EOL;
    echo '<input id="search" length="30" maxlength="100" type="search" name="search" placeholder="Search">'.PHP_EOL;
    echo '<input id="search_btn" type="submit" value="Search">'.PHP_EOL;
    echo '</form>'.PHP_EOL;
    echo '</div>'.PHP_EOL;
    echo '</div>'.PHP_EOL;
?>

<?php
    include_once 'setting_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'db_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'file_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'const_info.php';

    if (!isset($_SESSION)) {
        session_start();
    }

    $currentPage = $_POST['currentPage'];
    $mysqli = mysqli_connect($db['host'], $db['user'], $db['pass'], $db['name']);
    $mysqli->query("SET NAMES utf8");

    if ($_POST['method'] == 'add') {
        $sql = 'SELECT * FROM managers WHERE email="'.$_POST['email'].'"';
        $res = $mysqli->query($sql);

        if (mysqli_num_rows($res) == 1) {
            mysqli_close($mysqli);
            echo '<!doctype html>'.PHP_EOL;
            echo '<html>'.PHP_EOL;
            echo '<body onload="document.post_form.submit();">'.PHP_EOL;
            echo '<form name="post_form" method="post" action="message.php">'.PHP_EOL;
            echo '<input type="hidden" name="title" value="Error">'.PHP_EOL;
            echo '<input type="hidden" name="message" value="Manager '.$_POST['email'].' is already registered.">'.PHP_EOL;
            echo '<input type="hidden" name="from" value="'.basename($_SERVER['PHP_SELF'], '.php').'">'.PHP_EOL;
            echo '</form>'.PHP_EOL;
            echo '</body>'.PHP_EOL;
            echo '</html>'.PHP_EOL;
            mysqli_close($mysqli);
            exit(1);
        }

        $pwd_hash = hash('sha512', md5($db_settings['manager_default_password']));
        $sql = 'INSERT INTO managers (email, password, level, active) VALUES ("'.$_POST['email'].'", "'.$pwd_hash.'", "'.$_POST['level'].'", 0)';
        $res = $mysqli->query($sql);

        mysqli_close($mysqli);
    } else {
        $sql_admin = 'SELECT * FROM managers WHERE level="'.MANAGER_ADMIN.'"';
        $res_admin = $mysqli->query($sql_admin);
        $cnt_admin = mysqli_num_rows($res);

        $sql = 'SELECT * FROM managers WHERE email="'.$_POST['oldemail'].'"';
        $res = $mysqli->query($sql);
        $row = $res->fetch_array(MYSQLI_ASSOC);

        if (mysqli_num_rows($res) != 1) {
            echo '<!doctype html>'.PHP_EOL;
            echo '<html>'.PHP_EOL;
            echo '<body onload="document.post_form.submit();">'.PHP_EOL;
            echo '<form name="post_form" method="post" action="message.php">'.PHP_EOL;
            echo '<input type="hidden" name="title" value="Error">'.PHP_EOL;
            echo '<input type="hidden" name="message" value="Something happened during registeration process.">'.PHP_EOL;
            echo '<input type="hidden" name="from" value="'.basename($_SERVER['PHP_SELF'], '.php').'">'.PHP_EOL;
            echo '</form>'.PHP_EOL;
            echo '</body>'.PHP_EOL;
            echo '</html>'.PHP_EOL;
            mysqli_close($mysqli);
            exit(1);
        }

        if (($cnt_admin == 1) && ($row['level'] == MANAGER_ADMIN) && ($_POST['level'] != MANAGER_ADMIN)) {
            echo '<!doctype html>'.PHP_EOL;
            echo '<html>'.PHP_EOL;
            echo '<body onload="document.post_form.submit();">'.PHP_EOL;
            echo '<form name="post_form" method="post" action="message.php">'.PHP_EOL;
            echo '<input type="hidden" name="title" value="Error">'.PHP_EOL;
            echo '<input type="hidden" name="message" value="At least one administrator must be existed.">'.PHP_EOL;
            echo '<input type="hidden" name="from" value="'.basename($_SERVER['PHP_SELF'], '.php').'">'.PHP_EOL;
            echo '</form>'.PHP_EOL;
            echo '</body>'.PHP_EOL;
            echo '</html>'.PHP_EOL;
            mysqli_close($mysqli);
            exit(1);
        }

        $sql = 'UPDATE managers SET level="'.$_POST['level'].'" WHERE email="'.$_POST['oldemail'].'"';
        $res = $mysqli->query($sql);

        mysqli_close($mysqli);
    }

    header('Location: manager.php?currentPage='.$currentPage);
?>

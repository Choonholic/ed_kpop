<?php
    include_once 'setting_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'db_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'file_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'const_info.php';

    if (!isset($_SESSION)) {
        session_start();
    }

    $eml = $_POST['emailaddress'];
    $currentPage = $_POST['currentPage'];
    $mysqli = mysqli_connect($db['host'], $db['user'], $db['pass'], $db['name']);
    $mysqli->query("SET NAMES utf8");
    $sql = 'SELECT * FROM users WHERE email="'.$eml.'"';
    $res = $mysqli->query($sql);

    if (mysqli_num_rows($res) == 1) {
        mysqli_close($mysqli);
        echo '<!doctype html>'.PHP_EOL;
        echo '<html>'.PHP_EOL;
        echo '<body onload="document.post_form.submit();">'.PHP_EOL;
        echo '<form name="post_form" method="post" action="message.php">'.PHP_EOL;
        echo '<input type="hidden" name="title" value="Error">'.PHP_EOL;
        echo '<input type="hidden" name="message" value="User '.$eml.' is already registered.">'.PHP_EOL;
        echo '<input type="hidden" name="from" value="'.basename($_SERVER['PHP_SELF'], '.php?currentPage='.$currentPage).'">'.PHP_EOL;
        echo '</form>'.PHP_EOL;
        echo '</body>'.PHP_EOL;
        echo '</html>'.PHP_EOL;
        exit(1);
    }

    $pwd_hash = hash('sha512', md5($db_settings['user_default_password']));
    $sql = 'INSERT INTO users (email, nickname, password, active, created, activated, lastlogin, ip, location) VALUES ("'.$eml.'", "", "'.$pwd_hash.'", "'.USER_INACTIVE.'", "'.date("Y-m-d H:i:s").'", NULL, NULL, NULL, NULL)';
    $res = $mysqli->query($sql);

    mysqli_close($mysqli);

    if ($res != FALSE) {
        header('Location: user.php?currentPage='.$currentPage);
    } else {
        echo '<!doctype html>'.PHP_EOL;
        echo '<html>'.PHP_EOL;
        echo '<body onload="document.post_form.submit();">'.PHP_EOL;
        echo '<form name="post_form" method="post" action="message.php">'.PHP_EOL;
        echo '<input type="hidden" name="title" value="Error">'.PHP_EOL;
        echo '<input type="hidden" name="message" value="Something happened during registeration process.">'.PHP_EOL;
        echo '<input type="hidden" name="from" value="'.basename($_SERVER['PHP_SELF'], '.php?currentPage='.$currentPage).'">'.PHP_EOL;
        echo '</form>'.PHP_EOL;
        echo '</body>'.PHP_EOL;
        echo '</html>'.PHP_EOL;
    }
?>

<?php
    include_once 'setting_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'db_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'file_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'const_info.php';

    if (!isset($_SESSION)) {
        session_start();
    }

    $eml = $_SESSION['loggedin'];
    $nck = $_POST['nickname'];
    $pwd = $_POST['pwd'];
    $pwc = $_POST['pwd_chk'];

    if ($pwd != $pwc) {
        echo '<!doctype html>'.PHP_EOL;
        echo '<html>'.PHP_EOL;
        echo '<body onload="document.post_form.submit();">'.PHP_EOL;
        echo '<form name="post_form" method="post" action="message.php">'.PHP_EOL;
        echo '<input type="hidden" name="title" value="Error">'.PHP_EOL;
        echo '<input type="hidden" name="message" value="Please make sure password and verify fields are the same.">'.PHP_EOL;
        echo '<input type="hidden" name="from" value="'.basename($_SERVER['PHP_SELF'], '.php').'">'.PHP_EOL;
        echo '</form>'.PHP_EOL;
        echo '</body>'.PHP_EOL;
        echo '</html>'.PHP_EOL;
        exit(1);
    }

    $mysqli = mysqli_connect($db['host'], $db['user'], $db['pass'], $db['name']);
    $mysqli->query("SET NAMES utf8");
    $sql = 'SELECT * FROM users WHERE email="'.$eml.'"';
    $res = $mysqli->query($sql);

    if (mysqli_num_rows($res) == 0) {
        mysqli_close($mysqli);
        session_destroy();
        echo '<!doctype html>'.PHP_EOL;
        echo '<html>'.PHP_EOL;
        echo '<body onload="document.post_form.submit();">'.PHP_EOL;
        echo '<form name="post_form" method="post" action="message.php">'.PHP_EOL;
        echo '<input type="hidden" name="title" value="Error">'.PHP_EOL;
        echo '<input type="hidden" name="message" value="Something happened during activation process.">'.PHP_EOL;
        echo '<input type="hidden" name="from" value="'.basename($_SERVER['PHP_SELF'], '.php').'">'.PHP_EOL;
        echo '</form>'.PHP_EOL;
        echo '</body>'.PHP_EOL;
        echo '</html>'.PHP_EOL;
        exit(1);
    }

    $row = $res->fetch_array(MYSQLI_ASSOC);
    $active = USER_INACTIVE;
    $now = date("Y-m-d H:i:s");
    $pwd_hash = hash('sha512', md5($pwd));

    if ($row['active'] == USER_TRIAL_INACTIVE) {
        $active = USER_TRIAL_ACTIVE;
    } else {
        $active = USER_ACTIVE;
    }

    $num_ip = ip2long($_SERVER["REMOTE_ADDR"]);
    $sql_ip = 'SELECT * FROM ip2location WHERE ip_from<='.$num_ip.' AND ip_to>='.$num_ip;
    $res_ip = $mysqli->query($sql_ip);
    $row_ip = $res_ip->fetch_array(MYSQLI_ASSOC);

    $sql = 'UPDATE users SET nickname="'.$nck.'", password="'.$pwd_hash.'", active="'.$active.'", activated="'.$now.'", lastlogin="'.$now.'", ip="'.$_SERVER["REMOTE_ADDR"].'", location="'.$row_ip['country_name'].'" WHERE email="'.$eml.'"';
    $res = $mysqli->query($sql);
    mysqli_close($mysqli);

    if ($res != FALSE) {
        $_SESSION['active'] = USER_ACTIVE;
        $_SESSION['expire'] = date("Y-m-d H:i:s", strtotime("+".$eds_settings['trial_period']." hours", strtotime($now)));
        header('Location: mainmenu.php');
    } else {
        session_destroy();
        echo '<!doctype html>'.PHP_EOL;
        echo '<html>'.PHP_EOL;
        echo '<body onload="document.post_form.submit();">'.PHP_EOL;
        echo '<form name="post_form" method="post" action="message.php">'.PHP_EOL;
        echo '<input type="hidden" name="title" value="Error">'.PHP_EOL;
        echo '<input type="hidden" name="message" value="Something happened during activation process.">'.PHP_EOL;
        echo '<input type="hidden" name="from" value="'.basename($_SERVER['PHP_SELF'], '.php').'">'.PHP_EOL;
        echo '</form>'.PHP_EOL;
        echo '</body>'.PHP_EOL;
        echo '</html>'.PHP_EOL;
    }
?>

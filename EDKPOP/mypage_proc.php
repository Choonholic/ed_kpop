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
    $cpw = $_POST['current_pwd'];
    $pwd = $_POST['new_pwd'];
    $pwc = $_POST['pwd_chk'];

    if (($cpw != null) && ($pwd != null) && ($pwc != null)) {
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
        $row = $res->fetch_array(MYSQLI_ASSOC);
        $cpw_hash = hash('sha512', md5($cpw));

        if ($cpw_hash != $row['password']) {
            mysqli_close($mysqli);
            echo '<!doctype html>'.PHP_EOL;
            echo '<html>'.PHP_EOL;
            echo '<body onload="document.post_form.submit();">'.PHP_EOL;
            echo '<form name="post_form" method="post" action="message.php">'.PHP_EOL;
            echo '<input type="hidden" name="title" value="Error">'.PHP_EOL;
            echo '<input type="hidden" name="message" value="Please check current password again.">'.PHP_EOL;
            echo '<input type="hidden" name="from" value="'.basename($_SERVER['PHP_SELF'], '.php').'">'.PHP_EOL;
            echo '</form>'.PHP_EOL;
            echo '</body>'.PHP_EOL;
            echo '</html>'.PHP_EOL;
            exit(1);
        }

        $pwd_hash = hash('sha512', md5($pwd));
        $num_ip = ip2long($_SERVER["REMOTE_ADDR"]);
        $sql_ip = 'SELECT * FROM ip2location WHERE ip_from<='.$num_ip.' AND ip_to>='.$num_ip;
        $res_ip = $mysqli->query($sql_ip);
        $row_ip = $res_ip->fetch_array(MYSQLI_ASSOC);

        $sql = 'UPDATE users SET nickname="'.$nck.'", password="'.$pwd_hash.'", ip="'.$_SERVER["REMOTE_ADDR"].'", location="'.$row_ip['country_name'].'" WHERE email="'.$eml.'"';
        $res = $mysqli->query($sql);
        mysqli_close($mysqli);
    } else {
        $mysqli = mysqli_connect($db['host'], $db['user'], $db['pass'], $db['name']);
        $mysqli->query("SET NAMES utf8");

        $num_ip = ip2long($_SERVER["REMOTE_ADDR"]);
        $sql_ip = 'SELECT * FROM ip2location WHERE ip_from<='.$num_ip.' AND ip_to>='.$num_ip;
        $res_ip = $mysqli->query($sql_ip);
        $row_ip = $res_ip->fetch_array(MYSQLI_ASSOC);

        $sql = 'UPDATE users SET nickname="'.$nck.'", ip="'.$_SERVER["REMOTE_ADDR"].'", location="'.$row_ip['country_name'].'" WHERE email="'.$eml.'"';
        $res = $mysqli->query($sql);
        mysqli_close($mysqli);
    }

    $_SESSION['nickname'] = $nck;

    if ($_POST['from'] != null) {
        header('Location: '.$_POST['from'].'.php');
    }

    header('Location: mainmenu.php');
?>

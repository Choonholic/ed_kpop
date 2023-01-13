<?php
    include_once 'setting_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'db_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'file_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'const_info.php';

    if (!isset($_SESSION)) {
        session_start();
    }

    $eml = $_POST['emailaddress'];
    $pwd = $_POST['pwd'];

    if ($eml != "" && $pwd != "") {
        $mysqli = mysqli_connect($db['host'], $db['user'], $db['pass'], $db['name']);
        $mysqli->query("SET NAMES utf8");
        $sql = 'SELECT * FROM managers WHERE email="'.$eml.'"';
        $res = $mysqli->query($sql);

        mysqli_close($mysqli);

        if (mysqli_num_rows($res) == 1) {
            $row = $res->fetch_array(MYSQLI_ASSOC);
            $pwd_hash = hash('sha512', md5($pwd));

            if ($row['password'] == $pwd_hash) {
                $_SESSION['db_loggedin'] = $row['email'];
                $_SESSION['db_active'] = $row['active'];
                $_SESSION['db_level'] = $row['level'];

                if (isset($_SESSION['db_loggedin'])) {
                    if ($_SESSION['db_active'] == USER_ACTIVE) {
                        header('Location: ./home.php');
                    } else {
                        header('Location: ./activation.php');
                    }
                }
            }
        }
    }

    echo '<!doctype html>'.PHP_EOL;
    echo '<html>'.PHP_EOL;
    echo '<body onload="document.post_form.submit();">'.PHP_EOL;
    echo '<form name="post_form" method="post" action="message.php">'.PHP_EOL;
    echo '<input type="hidden" name="title" value="Error">'.PHP_EOL;
    echo '<input type="hidden" name="message" value="Email or Password is invalid.">'.PHP_EOL;
    echo '<input type="hidden" name="from" value="'.basename($_SERVER['PHP_SELF'], '.php').'">'.PHP_EOL;
    echo '</form>'.PHP_EOL;
    echo '</body>'.PHP_EOL;
    echo '</html>'.PHP_EOL;
?>

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
        $sql = 'SELECT * FROM users WHERE email="'.$eml.'"';
        $res = $mysqli->query($sql);

        mysqli_close($mysqli);

        if (mysqli_num_rows($res) == 1) {
            $row = $res->fetch_array(MYSQLI_ASSOC);
            $pwd_hash = hash('sha512', md5($pwd));
            $activated = $row['activated'];
            $now = time();
            $date_short = date("YmdHis", $now);
            $date = date("Y-m-d H:i:s", $now);

            if ($row['password'] == $pwd_hash) {
                $_SESSION['loggedin'] = $row['email'];
                $_SESSION['active'] = $row['active'];
                $_SESSION['nickname'] = $row['nickname'];

                if (isset($_SESSION['loggedin'])) {
                    switch ($_SESSION['active']) {
                        case USER_ACTIVE:
                            $mysqli = mysqli_connect($db['host'], $db['user'], $db['pass'], $db['name']);
                            $mysqli->query("SET NAMES utf8");

                            if ($activated == NULL) {
                                $sql = 'UPDATE users SET activated="'.$date.'" WHERE email="'.$eml.'"';
                                $res = $mysqli->query($sql);
                            }

                            $num_ip = ip2long($_SERVER["REMOTE_ADDR"]);
                            $sql_ip = 'SELECT * FROM ip2location WHERE ip_from<='.$num_ip.' AND ip_to>='.$num_ip;
                            $res_ip = $mysqli->query($sql_ip);
                            $row_ip = $res_ip->fetch_array(MYSQLI_ASSOC);

                            $sql = 'UPDATE users SET lastlogin="'.$date.'", ip="'.$_SERVER["REMOTE_ADDR"].'", location="'.$row_ip['country_name'].'" WHERE email="'.$eml.'"';
                            $res = $mysqli->query($sql);

                            $uniquekey = $date_short.'_LOGIN';
                            $message = 'User ['.$eml.'] has logged in from ['.$row_ip['country_name'].' ('.$_SERVER["REMOTE_ADDR"].')].';
                            $sql = 'INSERT INTO logs (uniquekey, type, date, email, message) VALUES ("'.$uniquekey.'", "LOGIN", "'.$date.'", "'.$eml.'", "'.$message.'")';
                            $res = $mysqli->query($sql);

                            mysqli_close($mysqli);

                            header('Location: mainmenu.php');
                            break;
                        case USER_TRIAL_ACTIVE:
                            $mysqli = mysqli_connect($db['host'], $db['user'], $db['pass'], $db['name']);
                            $mysqli->query("SET NAMES utf8");

                            if ($activated == NULL) {
                                $sql = 'UPDATE users SET activated="'.$date.'" WHERE email="'.$eml.'"';
                                $res = $mysqli->query($sql);
                                $activated = $date;
                            }

                            $sql = 'UPDATE users SET lastlogin="'.$date.'", ip="'.$_SERVER["REMOTE_ADDR"].'", location="'.$row_ip['country_name'].'" WHERE email="'.$eml.'"';
                            $res = $mysqli->query($sql);

                            $_SESSION['expire'] = date("Y-m-d H:i:s", strtotime("+".$eds_settings['trial_period']." hours", strtotime($activated)));

                            if ($date > $_SESSION['expire']) {
                                mysqli_close($mysqli);

                                header('Location: signout.php');
                            }

                            $uniquekey = $date_short.'_LOGIN';
                            $message = 'Trial User ['.$eml.'] has logged in from ['.$_SERVER["REMOTE_ADDR"].'].';
                            $sql = 'INSERT INTO logs (uniquekey, type, date, email, message) VALUES ("'.$uniquekey.'", "TRIAL_LOGIN", "'.$date.'", "'.$eml.'", "'.$message.'")';
                            $res = $mysqli->query($sql);

                            mysqli_close($mysqli);

                            header('Location: mainmenu.php');
                            break;
                        default:
                            header('Location: activation.php');
                            break;
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

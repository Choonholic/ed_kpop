<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ED: Online Personal Studio</title>
    <?php
        include_once 'setting_info.php';
        include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'db_info.php';
        include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'file_info.php';
        include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'const_info.php';

        if (!isset($_SESSION)) {
            session_start();
        }

        if (isset($_SESSION['db_loggedin'])) {
            if ($_SESSION['db_active'] == USER_ACTIVE) {
                header('Location: home.php');
            } else {
                header('Location: activation.php');
            }
        }
    ?>
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="stylesheet" type="text/css" href="css/common.css">
    <link rel="stylesheet" type="text/css" href="css/index.css">
</head>
<body>
    <div id="header">
        <div id="logo">
            <a href="index.php"><img src="images/logo.png"></a>
        </div>
        <div id="version">Version <b><?php echo $db_settings['version']; ?></b></div>
    </div>
    <div id='container'>
        <div id="login_box">
            <div id="login_box_title">Dashboard Login</div>
            <form id="login_box_form" name="login_form" action="login_proc.php" method="post">
                <div id="email_box">
                    <input id="email" length="30" maxlength="100" type="email" name="emailaddress" style="ime-mode: inactive;" placeholder="Email" required>
                </div>
                <div id="password_box">
                    <input id="password" length="30" maxlength="100" type="password" name="pwd" style="ime-mode: disabled;" placeholder="Password" required>
                </div>
                <div>
                    <input id="signin_btn" type="submit" value="Sign-in">
                </div>
            </form>
        </div>
    </div>
</body>
</html>

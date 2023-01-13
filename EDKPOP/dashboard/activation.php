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
            }
        } else {
            header('Location: index.php');
        }
    ?>
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="stylesheet" type="text/css" href="css/common.css">
    <link rel="stylesheet" type="text/css" href="css/activation.css">
</head>
<body>
    <div id="header">
        <div id="logo" onclick="location.href='index.php';">
            <img src="images/logo.png">
        </div>
        <div id="username"><?php echo $_SESSION['db_loggedin']; ?></div>
    </div>
    <div id='container'>
        <div id="activation_box">
            <form id="activation_box_form" name="activation_form" action="activation_proc.php" method="post">
                <div id="activation_title">Activate your account</div>
                <div id="password_box">
                    <input id="password" length="30" maxlength="100" type="password" name="pwd" style="ime-mode: disabled;" placeholder="Password" required>
                </div>
                <div id="password_verify_box">
                    <input id="password_verify" length="30" maxlength="100" type="password" name="pwd_chk" style="ime-mode: disabled;" placeholder="Verify Password" required>
                </div>
                <div>
                    <input id="activation_btn" type="submit" value="Activate">
                </div>
            </form>
        </div>
    </div>
</body>
</html>

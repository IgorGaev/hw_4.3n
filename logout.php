<?php
session_start();

$old_user = $_SESSION['valid_user'];
unset($_SESSION['valid_user']);
session_destroy();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <h1>Выход</h1>
        <?php
        if(!empty($old_user)) {
            echo 'Успешный выход.<br/>';
        } else {
            echo 'Вы не входили в систему.';
        }
        ?>
        <a href="reg.php">На главную страницу</a>
    </body>
</html>        


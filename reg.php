<?php
session_start();
echo 'Введите данные для регистрации или войдите, если уже регистрировались: <br/>';
if (isset($_POST['login']) && isset($_POST['password'])) {
    if (isset($_POST['sign_in'])) {
        $login = $_POST['login'];
        $password = md5(trim($_POST['password']));

        require_once 'connect.php';

        $query = "select id, login from user where login='$login' and password='$password'";

        $result = $db_conn->query($query);
        $currentUser = $result->fetch_assoc();
        
        if ($result->num_rows > 0) {
            $_SESSION['valid_user'] = $login;
            $_SESSION['user_id'] = $currentUser['id'];
            
        } else {
            echo 'Неверный логин или пароль';
        }
        $db_conn->close();
    }

    if (isset($_POST['register'])) {
        $login = strip_tags(trim($_POST['login']));
        $password = md5(trim($_POST['password']));

        require_once 'connect.php';

        $query = "INSERT INTO user(login,password ) "
                . "VALUES ('$login', '$password')";

        $db_conn->query($query);
        echo 'Регистрация прошла успешно.<br/>';
        echo 'Вы не вошли в систему.';
        $db_conn->close();
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <?php
        if (isset($_SESSION['valid_user'])) {
            header('Location: todo.php');
            exit();
//            echo '<a href ="logout.php">Выход</a><br/>';
        } else {
            if (!isset($login)) {
                echo 'Вы не вошли в систему.';
            }
            echo '
            <form method="POST">
                <input type="text" name="login" placeholder="Логин">
                <input type="password" name="password" placeholder="Пароль">
                <input type="submit" name="sign_in" value="Вход"/>
                <input type="submit" name="register" value="Регистрация"/>
            </form>';
        }
        ?>
    </body>
</html>

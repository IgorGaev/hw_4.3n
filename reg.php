<?php
session_start();
echo 'Введите данные для регистрации или войдите, если уже регистрировались: <br/>';
if (isset($_POST['login']) && isset($_POST['password'])) {
    if (isset($_POST['sign_in'])) {
        $login = $_POST['login'];
        $password = md5(trim($_POST['password']));

        require_once 'connect.php';

        $query = "select id, login from user where login=? and password=?";
        $result = $pdo->prepare($query);
        $result->execute(array($login,$password));

        $currentUser = $result->fetch(PDO::FETCH_ASSOC);

        if ($result->rowCount() > 0) {
            $_SESSION['valid_user'] = $login;
            $_SESSION['user_id'] = $currentUser['id'];
        } else {
            echo 'Неверный логин или пароль';
        }
        $pdo= null;
    }

    if (isset($_POST['register'])) {
        $login = strip_tags(trim($_POST['login']));
        $password = md5(trim($_POST['password']));

        require_once 'connect.php';
        $query = "select login from user where login=?";
        $result = $pdo->prepare($query);
        $result->execute(array($login));
        if ($result->rowCount() > 0) {
            echo 'Пользователь с таким логином уже есть.<br/>';
           $pdo= null;
            
        } else {
            $query = "INSERT INTO user(login,password ) "
                    . "VALUES (:login, :password)";
            $result = $pdo->prepare($query);
            $result->execute(array('login' => $login, 'password'=>$password));
            echo 'Регистрация прошла успешно.<br/>';
            echo 'Вы не вошли в систему.';
            $pdo= null;
        }
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

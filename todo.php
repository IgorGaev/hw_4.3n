<?php
session_start();
require_once 'connect.php';
if (!isset($_SESSION['valid_user'])) {
    header('Location: reg.php');
    exit();
} else {
    $userid = $_SESSION['user_id'];
    $username = $_SESSION['valid_user'];
}
if (isset($_POST['description'])) {
    $description = strip_tags($_POST['description']);
    $date_added = $_POST['date'];
    $result = $pdo->prepare("INSERT INTO task(description, date_added, user_id)"
            . " VALUES (:description, :date_added, :userid)");
    $result->execute(array('description' => $description, 'date_added' => $date_added, 'userid' => $userid));
}

if (!empty($_GET['id'])) {
    $id = (int) $_GET['id'];
    $result = $pdo->prepare("DELETE FROM task WHERE id=?");
    $result->execute(array($id));
}

if (!empty($_GET['isdoneid'])) {
    $id = (int) $_GET['isdoneid'];
    $result = $pdo->prepare("UPDATE `task` SET is_done=1 where id=?");
    $result->execute(array($id));
}

if (isset($_POST['assigned_user_id'])) {
    $assignedUserId = $_POST['assigned_user_id'];
    $id = (int) $_POST['id'];
    $result = $pdo->prepare("UPDATE `task` SET assigned_user_id=? where id=? ");
    $result->execute(array($assignedUserId, $id));
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <style>
            table {border-spacing: 0;
                   border-collapse: collapse;}
            table td, table th {border: 1px solid #ccc;
                                padding: 5px;}
            table th {background: #eee;    }
        </style>
    </head>
    <body>
        <h1>Здравствуйте, <?= $username ?>! Вот ваш список дел:</h1>
        <form method = "POST" >
            <input type = "text" name = "description" placeholder = "Описание задачи">
            <input type = "hidden" name = "date" value = "<?= date('Y-m-d H:i:s') ?>">
            <input type = "submit" value = "Добавить">
        </form >
        <table>
            <thead>
                <tr>
                    <th>Описание задачи</th>
                    <th>Дата добавления</th>
                    <th>Статус</th>
                    <th>Действие</th>
                    <th>Ответственный</th>
                    <th>Автор</th>
                    <th>Закрепить задачу за пользователем</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $pdo->prepare("SELECT task.id, user_id, description, "
                        . "user.login, date_added,is_done FROM task LEFT JOIN user "
                        . "ON task.assigned_user_id=user.id WHERE task.user_id=?");
                $result->execute(array($userid));

                //$userList = $pdo->query("SELECT id, login FROM user");
                //Если делать как в строке 77, то работает <select> только в первой строке таблицы.
                //пришлось вызывать запрос ("SELECT id, login FROM user")  в цикле.
                //Когда соединение было не через PDO, такой проблемы не было.

                foreach ($result as $row):
                    ?>
                    <tr>
                        <td><?= $row['description'] ?></td>
                        <td><?= $row['date_added'] ?></td>
                        <td><?=
                            ($row['is_done'] == 0) ?
                                    '<span style="color: red;">В процессе</span>' : '<span style="color: green;">Выполнено</span>';
                            ?></td>
                        <td>
                            <a href='todo.php?isdoneid=<?= $row['id'] ?>'>Выполнить</a>
                            <a href='todo.php?id=<?= $row['id'] ?>'>Удалить</a>
                        </td>
                        <td><?php
                            if (isset($row['login'])) {
                                echo $row['login'];
                            } else {
                                echo 'Вы';
                            }
                            ?></td>
                        <td><?= $username ?></td>
                        <td><form method="POST">
                                <select name="assigned_user_id">
                                    <?php
                                    $userList = $pdo->query("SELECT id, login FROM user"); //знаю что не правильно, иначе работает только в первой строке таблицы.
                                    foreach ($userList as $user):
                                        ?> 
                                        <option label="<?= $user['login'] ?>" value="<?= $user['id'] ?>"></option>                            
    <?php endforeach; ?>
                                </select>
                                <input type = "hidden" name = "id" value = "<?= $row['id'] ?>">
                                <input type='submit' name='assign' value='Переложить ответственность' />
                            </form>
                        </td>
                    </tr>
<?php endforeach; ?>
            </tbody>
        </table>

        <p><strong>Также, посмотрите, что от Вас требуют другие люди:</strong></p>

        <table>
            <thead>
                <tr>
                    <th>Описание задачи</th>
                    <th>Дата добавления</th>
                    <th>Статус</th>
                    <th>Действие</th>
                    <th>Ответственный</th>
                    <th>Автор</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $pdo->prepare("SELECT task.id, user_id, description, "
                        . "user.login, date_added,is_done FROM task LEFT JOIN user "
                        . "ON task.user_id=user.id WHERE task.assigned_user_id=?");
                $result->execute(array($userid));
                foreach ($result as $row):
                    ?>
                    <tr>
                        <td><?= $row['description'] ?></td>
                        <td><?= $row['date_added'] ?></td>
                        <td><?=
                            ($row['is_done'] == 0) ?
                                    '<span style="color: red;">В процессе</span>' : '<span style="color: green;">Выполнено</span>';
                            ?></td>
                        <td>
                            <a href='todo.php?isdoneid=<?= $row['id'] ?>'>Выполнить</a>
                            <a href='todo.php?id=<?= $row['id'] ?>'>Удалить</a>
                        </td>
                        <td><?= $username ?></td>
                        <td><?php
                            if (isset($row['login'])) {
                                echo $row['login'];
                            } else {
                                echo 'Вы';
                            }
                            ?></td>
                    </tr>
<?php endforeach; ?>
            </tbody>
        </table>    

        <?php
        echo '<a href ="logout.php">Выход</a><br/>';
        ?>
    </body>
</html>

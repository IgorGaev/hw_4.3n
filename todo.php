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
    $query = "INSERT INTO task(description, date_added, user_id)"
            . " VALUES ('$description', '$date_added', '$userid')";
    $db_conn->query($query);
}

if (!empty($_GET['id'])) {
    $id = $_GET['id'];
    $db_conn->query("DELETE FROM task WHERE id='$id'");
}

if (!empty($_GET['isdoneid'])) {
    $id = $_GET['isdoneid'];
    $db_conn->query("UPDATE `task` SET is_done=1 where id='$id'");
}

if (isset($_POST['assigned_user_id'])) {
    $assignedUserId = $_POST['assigned_user_id'];
    $id = $_POST['id'];
    $db_conn->query("UPDATE `task` SET assigned_user_id='$assignedUserId' where id='$id' ");
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
                $result = $db_conn->query("SELECT task.id, user_id, description, user.login, date_added,is_done FROM task LEFT JOIN user ON task.assigned_user_id=user.id WHERE task.user_id='$userid'");
                $userList = $db_conn->query("SELECT * FROM user");
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
                            if (isset($row['login']))
                                echo $row['login'];
                            else
                                echo 'Вы';
                            ?></td>
                        <td><?= $username ?></td>
                        <td><form method="POST">
                                <select name="assigned_user_id">
    <?php foreach ($userList as $user): ?> 
                                        <option value="<?= $user['id'] ?>"><?= $user['login'] ?></option>                            
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
                $result = $db_conn->query("SELECT task.id, user_id, description, user.login, date_added,is_done FROM task LEFT JOIN user ON task.user_id=user.id WHERE task.assigned_user_id='$userid'");
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
                    if (isset($row['login']))
                        echo $row['login'];
                    else
                        echo 'Вы';
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

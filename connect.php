<?php
$db_conn = new mysqli('localhost', 'root', '', 'base');
$db_conn->set_charset("utf8");

if (mysqli_connect_errno()) {
    echo 'Невозможно подключится к базе данных: ' . mysqli_connect_error();
    exit();
}
?>
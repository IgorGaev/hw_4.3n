<?php

//$db_conn = new mysqli('localhost', 'igaev', 'neto1673', 'igaev');
//$db_conn = new mysqli('localhost', 'root', '', 'base');
//$db_conn->set_charset("utf8");
//
//if (mysqli_connect_errno()) {
//    echo 'Невозможно подключится к базе данных: ' . mysqli_connect_error();
//    exit();
//}
//$host = 'localhost';
//$db = 'base';
//$user = 'root';
//$pass = '';
//$charset = 'utf8';
//$dsn = "mysql:host=$host;dbname=$db;charset=$charset";


$host = 'localhost';
$db = 'igaev';
$user = 'igaev';
$pass = 'neto1673';
$charset = 'utf8';
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
    $pdo = new PDO($dsn, $user, $pass);
} catch (PDOException $e) {
    print "Невозможно подключится к базе данных: " . $e->getMessage() . "<br/>";
    die();
}
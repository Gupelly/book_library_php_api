<?php
$servername = 'mysql-8.0';  
$username = 'root';          
$password = '';              
$dbname = 'book_library';    

// Создаем подключение
$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

<?php
function generateToken()
{
	$bytes = openssl_random_pseudo_bytes(20, $cstrong);
	return bin2hex($bytes); 
}

function authenticateUser($login, $password, $dbConnection) {
    $query = sprintf("SELECT * FROM users WHERE login = '%s'", $login);
    $user = mysqli_query($dbConnection, $query);
    $user = mysqli_fetch_assoc($user);
    
    if ($user && $password === $user['password_hash']) {
        $token = generateToken(); 
        return $token;
    }
    
    return null; 
}

function registerUser($login, $password, $confirmPassword, $dbConnection) {
    // Проверка совпадения паролей
    if ($password !== $confirmPassword) {
        $res = [
            'status' => 'error', 
            'message' => 'Password confirmation failed'
        ];
        return json_encode($res);
    }

    // Проверка наличия пользователя в базе данных
    $query = sprintf("SELECT * FROM users WHERE login = '%s'", $login);
    $user = mysqli_query($dbConnection, $query);
    $user = mysqli_fetch_assoc($user);

    if ($user) {
        $res = [
            'status' => 'error', 
            'message' => 'User with this login already exists'
        ];
        return json_encode($res);
    }

    // Хеширование пароля
    $passwordHash = password_hash($password, PASSWORD_BCRYPT);
    
    // Создание нового пользователя
    $insertQuery =  sprintf("INSERT INTO users (login, password_hash) VALUES ('%s', '%s')",
    $login, $passwordHash);
    mysqli_query($dbConnection, $insertQuery);

    http_response_code(201);

    $res = [
        "status"=> "success",
        'message' => 'The user has been registered successfully'
    ];

    return json_encode($res);
}
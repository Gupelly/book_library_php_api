<?php
function getAllUsers($dbConnection) {
    $query = "SELECT id, login FROM users";
    $users = mysqli_query($dbConnection, $query);
    $userList = [];

    while ($user = mysqli_fetch_assoc($users)) {
        $userList[] = $user;
    };

    return json_encode($userList);
}

function grantAccessToLibrary($ownerId, $granteeId, $dbConnection) {
    // Проверка на существование записи о доступе
    $queryAccess = sprintf("SELECT * FROM access_rights WHERE ownned_id = '%s' AND grantee_id = '%s'",
    $ownerId, $granteeId);
    $access = mysqli_query($dbConnection, $queryAccess);
    $access = mysqli_fetch_assoc($access);

    if ($access) {
        $res = [
            'status' => 'error', 
            'message' => 'Access has already been granted'
        ];
        return json_encode($res);
    }

    // Создание записи о доступе
    $insertAccess = sprintf("INSERT INTO access_rights (ownned_id, grantee_id) VALUES ('%s', '%s')",
    $ownerId, $granteeId);
    mysqli_query($dbConnection, $insertAccess);

    http_response_code(201);

    $res = [
        'status' => 'success', 
        'message' => 'Access to the library has been granted successfully'
    ];

    return json_encode($res);
}



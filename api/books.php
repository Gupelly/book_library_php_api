<?php
function getUserBooks($userId, $dbConnection) {
    $query = sprintf("SELECT id, title FROM books WHERE user_id = '%s'", $userId);
    $books = mysqli_query($dbConnection, $query);
    $bookList = [];

    while ($book = mysqli_fetch_assoc($books)) {
        $bookList[] = $book;
    };

    return $bookList;  
}

function createBook($userId, $title, $dbConnection,  $text = null, $file = null,) {
    if ($text === null && $file === null) {
        return ['status' => 'error', 'message' => 'You must provide the text or upload a text file'];
    }

    if ($file !== null) {
        $text = file_get_contents($file);
        
        if ($text === false) {
            return ['status' => 'error', 'message' => 'Error reading file'];
        }
    }

    $text = trim($text); 

    $query = sprintf("INSERT INTO books (user_id, title, text) VALUES ('%s', '%s', '%s')", 
    $userId, $title, $text);
    mysqli_query($dbConnection, $query);

    http_response_code(201);

    return ['status' => 'success', 'message' => 'The book has been created successfully'];
}

function openBook($bookId, $dbConnection) { 
    $query = sprintf("SELECT title, text FROM books WHERE id = '%s'", $bookId);
    $book = mysqli_query($dbConnection, $query);
    $book = mysqli_fetch_assoc($book);

    return $book;
}

function saveBook($bookId, $title, $text, $dbConnection) {
    if (empty($title) || empty($text)) {
        return ['status' => 'error', 'message' => 'The title and text of the book cannot be empty'];
    }

    $query = sprintf("UPDATE books SET title = '%s', text = '%s', updated_at = NOW() WHERE id = '%s'",
    $title, $text, $bookId);
    mysqli_query($dbConnection, $query);

    http_response_code(201);

    return ['status' => 'success', 'message' => 'The book has been updated successfully'];
}


function deleteBook($bookId, $dbConnection) {
    $query = sprintf("UPDATE books SET deleted_at = NOW() WHERE id = '%s'", $bookId);
    mysqli_query($dbConnection, $query);
    return ['status' => 'success', 'message' => 'The book has been deleted successfully'];
}


function restoreBook($bookId, $dbConnection) {
    $query = sprintf("UPDATE books SET deleted_at = NULL WHERE id = '%s'", $bookId);
    mysqli_query($dbConnection, $query);
    return ['status' => 'success', 'message' => 'The book has been restored successfully'];
}


function getBooksByUserId($requesterId, $ownerId, $dbConnection) {
    $query = sprintf("SELECT * FROM access_rights WHERE ownned_id = '%s' AND grantee_id = '%s'",
    $ownerId, $requesterId);
    $access = mysqli_query($dbConnection, $query);
    $access = mysqli_fetch_assoc($access);

    if (!$access) {
        return ['status' => 'error', 'message' => 'No access to this library'];
    }

    $booksQuery = sprintf("SELECT id, title FROM books WHERE user_id = '%s' AND deleted_at IS NULL",
    $ownerId);
    $books = mysqli_query($dbConnection, $booksQuery);
    $bookList = [];

    while ($user = mysqli_fetch_assoc($books)) {
        $bookList[] = $user;
    };

    return json_encode($bookList);
    
}
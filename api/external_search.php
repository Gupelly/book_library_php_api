<?php 
function searchBooks($query) {
    $googleApiUrl = "https://www.googleapis.com/books/v1/volumes?q=" . urlencode($query);
    $mannApiUrl = "https://www.mann-ivanov-ferber.ru/book/search.ajax?q=" . urlencode($query);
    
    $results = [
        'googleBooks' => [],
        'mannBooks' => []
    ];

    $googleResponse = file_get_contents($googleApiUrl);
    if ($googleResponse !== false) {
        $googleData = json_decode($googleResponse, true);
        if (isset($googleData['items'])) {
            foreach ($googleData['items'] as $item) {
                $results['googleBooks'][] = [
                    'title' => $item['volumeInfo']['title'],
                    'authors' => $item['volumeInfo']['authors'] ?? [],
                    'description' => $item['volumeInfo']['description'] ?? ''
                ];
            }
        }
    }

    $mannResponse = file_get_contents($mannApiUrl);
    if ($mannResponse !== false) {
        $mannData = json_decode($mannResponse, true);
        if (isset($mannData['books'])) {
            foreach ($mannData['books'] as $book) {
                $results['mannBooks'][] = [
                    'title' => $book['title'],
                    'url' => $book['url']
                ];
            }
        }
    }

    return $results;
}


function createBookByID($bookID, $userId, $title, $text, $dbConnection) {
    if (empty($bookID) || empty($text)) {
        return ['status' => 'error', 'message' => 'You must specify the ID and text of the book'];
    }

    $query = sprintf("INSERT INTO books (user_id, external_uuid, title, text) VALUES ('%s', '%s', '%s', '%s')",
    $userId, $bookID, $title, $text);
    mysqli_query($dbConnection, $query);

    return ['status' => 'success', 'message' => 'The book has been created successfully'];
}
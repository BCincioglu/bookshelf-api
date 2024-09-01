<?php

require_once 'util/validation.php';

// Routing Function
function routeRequestBook($method, $request, $input) {
    try {
        switch ($method) {
            case 'GET':
                if (isset($request[0]) && $request[0] === 'books') {
                    if (isset($request[1])) {
                        getBook($request[1]);
                    } else {
                        listBooks();
                    }
                } else {
                    header('HTTP/1.0 404 Not Found');
                    echo json_encode(['error' => 'Endpoint not found']);
                }
                break;

            case 'POST':
                if (isset($request[0]) && $request[0] === 'books') {
                    if (validateAddBookInput($input)) {
                        addBook($input);
                        header('HTTP/1.1 201 Created');
                    } else {
                        header('HTTP/1.0 400 Bad Request');
                        echo json_encode(['error' => 'Invalid input']);
                    }
                }
                break;

            case 'PUT':
                if ($request[0] === 'books' && isset($request[1])) {
                    if (validateUpdateBookInput($input)) {
                        updateBook($request[1], $input);
                    } else {
                        header('HTTP/1.0 400 Bad Request');
                        echo json_encode(['error' => 'Invalid input']);
                    }
                }
                break;

            case 'DELETE':
                if ($request[0] === 'books' && isset($request[1])) {
                    deleteBook($request[1]);
                }
                break;

            default:
                header('HTTP/1.0 405 Method Not Allowed');
                echo json_encode(['error' => 'Method not allowed']);
                break;
        }
    } catch (Exception $e) {
        header('HTTP/1.0 500 Internal Server Error');
        echo json_encode(['error' => 'Internal server error', 'details' => $e->getMessage()]);
    }
}

?>
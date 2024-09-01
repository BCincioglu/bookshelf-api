<?php

//Controllers
function listBooks($db = null) {
    try {
        if ($db === null) {
            $db = new Database();
        }
        $pdo = $db->getConnection();
        $stmt = $pdo->query("SELECT * FROM books");
        $books = $stmt->fetchAll();
        echo json_encode($books);
    } catch (PDOException $e) {
        header('HTTP/1.0 500 Internal Server Error');
        echo json_encode(['error' => 'Failed to fetch books']);
    }
}

function getBook($id) {
    try {
        $db = new Database();
        $pdo = $db->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
        $stmt->execute([$id]);
        $book = $stmt->fetch();
        if ($book) {
            echo json_encode($book);
        } else {
            header('HTTP/1.0 404 Not Found');
            echo json_encode(['error' => 'Book not found']);
        }
    } catch (PDOException $e) {
        header('HTTP/1.0 500 Internal Server Error');
        echo json_encode(['error' => 'Failed to fetch book']);
    }
}


function addBook($input) {
    try {
        $db = new Database();
        $pdo = $db->getConnection();
        $stmt = $pdo->prepare("INSERT INTO books (title, author, isbn, price) VALUES (?, ?, ?, ?)");
        $stmt->execute([$input['title'], $input['author'], $input['isbn'], $input['price']]);
        $bookId = $pdo->lastInsertId();
        header('HTTP/1.0 201 Created');
        echo json_encode(['message' => 'Book added']);
        echo json_encode(['id' => $bookId]);
    } catch (PDOException $e) {
        header('HTTP/1.0 500 Internal Server Error');
        echo json_encode(['error' => 'Failed to add book']);
    }
}

function updateBook($id, $input) {
    $db = new Database();
    $pdo = $db->getConnection();

    $stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
    $stmt->execute([$id]);
    $existingBook = $stmt->fetch();

    if (!$existingBook) {
        header('HTTP/1.0 404 Not Found');
        echo json_encode(['error' => 'Book not found']);
        return;
    }

    $updateFields = [];
    $params = [];

    if (!empty($input['title'])) {
        $updateFields[] = 'title = ?';
        $params[] = $input['title'];
    }

    if (!empty($input['author'])) {
        $updateFields[] = 'author = ?';
        $params[] = $input['author'];
    }

    if (!empty($input['isbn'])) {
        $updateFields[] = 'isbn = ?';
        $params[] = $input['isbn'];
    }

    if (!empty($input['price'])) {
        $updateFields[] = 'price = ?';
        $params[] = $input['price'];
    }

    if (empty($updateFields)) {
        header('HTTP/1.0 400 Bad Request');
        echo json_encode(['error' => 'No fields provided for update']);
        return;
    }

    // Dynamix SQL Query
    $sql = "UPDATE books SET " . implode(', ', $updateFields) . " WHERE id = ?";
    $params[] = $id;

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        echo json_encode(['message' => 'Book updated']);
    } catch (PDOException $e) {
        header('HTTP/1.0 400 Bad Request');
        echo json_encode(['error' => 'Could not update book', 'details' => $e->getMessage()]);
    }
}


function deleteBook($id) {
    try {
        $db = new Database();
        $pdo = $db->getConnection();
        $stmt = $pdo->prepare("DELETE FROM books WHERE id = ?");
        $stmt->execute([$id]);
        if ($stmt->rowCount() > 0) {
            echo json_encode(['message' => 'Book deleted']);
        } else {
            header('HTTP/1.0 404 Not Found');
            echo json_encode(['error' => 'Book not found']);
        }
    } catch (PDOException $e) {
        header('HTTP/1.0 500 Internal Server Error');
        echo json_encode(['error' => 'Failed to delete book']);
    }
}

?>

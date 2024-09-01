<?php

// Validation
function validateAddBookInput($input) {
    if (!isset($input['title'], $input['author'], $input['isbn'], $input['price'])) {
        return false;
    }

    if (!is_string($input['title']) || !is_string($input['author']) || !is_string($input['isbn']) || !is_numeric($input['price'])) {
        return false;
    }

    // ISBN (13 char)
    if (strlen($input['isbn']) !== 13) {
        return false;
    }

    if ($input['price'] <= 0) {
        return false;
    }

    return true;
}

function validateUpdateBookInput($input) {
    $validKeys = ['title', 'author', 'isbn', 'price'];

    foreach ($input as $key => $value) {
        if (!in_array($key, $validKeys)) {
            return false; 
        }

        if (empty($value)) {
            return false;
        }
    }

    if (isset($input['title']) && !is_string($input['title'])) {
        return false;
    }
    if (isset($input['author']) && !is_string($input['author'])) {
        return false;
    }
    if (isset($input['isbn'])) {
        if (!is_string($input['isbn']) || strlen($input['isbn']) !== 13) { // ISBN (13 char)
            return false;
        }
    }
    if (isset($input['price'])) {
        if (!is_numeric($input['price']) || $input['price'] <= 0) {
            return false;
        }
    }

    return true;
}

?>
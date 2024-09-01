<?php

use PHPUnit\Framework\TestCase;

require_once 'conf/db.php';
require_once 'controller/bookController.php';

/* 

    Testlerin hata vermeden çalışabilmesi için controller'daki header()'ların yorum satırına alınması gerekli!!!

*/

class BookControllerTest extends TestCase
{
    private $db;

    protected function setUp(): void {

        $this->db = new Database();
        $pdo = $this->db->getConnection();
        $pdo->exec("TRUNCATE TABLE books");

        $pdo->exec("INSERT INTO books (title, author, isbn, price) VALUES
                    ('Clean Code', 'Robert C. Martin', '9780132350884', '32.99'),
                    ('The Pragmatic Programmer', 'Andrew Hunt', '9780201616224', '37.50')");
    }

    private function captureOutput(callable $function) {
        ob_start();
        $function();
        return ob_get_clean();
    }

    public function testListBooks() {
        $response = $this->captureOutput(function() {
            listBooks($this->db);
        });

        $expectedJson = json_encode([
            ["id" => 1, "title" => "Clean Code", "author" => "Robert C. Martin", "isbn" => "9780132350884", "price" => "32.99"],
            ["id" => 2, "title" => "The Pragmatic Programmer", "author" => "Andrew Hunt", "isbn" => "9780201616224", "price" => "37.50"]
        ]);

        $actual = json_decode($response, true);

        foreach ($actual as &$book) {
            unset($book['created_at']);
        }

        $this->assertJsonStringEqualsJsonString($expectedJson, json_encode($actual));
    }
    
    public function testGetBook() {
        $response = $this->captureOutput(function() {
            getBook(1, $this->db);
        });

        $expectedJson = json_encode([
            "id" => 1,
            "title" => "Clean Code",
            "author" => "Robert C. Martin",
            "isbn" => "9780132350884",
            "price" => "32.99",
        ]);

        $actual = json_decode($response, true);

        unset($actual['created_at']);
    
        $this->assertJsonStringEqualsJsonString($expectedJson, json_encode($actual));
    }

    // public function testAddBook() {
    //     $_POST = [
    //         "title" => "The Pragmatic Programmer",
    //         "author" => "Andrew Hunt",
    //         "isbn" => "9780201616224",
    //         "price" => "37.50"
    //     ];

    //     $response = $this->captureOutput(function() {
    //         addBook($this->db);
    //     });

    //     $expectedJson = json_encode([
    //         "message" => "Book added"
    //     ]);

    //     $actual = json_decode($response, true);

    //     foreach ($actual as &$book) {
    //         unset($book['created_at']);
    //     }

    //     $this->assertJsonStringEqualsJsonString($expectedJson, json_encode($actual));
    // }

    
    // public function testUpdateBook() {
    //     $_POST['title'] = 'Updated Clean Code';
    //     $_POST['author'] = 'Robert C. Martin';
    //     $_POST['isbn'] = '9780132350884';
    //     $_POST['price'] = '35.99';

    //     $response = $this->captureOutput(function() {
    //         updateBook(1, $this->db);
    //     });

    //     $expectedJson = json_encode([
    //         "id" => 1,
    //         "title" => "Updated Clean Code",
    //         "author" => "Robert C. Martin",
    //         "isbn" => "9780132350884",
    //         "price" => "35.99"
    //     ]);

    //     $actual = json_decode($response, true);

    //     foreach ($actual as &$book) {
    //         unset($book['created_at']);
    //     }

    //     $this->assertJsonStringEqualsJsonString($expectedJson, json_encode($actual));
    // }
    
    public function testDeleteBook() {
        $response = $this->captureOutput(function() {
            deleteBook(1, $this->db);
        });

        $this->assertEquals('{"message":"Book deleted"}', $response);

        $response = $this->captureOutput(function() {
            getBook(1, $this->db);
        });

        $this->assertEquals('{"error":"Book not found"}', $response);
    }

}

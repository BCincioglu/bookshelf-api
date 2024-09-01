<?php
use PHPUnit\Framework\TestCase;

require_once 'conf/db.php';
require_once 'controller/bookController.php';

class BookControllerTest extends TestCase
{
    private $db;

    protected function setUp(): void {
        ob_start(); // Output buffering'i başlatıyoruz.

        // Test için Database sınıfını test modunda başlatıyoruz.
        $this->db = new Database(true);

        // Test veritabanını temizleyip test verilerini ekliyoruz.
        $pdo = $this->db->getConnection();
        $pdo->exec("TRUNCATE TABLE books");

        $pdo->exec("INSERT INTO books (id, title, author, isbn, price, created_at) VALUES
            (1, 'Clean Code', 'Robert C. Martin', '9780132350884', '32.99', '2024-08-31 14:13:35'),
            (2, 'The Pragmatic Programmer', 'Andrew Hunt', '9780201616224', '37.50', '2024-08-31 14:13:35')");
    }

    protected function tearDown(): void {
        ob_end_clean(); // Output buffering'i bitiriyoruz ve temizliyoruz.
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

        // created_at alanını her bir kitaptan kaldır
        foreach ($actual as &$book) {
            unset($book['created_at']);
        }

        $this->assertEquals($expectedJson, $actual);
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

        // created_at alanını kaldır
        unset($actual['created_at']);
    
        $this->assertEquals($expectedJson, $actual);
    }

    public function testAddBook() {
        $_POST['title'] = 'Test Driven Development';
        $_POST['author'] = 'Kent Beck';
        $_POST['isbn'] = '9780321146533';
        $_POST['price'] = '29.99';

        $response = $this->captureOutput(function() {
            addBook($this->db);
        });

        $expectedJson = json_encode([
            "id" => 3, // Bu ID, test veritabanındaki mevcut son ID'ye göre belirlenir.
            "title" => "Test Driven Development",
            "author" => "Kent Beck",
            "isbn" => "9780321146533",
            "price" => "29.99",
            "created_at" => date('Y-m-d H:i:s')
        ]);

        $this->assertJsonStringEqualsJsonString($expectedJson, $response);
    }

    public function testUpdateBook() {
        $_POST['title'] = 'Updated Clean Code';
        $_POST['author'] = 'Robert C. Martin';
        $_POST['isbn'] = '9780132350884';
        $_POST['price'] = '35.99';

        $response = $this->captureOutput(function() {
            updateBook(1, $this->db);
        });

        $expectedJson = json_encode([
            "id" => 1,
            "title" => "Updated Clean Code",
            "author" => "Robert C. Martin",
            "isbn" => "9780132350884",
            "price" => "35.99",
            "created_at" => date('Y-m-d H:i:s') // Orijinal oluşturulma tarihi
        ]);

        $this->assertJsonStringEqualsJsonString($expectedJson, $response);
    }

    public function testDeleteBook() {
        $response = $this->captureOutput(function() {
            deleteBook(1, $this->db);
        });

        $this->assertEquals('{"message":"Book deleted"}', $response);


        // Silindiğini doğrulamak için aynı ID ile tekrar dene
        $response = $this->captureOutput(function() {
            getBook(1, $this->db);
        });

        $this->assertEquals('{"error":"Book not found"}', $response);
    }
}

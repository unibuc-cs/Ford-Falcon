<?php
use PHPUnit\Framework\TestCase;

class LoginTest extends TestCase {
    private $conn;

    protected function setUp(): void {
        putenv('IS_TESTING=true');

        $this->conn = new mysqli("localhost", "root", "", "test");
        
        // Inserare utilizator cu parola criptată
        $hashedPassword = password_hash('testpassword', PASSWORD_DEFAULT);
        $sql = "INSERT INTO user (id, email, password, username)
                VALUES (1 ,
                'testuser@example.com', 
                        '$hashedPassword', 
                        'testuser')";
        
        // if ($this->conn->query($sql) === TRUE) {
        //     error_log("Test user created successfully!");
        // } else {
        //     error_log("Error: " . $this->conn->error);
        // }
    }

    /**
     * @runInSeparateProcess
     */
    public function testSuccessfulLogin() {
        $_POST['username'] = 'testuser';
        $_POST['password'] = 'testpassword';
        $_SERVER['REQUEST_METHOD'] = 'POST';

        // Disable redirection in the test
        ob_start();
        include __DIR__ . '/../interfata/loginh.php';
        ob_end_clean();

        // Ensure the session has been set up properly
        $this->assertTrue(isset($_SESSION['loggedin']) && $_SESSION['loggedin']);
        $this->assertEquals('testuser', $_SESSION['username']);
    }

    protected function tearDown(): void {
        // Clean up the database and close the connection
        $this->conn->query("DELETE FROM user");
        $this->conn->close();
    }
}

?>

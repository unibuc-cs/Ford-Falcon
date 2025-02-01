<?php
use PHPUnit\Framework\TestCase;

class CalendarTest extends TestCase {
    private $conn;

    protected function setUp(): void {
        putenv('IS_TESTING=true');

        $this->conn = new mysqli("localhost", "root", "", "test");

        $this->conn->query("DELETE FROM userincalendar");
        $this->conn->query("DELETE FROM calendar");
        $this->conn->query("DELETE FROM user");
        
        $hashedPassword = password_hash('testpassword', PASSWORD_DEFAULT);
        $sql = "INSERT INTO user (id, email, password, username)
                VALUES (1 ,
                'testuser@example.com', 
                        '$hashedPassword', 
                        'testuser');
                INSERT INTO calendar (id, adminId, name, code) 
                VALUES (1, 1, 'testcalendar', 'testcode');
                INSERT INTO userincalendar (id, userId, calendarId) 
                VALUES (1, 1, 1);";
        
        if ($this->conn->multi_query($sql)) {
            while ($this->conn->more_results() && $this->conn->next_result()) { }
        } else {
            $this->fail("SQL Error: " . $this->conn->error);
        }
    }

    /**
     * @runInSeparateProcess
     */
    public function testCalendarInHomepage() {
        ob_start();
        include __DIR__ . '/../interfata/homepage.php';
        $output = ob_get_clean();

        $this->assertTrue(isset($_SESSION['loggedin']) && $_SESSION['loggedin']);
        $this->assertStringContainsString("<a href='calendar.php?calendar_id=1'>", $output);
    }

    protected function tearDown(): void {
        $this->conn->close();
    }
}

?>

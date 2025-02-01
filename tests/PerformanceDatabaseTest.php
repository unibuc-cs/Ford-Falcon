<?php
use PHPUnit\Framework\TestCase;

class PerformanceDatabaseTest extends TestCase
{
    private $conn;

    protected function setUp(): void
    {
        $this->conn = new mysqli("localhost", "root", "", "test");

        if ($this->conn->connect_error) {
            die("Eroare la conectare: " . $this->conn->connect_error);
        }
    }

    protected function tearDown(): void
    {
        if ($this->conn) {
            $this->conn->close();
        }
    }

    public function testDatabasePerformance()
    {
        $startTime = microtime(true);
        $sql = "SELECT * FROM user";
        $result = $this->conn->query($sql);
        $endTime = microtime(true);
        $queryTime = $endTime - $startTime;

        $this->assertLessThan(0.1, $queryTime);

        $this->assertTrue($result->num_rows > 0); 

    }
}
?>

<?php
//design pattern: sigleton pattern
use PHPUnit\Framework\TestCase;

class PerformanceDatabaseTest extends TestCase
{
    private $conn;

    protected function setUp(): void
    {
        // Conectare la baza de date
        $this->conn = new mysqli("localhost", "root", "", "proiect_mds");

        if ($this->conn->connect_error) {
            die("Eroare la conectare: " . $this->conn->connect_error);
        }
    }

    protected function tearDown(): void
    {
        // Închidere conexiune la baza de date
        if ($this->conn) {
            $this->conn->close();
        }
    }

    public function testDatabasePerformance()
    {
        // Măsurarea timpului pentru o interogare simplă
        $startTime = microtime(true);
        $sql = "SELECT * FROM user";
        $result = $this->conn->query($sql);
        $endTime = microtime(true);
        $queryTime = $endTime - $startTime;

        // Assert pentru timpul de execuție
        $this->assertLessThan(0.1, $queryTime); // Timpul ar trebui să fie mai mic de 0.1 secunde

        // Assert pentru rezultatele interogării
        $this->assertTrue($result->num_rows > 0); // Verificăm că avem cel puțin un rând în rezultate

        // În funcție de specificul aplicației tale, poți adăuga și alte aserțiuni relevante
    }
}
?>

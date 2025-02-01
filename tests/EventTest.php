<?php
use PHPUnit\Framework\TestCase;

class EventTest extends TestCase {
    private $conn;

    protected function setUp(): void {
        putenv('IS_TESTING=true');

        $this->conn = new mysqli("localhost", "root", "", "test");

        $sql = "INSERT INTO event ( calendarId, date, time, location, description, type, title)
                VALUES (1, '2025-02-01', '10:00:00', 'Online', 'Test event description', 'meeting', 'Test Event')";

        if (!$this->conn->query($sql)) {
            $this->fail("Failed to insert test event: " . $this->conn->error);
        }
    }


    public function testEventInsertion() {
        $result = $this->conn->query("SELECT * FROM event WHERE title = 'Test Event'");

        $this->assertEquals(1, $result->num_rows, "Event should exist in the database.");

        $event = $result->fetch_assoc();

        $this->assertEquals('2025-02-01', $event['date'], "Event date should match.");
        $this->assertEquals('10:00:00', $event['time'], "Event time should match.");
        $this->assertEquals('Online', $event['location'], "Event location should match.");
        $this->assertEquals('Test event description', $event['description'], "Event description should match.");
        $this->assertEquals('meeting', $event['type'], "Event type should match.");
        $this->assertEquals('Test Event', $event['title'], "Event title should match.");
    }

    protected function tearDown(): void {
        $this->conn->query("DELETE FROM event");
        $this->conn->close();
    }
}
?>

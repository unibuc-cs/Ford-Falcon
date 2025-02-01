<?php
use PHPUnit\Framework\TestCase;

class EventTest extends TestCase {
    private $conn;

    // Set up the database connection and prepare test data
    protected function setUp(): void {
        putenv('IS_TESTING=true'); // Optional, set environment variable for testing

        // Create a connection to the database
        $this->conn = new mysqli("localhost", "root", "", "test");

        // Insert a test event
        $sql = "INSERT INTO event ( calendarId, date, time, location, description, type, title)
                VALUES (1, '2025-02-01', '10:00:00', 'Online', 'Test event description', 'meeting', 'Test Event')";

        if (!$this->conn->query($sql)) {
            $this->fail("Failed to insert test event: " . $this->conn->error);
        }
    }

    /**
     * Test that the event was inserted successfully
     */
    public function testEventInsertion() {
        // Query the database to check if the event exists
        $result = $this->conn->query("SELECT * FROM event WHERE title = 'Test Event'");

        // Check that the event is inserted correctly
        $this->assertEquals(1, $result->num_rows, "Event should exist in the database.");

        // Fetch the event data
        $event = $result->fetch_assoc();

        // Check that the event details match the inserted values
        $this->assertEquals('2025-02-01', $event['date'], "Event date should match.");
        $this->assertEquals('10:00:00', $event['time'], "Event time should match.");
        $this->assertEquals('Online', $event['location'], "Event location should match.");
        $this->assertEquals('Test event description', $event['description'], "Event description should match.");
        $this->assertEquals('meeting', $event['type'], "Event type should match.");
        $this->assertEquals('Test Event', $event['title'], "Event title should match.");
    }

    protected function tearDown(): void {
        // Clean up the database and close the connection
        $this->conn->query("DELETE FROM event");
        $this->conn->close();
    }
}
?>

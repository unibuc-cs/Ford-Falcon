<?php
use PHPUnit\Framework\TestCase;

class CommentTest extends TestCase {
    private $conn;

    // Set up the database connection and prepare test data
    protected function setUp(): void {
        putenv('IS_TESTING=true'); // Optional, set environment variable for testing

        // Create a connection to the database
        $this->conn = new mysqli("localhost", "root", "", "test");

        // Insert a test comment
        $sql = "INSERT INTO comments (calendar_id, user_id, comment, created_at)
                VALUES (1, 1, 'This is a test comment', NOW())";

        if (!$this->conn->query($sql)) {
            $this->fail("Failed to insert test comment: " . $this->conn->error);
        }
    }

    /**
     * Test that the comment was inserted successfully
     */
    public function testCommentInsertion() {
        // Query the database to check if the comment exists
        $result = $this->conn->query("SELECT * FROM comments WHERE comment = 'This is a test comment'");

        // Check that the comment is inserted correctly
        $this->assertEquals(1, $result->num_rows, "Comment should exist in the database.");

        // Fetch the comment data
        $comment = $result->fetch_assoc();

        // Check that the comment details match the inserted values
        $this->assertEquals(1, $comment['calendar_id'], "Comment calendar_id should match.");
        $this->assertEquals(1, $comment['user_id'], "Comment user_id should match.");
        $this->assertEquals('This is a test comment', $comment['comment'], "Comment content should match.");
        $this->assertNotEmpty($comment['created_at'], "Created_at should not be empty.");
    }

    // Clean up after the test
    protected function tearDown(): void {
        // Clean up the database and close the connection
        $this->conn->query("DELETE FROM comments");
        $this->conn->close();
    }
}
?>

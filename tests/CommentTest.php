<?php
use PHPUnit\Framework\TestCase;

class CommentTest extends TestCase {
    private $conn;

    protected function setUp(): void {
        putenv('IS_TESTING=true'); 

        $this->conn = new mysqli("localhost", "root", "", "test");

        $sql = "INSERT INTO comments (calendar_id, user_id, comment, created_at)
                VALUES (1, 1, 'This is a test comment', NOW())";

        if (!$this->conn->query($sql)) {
            $this->fail("Failed to insert test comment: " . $this->conn->error);
        }
    }

    public function testCommentInsertion() {
        $result = $this->conn->query("SELECT * FROM comments WHERE comment = 'This is a test comment'");

        $this->assertEquals(1, $result->num_rows, "Comment should exist in the database.");

        $comment = $result->fetch_assoc();

        $this->assertEquals(1, $comment['calendar_id'], "Comment calendar_id should match.");
        $this->assertEquals(1, $comment['user_id'], "Comment user_id should match.");
        $this->assertEquals('This is a test comment', $comment['comment'], "Comment content should match.");
        $this->assertNotEmpty($comment['created_at'], "Created_at should not be empty.");
    }

    protected function tearDown(): void {
        $this->conn->query("DELETE FROM comments");
        $this->conn->close();
    }
}
?>

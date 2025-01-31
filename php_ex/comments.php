<?php
include '../app/db.php';

if (!isset($_GET['calendar_id']) || !is_numeric($_GET['calendar_id'])) {
    die("ID-ul calendarului este invalid.");
}

$calendar_id = $_GET['calendar_id'];

// Interogare pregătită pentru a preveni injecțiile SQL
$stmt = $conn->prepare("SELECT c.*, u.username FROM comments c JOIN user u ON c.user_id = u.id WHERE calendar_id = ?");
$stmt->bind_param("i", $calendar_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $created_at = htmlspecialchars($row['created_at']);
        $username = htmlspecialchars($row['username']);
        $comment = htmlspecialchars($row['comment']);

        echo "<hr><p style='opacity:0.5;'>$created_at</p>";
        echo "<p><strong>$username</strong>: $comment</p>";
    }
} else {
    echo "<p>Nu există comentarii.</p>";
}

// Închide conexiunea și resursele utilizate
$stmt->close();
$conn->close();
?>

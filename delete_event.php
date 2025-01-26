<?php
include 'db.php';

// Pornește sesiunea
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eventId']) && isset($_POST['userId'])) {
    $eventId = $_POST['eventId'];
    $userId = $_POST['userId'];

    // Verifică dacă utilizatorul este adminul calendarului
    $stmt = $conn->prepare("
        SELECT c.adminId 
        FROM calendar c 
        JOIN event e ON c.id = e.calendarId 
        WHERE e.id = ? AND c.adminId = ?
    ");
    $stmt->bind_param("ii", $eventId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Utilizatorul este admin, șterge mai întâi rândurile dependente din userinevent
        $stmt = $conn->prepare("DELETE FROM userinevent WHERE eventId = ?");
        $stmt->bind_param("i", $eventId);
        if (!$stmt->execute()) {
            echo "Error deleting dependencies: " . $stmt->error;
            exit;
        }

        // Apoi șterge evenimentul
        $stmt = $conn->prepare("DELETE FROM event WHERE id = ?");
        $stmt->bind_param("i", $eventId);
        if ($stmt->execute()) {
            echo "Event deleted successfully!";
        } else {
            echo "Error deleting event: " . $stmt->error;
        }
    } else {
        echo "You are not authorized to delete this event.";
    }
    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>

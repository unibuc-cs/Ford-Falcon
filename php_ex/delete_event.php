<?php
include '../app/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['eventId']) && !empty($_POST['userId'])) {
    $eventId = filter_var($_POST['eventId'], FILTER_VALIDATE_INT);
    $userId = filter_var($_POST['userId'], FILTER_VALIDATE_INT);

    if (!$eventId || !$userId) {
        echo "Date invalide.";
        exit();
    }

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

        $stmt = $conn->prepare("DELETE FROM userinevent WHERE eventId = ?");
        $stmt->bind_param("i", $eventId);
        if (!$stmt->execute()) {
            echo "Eroare la ștergerea dependențelor: " . htmlspecialchars($stmt->error);
            exit();
        }

        $stmt = $conn->prepare("DELETE FROM event WHERE id = ?");
        $stmt->bind_param("i", $eventId);
        if ($stmt->execute()) {
            echo "Evenimentul a fost șters cu succes.";
        } else {
            echo "Eroare la ștergerea evenimentului: " . htmlspecialchars($stmt->error);
        }
    } else {
        echo "Nu aveți autorizație pentru a șterge acest eveniment.";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Cerere invalidă.";
}
?>
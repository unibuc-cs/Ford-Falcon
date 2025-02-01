<?php
include '../app/db.php';

session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["userId"]) && isset($_POST["eventId"]) && isset($_POST["availabilityStatus"])) {
    $userId = $_POST['userId'];
    $eventId = $_POST['eventId'];
    $availabilityStatus = $_POST['availabilityStatus'];

    error_log("userId: $userId, eventId: $eventId, availabilityStatus: $availabilityStatus");

    $stmt = $conn->prepare("SELECT * FROM userinevent WHERE userId = ? AND eventId = ?");
    $stmt->bind_param("ii", $userId, $eventId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $stmt = $conn->prepare("UPDATE userinevent SET displonibility = ? WHERE userId = ? AND eventId = ?");
        $stmt->bind_param("sii", $availabilityStatus, $userId, $eventId);
    } else {
        $stmt = $conn->prepare("INSERT INTO userinevent (userId, eventId, displonibility) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $userId, $eventId, $availabilityStatus);
    }

    if ($stmt->execute()) {
        echo "Availability submitted successfully!";
    } else {
        echo "Error executing query: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request method or missing parameters.";
}
?>
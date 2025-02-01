<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../app/db.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_name'])) {
    die("Utilizatorul nu este autentificat.");
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST["calendar_id"]) && !empty($_POST["comentariu"])) {
    $calendarId = trim($_POST['calendar_id']);
    $comment = trim($_POST['comentariu']);
    $created_at = date('Y-m-d H:i:s');

    if (!is_numeric($calendarId) || empty($comment)) {
        die("Datele introduse sunt invalide.");
    }

    $stmt = $conn->prepare("INSERT INTO comments (calendar_id, user_id, comment, created_at) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $calendarId, $user_id, $comment, $created_at);

    if ($stmt->execute()) {
        header("Location: ../interfata/calendar.php?calendar_id=$calendarId");
        exit();
    } else {
        echo "Eroare la adăugarea comentariului: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Date insuficiente pentru adăugarea comentariului.";
}

$conn->close();
?>
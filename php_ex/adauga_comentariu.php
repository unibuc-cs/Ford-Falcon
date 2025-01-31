<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include fișierul de conexiune la baza de date
include '../app/db.php';

// Verifică dacă utilizatorul este autentificat
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_name'])) {
    die("Utilizatorul nu este autentificat.");
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Verifică dacă a fost trimisă cererea POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST["calendar_id"]) && !empty($_POST["comentariu"])) {
    $calendarId = trim($_POST['calendar_id']);
    $comment = trim($_POST['comentariu']);
    $created_at = date('Y-m-d H:i:s');

    // Verificare suplimentară pentru datele introduse
    if (!is_numeric($calendarId) || empty($comment)) {
        die("Datele introduse sunt invalide.");
    }

    // Folosește prepared statements pentru a preveni injecțiile SQL
    $stmt = $conn->prepare("INSERT INTO comments (calendar_id, user_id, comment, created_at) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $calendarId, $user_id, $comment, $created_at);

    if ($stmt->execute()) {
        // Redirecționează înapoi la calendar după inserarea comentariului
        header("Location: ../interfata/calendar.php?calendar_id=$calendarId");
        exit();
    } else {
        echo "Eroare la adăugarea comentariului: " . $stmt->error;
    }

    // Închide statement-ul pregătit
    $stmt->close();
} else {
    echo "Date insuficiente pentru adăugarea comentariului.";
}

// Închide conexiunea la baza de date
$conn->close();
?>
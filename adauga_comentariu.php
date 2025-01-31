<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include fișierul de conexiune la baza de date
include 'db.php';

// Verifică dacă datele de sesiune sunt setate
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_name'])) {
    echo "Utilizatorul nu este autentificat.";
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Verifică dacă a fost trimisă cererea POST cu datele necesare
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["calendar_id"]) && isset($_POST["comentariu"])) {
    $calendarId = $_POST['calendar_id'];
    $comment = $_POST['comentariu'];

    // Escapare pentru a preveni injecțiile SQL
    $calendarId = mysqli_real_escape_string($conn, $calendarId);
    $comment = mysqli_real_escape_string($conn, $comment);
    $created_at = date('Y-m-d H:i:s');

    // Query pentru a insera comentariul în baza de date
    $sql = "INSERT INTO comments (calendar_id, user_id, comment, created_at) VALUES ('$calendarId', '$user_id', '$comment', '$created_at')";

    if (mysqli_query($conn, $sql)) {
        header("Location: calendar.php?calendar_id=$calendarId");
        exit();
    } else {
        echo "Eroare la adăugarea comentariului: " . mysqli_error($conn);
    }
} else {
    echo "Date insuficiente pentru adăugarea comentariului.";
}

// Închide conexiunea la baza de date la finalul scriptului
mysqli_close($conn);
?>

<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include '../app/db.php'; // Include fișierul pentru conexiunea la baza de date

// Verifică dacă utilizatorul este autentificat
if (!isset($_SESSION['id'])) {
    die("Eroare: Nu ești autentificat.");
}

$user_id = $_SESSION['id'];

// Pregătește interogarea folosind prepared statements pentru securitate
$stmt = $conn->prepare("
    SELECT u.username AS user_name, c.name AS calendar_name 
    FROM userincalendar uc 
    JOIN calendar c ON c.id = uc.calendarId 
    JOIN user u ON u.id = uc.userId 
    WHERE uc.userId = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Verifică dacă s-au găsit calendare
if ($result && $result->num_rows > 0) {
    echo "<h2>Calendarele la care ești implicat:</h2>";
    echo "<ul>";
    while ($row = $result->fetch_assoc()) {
        echo "<li><p>" . htmlspecialchars($row['user_name']) . " - " . htmlspecialchars($row['calendar_name']) . "</p></li>";
    }
    echo "</ul>";
} else {
    echo "<p>Nu ești implicat în niciun calendar.</p>";
}

// Închide resursele utilizate
$stmt->close();
$conn->close();
?>
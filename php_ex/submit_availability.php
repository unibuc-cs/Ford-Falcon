<?php
include '../app/db.php';
session_start();

// Verifică cererea POST și validează parametrii
$userId = filter_input(INPUT_POST, 'userId', FILTER_VALIDATE_INT);
$eventId = filter_input(INPUT_POST, 'eventId', FILTER_VALIDATE_INT);
$availabilityStatus = filter_input(INPUT_POST, 'availabilityStatus', FILTER_SANITIZE_STRING);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $userId && $eventId && $availabilityStatus) {

    // Verifică dacă utilizatorul este deja asociat cu evenimentul
    $stmt = $conn->prepare("SELECT * FROM userinevent WHERE userId = ? AND eventId = ?");
    $stmt->bind_param("ii", $userId, $eventId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Actualizează disponibilitatea dacă există deja o intrare
        $stmt = $conn->prepare("UPDATE userinevent SET availability = ? WHERE userId = ? AND eventId = ?");
        $stmt->bind_param("sii", $availabilityStatus, $userId, $eventId);
    } else {
        // Inserează o nouă intrare în tabel
        $stmt = $conn->prepare("INSERT INTO userinevent (userId, eventId, availability) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $userId, $eventId, $availabilityStatus);
    }

    if ($stmt->execute()) {
        echo "Disponibilitatea a fost setată cu succes!";
    } else {
        // Mesaj de eroare prietenos
        echo "Eroare la actualizarea disponibilității. Încercați din nou.";
    }

    // Închide statement-ul și conexiunea la baza de date
    $stmt->close();
    $conn->close();
} else {
    echo "Cerere invalidă sau parametri lipsă.";
}
?>
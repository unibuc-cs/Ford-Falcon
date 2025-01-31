<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verifică autentificarea utilizatorului
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: loginh.php");
    exit();
}

include '../app/db.php'; // Conectare la baza de date

// Verifică cererea POST
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['friend_username'])) {
    $friend_username = trim($_POST['friend_username']);
    $username = $_SESSION['username'];

    // Verifică dacă utilizatorul încearcă să-și trimită cerere de prietenie
    if ($friend_username === $username) {
        $error_message = "Nu poți trimite o cerere de prietenie către tine însuți.";
    } else {
        // Obține ID-urile celor doi utilizatori folosind o interogare pregătită
        $stmt = $conn->prepare("SELECT id FROM user WHERE username = ?");
        $stmt->bind_param("s", $friend_username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $friend_id = $result->fetch_assoc()['id'];

            // Obține ID-ul utilizatorului curent
            $stmt->prepare("SELECT id FROM user WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $user_id = $stmt->get_result()->fetch_assoc()['id'];

            // Verifică dacă există deja o prietenie sau o cerere de prietenie
            $stmt = $conn->prepare("SELECT 1 FROM friendship WHERE (userId1 = ? AND userId2 = ?) OR (userId1 = ? AND userId2 = ?)");
            $stmt->bind_param("iiii", $user_id, $friend_id, $friend_id, $user_id);
            $stmt->execute();
            if ($stmt->get_result()->num_rows > 0) {
                $error_message = "Voi sunteți deja prieteni.";
            } else {
                $stmt = $conn->prepare("SELECT 1 FROM friend_requests WHERE sender = ? AND receiver = ?");
                $stmt->bind_param("ss", $username, $friend_username);
                $stmt->execute();

                if ($stmt->get_result()->num_rows > 0) {
                    $error_message = "Cererea de prietenie a fost deja trimisă.";
                } else {
                    // Trimite cererea de prietenie
                    $stmt = $conn->prepare("INSERT INTO friend_requests (sender, receiver) VALUES (?, ?)");
                    $stmt->bind_param("ss", $username, $friend_username);
                    if ($stmt->execute()) {
                        echo '<script>alert("Cerere de prietenie trimisă!"); window.location.href = "friends.php";</script>';
                        exit();
                    } else {
                        $error_message = "Eroare la trimiterea cererii de prietenie.";
                    }
                }
            }
        } else {
            $error_message = "Utilizatorul nu a fost găsit.";
        }

        $stmt->close(); // Închide declarația pregătită
    }
}
$conn->close(); // Închide conexiunea la baza de date
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adaugă Prieten</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
            display: grid;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .form-container {
            width: 300px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .form-container input {
            width: calc(100% - 20px);
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .form-container button {
            width: 100%;
            padding: 10px;
            background-color: pink;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .form-container button:hover {
            background-color: #0056b3;
        }

        .error-message {
            color: red;
            text-align: center;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="form-container">
        <?php if (isset($error_message)) : ?>
            <div class="error-message"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>
        <form method="post">
            <label for="friend_username">Nume Utilizator Prieten:</label>
            <input type="text" id="friend_username" name="friend_username" required>
            <button type="submit">Adaugă Prieten</button>
        </form>
    </div>
</body>
</html>
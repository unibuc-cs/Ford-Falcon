<?php
session_start(); // Începe sesiunea pentru a gestiona starea de autentificare a utilizatorilor

// Verifică dacă utilizatorul nu este autentificat și îl redirecționează către pagina de login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: loginh.php");
    exit();
}

// Setează variabila de sesiune pentru afișarea butonului de navigare înapoi (nu este utilizată în acest cod)
$_SESSION['show_back_button'] = false;

// Funcție pentru generarea unui cod aleator
function generateRandomCode($length = 6) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomCode = '';

    for ($i = 0; $i < $length; $i++) {
        $randomCode .= $characters[rand(0, $charactersLength - 1)];
    }

    return $randomCode;
}

// Generare cod aleator pentru calendar
$randomCode = generateRandomCode(6);

// Verifică dacă s-a trimis o cerere POST și dacă există o sesiune pentru ID-ul utilizatorului
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['id'])) {
    include 'db.php'; // Include fișierul de conectare la baza de date

    // Curăță și preia numele calendarului din formular
    $name = trim($_POST['name']);
    $adminId = $_SESSION['id']; // ID-ul utilizatorului care creează calendarul

    // Inserează calendarul în tabelul 'calendar' din baza de date
    $stmt = $conn->prepare("INSERT INTO calendar (name, adminId, code) VALUES (?, ?, ?)");
    $stmt->bind_param("sis", $name, $adminId, $randomCode);

    if ($stmt->execute()) {
        $calendarId = $stmt->insert_id; // Obține ID-ul calendarului nou creat
        $stmt->close(); // Închide declarația pregătită

        // Inserează utilizatorul în calendarul nou creat
        $stmt = $conn->prepare("INSERT INTO userInCalendar (userId, calendarId) VALUES (?, ?)");
        $stmt->bind_param("ii", $adminId, $calendarId);
        $stmt->execute();

        $message = "Calendar creat cu succes!";
        echo '<script>alert("' . $message . '"); window.location.href = "homepage.php";</script>';
        exit(); // Asigură oprirea scriptului după redirecționare
    } else {
        echo "<p>Eroare: nu s-a putut crea calendarul.</p>";
    }

    mysqli_close($conn); // Închide conexiunea cu baza de date
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Calendar</title>
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
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .inapoi {
            position: absolute;
            top: 10px;
            left: 10px;
            padding: 10px;
            background-color: white;
            color: pink;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
    </style>
</head>

<body>
    <?php include 'header.php'; // Include bara de navigare sau header-ul ?>
    <button onclick="window.location.href='homepage.php'" class="inapoi">Înapoi</button>
    <div class="form-container">
        <form method="post">
            <label for="name">Nume Calendar:</label>
            <input type="text" id="name" name="name" required>
            <button type="submit" style="background-color:pink">Crează Calendar</button>
        </form>
    </div>
</body>
</html>
<?php
session_start(); // Începe sesiunea pentru a gestiona starea de autentificare a utilizatorilor

// Verifică dacă utilizatorul nu este autentificat și îl redirecționează către pagina de login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: loginh.php");
    exit();
}

// Verifică dacă s-a trimis o cerere POST și dacă există o sesiune pentru numele de utilizator
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['username'])) {
    include 'db.php'; // Include fișierul de conectare la baza de date

    // Curăță și preia numele de utilizator al prietenului din formular
    $friend_username = trim($_POST['friend_username']);
    $username = $_SESSION['username'];

    // Verifică dacă utilizatorul încearcă să trimită o cerere de prietenie către propriul său cont
    if ($friend_username == $username) {
        $error_message = "Nu poți trimite o cerere de prietenie către tine însuți.";
    } else {
        // Interogare pentru a verifica dacă există un utilizator cu numele de utilizator introdus
        $stmt = $conn->prepare("SELECT * FROM user WHERE username = ?");
        $stmt->bind_param("s", $friend_username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Obține ID-urile utilizatorilor implicati
            $friend_row = $result->fetch_assoc();
            $friend_id = $friend_row['id'];

            $stmt = $conn->prepare("SELECT id FROM user WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $user_result = $stmt->get_result();
            $user_row = $user_result->fetch_assoc();
            $user_id = $user_row['id'];

            // Verifică dacă există deja o prietenie între cei doi utilizatori
            $stmt = $conn->prepare("SELECT * FROM friendship WHERE (userId1 = ? AND userId2 = ?) OR (userId1 = ? AND userId2 = ?)");
            $stmt->bind_param("iiii", $user_id, $friend_id, $friend_id, $user_id);
            $stmt->execute();
            $friendship_result = $stmt->get_result();

            if ($friendship_result->num_rows == 0) {
                // Verifică dacă cererea de prietenie a mai fost deja trimisă
                $stmt = $conn->prepare("SELECT * FROM friend_requests WHERE sender = ? AND receiver = ?");
                $stmt->bind_param("ss", $username, $friend_username);
                $stmt->execute();
                $request_result = $stmt->get_result();

                if ($request_result->num_rows == 0) {
                    // Trimite cererea de prietenie
                    $stmt = $conn->prepare("INSERT INTO friend_requests (sender, receiver) VALUES (?, ?)");
                    $stmt->bind_param("ss", $username, $friend_username);
                    if ($stmt->execute()) {
                        $message = "Cerere de prietenie trimisă!";
                        echo '<script>alert("' . $message . '"); window.location.href = "friends.php";</script>';
                        exit(); // Asigură oprirea scriptului după redirecționare
                    } else {
                        $error_message = "Eroare: nu s-a putut trimite cererea de prietenie.";
                    }
                } else {
                    $error_message = "Cererea de prietenie a fost deja trimisă.";
                }
            } else {
                $error_message = "Voi sunteți deja prieteni.";
            }
        } else {
            $error_message = "Utilizatorul nu a fost găsit.";
        }

        $stmt->close(); // Închide declarația pregătită
    }

    mysqli_close($conn); // Închide conexiunea cu baza de date
}
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
    <?php
    include 'header.php'; // Include bara de navigare sau header-ul
    ?>
    <div class="form-container">
        <?php
        if (isset($error_message)) {
            echo '<div class="error-message">' . htmlspecialchars($error_message) . '</div>';
        }
        ?>
        <form method="post">
            <label for="friend_username">Nume Utilizator Prieten:</label>
            <input type="text" id="friend_username" name="friend_username" required>
            <button type="submit">Adaugă Prieten</button>
        </form>
    </div>
</body>

</html>
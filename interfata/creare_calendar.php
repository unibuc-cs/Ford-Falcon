<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: loginh.php");
    exit();
}

$_SESSION['show_back_button'] = false;

function generateRandomCode($length = 6) {
    return substr(bin2hex(random_bytes($length)), 0, $length);
}

$randomCode = generateRandomCode(6);

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_SESSION['id'])) {
    include '../app/db.php';

    $name = trim($_POST['name']);
    $adminId = $_SESSION['id'];

    if (!empty($name)) {
        $stmt = $conn->prepare("INSERT INTO calendar (name, adminId, code) VALUES (?, ?, ?)");
        $stmt->bind_param("sis", $name, $adminId, $randomCode);

        if ($stmt->execute()) {
            $calendarId = $stmt->insert_id;
            $stmt->close();

            $stmt = $conn->prepare("INSERT INTO userInCalendar (userId, calendarId) VALUES (?, ?)");
            $stmt->bind_param("ii", $adminId, $calendarId);
            if ($stmt->execute()) {
                echo '<script>alert("Calendar creat cu succes!"); window.location.href = "homepage.php";</script>';
                exit();
            } else {
                echo "<p>Eroare: nu s-a putut asocia utilizatorul cu calendarul.</p>";
            }
        } else {
            echo "<p>Eroare: nu s-a putut crea calendarul.</p>";
        }
        $stmt->close();
    } else {
        echo "<p>Eroare: Numele calendarului este invalid.</p>";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crează Calendar</title>
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
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .form-container input {
            width: 100%;
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
        .form-container button:hover {
            background-color: #0056b3;
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
    <?php include 'header.php'; ?>
    <button onclick="window.location.href='homepage.php'" class="inapoi">Înapoi</button>
    <div class="form-container">
        <form method="post">
            <label for="name">Nume Calendar:</label>
            <input type="text" id="name" name="name" required>
            <button type="submit" name="submit_calendar" style="background-color:pink">Crează Calendar</button>
        </form>
    </div>
</body>
</html>

<td data-date="3" data-month="2" data-year="2025" data-month_name="Februarie" class="date-picker"><span>3</span></td>
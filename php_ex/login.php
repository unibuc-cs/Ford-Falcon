<?php
session_start(); 

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header("location: ../interfata/homepage.php");
    exit;
}

$error_message = "Eroare la autentificare.";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    include '../app/db.php';

    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        $stmt = $conn->prepare("SELECT id, username, password FROM user WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $row = $result->fetch_assoc();

            if (password_verify($password, $row['password'])) {
                $_SESSION['loggedin'] = true;
                $_SESSION['id'] = $row['id'];
                $_SESSION['username'] = $row['username'];

                header("location: ../interfata/homepage.php");
                exit;
            } else {
                $error_message = "Numele de utilizator sau parola sunt incorecte.";
            }
        } else {
            $error_message = "Numele de utilizator sau parola sunt incorecte.";
        }

        $stmt->close();
    } else {
        $error_message = "Completați toate câmpurile.";
    }

    $conn->close();
}
?>
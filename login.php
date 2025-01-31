<?php
session_start(); // Începe sesiunea pentru a gestiona starea de autentificare a utilizatorilor

// Redirecționează utilizatorul către homepage.php dacă acesta este deja autentificat
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header("location: homepage.php");
    exit;
}

// Verifică dacă formularul de login a fost trimis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'db.php'; // Include fișierul de conectare la baza de date
    
    // Escapare și curățare date introduse pentru a preveni SQL injection
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Interogare SQL pentru a selecta utilizatorul bazat pe numele de utilizator
    $sql = "SELECT id, username, password FROM user WHERE username = '$username'";
    $result = mysqli_query($conn, $sql); // Execută interogarea în baza de date
    $row = mysqli_fetch_assoc($result); // Extrage rezultatul într-un array asociativ

    // Verifică dacă a fost găsit un singur rând (utilizator) cu numele de utilizator dat
    if (mysqli_num_rows($result) == 1) {
        // Verifică dacă parola introdusă se potrivește cu parola stocată în baza de date (folosind password_verify)
        if (password_verify($password, $row['password'])) {
            // Autentificare reușită, setează variabilele de sesiune
            $_SESSION['loggedin'] = true;
            $_SESSION['id'] = $row['id'];
            $_SESSION['username'] = $username;
            header("location: homepage.php"); // Redirecționează utilizatorul către homepage.php
            exit;
        } else {
            echo "Parolă incorectă."; // Afișează un mesaj de eroare dacă parola introdusă nu este corectă
        }
    } else {
        echo "Nu există niciun cont cu acest nume de utilizator."; // Afișează un mesaj dacă numele de utilizator nu există în baza de date
    }

    mysqli_close($conn); // Închide conexiunea cu baza de date
}
?>
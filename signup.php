<?php

include 'db.php';  // Se include fișierul de conectare la baza de date
session_start();   // Se începe sesiunea pentru utilizarea tokenului CSRF

// Generare token CSRF la prima accesare a paginii sau la fiecare sesiune nouă
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));  // Generează un token CSRF aleatoriu și îl stochează în sesiune
}

// Verifică dacă formularul de înregistrare a fost trimis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificare token CSRF pentru a preveni atacurile CSRF
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        echo "<script>alert('Invalid CSRF token');</script>";  // Dacă tokenul CSRF nu este valid, afișează o alertă și oprește execuția
        exit;
    }

    // Preia datele introduse de utilizator din formular și previne injectarea SQL
    $username = trim($_POST['username']);   // Preia și curăță numele de utilizator
    $password = trim($_POST['password']);   // Preia și curăță parola
    $email = trim($_POST['email']);         // Preia și curăță adresa de email
    $rpassword = trim($_POST['password-r']);  // Preia și curăță rescrierea parolei

    // Validează corectitudinea datelor introduse de utilizator
    if ($password != $rpassword) {
        echo "<script>alert('Parolele nu sunt identice');</script>";  // Afișează o alertă dacă parolele nu coincid
    } elseif (strlen($password) < 4) {
        echo "<script>alert('Parola trebuie sa aiba minim 4 caractere');</script>";  // Afișează o alertă dacă parola este prea scurtă
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Adresa de email nu este validă.');</script>";  // Afișează o alertă dacă adresa de email nu este validă
    } else {
        // Verifică dacă există deja un utilizator cu aceeași adresă de email în baza de date
        $stmt = $conn->prepare("SELECT * FROM user WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "<script>alert('Exista deja un utilizator cu acest email.');</script>";  // Afișează o alertă dacă există deja un utilizator cu aceeași adresă de email
        } else {
            // Verifică dacă există deja un utilizator cu același nume de utilizator în baza de date
            $stmt = $conn->prepare("SELECT * FROM user WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                echo "<script>alert('Acest nume de utilizator este deja folosit.');</script>";  // Afișează o alertă dacă există deja un utilizator cu același nume de utilizator
            } else {
                // Hash parola utilizând funcția password_hash() pentru a o stoca sigur în baza de date
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Inserează utilizatorul nou în baza de date
                $stmt = $conn->prepare("INSERT INTO user (username, password, email) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $username, $hashed_password, $email);
                if ($stmt->execute()) {
                    echo "<script>alert('Cont creat cu succes'); window.location='loginh.php';</script>";  // Afișează o alertă de succes și redirecționează către pagina de login
                } else {
                    echo "<script>alert('Eroare la înregistrare. Încercați din nou mai târziu.');</script>";  // Afișează o alertă în caz de eroare la înregistrare
                }
            }
        }
        $stmt->close();  // Închide declarația preparată pentru a elibera resursele
    }

    mysqli_close($conn);  // Închide conexiunea cu baza de date
}
?>

<!-- Începe structura HTML pentru pagina de înregistrare -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
</head>

<body>
    <?php
    include 'header.php';  // Include antetul (header-ul) paginii dintr-un fișier separat
    ?>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #ffece0;
            margin: 0;
            padding: 0;
            display: grid;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        form {
            background-color: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="password"] {
            width: calc(100% - 20px);
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
    <div>
        <h2>Formular de înregistrare</h2>
        <!-- Formularul de înregistrare cu acțiunea trimisă către pagina curentă -->
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div>
                <label>Nume Utilizator:</label>
                <input type="text" name="username" required>  <!-- Câmp pentru numele de utilizator, obligatoriu -->
            </div>
            <div>
                <label>Parolă:</label>
                <input type="password" name="password" required>  <!-- Câmp pentru parolă, obligatoriu -->
            </div>
            <div>
                <label>Rescrie parola:</label>
                <input type="password" name="password-r" required>  <!-- Câmp pentru rescrierea parolei, obligatoriu -->
            </div>
            <div>
                <label>Email:</label>
                <input type="text" name="email" required>  <!-- Câmp pentru adresa de email, obligatoriu -->
            </div>
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <!-- Câmp ascuns pentru tokenul CSRF -->
            <div>
                <input type="submit" value="Înregistrare">  <!-- Buton de submit pentru trimiterea formularului -->
            </div>
        </form>
    </div>
</body>

</html>
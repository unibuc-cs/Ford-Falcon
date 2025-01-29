<?php
session_start();  // Începe sesiunea pentru utilizarea sesiunilor PHP

// Redirecționează utilizatorul către pagina de start (homepage.php) dacă acesta este deja autentificat
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header("location: homepage.php");
    exit;
}

$error_message = "";

// Verifică dacă formularul de login a fost trimis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'db.php';  // Include fișierul de conectare la baza de date

    $username = trim($_POST['username']);  // Preia și curăță numele de utilizator introdus
    $password = trim($_POST['password']);  // Preia și curăță parola introdusă

    // Preparează declarația SQL pentru a preveni injecția SQL
    $stmt = $conn->prepare("SELECT id, username, password FROM user WHERE username = ?");
    $stmt->bind_param("s", $username);  // Leagă parametrul pentru a evita SQL injection
    $stmt->execute();  // Execută interogarea
    $result = $stmt->get_result();  // Obține rezultatul interogării
    $row = $result->fetch_assoc();  // Extrage rândul rezultat

    // Verifică dacă există un singur rând (utilizator) returnat
    if ($result->num_rows == 1) {
        // Verifică dacă parola introdusă se potrivește cu parola stocată (folosind password_verify)
        if (password_verify($password, $row['password'])) {
            // Autentificare reușită, setează variabilele de sesiune
            $_SESSION['loggedin'] = true;
            $_SESSION['id'] = $row['id'];
            $_SESSION['username'] = $username;
            // Redirecționează către pagina de start (homepage.php)
            header("location: homepage.php");
            exit;
        } else {
             $error_message = "Parolă incorectă.";
        }
    } else {
         $error_message = "Nu există niciun cont cu acest nume de utilizator.";  // Afișează o alertă dacă numele de utilizator nu există în baza de date
    }

    $stmt->close();  // Închide declarația preparată pentru a elibera resursele
    mysqli_close($conn);  // Închide conexiunea cu baza de date
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formular de Login</title>
    <!-- Adaugă CSS Bootstrap -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Stiluri CSS pentru pagina de login */
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

        .container {
            margin-top: 50px;
            animation: zoomIn 0.5s ease;
        }

        @keyframes zoomIn {
            from {
                transform: scale(0);
            }

            to {
                transform: scale(1);
            }
        }

        .signup-link {
            display: block;
            text-align: center;
            margin-top: 20px;
        }

        .cont {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        form {
            background-color: #ffece0;
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

        a {
            text-align: center;
            display: block;
            margin-top: 10px;
            text-decoration: none;
            color: #007bff;
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?> <!-- Include antetul (header-ul) paginii -->
    <div class="cont">
        <img src="logo.png" alt="Logo" style="display: inline-block; height: 50%; position: relative; right:100px; border-radius: 50px;">
        <div class="container">
            <h2>Formular de Login</h2>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label for="username">Nume Utilizator:</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Parolă:</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
				<p id="error-message" style="color: red; text-align: center; margin-top: 10px;">
				<?php echo htmlspecialchars($error_message); ?>
				</p>

                <button type="submit" class="btn btn-primary">Login</button>
            </form>
            <a href="../Ford-Falcon/signup.php" class="signup-link">Înregistrare</a> <!-- Link către pagina de înregistrare -->
        </div>
    </div>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
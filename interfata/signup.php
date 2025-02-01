<?php

include '../app/db.php';  
session_start();   

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));  
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        echo "<script>alert('Invalid CSRF token');</script>";  
        exit;
    }

    $username = trim($_POST['username']);   
    $password = trim($_POST['password']);   
    $email = trim($_POST['email']);         
    $rpassword = trim($_POST['password-r']);  

    if ($password != $rpassword) {
        echo "<script>alert('Parolele nu sunt identice');</script>";  
    } elseif (strlen($password) < 4) {
        echo "<script>alert('Parola trebuie sa aiba minim 4 caractere');</script>";  
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Adresa de email nu este validă.');</script>";  
    } else {
        $stmt = $conn->prepare("SELECT * FROM user WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "<script>alert('Exista deja un utilizator cu acest email.');</script>";  
        } else {
            $stmt = $conn->prepare("SELECT * FROM user WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                echo "<script>alert('Acest nume de utilizator este deja folosit.');</script>";  
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $conn->prepare("INSERT INTO user (username, password, email) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $username, $hashed_password, $email);
                if ($stmt->execute()) {
                    echo "<script>alert('Cont creat cu succes'); window.location='loginh.php';</script>";  
                } else {
                    echo "<script>alert('Eroare la înregistrare. Încercați din nou mai târziu.');</script>";  
                }
            }
        }
        $stmt->close();  
    }

    mysqli_close($conn);  
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
</head>

<body>
    <?php
    include 'header.php';  
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
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div>
                <label>Nume Utilizator:</label>
                <input type="text" name="username" required>  
            </div>
            <div>
                <label>Parolă:</label>
                <input type="password" name="password" required>  
            </div>
            <div>
                <label>Rescrie parola:</label>
                <input type="password" name="password-r" required>  
            </div>
            <div>
                <label>Email:</label>
                <input type="text" name="email" required>  
            </div>
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <div>
                <input type="submit" value="Înregistrare">  
            </div>
        </form>
    </div>
</body>

</html>

<?php
session_start();  

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header("location: homepage.php");
    exit;
}

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include __DIR__ . '/../app/db.php';  

    $username = trim($_POST['username']);  
    $password = trim($_POST['password']);  

    
    $stmt = $conn->prepare("SELECT id, username, password FROM user WHERE username = ?");
    $stmt->bind_param("s", $username);  
    $stmt->execute();  
    $result = $stmt->get_result();  
    $row = $result->fetch_assoc();  

    
    if ($result->num_rows == 1) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['loggedin'] = true;
            $_SESSION['id'] = $row['id'];
            $_SESSION['username'] = $username;
            if(getenv('IS_TESTING')) 
                return;
            header("location: homepage.php");
            exit;
        } else {
             $error_message = "Parolă incorectă.";
        }
    } else {
         $error_message = "Nu există niciun cont cu acest nume de utilizator.";  
    }

    $stmt->close(); 
    mysqli_close($conn);  
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formular de Login</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
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
    <?php include 'header.php'; ?> 
    <div class="cont">
        <img src="../photos/logo.png" alt="Logo" style="display: inline-block; height: 50%; position: relative; right:100px; border-radius: 50px;">
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
            <a href="signup.php" class="signup-link">Înregistrare</a> 
        </div>
    </div>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
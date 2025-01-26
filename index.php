<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to GroupCalendar</title>
    <!-- Adaugă Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden;
        }

        .presentation {
            animation: fadeIn 2s ease-in-out;
        }

        .presentation img {
            max-width: 100%;
            height: auto;
            animation: slideIn 2s ease-in-out;
            z-index: 1;
        }

        .presentation-text {
            animation: slideInText 2s ease-in-out;
            z-index: 2;
            position: relative;
            top: -100px;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        @keyframes slideIn {
            from {
                transform: translateX(-100%);
            }
            to {
                transform: translateX(0);
            }
        }

        @keyframes slideInText {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
    </style>
</head>
<body>
    <div class="presentation text-center">
        <img src="group.png" alt="GroupCalendar Presentation Image">
        <div class="presentation-text">
            <h1>GroupCalendar</h1>
            <p>Organizeaza-ti evenimentele de grup cu ușurință.</p>
        </div>
    </div>
    <!-- Adaugă Bootstrap JS pentru funcționalități suplimentare (opțional) -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- Redirecționează către pagina de login după terminarea animației -->
    <script>
        // După 2.5 secunde (timpul total al animației), redirecționează către pagina de login
        setTimeout(function () {
            window.location.href = "loginh.php";
        }, 6500);
    </script>
</body>

</html>

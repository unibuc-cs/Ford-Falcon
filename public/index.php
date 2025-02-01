<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bine ați venit la GroupCalendar</title>
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
        <img src="../photos/group.png" alt="GroupCalendar Presentation Image">
        <div class="presentation-text">
            <h1>GroupCalendar</h1>
            <p>Organizează-&#539;i evenimentele de grup cu ușurință.</p>
        </div>
    </div>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        setTimeout(() => window.location.href = "../interfata/loginh.php", 5000);
    </script>
</body>
</html>
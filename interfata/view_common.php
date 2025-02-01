<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: loginh.php");
    exit();
}

include '../app/db.php';

$username = $_SESSION['username'];
$friend_username = filter_input(INPUT_GET, 'friend', FILTER_SANITIZE_STRING);

$stmt = $conn->prepare("
    SELECT 
        (SELECT id FROM user WHERE username = ?) AS user_id,
        (SELECT id FROM user WHERE username = ?) AS friend_id
");
$stmt->bind_param("ss", $username, $friend_username);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $row = $result->fetch_assoc()) {
    $user_id = $row['user_id'];
    $friend_id = $row['friend_id'];

    if (!$user_id || !$friend_id) {
        die("Eroare: Nu am putut găsi ID-urile utilizatorilor specificați.");
    }
} else {
    die("Eroare: Nu am putut obține informațiile despre utilizatori.");
}

$stmt = $conn->prepare("
    SELECT c.id, c.name 
    FROM calendar c
    JOIN userincalendar uc1 ON c.id = uc1.calendarId
    JOIN userincalendar uc2 ON c.id = uc2.calendarId
    WHERE uc1.userId = ? AND uc2.userId = ?
");
$stmt->bind_param("ii", $user_id, $friend_id);
$stmt->execute();
$common_calendars = $stmt->get_result();

if (!$common_calendars) {
    die("Eroare la preluarea calendarelor: " . htmlspecialchars($conn->error));
}
?>

<!DOCTYPE html>
<html lang="ro">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendare comune</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
        }

        .container {
            padding-top: 100px; 
        }

        .calendar-button {
            background-color: #ffd2c6;
            color: white;
            width: 800px;
            height: 80px;
            text-align: center;
            justify-content: center;
            margin-top: 2vh;
            border-radius: 25px;
        }

        .calendar-button:hover {
            background-color: #ff9b8f;
        }

        #noCommonCalendars {
            text-align: center;
            font-style: italic;
            color: #999;
            margin-top: 20px;
        }

        h1 {
            margin-top: 20px;
            text-align: center;
        }
        .list-group a{
            text-decoration: none;
            color: black;
        }
        .list-group a:hover{
            color: pink;
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>

    <div class="container">
        <h1>Calendare în comun cu <?php echo htmlspecialchars($friend_username); ?></h1>

        <?php if ($common_calendars->num_rows > 0): ?>
            <ul class="list-group">
                <?php while ($row = $common_calendars->fetch_assoc()): ?>
                    <li class="list-group-item">
                        <a href="calendar.php?calendar_id=<?php echo htmlspecialchars($row['id']); ?>">
                            <?php echo htmlspecialchars($row['name']); ?>
                        </a>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p id="noCommonCalendars" class="text-center text-muted">Nu ai calendare în comun cu <?php echo htmlspecialchars($friend_username); ?>.</p>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
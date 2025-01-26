<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: loginh.php");
    exit();
}

include 'db.php'; // Include database connection

$username = $_SESSION['username'];
$friend_username = isset($_GET['friend']) ? trim($_GET['friend']) : '';

// Get user ID from username using prepared statements
$stmt = $conn->prepare("SELECT id FROM user WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
if ($result && $result->num_rows > 0) {
    $user_row = $result->fetch_assoc();
    $user_id = $user_row['id'];
} else {
    die("Error retrieving user ID for username: " . htmlspecialchars($username));
}

// Get friend ID from friend username using prepared statements
$stmt = $conn->prepare("SELECT id FROM user WHERE username = ?");
$stmt->bind_param("s", $friend_username);
$stmt->execute();
$result = $stmt->get_result();
if ($result && $result->num_rows > 0) {
    $friend_row = $result->fetch_assoc();
    $friend_id = $friend_row['id'];
} else {
    die("Error retrieving user ID for friend username: " . htmlspecialchars($friend_username));
}

// Get common calendars using prepared statements
$stmt = $conn->prepare("
    SELECT c.id, c.name FROM calendar c
    JOIN userincalendar uc1 ON c.id = uc1.calendarId
    JOIN userincalendar uc2 ON c.id = uc2.calendarId
    WHERE uc1.userId = ? AND uc2.userId = ?
");
$stmt->bind_param("ii", $user_id, $friend_id);
$stmt->execute();
$common_calendars = $stmt->get_result();
if (!$common_calendars) {
    die("Error retrieving common calendars: " . htmlspecialchars($conn->error));
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Common Calendars</title>
    <!-- Adaugă Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
        }

        .container {
            padding-top: 100px; /* Adjust this value if your header height is different */
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
    <?php
    include 'header.php';
    ?>
    <div class="container">
        <h1>Common Calendars with <?php echo htmlspecialchars($friend_username); ?></h1>

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
            <p id="noCommonCalendars">You have no common calendars with <?php echo htmlspecialchars($friend_username); ?>.</p>
        <?php endif; ?>
    </div>

    <!-- Adaugă jQuery înainte de Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <!-- Adaugă Bootstrap JS pentru funcționalități suplimentare (opțional) -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
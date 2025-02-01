<?php
session_start(); // Începe sesiunea pentru a gestiona starea de autentificare a utilizatorilor

// Verifică dacă utilizatorul nu este autentificat și îl redirecționează către pagina de login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: loginh.php");
    exit();
}

include '../app/db.php'; // Include conexiunea la baza de date

$username = $_SESSION['username']; // Preia numele utilizatorului din sesiune
$_SESSION['show_back_button'] = false; // Setează variabila de sesiune pentru afișarea butonului de navigare înapoi (nu este utilizată în acest cod)

// Obține ID-ul utilizatorului bazat pe numele său de utilizator utilizând declarații pregătite
$stmt = $conn->prepare("SELECT id FROM user WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $user_row = $result->fetch_assoc();
    $user_id = $user_row['id'];
} else {
    die("Eroare la preluarea ID-ului de utilizator pentru: " . htmlspecialchars($username));
}

// Obține lista de prieteni utilizând declarații pregătite
$stmt = $conn->prepare("
    SELECT u.username AS friend_username FROM friendship f
    JOIN user u ON (f.userId1 = u.id OR f.userId2 = u.id)
    WHERE (f.userId1 = ? OR f.userId2 = ?) AND u.username != ?
");
$stmt->bind_param("iis", $user_id, $user_id, $username);
$stmt->execute();
$friends = $stmt->get_result();

if (!$friends) {
    die("Eroare la preluarea prietenilor: " . htmlspecialchars($conn->error));
}

// Tratare formular pentru ștergerea unui prieten
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['remove_friend'])) {
    $friend_username = trim($_POST['friend_username']);

    // Verifică și sanitizează inputul
    if (!empty($friend_username)) {
        // Obține ID-ul prietenului din baza de date
        $stmt = $conn->prepare("SELECT id FROM user WHERE username = ?");
        $stmt->bind_param("s", $friend_username);
        $stmt->execute();
        $friend_result = $stmt->get_result();

        if ($friend_result && $friend_result->num_rows > 0) {
            $friend_row = $friend_result->fetch_assoc();
            $friend_id = $friend_row['id'];

            // Șterge relația de prietenie
            $stmt = $conn->prepare("
                DELETE FROM friendship 
                WHERE (userId1 = ? AND userId2 = ?) 
                   OR (userId1 = ? AND userId2 = ?)
            ");
            $stmt->bind_param("iiii", $user_id, $friend_id, $friend_id, $user_id);

            if ($stmt->execute()) {
                echo "<script>alert('Prieten șters cu succes!'); window.location.href='friends.php';</script>";
            } else {
                echo "<script>alert('Eroare la ștergerea prietenului.');</script>";
            }
        } else {
            echo "<script>alert('Nu a fost găsit acest prieten.');</script>";
        }
    } else {
        echo "<script>alert('Numele prietenului nu poate fi gol.');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prieteni</title>
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

        #noCalendars {
            text-align: center;
            font-style: italic;
            color: #999;
            top: 20vh;
            position: relative;
        }

        #createButton,
        #addFriendButton,
        #friendRequestsButton {
            font-size: 5vw;
            top: 19vh;
            position: absolute;
            left: 3vw;
            background-color: #ffd2c6;
            color: white;
            height: calc(10vh + 3vw);
            width: calc(3.5vw + 6vh);
        }

        #addFriendButton {
            top: 25vh;
        }

        #friendRequestsButton {
            top: 46vh;
        }

        .friend-list {
            list-style-type: none;
            padding: 0;
            max-width: 600px; /* Adjust the width as needed */
            margin: 0 auto; /* Center the list horizontally */
        }

        .friend-list li {
            display: flex; /* Use Flexbox for alignment */
            justify-content: space-between; /* Push the button to the right */
            align-items: center; /* Vertically align content */
            margin: 20px 0;
            padding: 10px;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
            width: 100%; /* Ensure fixed width for the list items */
            border-radius: 5px;
            border: 1px solid pink;
            background-color: #fff; /* Set a background color */
        }

        .friend-list li:hover {
            background-color: pink;
        }

        .friend-list a {
            flex-grow: 1; /* Allow the link to take up remaining space */
            text-decoration: none;
            color: #333;
            font-size: 16px;
            padding-right: 10px; /* Add some space between the link and the button */
        }

        .friend-list a:hover {
            text-decoration: underline;
            color: #ff4d4d;
        }

        .friend-list button {
            background-color: #ff4d4d;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 5px 10px;
            cursor: pointer;
            font-size: 14px;
        }

        .friend-list button:hover {
            background-color: #e60000;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    <button class="back-button" onclick="window.location.href='homepage.php'">&Icirc;napoi</button>

    <div class="container">
        <h1>Prieteni</h1>
        <a href="add_friend.php"><button id="addFriendButton">+</button></a>
        <a href="manage_friend_requests.php"><button id="friendRequestsButton"><img src="../photos/add-user.png" style="width:100%;height:100%;"></button></a>
        <h2>Listă de prieteni</h2>
        <?php if ($friends->num_rows > 0): ?>
            <ul class="friend-list">
                <?php while ($row = $friends->fetch_assoc()): ?>
                    <li>
                        <a href="view_common.php?friend=<?php echo urlencode($row['friend_username']); ?>">
                            <?php echo htmlspecialchars($row['friend_username']); ?>
                        </a>
                        <form method="post" action="" style="display:inline;">
                            <input type="hidden" name="friend_username" value="<?php echo htmlspecialchars($row['friend_username']); ?>">
                            <button type="submit" name="remove_friend">Remove</button>
                        </form>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p id="noFriends">Nu ai nici un prieten încă.</p>
        <?php endif; ?>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
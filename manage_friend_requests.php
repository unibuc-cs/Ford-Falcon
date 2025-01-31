<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit();
}

include 'db.php'; // Include database connection

$username = $_SESSION['username'];

// Get user ID from username
$result = mysqli_query($conn, "SELECT id FROM user WHERE username='$username'");
if ($result && mysqli_num_rows($result) > 0) {
    $user_row = mysqli_fetch_assoc($result);
    $user_id = $user_row['id'];
} else {
    die("Eroare la găsirea user-ului: $username");
}

// Handle friend request acceptance/rejection
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['accept_request'])) {
        $sender = $_POST['sender'];

        // Get sender ID from sender username
        $result = mysqli_query($conn, "SELECT id FROM user WHERE username='$sender'");
        if ($result && mysqli_num_rows($result) > 0) {
            $sender_row = mysqli_fetch_assoc($result);
            $sender_id = $sender_row['id'];
        } else {
            die("Eroare la preluarea ID-ului de utilizator pentru expeditor: $sender");
        }

        $query = "INSERT INTO friendship (userId1, userId2) VALUES ('$user_id', '$sender_id')";
        if (!mysqli_query($conn, $query)) {
            die("Eroare la inserarea prieteniei: " . mysqli_error($conn));
        }

        $query = "DELETE FROM friend_requests WHERE sender='$sender' AND receiver='$username'";
        if (!mysqli_query($conn, $query)) {
            die("Eroare la ștergerea cererii de prietenie: " . mysqli_error($conn));
        }
        echo "Cerere de prietenie acceptată!";
    }

    if (isset($_POST['reject_request'])) {
        $sender = $_POST['sender'];
        $query = "DELETE FROM friend_requests WHERE sender='$sender' AND receiver='$username'";
        if (!mysqli_query($conn, $query)) {
            die("Eroare la refuzarea cererii de prietenie: " . mysqli_error($conn));
        }
        echo "Cerere de prietenie refuzată.";
    }
}

// Get friend requests
$friend_requests = mysqli_query($conn, "SELECT * FROM friend_requests WHERE receiver='$username'");
if (!$friend_requests) {
    die("Eroare la preluarea listei de cereri de prietenie: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Cereri de prietenie</title>
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

        .list-group-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        #noFriendRequests {
            text-align: center;
            font-style: italic;
            color: #999;
            margin-top: 20px;
        }

        h1 {
            margin-top: 20px;
            text-align: center;
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>
    <div class="container">
        <h1>Cereri de prietenie</h1>
        <?php if (mysqli_num_rows($friend_requests) > 0): ?>
            <ul class="list-group">
                <?php while ($row = mysqli_fetch_assoc($friend_requests)): ?>
                    <li class="list-group-item">
                        <?php echo htmlspecialchars($row['sender']); ?>
                        <form method="post" action="" style="display:inline;">
                            <input type="hidden" name="sender" value="<?php echo htmlspecialchars($row['sender']); ?>">
                            <button type="submit" name="accept_request" class="btn btn-success">Acceptă</button>
                            <button type="submit" name="reject_request" class="btn btn-danger">Respinge</button>
                        </form>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p id="noFriendRequests">Nu ai cereri de prietenie.</p>
        <?php endif; ?>
    </div>

    <!-- Adaugă jQuery înainte de Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <!-- Adaugă Bootstrap JS pentru funcționalități suplimentare (opțional) -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
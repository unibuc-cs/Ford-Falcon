<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../php_ex/login.php");
    exit();
}

include '../app/db.php';

$username = $_SESSION['username'];

$stmt = $conn->prepare("SELECT id FROM user WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $user_row = $result->fetch_assoc();
    $user_id = $user_row['id'];
} else {
    die("Eroare: nu am putut găsi utilizatorul.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $sender = filter_input(INPUT_POST, 'sender', FILTER_SANITIZE_STRING);

    $stmt = $conn->prepare("SELECT id FROM user WHERE username = ?");
    $stmt->bind_param("s", $sender);
    $stmt->execute();
    $sender_result = $stmt->get_result();

    if ($sender_result && $sender_result->num_rows > 0) {
        $sender_row = $sender_result->fetch_assoc();
        $sender_id = $sender_row['id'];

        if (isset($_POST['accept_request'])) {
            $stmt = $conn->prepare("INSERT INTO friendship (userId1, userId2) VALUES (?, ?)");
            $stmt->bind_param("ii", $user_id, $sender_id);
            $stmt->execute();

            $stmt = $conn->prepare("DELETE FROM friend_requests WHERE sender = ? AND receiver = ?");
            $stmt->bind_param("ss", $sender, $username);
            $stmt->execute();

            echo "<script>alert('Cerere de prietenie acceptată!'); window.location.href = 'manage_friend_requests.php';</script>";
        } elseif (isset($_POST['reject_request'])) {
            $stmt = $conn->prepare("DELETE FROM friend_requests WHERE sender = ? AND receiver = ?");
            $stmt->bind_param("ss", $sender, $username);
            $stmt->execute();

            echo "<script>alert('Cerere de prietenie refuzată.'); window.location.href = 'manage_friend_requests.php';</script>";
        }
    }
}

$stmt = $conn->prepare("SELECT sender FROM friend_requests WHERE receiver = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$friend_requests = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="ro">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cereri de prietenie</title>
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

        <?php if ($friend_requests->num_rows > 0): ?>
            <ul class="list-group">
                <?php while ($row = $friend_requests->fetch_assoc()): ?>
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
            <p class="text-center text-muted">Nu ai cereri de prietenie.</p>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
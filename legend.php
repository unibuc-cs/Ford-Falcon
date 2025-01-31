<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: loginh.php");
    exit();
}

include 'db.php';

$calendar_id = isset($_GET['calendar_id']) ? intval($_GET['calendar_id']) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Legend</title>
    <link rel="stylesheet" href="./style.css"> <!-- Încarcă fișierul CSS separat pentru stilizare -->
    <style>
        .event-color {
            display: inline-block;
            width: 20px;
            height: 20px;
            margin-right: 10px;
        }
        .search-bar {
            width: 200px;
            padding: 10px;
            margin: 10px auto;
            display: block;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        .legend ul {
            padding: 0;
            list-style-type: none;
        }
        .legend li {
            padding: 5px 0;
        }
        .event-details {
            display: none;
        }
    </style>
</head>
<body>
    <div class="legend">
        <h3>Legenda:</h3>
        <input type="text" id="searchBar" class="search-bar" onkeyup="filterEvents()" placeholder="Căutați evenimente...">
        <br>
        <?php
        if ($calendar_id > 0) {
            // Fetch all events ordered by date and time using prepared statements
            $stmt = $conn->prepare("SELECT title, description, location, type FROM event WHERE calendarId = ? ORDER BY date ASC, time ASC");
            $stmt->bind_param("i", $calendar_id);
            $stmt->execute();
            $event_result = $stmt->get_result();

            if ($event_result->num_rows > 0) {
                echo "<ul id='eventList'>";
                while ($row = $event_result->fetch_assoc()) {
                    echo "<li><div class='event-color' style='background-color:" . htmlspecialchars($row['type']) . "'></div><span class='event-title'>" . htmlspecialchars($row['title']) . "</span><span class='event-details' style='display:none;'>" . htmlspecialchars($row['description']) . " " . htmlspecialchars($row['location']) . "</span></li>";
                }
                echo "</ul>";
            } else {
                echo "Nu există evenimente de afișat în legendă.";
            }
            $stmt->close();
        } else {
            echo "Nu ați specificat un ID de calendar.";
        }
        mysqli_close($conn);
        ?>
    </div>

    <script>
        function filterEvents() {
            var input, filter, ul, li, title, details, i, txtValue, detailsValue;
            input = document.getElementById('searchBar');
            filter = input.value.toUpperCase();
            ul = document.getElementById("eventList");
            li = ul.getElementsByTagName('li');

            for (i = 0; i < li.length; i++) {
                title = li[i].getElementsByClassName("event-title")[0];
                details = li[i].getElementsByClassName("event-details")[0];
                txtValue = title.textContent || title.innerText;
                detailsValue = details.textContent || details.innerText;
                if (filter === "" || txtValue.toUpperCase().indexOf(filter) > -1 || detailsValue.toUpperCase().indexOf(filter) > -1) {
                    li[i].style.display = "";
                } else {
                    li[i].style.display = "none";
                }
            }
        }
    </script>
</body>
</html>
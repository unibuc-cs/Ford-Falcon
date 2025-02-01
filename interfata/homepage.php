
<?php
session_start();
include __DIR__ . '/../app/db.php';

$success_message = false;
$error_message = "";
$successs_message = false;
$errorr_message = "";


if(getenv('IS_TESTING')) 
{
    $_SESSION['loggedin'] = true;
    $_SESSION['username'] = 'testuser';
    $_SESSION['id'] = 1;
}

// Verifică dacă utilizatorul este autentificat
if (!isset($_SESSION['id']) || !isset($_SESSION['username'])) {
    header("Location: loginh.php");
    exit();
}

$user_id = $_SESSION['id'];
$user_name = $_SESSION['username'];
$_SESSION['show_back_button'] = false;

// Procesare formular pentru adăugarea unui calendar folosind un cod
if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['calendar_code'])) {
    $calendar_code = trim($_POST['calendar_code']);
    
    // Verificare existența calendarului în baza de date folosind prepared statements
    $stmt = $conn->prepare("SELECT id, adminId FROM calendar WHERE code = ?");
    $stmt->bind_param("s", $calendar_code);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $calendar_row = $result->fetch_assoc();
        $calendar_id = $calendar_row['id'];
        $admin_id = $calendar_row['adminId'];
        
        // Verificare prietenie folosind prepared statements
        $stmt = $conn->prepare("
            SELECT * FROM friendship 
            WHERE (userId1 = ? AND userId2 = ?) 
               OR (userId1 = ? AND userId2 = ?)
        ");
        $stmt->bind_param("iiii", $user_id, $admin_id, $admin_id, $user_id);
        $stmt->execute();
        $friend_result = $stmt->get_result();

        if ($friend_result->num_rows > 0) {
            // Verificare dacă utilizatorul este deja în calendar
            $stmt = $conn->prepare("SELECT * FROM userincalendar WHERE userId = ? AND calendarId = ?");
            $stmt->bind_param("ii", $user_id, $calendar_id);
            $stmt->execute();
            $check_result = $stmt->get_result();
            
            if ($check_result->num_rows == 0) {
                // Adăugare utilizator în calendar
                $stmt = $conn->prepare("INSERT INTO userincalendar (userId, calendarId) VALUES (?, ?)");
                $stmt->bind_param("ii", $user_id, $calendar_id);
                if ($stmt->execute()) {
                    $success_message = true; // Setează succesul
                    $error_message = "";
                } else {
                    $error_message = "Eroare la adăugarea dvs. la calendar.";
                }
            } else {
                $error_message = "Sunteți deja membru/ă al acestui calendar.";
            }
        } else {
            $error_message = "Trebuie sa fiți prieten/ă cu deținătorul pentru a fi adaugat/ă la calendar.";
        }
    } else {
        $error_message = "Cod de calendar invalid!.";
    }
    $stmt->close();
}

// Modificăm interogarea pentru a include numele creatorului calendarului pentru calendarele adăugate prin cod
$stmt = $conn->prepare("
    SELECT 
        u.username AS user_name, 
        c.name AS calendar_name, 
        uc.calendarId AS calendar_id,
		c.adminId AS creator_id,
        (SELECT username FROM user WHERE id = c.adminId) AS creator_name
    FROM 
        userincalendar uc 
    JOIN 
        calendar c ON c.id = uc.calendarId 
    JOIN 
        user u ON u.id = uc.userId 
    WHERE 
        uc.userId = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$calendar_result = $stmt->get_result();
include __DIR__ . '/../interfata/header.php';
?>
<a href="creare_calendar.php"><button id="createButton">+</button></a>
<div id="tableContainer">
<?php
if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['delete_calendar']) && isset($_POST['calendar_id'])) {
        $calendar_id = (int)$_POST['calendar_id'];
    
        // Șterge mai întâi înregistrările din userinevent legate de evenimentele calendarului
        $stmt = $conn->prepare("
            DELETE ue FROM userinevent ue
            JOIN event e ON ue.eventId = e.id
            WHERE e.calendarId = ?
        ");
        $stmt->bind_param("i", $calendar_id);
        $stmt->execute();
        $stmt->close();
    
        // Șterge evenimentele asociate calendarului
        $stmt = $conn->prepare("DELETE FROM event WHERE calendarId = ?");
        $stmt->bind_param("i", $calendar_id);
        $stmt->execute();
        $stmt->close();
    
        // Șterge utilizatorii din calendar
        $stmt = $conn->prepare("DELETE FROM userincalendar WHERE calendarId = ?");
        $stmt->bind_param("i", $calendar_id);
        $stmt->execute();
        $stmt->close();
    
        // Șterge calendarul dacă utilizatorul este creatorul
        $stmt = $conn->prepare("DELETE FROM calendar WHERE id = ? AND adminId = ?");
        $stmt->bind_param("ii", $calendar_id, $user_id);
        if ($stmt->execute()) {
            $successs_message = "Calendar șters cu succes.";
            header("Location: homepage.php");
            exit();
        } else {
            $errorr_message = "Eroare la ștergerea calendarului.";
        }
        $stmt->close();
    }
    

    if (isset($_POST['leave_calendar']) && isset($_POST['calendar_id'])) {
        $calendar_id = (int)$_POST['calendar_id'];
        // Șterge utilizatorul din calendar
        $stmt = $conn->prepare("DELETE FROM userincalendar WHERE calendarId = ? AND userId = ?");
        $stmt->bind_param("ii", $calendar_id, $user_id);
        if ($stmt->execute()) {
            $successs_message = "Ai părăsit calendarul.";
			header("Location: homepage.php");
			exit();
        } else {
            $errorr_message = "Eroare la părăsirea calendarului.";
        }
        $stmt->close();
    }
}

?>
<?php
// Verifică dacă s-au găsit calendare
if ($calendar_result->num_rows > 0) {
    // Există calendare la care utilizatorul este implicat, le afișăm
    while ($row = $calendar_result->fetch_assoc()) {
        // Afișează numele creatorului pentru calendarele adăugate prin cod
        $display_name = $row['user_name'] == $user_name ? $row['creator_name'] : $row['user_name'];
        echo "<a href='calendar.php?calendar_id=" . htmlspecialchars($row['calendar_id']) . "'><div class='calendar-button'>";
        echo "<h3>" . htmlspecialchars($display_name) . " - " . htmlspecialchars($row['calendar_name']) . "</h3>";
        echo "</div></a>";
		
		 $is_creator = $row['creator_id'] == $user_id;
		
		// Buton de ștergere sau ieșire
        if ($is_creator) {
            echo "<form method='post' action='' style='display:inline;'>
                    <input type='hidden' name='calendar_id' value='" . htmlspecialchars($row['calendar_id']) . "'>
                    <button type='submit' name='delete_calendar' class='action-button'>Șterge</button>
                  </form>";
        } else {
            echo "<form method='post' action='' style='display:inline;'>
                    <input type='hidden' name='calendar_id' value='" . htmlspecialchars($row['calendar_id']) . "'>
                    <button type='submit' name='leave_calendar' class='action-button'>Ieșire</button>
                  </form>";
        }
    }
} else {
    echo "<p id='noCalendars'>Nu ești implicat/ă în niciun calendar.</p>";
}
?>
</div>
<div id="codeFormContainer">
    <form method="post" action="">
        <input type="text" name="calendar_code" placeholder="Introduceți cod calendar" required>
		<p id="error-message" style="color: red; text-align: center; margin-top: 10px;">
				<?php echo htmlspecialchars($error_message); ?>
				</p>
        <button type="submit" id="addCalendarButton">Adaugă calendar</button>
    </form>
</div>
<div class="evenimente">
<?php
include __DIR__ . '/../app/db.php';

// Obține data curentă și calculează ziua următoare
$current_date = date('Y-m-d');
$next_day = date('Y-m-d', strtotime($current_date . ' +1 day'));
// Interogare SQL pentru a selecta evenimentele care au loc în ziua următoare
$query = "SELECT e.* FROM event e join userinevent u ON e.id=u.eventId WHERE DATE(e.date) = '$next_day' and userId = '$user_id'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<hr style='width:200px; height:2px; color:pink; background-color:pink;'>";
        echo "<div>";
        echo "<p>Eveniment: " . htmlspecialchars($row['title']) . "</p>";
        echo "<p>Data: " . htmlspecialchars($row['date']) . "</p>";
        echo "<p>Descriere: " . htmlspecialchars($row['description']) . "</p>";
        echo "</div>";
        echo "<hr style='width:200px; height:2px; color:pink; background-color:pink;'>";
    }
} else {
    echo "<p>Nu există evenimente pentru ziua următoare.</p>";
}

mysqli_close($conn);
?>

</div>
<script>


 // Verifică dacă adăugarea a avut succes
    <?php if ($success_message): ?>
        const button = document.getElementById('addCalendarButton');
        button.textContent = "✔ Added";
        button.style.backgroundColor = "#4caf50"; // Verde
        button.style.cursor = "default";
        button.disabled = true;

        // Revine la starea inițială după 2 secunde
        setTimeout(() => {
            button.textContent = "Add Calendar";
            button.style.backgroundColor = "pink";
            button.style.cursor = "pointer";
            button.disabled = false;
        }, 2000);
    <?php endif; ?>

    window.addEventListener('pageshow', function (event) {
        if (event.persisted || (window.performance && window.performance.navigation.type === 2)) {
            // Utilizatorul a navigat înapoi
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "../php_ex/logout.php", false);  // Sincron - poate afecta performanța
            xhr.send();
        }
    });
</script>
<style>

.popup {
    display: none; /* Ascunde popup-ul în mod implicit */
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 1000;
    background-color: #4caf50; /* Verde pentru succes */
    color: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    text-align: center;
    font-family: Arial, sans-serif;
}

.popup button {
    margin-top: 15px;
    padding: 10px 20px;
    background-color: white;
    color: #4caf50;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
}

.popup button:hover {
    background-color: #f1f1f1;
}


.action-button {
    background-color: #ff4d4d; /* Roșu */
    color: white;
    border: none;
    border-radius: 5px;
    padding: 8px 12px;
    cursor: pointer;
    margin-left: 10px;
    transition: background-color 0.3s ease;
}

.action-button:hover {
    background-color: #e60000; /* Roșu închis */
}


#addCalendarButton {
    width: 100%;
    padding: 10px;
    background-color: pink;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

#addCalendarButton:disabled {
    cursor: default;
    opacity: 0.7;
}
    #tableContainer {
        position: absolute;
        top: 35vh;
        display: flex;
        flex-direction: column;
        flex-wrap: wrap;
        width: 70vw; /* Ajustare pentru a face loc formularului */
        align-items: center;
        justify-content: center;
        margin: 0 auto;
    }

    .evenimente{
        position: absolute;
        top: 40vh;
        right: 3vw;
        display: grid;
        width: 25vw;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);

    }

    #tableContainer a {
        /* width: 100%; */
        padding: 5px;
    }

    a:hover {
        text-decoration: none;
    }

    #codeFormContainer {
        position: absolute;
        top: 15vh;
        right: 3vw;
        background-color: #ffd2c6;
        padding: 20px;
        border-radius: 10px;
    }

    .calendar-button {
        background-color: #ffd2c6;
        color: white;
        width: 70vw;
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

    #createButton {
        font-size: 5vw;
        top: 15vh;
        position: absolute;
        left: 3vw;
        background-color: #ffd2c6;
        color: white;
        width: 6vw;
    }

    #codeFormContainer input {
        width: calc(100% - 20px);
        padding: 8px;
        margin-bottom: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    #codeFormContainer button {
        width: 100%;
        padding: 10px;
        background-color: pink;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    #codeFormContainer button:hover {
        background-color: #0056b3;
    }
</style>

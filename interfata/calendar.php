<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar</title>
    <link rel="stylesheet" href="http://localhost/ford-falcon/public/style.css">
    <style>
        .cod{
            flex: 0 0 30%;
            padding-left: 20px;
        }
        .com{
            border: 7px solid #ffd2c6;
            border-radius: 5px;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
            width: 90%;
            height: 400px;
            position: relative;
            left: 5%;
        }
        .comentari{
            flex: 0 0 70%;
            padding-right: 20px;
        }
        .comentari h2{
            text-align: center;
            position: relative;
            background-color: rgba(255, 210, 198, 0.5);
            color: white;
            border-radius: 5px;
            width: 100%;
            position: relative;
            left: 1%;

        }
        #calendarCode{
            border: none;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
        }
        #copy{
            background-color: #ffd2c6;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 5px;
            cursor: pointer;
            position: relative;
            left: 1%;
        }
        #submit{
            background-color: #ffd2c6;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 5px;
            cursor: pointer;
            width: 100px;
            position: relative;
            left: 45%;
            bottom: 0px;
        }
        .comentari iframe{
            width: 95%;
            height: 200px;
            top: 10px;
            position: relative;
            left: 1%;
            border: none;
        }
        .comentari textarea{
            width: 98%;
            height: 100px;
            border: none;
            position: relative;
            left: 1%;
            background-color: #f2f2f2;
        }
        .comentari textarea:focus{
            outline: none;
        }
        .event-indicator {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-top: 2px;
            display: inline-block;
        }
        .popup {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.4);
        }
        .popup-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        .comentari{
            position: relative;
            top:200px;
        }
    </style>
</head>
<body>
    <?php
    session_start();
    if (!isset($_SESSION['id'])) {
        header('Location: loginh.php');
        exit();
    }

    include '../app/db.php';

    $user_id = $_SESSION['id'];
    $user_name = $_SESSION['username'];
    $_SESSION['show_back_button'] = true;

    include 'header.php';

    if (isset($_GET['calendar_id'])) {
        $calendar_id = $_GET['calendar_id'];

        $stmt = $conn->prepare("SELECT name FROM calendar WHERE id = ?");
        $stmt->bind_param("i", $calendar_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        $currentDate = date('Y-m-d');
        $deleteExpiredEventsQuery = "DELETE FROM event WHERE date < ? AND calendarId = ?";
        $stmt = $conn->prepare($deleteExpiredEventsQuery);
        $stmt->bind_param("si", $currentDate, $calendar_id);
        $stmt->execute();
    } else {
        echo "Nu ați specificat un ID de calendar.";
    }
    ?>
    <div class="tot">
        <div class="wrapper">
            <div class="container-calendar">
                <div id="left">
                    <h1><?php echo htmlspecialchars($row['name']); ?></h1>
                    <form method="post" id="eventForm">
                        <div id="event-section">
                            <input type="hidden" name="form_typev" value="event_form">
                            <h3>Adaugă eveniment </h3>
                            <label for="eventDate">Dată:</label>
                            <input type="date" id="eventDate" name="eventDate" required><br>
                            <label for="eventTime">Oră:</label>
                            <input type="time" id="eventTime" name="eventTime" required><br>
                            <label for="eventLocation">Locație:</label>
                            <input type="text" id="eventLocation" name="eventLocation" placeholder="Locație eveniment" required><br>
                            <label for="eventTitle">Titlu:</label>
                            <input type="text" id="eventTitle" name="eventTitle" placeholder="Titlu eveniment" required><br>
                            <label for="eventDescription">Descriere:</label>
                            <input type="text" id="eventDescription" name="eventDescription" placeholder="Descrierea evenimentului"><br>
                            <label for="eventColor">Culoare:</label>
                            <input type="color" id="eventColor" name="eventColor" required><br>
                            <button type="submit" id="addEvent">Add</button>
                        </div>
                    </form>
                </div>
                <div id="right">
                    <h3 id="monthAndYear"></h3>
                    <div class="button-container-calendar">
                        <button id="previous" onclick="previous()">&#10094;</button>
                        <button id="next" onclick="next()">&#10095;</button>
                    </div>
                    <table class="table-calendar" id="calendar" data-lang="en">
                        <thead id="thead-month"></thead>
                        <tbody id="calendar-body"></tbody>
                    </table>
                    <div class="footer-container-calendar">
                        <label for="month">Jump To: </label>
                        <select id="month" onchange="jump()">
                            <option value=0>Ian</option>
                            <option value=1>Feb</option>
                            <option value=2>Mar</option>
                            <option value=3>Apr</option>
                            <option value=4>Mai</option>
                            <option value=5>Iun</option>
                            <option value=6>Iul</option>
                            <option value=7>Aug</option>
                            <option value=8>Sep</option>
                            <option value=9>Oct</option>
                            <option value=10>Noi</option>
                            <option value=11>Dec</option>
                        </select>
                        <select id="year" onchange="jump()"></select>
                    </div>
                </div>
            </div>
        </div>
        <div class="cod">
            <?php 
                $stmt = $conn->prepare("SELECT code FROM calendar WHERE id = ?");
                $stmt->bind_param("i", $calendar_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $code = $row['code'];
            ?>
            <div>
                <label for="calendarCode">Codul calendarului:</label>
                <input type="text" id="calendarCode" value="<?php echo htmlspecialchars($code); ?>" readonly>
                <button onclick="copyToClipboard()" id="copy">Copy</button>
            </div>
            <script>
                function copyToClipboard() {
                    var copyText = document.getElementById("calendarCode");
                    copyText.select();
                    copyText.setSelectionRange(0, 99999); 
                    document.execCommand("copy");
                    alert("Codul a fost copiat: " + copyText.value);
                }
            </script>
            <?php
                } else {
                    echo "Codul nu a fost găsit.";
                }
            ?>
            <div class="legend">
                <iframe src="legend.php?calendar_id=<?php echo $calendar_id; ?>" width="350" height="500" style="border:none; box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);"></iframe>
            </div>
        </div>
    </div>
    <div id="eventPopup" class="popup">
        <div class="popup-content">
            <span class="close" onclick="closePopup()">&times;</span>
            <h2>Evenimente pe <span id="popupDate"></span></h2>
            <ul id="eventList"></ul>
            <div id="availabilitySection" style="display: none;">
                <h3>Alătură-te evenimentului</h3>
                <label for="availabilityStatus">Valabilitate:</label>
                <select id="availabilityStatus" name="availabilityStatus" required>
                    <option value="Valabil">Liber</option>
                    <option value="Nevalabil">Indisponibil</option>
                    <option value="Nesigur">Nesigur</option>
                </select><br>
                <button id="joinButton">Trimite</button>
            </div>
        </div>
    </div>
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['form_typev']) && $_POST['form_typev'] == 'event_form') {
            include '../app/db.php';

            $eventDate = $_POST['eventDate'];
            $eventTime = $_POST['eventTime'];
            $eventLocation = $_POST['eventLocation'];
            $eventTitle = $_POST['eventTitle'];
            $eventDescription = $_POST['eventDescription'];
            $eventColor = $_POST['eventColor'];

            $stmt = $conn->prepare("SELECT * FROM event WHERE type = ? and calendarId = ?");
            $stmt->bind_param("si", $eventColor, $calendar_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                #echo "<script>alert('Evenimentul nu poate fi adăugat. Alegeți o altă culoare.');</script>";
            } else {
                $stmt = $conn->prepare("INSERT INTO event (calendarId, date, time, location, title, description, type) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("issssss", $calendar_id, $eventDate, $eventTime, $eventLocation, $eventTitle, $eventDescription, $eventColor);

                if ($stmt->execute()) {
                    echo "<script>alert('Eveniment adăugat cu succes!'); </script>";
                } else {
                    echo "Eroare: " . $stmt->error;
                }
            }
        }
    }

    $stmt = $conn->prepare("SELECT id , date, time, type, description FROM event WHERE calendarId = ?");
    $stmt->bind_param("i", $calendar_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $events = array();
    $users = array();
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }

    $userquery = "SELECT e.id , e.date, e.time, u.username, ue.displonibility FROM event e JOIN userinevent ue ON e.id = ue.eventId JOIN user u ON u.id = ue.userId WHERE calendarId = ?";
    $stmt = $conn->prepare($userquery);
    $stmt->bind_param("i", $calendar_id);
    $stmt->execute();
    $userresult = $stmt->get_result();
    while ($row = $userresult->fetch_assoc()) {
        $users[] = $row;
    }

    $usersJson = json_encode($users);
    $eventsJson = json_encode($events);
    $conn->close();

    ?>
    <section class="comentari">
        <h2>Comentarii</h2>
        <div class="com">
            <iframe src="../php_ex/comments.php?calendar_id=<?php echo $calendar_id; ?>"></iframe>
            <hr style="height:5px;border-width:0;color:#ffd2c6;background-color:#ffd2c6; opacity:0.5">
            <form method="post" id="comform">
                <input type="hidden" name="form_type" value="com_form">
                <textarea name="comment" id="comment" cols="60" rows="3" placeholder="Text aici"></textarea>
                <input type="submit" value="Trimite" id="submit">
            </form>
        </div>
        <?php
		$message = "";
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST['form_type']) && $_POST['form_type'] == 'com_form') {
                include '../app/db.php';
                $comment = $_POST['comment'];
                $created_at = date('Y-m-d H:i:s');
                $stmt = $conn->prepare("INSERT INTO comments (calendar_id, user_id, comment, created_at) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("iiss", $calendar_id, $user_id, $comment, $created_at);

                if ($stmt->execute()) {
                    $message="merge";
                } else {
                    echo "Eroare: " . $stmt->error;
                }
            }
        }
        ?>
    </section>

    <script>
        let eventsphp = <?php echo $eventsJson; ?>;
        let usersphp = <?php echo $usersJson; ?>;
        console.log(usersphp);
        let events = [];

        eventsphp.forEach(event => {
            let eventId = event.id;
            let date = event.date;
            let time = event.time;
            let type = event.type;
            let description = event.description;
            if (date && type) {
                events.push({
                    id: eventId,
                    date: date,
                    time: time,
                    type: type,
                    description: description
                });
            }
        });

        let availability = {};

        function deleteEvent(eventId) {
            let eventIndex =
                events.findIndex((event) =>
                    event.id === eventId);

            if (eventIndex !== -1) {
                events.splice(eventIndex, 1);
                showCalendar(currentMonth, currentYear);
                displayReminders();
            }
        }

        function generate_year_range(start, end) {
            let years = "";
            for (let year = start; year <= end; year++) {
                years += "<option value='" +
                    year + "'>" + year + "</option>";
            }
            return years;
        }

        today = new Date();
        currentMonth = today.getMonth();
        currentYear = today.getFullYear();
        selectYear = document.getElementById("year");
        selectMonth = document.getElementById("month");

        createYear = generate_year_range(1970, 2050);

        document.getElementById("year").innerHTML = createYear;

        let calendar = document.getElementById("calendar");

        let months = [
            "Ianuarie",
            "Februarie",
            "Martie",
            "Aprilie",
            "Mai",
            "Iunie",
            "Iulie",
            "August",
            "Septembrie",
            "Octombrie",
            "Noiembrie",
            "Decembrie"
        ];
        let days = [
            "Dum", "Luni", "Mar", "Mie", "Joi", "Vin", "Sâm"];

        $dataHead = "<tr>";
        for (dhead in days) {
            $dataHead += "<th data-days='" +
                days[dhead] + "'>" +
                days[dhead] + "</th>";
        }
        $dataHead += "</tr>";

        document.getElementById("thead-month").innerHTML = $dataHead;

        monthAndYear =
            document.getElementById("monthAndYear");
        showCalendar(currentMonth, currentYear);

        function next() {
            currentYear = currentMonth === 11 ?
                currentYear + 1 : currentYear;
            currentMonth = (currentMonth + 1) % 12;
            showCalendar(currentMonth, currentYear);
        }

        function previous() {
            currentYear = currentMonth === 0 ?
                currentYear - 1 : currentYear;
            currentMonth = currentMonth === 0 ?
                11 : currentMonth - 1;
            showCalendar(currentMonth, currentYear);
        }

        function jump() {
            currentYear = parseInt(selectYear.value);
            currentMonth = parseInt(selectMonth.value);
            showCalendar(currentMonth, currentYear);
        }

        function showCalendar(month, year) {
            let firstDay = new Date(year, month, 1).getDay();
            tbl = document.getElementById("calendar-body");
            tbl.innerHTML = "";
            monthAndYear.innerHTML = months[month] + " " + year;
            selectYear.value = year;
            selectMonth.value = month;
            let date = 1;
            for (let i = 0; i < 6; i++) {
                let row = document.createElement("tr");
                for (let j = 0; j < 7; j++) {
                    if (i === 0 && j < firstDay) {
                        cell = document.createElement("td");
                        cellText = document.createTextNode("");
                        cell.appendChild(cellText);
                        row.appendChild(cell);
                    } else if (date > daysInMonth(month, year)) {
                        break;
                    } else {
                        cell = document.createElement("td");
                        cell.setAttribute("data-date", date);
                        cell.setAttribute("data-month", month + 1);
                        cell.setAttribute("data-year", year);
                        cell.setAttribute("data-month_name", months[month]);
                        cell.className = "date-picker";
                        cell.innerHTML = "<span>" + date + "</span>";

                        if (date === today.getDate() && year === today.getFullYear() && month === today.getMonth()) {
                            cell.className = "date-picker selected";
                        }

                        if (hasEventOnDate(date, month, year)) {
                            let eventsOnThisDate = getEventsOnDate(date, month, year);
                            eventsOnThisDate.forEach(event => {
                                let eventIndicator = document.createElement("div");
                                eventIndicator.className = "event-indicator";
                                eventIndicator.style.backgroundColor = event.type; 
                                cell.appendChild(eventIndicator);
                            });
                        }

                        cell.addEventListener("click", function () {
                            let date = this.getAttribute("data-date");
                            let month = this.getAttribute("data-month") - 1;
                            let year = this.getAttribute("data-year");
                            showPopup(date, month, year);
                        });

                        row.appendChild(cell);
                        date++;
                    }
                }
                tbl.appendChild(row);
            }
        }

        function hasEventOnDate(date, month, year) {
            return getEventsOnDate(date, month, year).length > 0;
        }

        function getEventsOnDate(date, month, year) {
            return events.filter(function (event) {
                let eventDate = new Date(event.date);
                return (
                eventDate.getDate() === parseInt(date) &&
                eventDate.getMonth() === parseInt(month) &&
                eventDate.getFullYear() === parseInt(year)
                );
            });
        }


        function createEventTooltip(date, month, year) {
            let tooltip = document.createElement("div");
            tooltip.className = "event-tooltip";
            let eventsOnDate = getEventsOnDate(date, month, year);
            for (let i = 0; i < eventsOnDate.length; i++) {
                let event = eventsOnDate[i];
                let eventElement = document.createElement("div");
                eventElement.style.backgroundColor = event.type;

                tooltip.appendChild(eventElement);
            }

            return tooltip;
        }

        function getEventsOnDate(date, month, year) {
            return events.filter(function (event) {
                let eventDate = new Date(event.date);
                return (
                    eventDate.getDate() === parseInt(date) &&
                    eventDate.getMonth() === parseInt(month) &&
                    eventDate.getFullYear() === parseInt(year)
                );
            });
        }

        function hasEventOnDate(date, month, year) {
            return getEventsOnDate(date, month, year).length > 0;
        }

        function daysInMonth(iMonth, iYear) {
            return 32 - new Date(iYear, iMonth, 32).getDate();
        }

        function showPopup(date, month, year) {
            let eventsOnDate = getEventsOnDate(date, month, year);
            eventsOnDate.sort((a, b) => new Date(`1970-01-01T${a.time}`) - new Date(`1970-01-01T${b.time}`));

            let eventList = document.getElementById("eventList");
            eventList.innerHTML = "";

            eventsOnDate.forEach(event => {
                let listItem = document.createElement("li");
                listItem.innerHTML = `${event.time} - ${event.description}`;

					let deleteButton = document.createElement("button");
					deleteButton.textContent = "Delete";
					deleteButton.onclick = function () {
						if (confirm("Ești sigur/ă că vrei să ștergi acest eveniment?")) {
							deleteEvent(event.id); 
						}
					};
					listItem.appendChild(deleteButton);

                let usersForEvent = usersphp.filter(user => user.id === event.id);
                console.log(usersForEvent);
                if (usersForEvent.length > 0) {
                    let userList = document.createElement("ul");
                    usersForEvent.forEach(user => {
                        let userItem = document.createElement("li");
                        userItem.innerHTML = `${user.username}: ${user.displonibility}`;
                        userList.appendChild(userItem);
                    });
                    listItem.appendChild(userList);
                } else {
                    let noUsers = document.createElement("p");
                    noUsers.innerHTML = "Nici un utilizator valabil.";
                    listItem.appendChild(noUsers);
                }
                
                let joinButton = document.createElement("button");
                joinButton.innerHTML = "Join";
                joinButton.onclick = function () {
    joinButton.style.display = "none"; 

    let availabilityForm = document.createElement("div");

    let label = document.createElement("label");
    label.setAttribute("for", `availabilityStatus_${event.id}`);
    label.textContent = "Valabilitate:";
    availabilityForm.appendChild(label);

    let select = document.createElement("select");
    select.setAttribute("id", `availabilityStatus_${event.id}`);
    let options = [
        { value: "Valabil", text: "Liber" },
        { value: "Nevalabil", text: "Indisponibil" },
        { value: "Nesigur", text: "Nesigur" }
    ];

    options.forEach(optionData => {
        let option = document.createElement("option");
        option.value = optionData.value;
        option.textContent = optionData.text;
        select.appendChild(option);
    });

    availabilityForm.appendChild(select);

    let submitButton = document.createElement("button");
    submitButton.textContent = "Trimite";
    submitButton.onclick = function () {
        let availabilityStatus = select.value; 
        if (availabilityStatus) {
            sendAvailability(event.id, availabilityStatus); 
        }
    };

    availabilityForm.appendChild(submitButton);

    listItem.appendChild(availabilityForm);
};
                listItem.appendChild(joinButton);
                eventList.appendChild(listItem);
            });

            document.getElementById("popupDate").textContent = `${date} ${months[month]} ${year}`;
            document.getElementById("eventPopup").style.display = "block";
        }

        function closePopup() {
            document.getElementById("eventPopup").style.display = "none";
        }

        function showAvailabilityForm(eventId, eventTime) {
            document.getElementById("availabilitySection").style.display = "block";
            document.getElementById("eventList").style.display = "none";
            document.getElementById("popupDate").style.display = "none";
            window.currentEventId = eventId;
        }

        function sendAvailability(eventId, availabilityStatus) {
            let userId = <?php echo $user_id;?>;

            console.log("Sending data:", userId, eventId, availabilityStatus);

            $.ajax({
                type: 'POST',
                url: 'http://localhost/ford-falcon/php_ex/submit_availability.php',
                data: {
                    userId: userId,
                    eventId: eventId,
                    availabilityStatus: availabilityStatus
                },
                success: function(response) {
                    console.log("Response from PHP:", response);
                    alert(response); 
					window.location.reload();
                },
                error: function(xhr, status, error) {
                    console.error("Error:", xhr.status, error);
                    alert("Eroare la setarea valbilității. Vă rugăm încercați din nou.");
                }
            });
        }
		function deleteEvent(eventId) {
    let userId = <?php echo $user_id; ?>;

	if (event) {
        event.preventDefault();
    }

    $.ajax({
        type: 'POST',
        url: 'http://localhost/ford-falcon/php_ex/delete_event.php',
        data: {
            eventId: eventId,
            userId: userId
        },
        success: function (response) {
            console.log("Response from PHP:", response);
            alert(response); 
            window.location.reload(); 
        },
        error: function (xhr, status, error) {
            console.error("Error:", error);
            alert("Eroare la ștergerea evenimentului. Vă rugăm încercați din nou.");
        }
    });
}


        showCalendar(currentMonth, currentYear);
    </script>
</body>
</html>
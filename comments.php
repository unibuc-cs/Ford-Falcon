<?php
            include 'db.php';
            $calendar_id = $_GET['calendar_id'];
            $query = "SELECT c.*, u.username FROM comments c join user u ON c.user_id=u.id WHERE calendar_id = '$calendar_id'";
            $result = mysqli_query($conn, $query);
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<hr><p style='opacity:0.5;'>" . $row['created_at'] . "</p>";
                    echo "<p> <strong>" . $row['username'] . "</strong> : " . $row['comment'] . "</p>";
                }
            } else {
                echo "<p>Nu există comentarii.</p>";
            }
            mysqli_close($conn);
            ?>
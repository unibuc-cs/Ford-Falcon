<?php
$conn = mysqli_connect("localhost", "root", "root127", "calendar_mds");

if($conn === false){
    die("Eroare la conectare. " . mysqli_connect_error());
}
?>
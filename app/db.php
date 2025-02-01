<?php
$default_db = 'development';
if (getenv('IS_TESTING')) {
    $conn = mysqli_connect("localhost", "root", "", "test");
} elseif (getenv('DEVELOPMENT')) {
    $conn = mysqli_connect("localhost", "root", "", "development"); 
} elseif (getenv('PRODUCTION')) {
    $conn = mysqli_connect("localhost", "root", "", "production");
} else{
    $conn = mysqli_connect("localhost", "root", "", $default_db);
}

if($conn === false){
    die("Eroare la conectare. " . mysqli_connect_error());
}
?>
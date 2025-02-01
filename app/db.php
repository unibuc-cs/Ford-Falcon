<?php
if (getenv('IS_TESTING')) {
    // Testing environment - use the test database
    $conn = mysqli_connect("localhost", "root", "", "test");
} elseif (getenv('DEVELOPMENT')) {
    // Development environment - use the development database
    $conn = mysqli_connect("localhost", "root", "", "development"); 
} else {
    // Production environment - use the production database
    $conn = mysqli_connect("localhost", "root", "", "calendar_mds");
}

if($conn === false){
    die("Eroare la conectare. " . mysqli_connect_error());
}
?>
<?php
$default_db = 'development';
if (getenv('IS_TESTING')) {
    // Testing environment - use the test database
    $conn = mysqli_connect("localhost", "root", "", "test");
} elseif (getenv('DEVELOPMENT')) {
    // Development environment - use the development database
    $conn = mysqli_connect("localhost", "root", "", "development"); 
} elseif (getenv('PRODUCTION')) {
    // Production environment - use the production database
    $conn = mysqli_connect("localhost", "root", "", "production");
} else{
    $conn = mysqli_connect("localhost", "root", "", $default_db);
}

if($conn === false){
    die("Eroare la conectare. " . mysqli_connect_error());
}
?>
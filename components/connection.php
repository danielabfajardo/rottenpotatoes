<?php
    $host = "localhost";
    $username = "team22";
    $password = "team22";
    $dbname = "finaldb";

    $conn = mysqli_connect($host, $username, $password, $dbname);

    if (!$conn) {
        die("Connection failed!" . mysqli_connect_error());
    }
?>

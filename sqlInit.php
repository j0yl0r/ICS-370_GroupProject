<?php
    $conn = null;

    if($conn == null){
        // Initialize connection
        $conn = new mysqli("localhost", "root", "", "scm-db");
        if ($conn->connect_error) {
            die("<p>Connection failed: " . $conn->connect_error . "</p>");
        }
    }
?>
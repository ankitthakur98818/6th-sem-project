<?php
try {
    $conn = new PDO(
        "pgsql:host=localhost;port=5432;dbname=allnepalspices;options='--client_encoding=UTF8'",
        "postgres",
        "root",
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 5
        ]
    );

     echo "Database connected successfully";

} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

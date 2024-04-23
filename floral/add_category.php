<?php
session_start();
require_once('db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $categoryName = $_POST['Category'];

    $sqlInsertCategory = "INSERT INTO categories (CategoryName) VALUES (?)";
    $stmtInsertCategory = $conn->prepare($sqlInsertCategory);

    if ($stmtInsertCategory) {
        $stmtInsertCategory->bind_param("s", $categoryName);
        $stmtInsertCategory->execute();

        if ($stmtInsertCategory->affected_rows > 0) {
            $stmtInsertCategory->close();
            header("Location: admin_panel.php");
            exit();
        } else {
            echo "Błąd dodawania produktu.";
        }

        $stmtInsertCategory->close();
    } else {
        echo "Błąd przygotowania zapytania: " . $conn->error;
    }

    $stmtCheckCategory->close();
}

$conn->close();
?>
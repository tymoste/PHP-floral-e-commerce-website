<?php
session_start();
require_once('db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $productName = $_POST['productName'];
    $price = $_POST['price'];
    $inStock = 9999;
    $categoryID = $_POST['categoryID'];

    $imagePath = $_FILES['image']['tmp_name'];
    $imageData = file_get_contents($imagePath);

    $sqlInsertProduct = "INSERT INTO products (ProductName, Price, InStock, image, CategoryID) VALUES (?, ?, ?, ?, ?)";
    $stmtInsertProduct = $conn->prepare($sqlInsertProduct);

    if ($stmtInsertProduct) {
        $stmtInsertProduct->bind_param("sdisi", $productName, $price, $inStock, $imageData, $categoryID);
        $stmtInsertProduct->execute();

        if ($stmtInsertProduct->affected_rows > 0) {
            $stmtInsertProduct->close();
            header("Location: admin_panel.php");
            exit();
        } else {
            echo "Błąd dodawania produktu.";
        }

        $stmtInsertProduct->close();
    } else {
        echo "Błąd przygotowania zapytania: " . $conn->error;
    }
}

$conn->close();
?>

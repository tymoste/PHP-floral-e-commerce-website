<?php
session_start();
require_once('db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $productID = substr($_POST['prodID'], 0, -1);
    $productName = $_POST['productName'];
    $price = $_POST['price'];
    $inStock = 9999;
    $categoryID = $_POST['categoryID'];

    $imagePath = $_FILES['image']['tmp_name'];
    $imageData = file_get_contents($imagePath);

    $sqlInsertProduct = "UPDATE products SET ProductName = ?, Price = ?, InStock = ?, image = ?, CategoryID = ? WHERE ProductID = $productID";
    echo gettype($productID);
    $stmtInsertProduct = $conn->prepare($sqlInsertProduct);

    if ($stmtInsertProduct) {
        $stmtInsertProduct->bind_param("sdisi", $productName, $price, $inStock, $imageData, $categoryID);
        $stmtInsertProduct->execute();

        if ($stmtInsertProduct->affected_rows > 0) {
            $stmtInsertProduct->close();
            header("Location: index.php");
            exit();
        } else {
            echo "Błąd dodawania produktu.";
        }

        $stmtInsertProduct->close();
    } else {
        echo "Błąd przygotowania zapytania: " . $conn->error;
    }

    $stmtCheckCategory->close();
}

$conn->close();
?>
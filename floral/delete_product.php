<?php
session_start();
require_once('db.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['prodID'])) {
    $productID = $_POST['prodID'];

    $sqlDelete = "UPDATE products SET CategoryID = 9999 WHERE ProductID = ?";
    $stmtDelete = $conn->prepare($sqlDelete);
    $stmtDelete->bind_param("i", $productID);

    if ($stmtDelete->execute()) {
        $stmtDelete->close();
        header("Location: index.php");
        exit();
    } else {
        echo "Błąd usuwania adresu: " . $stmtDelete->error;
    }
} else {
    echo "Błąd: Brak przekazanych parametrów.";
}
?>
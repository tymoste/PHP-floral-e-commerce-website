<?php
session_start();
require_once('db.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['CategoryID'])) {
    $CategoryID = $_GET['CategoryID'];

    $sqlUpdateProducts = "UPDATE products SET CategoryID = 999 WHERE CategoryID = ?";
    $stmtUpdateProducts = $conn->prepare($sqlUpdateProducts);
    $stmtUpdateProducts->bind_param("i", $CategoryID);
    if(!$stmtUpdateProducts->execute()){
        echo "Błąd zmiany kategorii: " . $stmtUpdateProducts->error;
    }

    $sqlDelete = "DELETE FROM categories WHERE CategoryID = ?";
    $stmtDelete = $conn->prepare($sqlDelete);
    $stmtDelete->bind_param("i", $CategoryID);

    if ($stmtDelete->execute()) {
        $stmtDelete->close();
        header("Location: admin_panel.php");
        exit();
    } else {
        echo "Błąd usuwania kategorii: " . $stmtDelete->error;
    }
} else {
    echo "Błąd: Brak przekazanych parametrów.";
}
?>

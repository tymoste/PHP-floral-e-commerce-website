<?php
    session_start();
    require_once('db.php');

    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    $cartID = $_SESSION['cartID'];
    $productID = $_POST['prodID'];
    $quantity = $_POST['quantity'];

    $sqlSelect = "SELECT * FROM cartitems WHERE cartID = ? AND ProductID = ?";
    $stmtSelect = $conn->prepare($sqlSelect);
    $stmtSelect->bind_param("ii", $cartID, $productID);
    $stmtSelect->execute();
    $item = $stmtSelect->get_result();

    if($item->num_rows > 0){
        $row = $item->fetch_assoc();
        $newQuantity = $quantity + intval($row['Quantity']);
        $sqlUpadate = "UPDATE cartitems SET Quantity = ? WHERE cartID = ? AND ProductID = ?";
        $stmtUpdate = $conn->prepare($sqlUpadate);
        $stmtUpdate->bind_param("iii", $newQuantity, $cartID, $productID);
        $stmtUpdate->execute();
        header("Location: index.php");
        exit();
    } else {
        $sqlInsert = "INSERT INTO cartitems (cartID, ProductID, Quantity) VALUES (?, ?, ?)";
        $stmtInsert = $conn->prepare($sqlInsert);
        $stmtInsert->bind_param("iii", $cartID, $productID, $quantity);
        $stmtInsert->execute();
        header("Location: index.php");
        exit();
    }

?>
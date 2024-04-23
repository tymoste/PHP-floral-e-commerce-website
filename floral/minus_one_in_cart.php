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

    $newQuantity = intval($quantity) - 1;

    if($newQuantity <= 0){
        $sql = "DELETE FROM cartitems WHERE cartID = ? AND ProductID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $cartID, $productID);
        $stmt->execute();
        $stmt->close();
        header("Location: cart.php");
        exit();
    } else{
        $sql = "UPDATE cartitems SET Quantity = ? WHERE cartID = ? AND ProductID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $newQuantity, $cartID, $productID);
        $stmt->execute();
        $stmt->close();
        header("Location: cart.php");
        exit();
    }
?>
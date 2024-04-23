<?php
session_start();
require_once('db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $clientID = $_SESSION['clientID'];
    $contact = $_POST['contact'];
    $contactArray = explode(",", $contact);
    print_r($contactArray);
    $city = $contactArray[0];
    $zipcode = $contactArray[1];
    $street = $contactArray[2];
    $phoneNumber = $_POST['phoneNumber'];
    //$orderDate = "NOW()";
    $total = $_POST['total'];
    $payment = $_POST['payment'];
    $statusID = 1;
   
    $sqlInsert = "INSERT INTO orders(ClientID, City, ZipCode, Street, PhoneNumber, OrderDate, Total, PaymentMethod, StatusID) VALUES (?, ?, ?, ?, ?, NOW(), ?, ?, ?)";
    $stmtInsert = $conn->prepare($sqlInsert);
    if ($stmtInsert) {
        $stmtInsert->bind_param("issssdii", $clientID, $city, $zipcode, $street, $phoneNumber, $total, $payment, $statusID);
        $stmtInsert->execute();
        $orderID = $conn->insert_id;

        if ($stmtInsert->affected_rows > 0) {
            $stmtInsert->close();

            $sqlSelect = "SELECT * FROM cartitems WHERE cartID = ?";
            $stmtSelect = $conn->prepare($sqlSelect);
            $stmtSelect->bind_param("i", $_SESSION['cartID']);
            $stmtSelect->execute();
            $selectResult = $stmtSelect->get_result();
            while($row = $selectResult->fetch_assoc()){
                $sqlInsertItem = "INSERT INTO orderitems VALUES (?, ?, ?)";
                $stmtInsertItem = $conn->prepare($sqlInsertItem);
                $stmtInsertItem->bind_param("iii", $orderID, $row['ProductID'], $row['Quantity']);
                $stmtInsertItem->execute();
                $stmtInsertItem->close();
            }
            
            $sqlSelect = "DELETE FROM cartitems WHERE cartID = ?";
            $stmtSelect = $conn->prepare($sqlSelect);
            $stmtSelect->bind_param("i", $_SESSION['cartID']);
            $stmtSelect->execute();

            header("Location: thank_you.php");
            exit();
        } else {
            echo "Błąd dodawania produktu.";
        }
    } else {
        echo "Błąd przygotowania zapytania: " . $conn->error;
    }
}

$conn->close();
?>
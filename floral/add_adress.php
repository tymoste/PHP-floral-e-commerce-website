<?php
session_start();
require_once('db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userID = $_SESSION['user_id'];
$city = $_POST['City'];
$zipcode = $_POST['ZipCode'];
$street = $_POST['Street'];
$phoneNumber = $_POST['PhoneNumber'];

$sqlClientID = "SELECT * FROM clients WHERE UserID = ?";
$stmtClientID = $conn->prepare($sqlClientID);
$stmtClientID->bind_param("i", $userID);
$stmtClientID->execute();
$resultClientID = $stmtClientID->get_result();

if ($resultClientID->num_rows > 0) {
    $rowClientID = $resultClientID->fetch_assoc();
    $clientID = $rowClientID['ClientID'];

    $sqlInsert = "INSERT INTO contacts (ClientID, City, ZipCode, Street, PhoneNumber) VALUES (?, ?, ?, ?, ?)";
    $stmtInsert = $conn->prepare($sqlInsert);
    $stmtInsert->bind_param("issss", $clientID, $city, $zipcode, $street, $phoneNumber);

    if ($stmtInsert->execute()) {
        header("Location: account.php");
        exit();
    } else {
        echo "Błąd aktualizacji danych: " . $stmtInsert->error;
    }

    $stmtInsert->close();
} else {
    echo "Błąd: Użytkownik nie istnieje.";
}

$stmtClientID->close();
$conn->close();
?>

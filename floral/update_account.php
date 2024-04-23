<?php
session_start();
require_once('db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userID = $_SESSION['user_id'];
$name = $_POST['name'];
$surname = $_POST['surname'];

$sqlUpdate = "UPDATE clients SET Name = ?, Surname = ? WHERE UserID = ?";
$stmt = $conn->prepare($sqlUpdate);

if ($stmt) {
    $stmt->bind_param("ssi", $name, $surname, $userID);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $stmt->close();
        header("Location: account.php");
        exit();
    } else {
        echo "Błąd aktualizacji danych.";
    }

    $stmt->close();
} else {
    echo "Błąd przygotowania zapytania: " . $conn->error;
}

$conn->close();
?>

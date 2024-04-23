<?php
session_start();
require_once('db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['clientID']) && isset($_GET['contactID'])) {
    $clientID = $_GET['clientID'];
    $contactID = $_GET['ContactID'];

    $sqlDelete = "DELETE FROM contacts WHERE ClientID = ? AND ContactID = ?";
    $stmtDelete = $conn->prepare($sqlDelete);
    $stmtDelete->bind_param("ii", $clientID, $contactID);

    if ($stmtDelete->execute()) {
        $stmtDelete->close();
        header("Location: account.php");
        exit();
    } else {
        echo "Błąd usuwania adresu: " . $stmtDelete->error;
    }
} else {
    echo "Błąd: Brak przekazanych parametrów.";
}
?>

<?php
session_start();
require_once('db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userID = $_SESSION['user_id'];
$sqlUser = "SELECT * FROM clients WHERE UserID = ?";
$stmtUser = $conn->prepare($sqlUser);
$stmtUser->bind_param("i", $userID);
$stmtUser->execute();
$resultUser = $stmtUser->get_result();

if ($resultUser->num_rows > 0) {
    $userData = $resultUser->fetch_assoc();
} else {
    echo "Błąd: Użytkownik nie istnieje.";
    exit();
}

$userName = isset($userData['Name']) ? htmlspecialchars($userData['Name'], ENT_QUOTES, 'UTF-8') : "Nie ustawiono";
$userSurname = isset($userData['Surname']) ? htmlspecialchars($userData['Surname'], ENT_QUOTES, 'UTF-8') : "Nie ustawiono";

$sqlContacts = "SELECT * FROM contacts INNER JOIN clients ON contacts.ClientID = clients.ClientID INNER JOIN users ON users.UserID = clients.UserID WHERE users.userID = ?";
$stmtContacts = $conn->prepare($sqlContacts);
$stmtContacts->bind_param("i", $userID);
$stmtContacts->execute();
$resultContacts = $stmtContacts->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css?<?php echo time(); ?>">
    <title>Twoje Konto</title>
</head>
<body>

<nav>
        <a href = "index.php"><img class = "logo" src="logo.png" alt="LOGO"></a>
        <ul>
            <li><a href="index.php">Strona Główna</a></li>
            <li><a href="contact.php">Kontakt</a></li>
            <?php
                if(!isset($_SESSION['user_email'])){
                    echo "<li><a href='register.php'>Rejestracja</a></li>";
                    echo "<li><a href='login.php'>Logowanie</a></li>";
                }
            ?>
            <?php
                if (isset($_SESSION['user_email'])){
                    if($_SESSION['role'] == 2){
                        echo "<li><a href='account.php'>Twoje konto</a></li>";
                        echo "<li><a href='cart.php'>Koszyk</a></li>";
                    }
                    else {
                        echo "<li><a href='admin_panel.php'>Panel sterowania</a></li>";
                    }
                    echo "<li><a href='logout.php'>Wyloguj</a></li>";
                }
            ?>
        </ul>
    </nav>

    <section id="account">
        <div class="nameChange">
            <h2>Twoje Konto</h2>
            <form action="update_account.php" method="post">
                <div class="form-group">
                    <label for="name">Imię:</label>
                    <input type="text" id="name" name="name" value="<?php echo $userName; ?>" required>
                </div>
                <div class="form-group">
                    <label for="surname">Nazwisko:</label>
                    <input type="text" id="surname" name="surname" value="<?php echo $userSurname; ?>" required>
                </div>
                <button type="submit">Zapisz zmiany</button>
            </form>
        </div>
        <div class="manageAddress">
            <h2>Twoje adresy</h2>
            <div class="allControlers">
                <form action="add_adress.php" method="post">
                    <div class="newAddress">
                        <label for="City">Miasto</label>
                        <input type="text" id="City" name="City" required>
                        <label for="ZipCode">Kod pocztowy</label>
                        <input type="text" pattern="\d{2}-\d{3}" title="Podaj kod pocztowy w formacie 00-000" id="ZipCode" name="ZipCode" required>
                        <label for="Street">Ulica</label>
                        <input type="text" id="Street" name="Street" required>
                        <label for="PhoneNumber">Numer telefonu</label>
                        <input type="text" pattern="\d{9}" id="PhoneNumber" name="PhoneNumber" placeholder="123456789" required>
                    </div>
                    <button type="submit">Dodaj adres i numer telefonu</button>
                </form>
                <div class="myAddress">
                    <?php
                    if($resultContacts->num_rows > 0){
                        while($contact = $resultContacts->fetch_assoc()){
                            echo "<div class='address'>";
                            echo "<div class = 'addressInfo'>";
                            echo "Adres: " . htmlspecialchars("{$contact['City']}, {$contact['ZipCode']}, {$contact['Street']}", ENT_QUOTES, 'UTF-8') . " ";
                            echo "Telefon: " . htmlspecialchars($contact['PhoneNumber'], ENT_QUOTES, 'UTF-8');
                            echo "</div>";
                            echo "<button onclick='deleteAddress({$contact['ClientID']}, {$contact['ContactID']})'>Usuń</button>";
                            echo "</div>";
                        }
                    } else {
                        echo "<div class='address'>";
                        echo "Brak adresów";
                        echo "</div>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Simply Vintage Co. Wszelkie prawa zastrzeżone.</p>
    </footer>

    <script>
        function deleteAddress(clientID, addressID) {
            var confirmDelete = confirm("Czy na pewno chcesz usunąć ten adres?");
            if (confirmDelete) {
                window.location.href = "delete_adress.php?clientID=" + clientID + "&contactID=" + addressID;
            }
        }
    </script>

</body>
</html>

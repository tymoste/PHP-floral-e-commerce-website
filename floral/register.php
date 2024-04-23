<?php
session_start();
require_once('db.php');

function registerUser($name, $surname, $email, $password) {
    global $conn;

    $name = mysqli_real_escape_string($conn, $name);
    $surname = mysqli_real_escape_string($conn, $surname);
    $email = mysqli_real_escape_string($conn, $email);

    $sqlCheckEmail = "SELECT * FROM users WHERE Email = '$email'";
    $resultCheckEmail = $conn->query($sqlCheckEmail);

    if ($resultCheckEmail->num_rows > 0) {
        echo "Użytkownik o takim adresie e-mail już istnieje!";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (Email, Password, RoleID) VALUES (?, ?, 2)");
        $stmt->bind_param("ss", $email, $hashedPassword);
        $stmt->execute();
        $stmt->close();

        $userId = $conn->insert_id;

        $stmt = $conn->prepare("INSERT INTO clients (Name, Surname, UserID) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $name, $surname, $userId);
        $stmt->execute();
        $stmt->close();
        
        $clientId = $conn->insert_id;

        $stmt = $conn->prepare("INSERT INTO cart (ClientID) VALUES (?)");
        $stmt->bind_param("i", $clientId);
        $stmt->execute();
        $stmt->close();

        header('Location: index.php');
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    registerUser($name, $surname, $email, $password);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css?<?php echo time(); ?>">
    <title>Rejestracja - Simply Vintage Co.</title>
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

    <section id="registration" class="container">
        <h2>Rejestracja</h2>
        <form method="post" action="register.php" class="registration-form">
            <div class="form-group">
                <label for="name">Imię:</label>
                <input type="text" name="name" required>
            </div>

            <div class="form-group">
                <label for="surname">Nazwisko:</label>
                <input type="text" name="surname" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="password">Hasło:</label>
                <input type="password" name="password" required>
            </div>

            <button type="submit" name="register">Zarejestruj się</button>
        </form>
    </section>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Simply Vintage Co. Wszelkie prawa zastrzeżone.</p>
    </footer>


</body>
</html>

<?php
session_start();
require_once('db.php');

function loginUser($email, $password) {
    global $conn;

    $stmt = $conn->prepare("SELECT * FROM users INNER JOIN clients ON users.UserID = clients.UserID WHERE Email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['Password'])) {

            $stmt2 = $conn->prepare("SELECT * FROM cart INNER JOIN clients ON cart.ClientID = clients.ClientID WHERE clients.UserID = ?");
            $stmt2->bind_param("i", $row['UserID']);
            $stmt2->execute();
            $res = $stmt2->get_result();
            $row2 = $res->fetch_assoc();

            $_SESSION['user_id'] = $row['UserID'];
            $_SESSION['user_email'] = $row['Email'];
            $_SESSION['role'] = $row['RoleID'];
            $_SESSION['cartID'] = $row2['CartID'];
            header('Location: index.php');
            exit();
        } else {
            echo '<script>alert("Błędne hasło")</script>';
            exit();
        }
    } else {
        echo '<script>alert("Brak użytkownika o podanym adresie email")</script>';
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css?<?php echo time(); ?>">
    <title>Logowanie - Simply Vintage Co.</title>
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

    <section id="login" class="container">
        <h2>Logowanie</h2>
        <form method="post" action="login.php" class="login-form">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="password">Hasło:</label>
                <input type="password" name="password" required>
            </div>

            <button type="submit" name="login">Zaloguj się</button>
        </form>
    </section>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Simply Vintage Co. Wszelkie prawa zastrzeżone.</p>
    </footer>

</body>
</html>

<?php
// Obsługa formularza logowania
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    loginUser($email, $password);
}
?>

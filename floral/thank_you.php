<?php
session_start();
require_once('db.php');

$sqlCategories = "SELECT * FROM categories";
$resultCategories = $conn->query($sqlCategories);

$selectedCategoryID = '%';

if (isset($_GET['category']) && is_numeric($_GET['category'])) {
    $selectedCategoryID = $_GET['category'];
}

$sqlProducts = "SELECT * FROM products WHERE CategoryID like ?";
$stmtProducts = $conn->prepare($sqlProducts);
$stmtProducts->bind_param("s", $selectedCategoryID);
$stmtProducts->execute();
$resultProducts = $stmtProducts->get_result();
$stmtProducts->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css?<?php echo time(); ?>">
    <title>Dziękujemy - Simply Vintage Co.</title>
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

    <section id="thankYou">
                <h1>Dziękujemy za złożone zamówienie!</h1>
    </section>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Simply Vintage Co. Wszelkie prawa zastrzeżone.</p>
    </footer>

</body>
</html>

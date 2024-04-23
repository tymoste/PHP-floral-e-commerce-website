<?php
session_start();
require_once('db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$total = 0;

$sqlSelect = "SELECT * FROM cartitems INNER JOIN products ON cartitems.ProductID = products.ProductID WHERE cartID = ?";
$stmtSelect = $conn->prepare($sqlSelect);
$stmtSelect->bind_param("i", $_SESSION['cartID']);
$stmtSelect->execute();
$result = $stmtSelect->get_result();
if($result->num_rows > 0){
    while($row = $result->fetch_assoc()){
        $total += $row['Quantity']*$row['Price'];
    }
}


$sqlSelect = "SELECT * FROM clients INNER JOIN users ON users.userID = clients.userID WHERE clients.userID = ?";
$stmtSelect = $conn->prepare($sqlSelect);
$stmtSelect->bind_param("i", $_SESSION['user_id']);
$stmtSelect->execute();
$result = $stmtSelect->get_result();
$row = $result->fetch_assoc();
$_SESSION['clientID'] = $clientID = $row['ClientID'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css?<?php echo time(); ?>">
    <title>Koszyk</title>
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

    <section id="cart">
        <div class="cartProducts">
            <h2>Koszyk</h2>
            <div class="cartProductsWraper">
                <?php
                    $sqlSelect = "SELECT * FROM cartitems INNER JOIN products ON cartitems.ProductID = products.ProductID WHERE cartID = ?";
                    $stmtSelect = $conn->prepare($sqlSelect);
                    $stmtSelect->bind_param("i", $_SESSION['cartID']);
                    $stmtSelect->execute();
                    $result = $stmtSelect->get_result();

                    if($result->num_rows > 0){
                        while($row = $result->fetch_assoc()){
                            echo "<div class = 'cartProduct'>";
                                echo "<div class='product'>";
                                    echo "<img src='data:image/jpeg;base64," . base64_encode($row['image']) . "' alt='" . htmlspecialchars($row['ProductName'], ENT_QUOTES, 'UTF-8') . "'>";
                                    echo "<h3>" . htmlspecialchars($row['ProductName'], ENT_QUOTES, 'UTF-8') . "</h3>";
                                echo "</div>";
                                echo "<div class = cartProductControls>";
                                    echo "<form action = 'delete_from_cart.php' method = 'post'>";
                                        echo "<input type = 'hidden' name = 'prodID' id = 'prodID' value = '{$row['ProductID']}'>";
                                        echo "<button type = 'submit' class = 'delCartBtn'>Usuń</button>";
                                    echo "</form>";
                                    echo "<form action = 'minus_one_in_cart.php' method = 'post'>";
                                        echo "<input type = 'hidden' name = 'prodID' id = 'prodID' value = '{$row['ProductID']}'>";
                                        echo "<input type = 'hidden' name = 'quantity' id = 'quantity' value = '{$row['Quantity']}'>";
                                        echo "<button type = 'submit' class = 'minusCartBtn'><span>&#8722;</span></button>";
                                    echo "</form>";
                                    echo "<p>{$row['Quantity']}</p>";
                                    echo "<form action = 'plus_one_in_cart.php' method = 'post'>";
                                        echo "<input type = 'hidden' name = 'prodID' id = 'prodID' value = '{$row['ProductID']}'>";
                                        echo "<input type = 'hidden' name = 'quantity' id = 'quantity' value = '{$row['Quantity']}'>";
                                        echo "<button type = 'submit' class = 'plusCartBtn'><span>&#43;</span></button>";
                                    echo "</form>";
                                echo "</div>";
                            echo "</div>";
                        }
                    } else {
                        echo "Brak produktów w twoim koszyku";
                    }
                ?>
            </div>
        </div>
        <div class="orderForm">
                <h2>Szczegóły zamówienia</h2>
                <form action="order.php" method="post">
                        <label for="total">Wartość zamówienia: <?php echo $total ?></label>
                        <input type="hidden" id = "total" name = "total" value = "<?php echo $total ?>">
                        <br>
                        <label for="contact">Wybierz adres:</label>
                        <select name="contact" id="contact">
                            <?php
                            $sqlSelect = "SELECT * FROM contacts WHERE clientID = ?";
                            $stmtSelect = $conn->prepare($sqlSelect);
                            $stmtSelect->bind_param("i", $clientID);
                            $stmtSelect->execute();
                            $resultContacts = $stmtSelect->get_result();
                            while ($rowContact = $resultContacts->fetch_assoc()) {
                                echo "<option value='" . htmlspecialchars($rowContact['City'], ENT_QUOTES, 'UTF-8') . "," . htmlspecialchars($rowContact['ZipCode'], ENT_QUOTES, 'UTF-8') . "," . htmlspecialchars($rowContact['Street'], ENT_QUOTES, 'UTF-8') . "'>" . htmlspecialchars($rowContact['City'], ENT_QUOTES, 'UTF-8') . " " . htmlspecialchars($rowContact['Street'], ENT_QUOTES, 'UTF-8') . "</option>";
                            }
                            ?>
                        </select>
                        <br>
                        <label for="phoneNumber">Wybierz telefon:</label>
                        <select name="phoneNumber" id="phoneNumber">
                            <?php
                            $sqlSelect = "SELECT * FROM contacts WHERE clientID = ?";
                            $stmtSelect = $conn->prepare($sqlSelect);
                            $stmtSelect->bind_param("i", $clientID);
                            $stmtSelect->execute();
                            $resultContacts = $stmtSelect->get_result();
                            while ($rowContact = $resultContacts->fetch_assoc()) {
                                echo "<option value='" . htmlspecialchars($rowContact['PhoneNumber'], ENT_QUOTES, 'UTF-8') . "'>" . htmlspecialchars($rowContact['PhoneNumber'], ENT_QUOTES, 'UTF-8') . "</option>";
                            }
                            ?>
                        </select>
                        <br>
                        <label for="payment">Wybierz metodę płatności:</label>
                        <select name="payment" id="payment">
                            <?php
                            $sqlSelect = "SELECT * FROM paymentmethods";
                            $stmtSelect = $conn->prepare($sqlSelect);
                            $stmtSelect->execute();
                            $resultMethods = $stmtSelect->get_result();
                            while ($rowMethods = $resultMethods->fetch_assoc()) {
                                echo "<option value='" . htmlspecialchars($rowMethods['MethodID'], ENT_QUOTES, 'UTF-8') . "'>" . htmlspecialchars($rowMethods['MethodName'], ENT_QUOTES, 'UTF-8') . "</option>";
                            }
                            ?>
                        </select>
                        <br>
                        <button type = 'submit' class = 'orderBtn'>Złóż zamówienie</button>
                </form>
        </div>
    </section>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Simply Vintage Co. Wszelkie prawa zastrzeżone.</p>
    </footer>
</body>
</html>

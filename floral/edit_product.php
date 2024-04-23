<?php
session_start();
require_once('db.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: login.php");
    exit();
}

$productID = $_POST['prodID'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css?<?php echo time(); ?>">
    <title>Panel Administratora</title>
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

    <section id="edit">
        <h2>Edytuj produkt</h2>
        <form action="edit_product_handler.php" method="post" enctype="multipart/form-data">
            <?php

                $sqlProduct = "SELECT * FROM products WHERE ProductID = ?";
                $stmtProduct = $conn->prepare($sqlProduct);
                $stmtProduct->bind_param("i", $productID);
                $stmtProduct->execute();
                $resultProduct = $stmtProduct->get_result();
                $stmtProduct->close();
                $rowProduct = $resultProduct->fetch_assoc();

                echo "<input type = 'hidden' id='prodID' name='prodID' value = $productID>";
                echo "<label for='productName'>Nazwa produktu:</label>";
                echo "<input type='text' name='productName' value = {$rowProduct['ProductName']} required>";

                echo "<label for='price'>Cena:</label>";
                echo "<input type='number' step='0.01' name='price' value = {$rowProduct['Price']} required>";

                // echo "<label for='inStock'>Dostępne:</label>";
                // echo "<input type='number' name='inStock' value = {$row['InStock']} required>";

                echo "<label for='categoryID'>Kategoria:</label>";

                $sqlCategories = "SELECT * FROM categories";
                $resultCategories = $conn->query($sqlCategories);
                
                if ($resultCategories->num_rows > 0) {
                    echo "<select name='categoryID' required>";
                
                    while ($row = $resultCategories->fetch_assoc()) {
                        $categoryID = $row['CategoryID'];
                        $categoryName = $row['CategoryName'];

                        $selected = $categoryID == $rowProduct['CategoryID']?"selected":"";

                        if($row['CategoryID'] == 9999){
                        }else{
                            echo "<option value='$categoryID' $selected>$categoryName</option>";
                        }
                    }
                
                    echo "</select>";
                } else {
                    echo "No categories available.";
                }        
            

            echo "<label for='image'>Obraz:</label>";
            echo "<input type='file' name='image' accept='image/*' required>"
            ?>
            <button type="submit">Edytuj</button>
        </form>
    </section>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Simply Vintage Co. Wszelkie prawa zastrzeżone.</p>
    </footer>

</body>
</html>

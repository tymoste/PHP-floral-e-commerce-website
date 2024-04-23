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
    <title>Simply Vintage Co.</title>
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

    <section id="products">
        <div class="indexContainer">
            <div class="empty"></div>
            <h1>Nasze produkty</h1>
            <div class="selector">
                <label for="category">Wybierz kategorię:</label>
                <select name="category" id="category" onchange="changeCategory(this.value)">
                    <option value="0">Wszystkie</option>
                    <?php
                    while ($rowCategory = $resultCategories->fetch_assoc()) {
                        $selected = ($selectedCategoryID == $rowCategory['CategoryID']) ? "selected" : "";
                        if($rowCategory['CategoryID'] == 999 && $_SESSION['role'] != 1 || $rowCategory['CategoryID'] == 9999){
                        }else{
                            echo "<option value='" . htmlspecialchars($rowCategory['CategoryID'], ENT_QUOTES, 'UTF-8') . "' $selected>" . htmlspecialchars($rowCategory['CategoryName'], ENT_QUOTES, 'UTF-8') . "</option>";
                        }
                        
                    }
                    ?>
                </select>
            </div>
        </div>
        <?php
        if ($resultProducts->num_rows > 0) {
            while ($row = $resultProducts->fetch_assoc()) {
                if($row['CategoryID'] != 9999){
                    echo "<div class='product'>";
                        echo "<img src='data:image/jpeg;base64," . base64_encode($row['image']) . "' alt='" . htmlspecialchars($row['ProductName'], ENT_QUOTES, 'UTF-8') . "'>";
                        echo "<div class = 'productInfo'>";
                            echo "<h3>" . htmlspecialchars($row['ProductName'], ENT_QUOTES, 'UTF-8') . "</h3>";
                            echo "<p>Cena: " . htmlspecialchars($row['Price'], ENT_QUOTES, 'UTF-8') . "</p>";
                        echo "</div>";
                        if(isset($_SESSION['user_id'])) {
                            if($_SESSION['role'] == 1) {
                                echo "<div class = 'productControls'>";
                                    echo "<form action='delete_product.php' method='post'>";
                                    echo "<input type='hidden' id='prodID' name='prodID' value=". htmlspecialchars($row['ProductID'], ENT_QUOTES, 'UTF-8') ."/>";
                                    echo "<button type = 'submit' class = 'delProdBtn'>Usuń</button>";
                                    echo "</form>";
                                    echo "<form action='edit_product.php' method='post'>";
                                    echo "<input type='hidden' id='prodID' name='prodID' value=". htmlspecialchars($row['ProductID'], ENT_QUOTES, 'UTF-8') ."/>";
                                    echo "<button type = 'submit' class = 'editProdBtn'>Edytuj</button>";
                                    echo "</form>";
                                echo "</div>";
                            } else {
                                echo "<div class = 'productControls'>";
                                    echo "<form action='add_to_cart.php' method='post'>";
                                    echo "<input type='hidden' id='prodID' name='prodID' value=". htmlspecialchars($row['ProductID'], ENT_QUOTES, 'UTF-8') ."/>";
                                    echo "<div class = 'number-input'>";
                                    $funUp = "this.parentNode.querySelector('input[type=number]').stepUp()";
                                    $funDown = "this.parentNode.querySelector('input[type=number]').stepDown()";
                                        echo "<div onclick=$funDown ><span>&#8722;</span></div>";
                                        echo "<input class='quantity' min='1' name='quantity' max = ". htmlspecialchars($row['InStock'], ENT_QUOTES, 'UTF-8') ." value='1' type='number' required>";
                                        echo "<div onclick=$funUp class='plus'><span>&#43;</span></div>";
                                    echo "</div>";
                                    echo "<button type = 'submit' class = 'addToCart'>Dodaj do koszyka</button>";
                                    echo "</form>";   
                                echo "</div>"; 
                            }
                        }
                    echo "</div>";
                }
            }
        } else {
            echo "Brak dostępnych produktów.";
        }
        ?>
    </section>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Simply Vintage Co. Wszelkie prawa zastrzeżone.</p>
    </footer>

    <script>
        function changeCategory(categoryID) {
            if(categoryID == 0){
                window.location.href = "index.php"
            }else{
                window.location.href = "index.php?category=" + encodeURIComponent(categoryID);
            }
        }
    </script>

</body>
</html>

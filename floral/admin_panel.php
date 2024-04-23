<?php
session_start();
require_once('db.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: login.php");
    exit();
}

// $sqlOrders = "SELECT * FROM orders";
// $resultOrders = $conn->query($sqlOrders);
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

    <section id="panel">
        <div class="addProduct">
            <h2>Dodaj produkt</h2>
            <form action="add_product.php" method="post" enctype="multipart/form-data">
                <label for="productName">Nazwa produktu:</label>
                <input type="text" name="productName" required>

                <label for="price">Cena:</label>
                <input type="number" step="0.01" name="price" required>

                <!-- <label for="inStock">Dostępne:</label>
                <input type="number" name="inStock" required> -->

                <label for="categoryID">Kategoria:</label>
                <?php
                    $sqlCategories = "SELECT * FROM categories";
                    $resultCategories = $conn->query($sqlCategories);
                    
                    if ($resultCategories->num_rows > 0) {
                        echo "<select name='categoryID' required>";
                    
                        while ($row = $resultCategories->fetch_assoc()) {
                            $categoryID = $row['CategoryID'];
                            $categoryName = $row['CategoryName'];
                            
                            if($categoryID != 999 && $categoryID != 9999){
                                echo "<option value='$categoryID'>$categoryName</option>";
                            }
                        }
                    
                        echo "</select>";
                    } else {
                        echo "Brak kategorii.";
                    }        
                ?>

                <label for="image">Obraz:</label>
                <input type="file" name="image" accept="image/*" required>

                <button type="submit">Dodaj produkt</button>
            </form>
        </div>
        <div class="manageCategories">
            <h2>Zarządzaj kategoriami</h2>
            <form action="add_category.php" method = "POST">
                    <div class="newCategory">
                        <label for="Category">Kategoria</label>
                        <input type="text" id="Category" name="Category" required>
                        <button type="submit">Dodaj kategorię</button>
                    </div>
                    <div class="categories">
                        <?php
                            $sqlCategories = "SELECT * FROM categories WHERE CategoryName NOT LIKE 'inne' AND CategoryName NOT LIKE 'DELETED'";
                            $resultCategories = $conn->query($sqlCategories);
                            if($resultCategories->num_rows > 0){
                                while($category = $resultCategories->fetch_assoc()){
                                    echo "<div class='category'>";
                                    echo "<div class = 'categoryName'>" . htmlspecialchars($category['CategoryName'], ENT_QUOTES, 'UTF-8') . "</div>";
                                    echo "<button onclick='deleteCategory({$category['CategoryID']})'>Usuń</button>";
                                    echo "</div>";
                                }
                            } else {
                                echo "<div class='category'>";
                                echo "Brak kategorii";
                                echo "</div>";
                            }
                        ?>
                    </div>
            </form>
        </div>
    </section>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Simply Vintage Co. Wszelkie prawa zastrzeżone.</p>
    </footer>

    <script>
        function deleteCategory(CategoryID) {
            var confirmDelete = confirm("Czy na pewno chcesz usunąć tę kategorię?");
            if (confirmDelete) {
                window.location.href = "delete_category.php?CategoryID=" + CategoryID;
            }
        }
    </script>

</body>
</html>

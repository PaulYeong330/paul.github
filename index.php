<?php
require_once 'config.php';

// Fetch all products from database using prepared statement
try {
    $stmt = $pdo->prepare("SELECT * FROM products");
    $stmt->execute();
    $products = $stmt->fetchAll();
} catch(PDOException $e) {
    error_log("Error fetching products: " . $e->getMessage());
    $products = []; // Show empty products instead of crashing
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(STORE_NAME) ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <div class="container">
            <h1><?= e(STORE_NAME) ?></h1>
            <nav>
                <ul>
                    <li><a href="index.php">Products</a></li>
                    <li><a href="cart.php">Cart <?php 
                        $cartCount = 0;
                        if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
                            foreach ($_SESSION['cart'] as $item) {
                                $cartCount += $item['quantity'];
                            }
                        }
                        if ($cartCount > 0) echo "(" . e($cartCount) . ")";
                    ?></a></li>
                </ul>
            </nav>
        </div>
    </header>
    
    <main>
        <div class="container">
            <h2>Products</h2>
            <?php if (empty($products)): ?>
                <p>No products available at this time.</p>
            <?php else: ?>
                <div class="products">
                    <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <?php if (file_exists($product['image'])): ?>
                                <img src="<?= e($product['image']) ?>" alt="<?= e($product['name']) ?>">
                            <?php else: ?>
                                <div class="no-image">No image available</div>
                            <?php endif; ?>
                        </div>
                        <div class="product-info">
                            <h3><?= e($product['name']) ?></h3>
                            <p class="price">$<?= e(number_format($product['price'], 2)) ?></p>
                            <form action="add_to_cart.php" method="post">
                                <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
                                <input type="hidden" name="product_id" value="<?= e($product['id']) ?>">
                                <button type="submit" class="btn">Add to Cart</button>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>
    
    <footer>
        <div class="container">
            <p>&copy; <?= date('Y') ?> <?= e(STORE_NAME) ?></p>
        </div>
    </footer>
</body>
</html>

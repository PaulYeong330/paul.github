<?php
require_once 'config.php';

// Calculate cart total
$total = 0;
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['price'] * $item['quantity'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - <?= e(STORE_NAME) ?></title>
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
                        if (!empty($_SESSION['cart'])) {
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
            <h2>Shopping Cart</h2>
            
            <?php if (isset($_SESSION['message'])): ?>
                <div class="message <?= e($_SESSION['message_type']) ?>">
                    <?= e($_SESSION['message']) ?>
                </div>
                <?php 
                // Clear message after displaying
                unset($_SESSION['message']);
                unset($_SESSION['message_type']);
                ?>
            <?php endif; ?>
            
            <?php if (empty($_SESSION['cart'])): ?>
                <p>Your cart is empty. <a href="index.php">Continue shopping</a></p>
            <?php else: ?>
                <div class="cart">
                    <table>
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($_SESSION['cart'] as $item): ?>
                            <tr>
                                <td data-label="Product">
                                    <div class="cart-product">
                                        <?php if (file_exists($item['image'])): ?>
                                            <img src="<?= e($item['image']) ?>" alt="<?= e($item['name']) ?>">
                                        <?php else: ?>
                                            <div class="no-image">No image</div>
                                        <?php endif; ?>
                                        <span><?= e($item['name']) ?></span>
                                    </div>
                                </td>
                                <td data-label="Price">$<?= e(number_format($item['price'], 2)) ?></td>
                                <td data-label="Quantity">
                                    <form action="update_cart.php" method="post" class="quantity-form">
                                        <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
                                        <input type="hidden" name="product_id" value="<?= e($item['id']) ?>">
                                        <input type="number" name="quantity" value="<?= e($item['quantity']) ?>" min="1" max="10">
                                        <button type="submit" class="btn btn-small">Update</button>
                                    </form>
                                </td>
                                <td data-label="Subtotal">$<?= e(number_format($item['price'] * $item['quantity'], 2)) ?></td>
                                <td data-label="Actions">
                                    <form action="remove_from_cart.php" method="post">
                                        <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
                                        <input type="hidden" name="product_id" value="<?= e($item['id']) ?>">
                                        <button type="submit" class="btn btn-danger">Remove</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-right"><strong>Total:</strong></td>
                                <td><strong>$<?= e(number_format($total, 2)) ?></strong></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                    
                    <div class="cart-actions">
                        <a href="index.php" class="btn btn-secondary">Continue Shopping</a>
                        <a href="checkout.php" class="btn btn-primary">Proceed to Checkout</a>
                    </div>
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
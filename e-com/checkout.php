<?php
require_once 'config.php';

// Redirect if cart is empty
if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

// Calculate cart total
$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['quantity'];
}

// Process checkout form
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate form fields
    if (empty($_POST['name'])) {
        $errors[] = "Name is required";
    }
    
    if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required";
    }
    
    if (empty($_POST['address'])) {
        $errors[] = "Address is required";
    }
    
    // If no errors, process the order
    if (empty($errors)) {
        try {
            // Start transaction
            $pdo->beginTransaction();
            
            // Insert order
            $stmt = $pdo->prepare("INSERT INTO orders (customer_name, customer_email, total_amount) VALUES (?, ?, ?)");
            $stmt->execute([$_POST['name'], $_POST['email'], $total]);
            $order_id = $pdo->lastInsertId();
            
            // Insert order items
            $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            foreach ($_SESSION['cart'] as $item) {
                $stmt->execute([$order_id, $item['id'], $item['quantity'], $item['price']]);
            }
            
            // Commit transaction
            $pdo->commit();
            
            // Clear cart
            $_SESSION['cart'] = [];
            
            // Set success flag
            $success = true;
            
        } catch(PDOException $e) {
            // Rollback transaction on error
            $pdo->rollBack();
            $errors[] = "Order processing failed: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout -Electronic E-commerce Store</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Simple E-commerce Store</h1>
            <nav>
                <ul>
                    <li><a href="index.php">Products</a></li>
                    <li><a href="cart.php">Cart <?php 
                        $cartCount = 0;
                        foreach ($_SESSION['cart'] as $item) {
                            $cartCount += $item['quantity'];
                        }
                        if ($cartCount > 0) echo "($cartCount)";
                    ?></a></li>
                </ul>
            </nav>
        </div>
    </header>
    
    <main>
        <div class="container">
            <h2>Checkout</h2>
            
            <?php if ($success): ?>
                <div class="success-message">
                    <h3>Thank you for your order!</h3>
                    <p>Your order has been processed successfully.</p>
                    <a href="index.php" class="btn">Continue Shopping</a>
                </div>
            <?php else: ?>
                <?php if (!empty($errors)): ?>
                    <div class="error-messages">
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <div class="checkout-container">
                    <div class="order-summary">
                        <h3>Order Summary</h3>
                        <table>
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($_SESSION['cart'] as $item): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td>$<?php echo htmlspecialchars(number_format($item['price'] * $item['quantity'], 2)); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2" class="text-right"><strong>Total:</strong></td>
                                    <td><strong>$<?php echo htmlspecialchars(number_format($total, 2)); ?></strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    <div class="checkout-form">
                        <h3>Shipping Information</h3>
                        <form action="checkout.php" method="post">
                            <div class="form-group">
                                <label for="name">Full Name:</label>
                                <input type="text" id="name" name="name" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email:</label>
                                <input type="email" id="email" name="email" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="address">Address:</label>
                                <textarea id="address" name="address" required></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Place Order</button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>
    
    <footer>
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Electronic E-commerce Store</p>
        </div>
    </footer>
</body>
</html>
<?php

require 'dbconnection.php'; // Ensure database connection

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to place an order.");
}

$user_id = $_SESSION['user_id']; // Get user ID from session

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $oiltype = $_POST['oiltype'];
    $amount = (int)$_POST['amount']; // Convert amount to integer

    // Ensure amount is in multiples of 50 liters
    if ($amount % 50 !== 0) {
        die("Amount must be in multiples of 50 liters.");
    }

    // Calculate total price based on 400 per 50 liters
    $total_price = ($amount / 50) * 400;

    try {
        // Ensure $conn is set
        if (!isset($conn)) {
            throw new Exception("Database connection not found.");
        }

        // Insert order into database
        $sql = "INSERT INTO orders (user_id, product_name, quantity, total_price) 
                VALUES (:user_id, :oiltype, :amount, :total_price)";
        $stmt = $conn->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':oiltype', $oiltype, PDO::PARAM_STR);
        $stmt->bindParam(':amount', $amount, PDO::PARAM_INT);
        $stmt->bindParam(':total_price', $total_price, PDO::PARAM_STR);

        // Execute query
        if ($stmt->execute()) {
            header("Location: delivery.php");
            exit();
        } else {
            echo "Order submission failed.";
        }
    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order Your Product</title>
    <link rel="stylesheet" href="/api_project/css/order.css">
</head>
<body>
    <div class="navbar">
        <a href="dashboard.php">Home</a>
        <a href="#">About Us</a>
        <a href="#">Contact Us</a>
    </div>
    <br>

    <div class="container">
        <h1>Order Now!</h1>
        <form method="post">
            <table align="center">
                <tr>
                    <th></th>
                    <th>Oil Type</th>
                    <th>Amount (Liters)</th>
                    <th></th>
                </tr>
                <tr>
                    <td><img src="sunflower.jpg" width="150" height="150"></td>
                    <td>
                        <input type="radio" value="Sunflower Oil" name="oiltype" required>Sunflower Oil
                    </td>
                    <td><input type="number" name="amount" min="50" step="50" value="50" required></td>
                </tr>
                <tr>
                    <td><img src="soya.jpg" width="150" height="150"></td>
                    <td>
                        <input type="radio" value="Soya Oil" name="oiltype" required>Soya Oil
                    </td>
                    <td><input type="number" name="amount" min="50" step="50" value="50" required></td>
                </tr>
                <tr>
                    <td><img src="vegetable.jpg" width="150" height="150"></td>
                    <td>
                        <input type="radio" value="Vegetable Oil" name="oiltype" required>Vegetable Oil
                    </td>
                    <td><input type="number" name="amount" min="50" step="50" value="50" required></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td><input class="btn" type="submit" value="Order"></td>
                </tr>
            </table>
        </form>
    </div>
</body>
</html>

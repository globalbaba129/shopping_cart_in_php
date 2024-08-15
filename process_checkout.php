<?php
session_start();
include 'DB_Connection/connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $totalAmount = $_POST['total_amount'];
    $payMode = $_POST['pay_mode'];
    $products = $_POST['products'];

    // Insert order details into the database
    $insert_order = $conn->prepare('INSERT INTO `order` (`name`, `email`, `phone`, `address`, `pay_mode`, `products`, `amount_pay`) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $insert_order->bind_param('ssssssi', $name, $email, $phone, $address, $payMode, $products, $totalAmount);
    $insert_order->execute();

    // Clear cart
    $conn->query('DELETE FROM cart');

    $_SESSION['message'] = 'Order placed successfully!';
    $_SESSION['show_alert'] = true;
    header('Location: checkout.php');
    exit;
} else {
    // Redirect to checkout if the request method is not POST
    header('Location: checkout.php');
    exit;
}

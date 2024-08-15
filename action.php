<?php
session_start();
include 'DB_Connection/connection.php'; // Ensure the path to your connection file is correct

// Disable error reporting for production environment
error_reporting(0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['p_id'])) {
    // Retrieve POST data
    $p_id = $_POST['p_id'];
    $p_name = $_POST['p_name'];
    $p_image = $_POST['p_image'];
    $p_price = $_POST['p_price'];
    $p_code = $_POST['p_code'];
    $qty = 1; // Default quantity

    // Check if the product code exists in the cart
    $Fetch_allData = $conn->prepare("SELECT p_code FROM cart WHERE p_code = ?");
    $Fetch_allData->bind_param('s', $p_code);
    $Fetch_allData->execute();
    $result = $Fetch_allData->get_result();
    $Fetch = $result->fetch_assoc();
    $code = $Fetch['p_code'];

    if (!$code) {
      // Insert the product into the cart
      $stmt = $conn->prepare("INSERT INTO cart (p_name, p_price, p_image, p_qty, p_total, p_code) VALUES (?, ?, ?, ?, ?, ?)");
      $total = $p_price * $qty; // Calculate total price based on quantity
      $stmt->bind_param('sssids', $p_name, $p_price, $p_image, $qty, $total, $p_code);
      $stmt->execute();

      echo '<div class="alert alert-success">
                    <strong>Success!</strong> Product added to cart successfully.
                  </div>';
    } else {
      echo '<div class="alert alert-warning">
                    <strong>Warning!</strong> Product code already exists in the cart.
                  </div>';
    }
  }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
  if (isset($_GET['cartItem']) && $_GET['cartItem'] === 'cart-item') {
    // Get the number of items in the cart
    $stmt = $conn->prepare('SELECT COUNT(*) AS total FROM cart');
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    // Return the number of items in the cart
    echo $data['total'];
  }
}
if (isset($_GET['remove']) && !empty($_GET['remove'])) {
  $p_id = $_GET['remove'];

  // Prepare and execute delete query
  $stmt = $conn->prepare("DELETE FROM cart WHERE p_id = ?");
  $stmt->bind_param('i', $p_id);
  $stmt->execute();

  $_SESSION['show alert'] = 'block';
  $_SESSION['message'] = 'delete from cart';
  header('location:cart.php');
}
if (isset($_GET['clear'])) {
  // Prepare and execute delete query
  $stmt = $conn->prepare("DELETE FROM cart");
  $stmt->execute();
  $_SESSION['show alert'] = 'block';
  $_SESSION['message'] = 'delete all cart';
  header('location:cart.php');
}
if (isset($_POST['action']) && $_POST['action'] == 'update_quantity') {
  $id = $_POST['id'];
  $qty = $_POST['qty'];

  // Update the quantity in the cart
  $update_query = $conn->prepare('UPDATE cart SET p_qty = ? WHERE p_id = ?');
  $update_query->bind_param('ii', $qty, $id);
  $update_query->execute();

  // Fetch the updated price
  $price_query = $conn->prepare('SELECT p_price FROM cart WHERE p_id = ?');
  $price_query->bind_param('i', $id);
  $price_query->execute();
  $price_result = $price_query->get_result();
  $price_row = $price_result->fetch_assoc();
  $price = $price_row['p_price'];

  // Return the updated price
  echo $price;
}
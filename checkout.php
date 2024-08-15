<?php
session_start();
include 'navbar.php'; // Include the navigation bar
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .card {
            margin-bottom: 20px;
        }

        .card img {
            height: 200px;
            object-fit: cover;
        }

        .btn-action {
            margin-right: 5px;
        }

        .payment-method-form {
            display: none;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h1>Checkout</h1>
        <?php
        include 'DB_Connection/connection.php';

        // Check for a session message
        if (isset($_SESSION['show_alert']) && $_SESSION['show_alert']) {
            echo "<script>
                Swal.fire({
                    title: 'Success!',
                    text: '{$_SESSION['message']}',
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            </script>";
            // Unset session variables to prevent repeated alerts
            unset($_SESSION['show_alert']);
            unset($_SESSION['message']);
        }

        // Query to fetch cart items
        $Fetch_allData = $conn->prepare('SELECT * FROM cart');
        $Fetch_allData->execute();
        $result = $Fetch_allData->get_result();

        if ($result->num_rows > 0) {
            // Initialize total amount
            $totalAmount = 0;
            // Prepare a list of products for the form
            $productList = [];
            ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Loop through the cart items and display them
                    while ($row = $result->fetch_assoc()) {
                        $itemTotal = $row['p_price'] * $row['p_qty'];
                        $totalAmount += $itemTotal;
                        // Add product name to the list
                        $productList[] = $row['p_name'];
                        ?>
                        <tr>
                            <td><img src="<?php echo htmlspecialchars($row['p_image']); ?>" alt="<?php echo htmlspecialchars($row['p_name']); ?>" style="width: 100px;"></td>
                            <td><?php echo htmlspecialchars($row['p_name']); ?></td>
                            <td>Rs: <?php echo htmlspecialchars($row['p_price']); ?></td>
                            <td><?php echo htmlspecialchars($row['p_qty']); ?></td>
                            <td>Rs: <?php echo number_format($itemTotal, 2); ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
            <div class="text-right mt-3">
                <h3>Total: <span id="cart-total">Rs: <?php echo number_format($totalAmount, 2); ?></span></h3>
            </div>
            <form id="checkout-form" action="process_checkout.php" method="post" class="mt-4">
                <input type="hidden" name="total_amount" value="<?php echo $totalAmount; ?>">
                <input type="hidden" name="products" value="<?php echo htmlspecialchars(implode(',', $productList)); ?>">
                <h4>Enter your details:</h4>
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea id="address" name="address" class="form-control" rows="3" required></textarea>
                </div>
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="text" id="phone" name="phone" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="pay_mode">Payment Method</label>
                    <select id="pay_mode" name="pay_mode" class="form-control" required onchange="showPaymentForm()">
                        <option value="" disabled selected>Select Payment Method</option>
                        <option value="credit_card">Credit Card</option>
                        <option value="paypal">PayPal</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="cod">COD</option>
                    </select>
                </div>
                <!-- Credit Card Payment Form -->
                <div id="credit_card_form" class="payment-method-form">
                    <h4>Credit Card Details</h4>
                    <div class="form-group">
                        <label for="card_number">Card Number</label>
                        <input type="text" id="card_number" name="card_number" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="expiry_date">Expiry Date</label>
                        <input type="text" id="expiry_date" name="expiry_date" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="cvv">CVV</label>
                        <input type="text" id="cvv" name="cvv" class="form-control">
                    </div>
                </div>
                <!-- PayPal Payment Form -->
                <div id="paypal_form" class="payment-method-form">
                    <h4>PayPal Payment</h4>
                    <p>Redirecting to PayPal...</p>
                    <!-- Optionally, you can include a PayPal button or redirect code here -->
                </div>
                <!-- Bank Transfer Payment Form -->
                <div id="bank_transfer_form" class="payment-method-form">
                    <h4>Bank Transfer Details</h4>
                    <div class="form-group">
                        <label for="bank_name">Bank Name</label>
                        <input type="text" id="bank_name" name="bank_name" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="account_number">Account Number</label>
                        <input type="text" id="account_number" name="account_number" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="account_name">Account Name</label>
                        <input type="text" id="account_name" name="account_name" class="form-control">
                    </div>
                </div>
                <!-- COD Payment Form -->
                <div id="cod_form" class="payment-method-form">
                    <h4>Cash On Delivery</h4>
                    <p>You'll pay cash when the item is delivered to your address.</p>
                </div>
                <button type="submit" class="btn btn-success" onclick="showOrderDetails(event)">Complete Purchase</button>
            </form>
            <?php
        } else {
            echo '<p>Your cart is empty. <a href="shop_index_page.php" class="btn btn-primary btn-action">Continue Shopping</a></p>';
        }
        ?>
    </div>

    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS CDN -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function showPaymentForm() {
            const payMode = document.getElementById('pay_mode').value;
            document.querySelectorAll('.payment-method-form').forEach(form => form.style.display = 'none');
            if (payMode) {
                document.getElementById(payMode + '_form').style.display = 'block';
            }
        }

        function showOrderDetails(event) {
            event.preventDefault(); // Prevent form from submitting immediately
            
            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const address = document.getElementById('address').value;
            const phone = document.getElementById('phone').value;
            const payMode = document.getElementById('pay_mode').value;
            const totalAmount = document.querySelector('#cart-total').textContent;
            const products = document.querySelector('input[name="products"]').value;

            Swal.fire({
                title: 'Order Details',
                html: `
                    <p><strong>Name:</strong> ${name}</p>
                    <p><strong>Email:</strong> ${email}</p>
                    <p><strong>Address:</strong> ${address}</p>
                    <p><strong>Phone:</strong> ${phone}</p>
                    <p><strong>Payment Method:</strong> ${payMode}</p>
                    <p><strong>Total Amount:</strong> ${totalAmount}</p>
                    <p><strong>Products:</strong> ${products}</p>
                `,
                icon: 'info',
                confirmButtonText: 'Confirm',
                preConfirm: () => {
                    document.getElementById('checkout-form').submit();
                }
            });
        }
    </script>
</body>

</html>

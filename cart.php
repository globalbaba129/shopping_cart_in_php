<?php
session_start();
include 'navbar.php'; // Include the navigation bar
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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
    </style>
</head>

<body>
    <div class="container mt-5">
        <div style="display:<?php if (isset($_SESSION['show alert'])) { echo $_SESSION['show alert']; } else { echo 'none'; } unset($_SESSION['show alert']) ?>" class="alert alert-danger">
            <strong><?php if (isset($_SESSION['message'])) { echo $_SESSION['message']; } else { echo 'none'; } unset($_SESSION['message']) ?></strong> You should <a href="#" class="alert-link">Item Deleted</a>.
        </div>
        <h1>Your Cart</h1>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th><a href="action.php?clear=all" class="btn btn-danger" onclick="return confirm('Are you sure you want to clear the cart?')">Clear Cart</a></th>
                </tr>
            </thead>
            <tbody id="cart-items">
                <?php
                include 'DB_Connection/connection.php';

                // Query to fetch cart items
                $Fetch_allData = $conn->prepare('SELECT * FROM cart');
                $Fetch_allData->execute();
                $result = $Fetch_allData->get_result();

                // Initialize total amount
                $totalAmount = 0;
                $itemCount = $result->num_rows; // Get the number of items in the cart

                // Loop through the cart items and display them
                while ($row = $result->fetch_assoc()) {
                    $itemTotal = $row['p_price'] * $row['p_qty'];
                    $totalAmount += $itemTotal;
                ?>
                    <tr data-id="<?php echo $row['p_id']; ?>">
                        <td><img src="<?php echo $row['p_image']; ?>" alt="<?php echo $row['p_name']; ?>" style="width: 100px;"></td>
                        <td><?php echo $row['p_name']; ?></td>
                        <td class="item-price" data-id="<?php echo $row['p_id']; ?>">Rs: <?php echo $row['p_price']; ?></td>
                        <td><input type="number" class="p-qty" value="<?php echo $row['p_qty']; ?>" min="1"></td>
                        <td class="item-total">Rs: <?php echo $itemTotal ?></td>
                        <td>
                            <a href="action.php?remove=<?php echo $row['p_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to remove this item?')">Remove</a>
                        </td>
                    </tr>
                <?php
                }
                ?>
                <tr>
                    <td colspan="7">
                        <a href="shop_index_page.php" class="btn btn-primary btn-action">Continue Shopping</a>
                        <?php if ($itemCount > 0): ?>
                            <a href="checkout.php" class="btn btn-success btn-action float-right">Checkout</a>
                        <?php endif; ?>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="text-right mt-3">
            <h3>Total: <span id="cart-total">Rs: <?php echo $totalAmount ?></span></h3>
        </div>
    </div>

    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS CDN -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            // Function to update the cart item quantity and total
            function updateCartItem(id, qty) {
                $.ajax({
                    url: 'action.php',
                    type: 'POST',
                    data: {
                        action: 'update_quantity',
                        id: id,
                        qty: qty
                    },
                    success: function(response) {
                        var itemPrice = parseFloat($('.item-price[data-id="'+id+'"]').text().replace('Rs: ', ''));
                        var itemTotal = itemPrice * qty;
                        $('tr[data-id="'+id+'"] .item-total').text('Rs: ' + itemTotal.toFixed(2));
                        updateCartTotal();
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', status, error);
                    }
                });
            }

            // Function to update the cart total
            function updateCartTotal() {
                var total = 0;
                $('.item-total').each(function() {
                    var itemTotal = parseFloat($(this).text().replace('Rs: ', ''));
                    total += itemTotal;
                });
                $('#cart-total').text('Rs: ' + total.toFixed(2));
            }

            // Event handler for quantity change
            $('.p-qty').on('change', function() {
                var $row = $(this).closest('tr');
                var id = $row.data('id');
                var qty = $(this).val();
                updateCartItem(id, qty);
            });
        });
    </script>
</body>

</html>

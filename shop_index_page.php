<?php
include 'navbar.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .card {
            margin-bottom: 20px;
        }

        .card img {
            height: 200px;
            object-fit: cover;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <div class="massage"></div>
        <div class="row">
            <?php
            include 'DB_Connection/connection.php';

            $Fetch_allData = $conn->prepare('SELECT * FROM `product`');
            $Fetch_allData->execute();
            $result = $Fetch_allData->get_result();

            while ($row = $result->fetch_array()) {
            ?>
                <!-- Product Card 1 -->
                <div class="col-md-4">
                    <div class="card">
                        <img src="<?php echo htmlspecialchars($row['p_image']); ?>" class="card-img-top">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($row['p_name']); ?></h5>
                            <h5 class="card-title">Rs: <?php echo htmlspecialchars($row['p_price']); ?></h5>
                            <form action="" class="form_submit">
                                <input type="hidden" class="p_id" value="<?php echo htmlspecialchars($row['p_id']); ?>">
                                <input type="hidden" class="p_name" value="<?php echo htmlspecialchars($row['p_name']); ?>">
                                <input type="hidden" class="p_price" value="<?php echo htmlspecialchars($row['p_price']); ?>">
                                <input type="hidden" class="p_image" value="<?php echo htmlspecialchars($row['p_image']); ?>">
                                <input type="hidden" class="p_code" value="<?php echo htmlspecialchars($row['p_code']); ?>">
                                <button type="button" class="btn btn-primary addItemBtn">Add To Cart</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php
            }
            ?>
        </div>

        <!-- jQuery CDN -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <!-- Bootstrap JS CDN -->
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <script>
    $(document).ready(function() {
        $('.addItemBtn').click(function(e) {
            e.preventDefault();
            var form = $(this).closest('form');
            var p_id = form.find('.p_id').val();
            var p_name = form.find('.p_name').val();
            var p_image = form.find('.p_image').val();
            var p_price = form.find('.p_price').val();
            var p_code = form.find('.p_code').val();

            $.ajax({
                url: 'action.php',
                type: 'POST',
                data: {
                    p_id: p_id,
                    p_name: p_name,
                    p_image: p_image,
                    p_price: p_price,
                    p_code: p_code
                },
                success: function(response) {
                    $('.massage').html(response);
                    load_cart_items_number(); // Update cart item count after adding
                    window.scrollTo(0,0);
                },
            });
        });

        function load_cart_items_number() {
            $.ajax({
                url: 'action.php',
                type: 'GET',
                data: {
                    cartItem: 'cart-item'
                },
                success: function(response) {
                    $('#cart-item').text(response); // Update the cart item count display
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                    $('#cart-item').text('0'); // Default to 0 if there's an error
                }
            });
        }

        load_cart_items_number(); // Load the initial cart item count on page load
    });
</script>
    </div>
    <?php
    include 'fotter.php';
    ?>
</body>

</html>
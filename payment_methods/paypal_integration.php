<!-- PayPal Payment Form -->
<div id="paypal_form" class="payment-method-form">
    <h4>PayPal Payment</h4>
    <div id="paypal-button-container"></div>
    <script src="https://www.paypal.com/sdk/js?client-id=YOUR_CLIENT_ID"></script>
    <script>
        paypal.Buttons({
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: document.querySelector('input[name="total_amount"]').value
                        }
                    }]
                });
            },
            onApprove: function(data, actions) {
                return actions.order.capture().then(function(details) {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Payment completed by ' + details.payer.name.given_name,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    });
                    // Optionally, you can redirect to a confirmation page here
                });
            }
        }).render('#paypal-button-container');
    </script>
</div>

<?php
require_once '../config/config.php';
require_once '../includes/Database.php';
require_once '../includes/Auth.php';
require_once '../includes/functions.php';

$auth = new Auth();
requireLogin();

$settings = getSettings();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Digital Marketplace</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/styles.css') ?>">
    <?php if ($settings['payment_gateway'] === 'razorpay'): ?>
        <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <?php endif; ?>
</head>
<body>
    <div class="container mt-4" style="max-width: 600px;">
        <h1 class="mb-4">Checkout</h1>
        
        <div class="card mb-4">
            <h3 class="mb-3">Order Summary</h3>
            <div id="orderSummary"></div>
        </div>
        
        <div class="card mb-4">
            <h3 class="mb-3">Payment Method</h3>
            <p>Payment Gateway: <strong><?= ucfirst($settings['payment_gateway'] ?? 'Razorpay') ?></strong></p>
        </div>
        
        <div class="form-group">
            <label for="couponCode">Coupon Code (Optional)</label>
            <input type="text" id="couponCode" class="form-control" placeholder="Enter coupon code">
        </div>
        
        <button onclick="processCheckout()" class="btn btn-primary" style="width: 100%;">
            <i class="fas fa-lock"></i> Place Order
        </button>
    </div>

    <script src="<?= asset('js/app.js') ?>"></script>
    <script>
        async function processCheckout() {
            const couponCode = document.getElementById('couponCode').value;
            
            const result = await apiRequest('/checkout/process.php', {
                method: 'POST',
                body: JSON.stringify({
                    coupon_code: couponCode,
                    payment_method: '<?= $settings['payment_gateway'] ?? 'razorpay' ?>'
                })
            });
            
            if (result.success) {
                // For demo purposes, simulate payment success
                // In production, integrate with actual payment gateway
                
                const paymentResult = await apiRequest('/checkout/callback.php', {
                    method: 'POST',
                    body: JSON.stringify({
                        order_id: result.data.order_id,
                        transaction_id: 'TXN_' + Date.now(),
                        payment_status: 'completed'
                    })
                });
                
                if (paymentResult.success) {
                    showToast('Order placed successfully!', 'success');
                    setTimeout(() => {
                        window.location.href = 'orders.php';
                    }, 1500);
                } else {
                    showToast(paymentResult.message, 'error');
                }
            } else {
                showToast(result.message, 'error');
            }
        }
    </script>
</body>
</html>

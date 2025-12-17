<?php
require_once '../config/config.php';
require_once '../includes/Database.php';
require_once '../includes/Auth.php';
require_once '../includes/functions.php';

$auth = new Auth();
if ($auth->isLoggedIn()) {
    redirect(baseUrl());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Digital Marketplace</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/styles.css') ?>">
</head>
<body>
    <div class="container" style="max-width: 500px; margin-top: 4rem;">
        <div class="card">
            <div class="text-center mb-4">
                <div style="font-size: 3rem; color: var(--primary-color); margin-bottom: 1rem;">
                    <i class="fas fa-lock"></i>
                </div>
                <h1>Forgot Password?</h1>
                <p class="text-secondary">No worries, we'll send you reset instructions</p>
            </div>
            
            <div id="emailForm">
                <form id="forgotPasswordForm">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" class="form-control" placeholder="Enter your email" required>
                        <small class="text-secondary">Enter the email associated with your account</small>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        <i class="fas fa-paper-plane"></i> Send Reset Link
                    </button>
                </form>
            </div>

            <div id="successMessage" style="display: none;">
                <div class="alert alert-success text-center">
                    <i class="fas fa-check-circle" style="font-size: 3rem; color: #28a745; margin-bottom: 1rem;"></i>
                    <h4>Check Your Email</h4>
                    <p>We've sent a password reset link to your email address. Please check your inbox and follow the instructions.</p>
                    <div id="resetLinkDisplay" style="margin-top: 1rem; padding: 1rem; background: #f8f9fa; border-radius: 8px; word-break: break-all;">
                        <!-- Reset link will be displayed here in development -->
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-3">
                <a href="login.php">
                    <i class="fas fa-arrow-left"></i> Back to Login
                </a>
            </div>
        </div>
    </div>

    <script src="<?= asset('js/app.js') ?>"></script>
    <script>
        document.getElementById('forgotPasswordForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const email = document.getElementById('email').value;
            const submitBtn = e.target.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            
            // Disable button and show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
            
            try {
                const result = await apiRequest('/auth/forgot-password.php', {
                    method: 'POST',
                    body: JSON.stringify({ email })
                });
                
                if (result.success) {
                    // Hide the form
                    document.getElementById('emailForm').style.display = 'none';
                    
                    // Show success message
                    const successDiv = document.getElementById('successMessage');
                    successDiv.style.display = 'block';
                    
                    // In development, display the reset link
                    if (result.data && result.data.reset_link) {
                        const linkDisplay = document.getElementById('resetLinkDisplay');
                        linkDisplay.innerHTML = `
                            <small class="text-secondary"><strong>Development Mode:</strong></small><br>
                            <a href="${result.data.reset_link}" class="btn btn-sm btn-primary mt-2">
                                <i class="fas fa-key"></i> Reset Password Now
                            </a>
                        `;
                    }
                    
                    showToast('Reset link sent successfully!', 'success');
                } else {
                    showToast(result.message || 'Failed to send reset link', 'error');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                }
            } catch (error) {
                console.error('Forgot password error:', error);
                showToast('An error occurred. Please try again.', 'error');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            }
        });
    </script>
</body>
</html>

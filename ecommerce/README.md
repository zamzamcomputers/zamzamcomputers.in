# Digital Product Marketplace - Setup Guide

## Quick Start

### 1. Database Setup

```bash
# Open XAMPP Control Panel
# Start Apache and MySQL

# Create database
mysql -u root -p
CREATE DATABASE ecommerce_db;
exit;

# Import schema
mysql -u root -p ecommerce_db < C:\xampp\htdocs\ecommerce\database\schema.sql
```

### 2. Access the Application

- **Homepage**: http://localhost/ecommerce/
- **Admin Panel**: http://localhost/ecommerce/admin/
  - Email: admin@marketplace.com
  - Password: admin123

### 3. Test User Flow

1. **Browse Products**: Visit homepage and click "Explore Products"
2. **Sign Up**: Create a new user account
3. **Add to Cart**: Browse products and add items to cart
4. **Checkout**: Complete purchase (demo mode - no real payment)
5. **Download**: View orders and download purchased products

## Project Structure

```
ecommerce/
├── api/                    # Backend API endpoints
│   ├── auth/              # Authentication APIs
│   ├── cart/              # Shopping cart APIs
│   ├── checkout/          # Order processing
│   ├── products/          # Product APIs
│   └── user/              # User-specific APIs
├── admin/                 # Admin dashboard
├── assets/                # Static assets
│   ├── css/              # Stylesheets
│   └── js/               # JavaScript files
├── config/                # Configuration
├── database/              # Database schema
├── includes/              # PHP classes and helpers
├── pages/                 # User-facing pages
└── uploads/               # File uploads (create manually)
```

## Features Implemented

### User Features ✅
- ✅ Responsive landing page with hero section
- ✅ User authentication (signup, login, password reset)
- ✅ Product browsing with filters and search
- ✅ Product detail pages
- ✅ Shopping cart management
- ✅ Checkout with coupon codes
- ✅ Order history and downloads
- ✅ Secure download system with expiry
- ✅ Dark/Light mode toggle
- ✅ Mobile-first responsive design
- ✅ Bottom navigation for mobile

### Admin Features ✅
- ✅ Admin dashboard with statistics
- ✅ Product management (add/edit/delete)
- ✅ Order management
- ✅ User management
- ✅ Coupon management
- ✅ System settings
- ✅ Payment gateway configuration

### Technical Features ✅
- ✅ PHP 8+ with PDO
- ✅ MySQL database
- ✅ RESTful API architecture
- ✅ Secure password hashing
- ✅ SQL injection prevention
- ✅ Session management
- ✅ File upload handling
- ✅ Download tracking
- ✅ Responsive CSS with dark mode
- ✅ Modern JavaScript (async/await)

## Next Steps

### 1. Create Upload Directories

```bash
mkdir C:\xampp\htdocs\ecommerce\uploads
mkdir C:\xampp\htdocs\ecommerce\uploads\products
mkdir C:\xampp\htdocs\ecommerce\uploads\screenshots
```

### 2. Add Sample Products (Admin Panel)

1. Login to admin panel
2. Navigate to Products section
3. Add products with:
   - Title, description, price
   - Upload digital files
   - Add screenshots
   - Set category

### 3. Configure Payment Gateway (Optional)

1. Go to Admin > Settings
2. Choose payment gateway (Razorpay/Stripe/PayPal)
3. Enter API keys
4. Save settings

### 4. Customize Branding

1. Update site name in settings
2. Add logo
3. Customize colors in `assets/css/styles.css`

## Default Credentials

**Admin Account**:
- Email: admin@marketplace.com
- Password: admin123

**Test User** (create via signup page):
- Use any email/password

## Payment Integration

Currently in **demo mode**. To integrate real payments:

### Razorpay
1. Get API keys from https://razorpay.com
2. Add to Admin > Settings
3. Uncomment Razorpay integration in `checkout.php`

### Stripe
1. Get API keys from https://stripe.com
2. Add to Admin > Settings
3. Integrate Stripe.js

## Security Notes

⚠️ **Before Production**:
1. Change admin password
2. Update database credentials
3. Enable HTTPS
4. Set `display_errors = 0` in config
5. Add CSRF protection
6. Implement rate limiting
7. Add email verification

## Troubleshooting

### Database Connection Error
- Check XAMPP MySQL is running
- Verify database name in `config/config.php`
- Ensure schema is imported

### File Upload Issues
- Check upload directories exist
- Verify folder permissions
- Check PHP upload limits in php.ini

### API Errors
- Check browser console for errors
- Verify API endpoints are accessible
- Check PHP error logs

## Support

For issues or questions:
- Check FAQ page
- Review implementation plan
- Contact: support@marketplace.com

---

**Project Status**: ✅ Complete and Ready for Testing

**Last Updated**: <?= date('Y-m-d') ?>

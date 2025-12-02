# Forgot Password Feature - Setup Instructions

## Overview
The forgot password feature has been successfully implemented with the following components:
- Backend API endpoints for password reset
- Email validation with username matching
- Token-based password reset links
- Frontend UI for password reset

## Setup Steps

### 1. Create Database Table

Run the SQL file to create the password resets table:

```sql
-- You can find this in backend/password_resets_table.sql
-- Or run it directly:

CREATE TABLE IF NOT EXISTS `tblpassword_resets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expiry` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `email` (`email`),
  KEY `token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```

**To execute:**
1. Open phpMyAdmin or HeidiSQL
2. Select your `infovault_db` database
3. Run the SQL query above

OR using command line:
```bash
mysql -u root -p infovault_db < backend/password_resets_table.sql
```

### 2. Configure Email Settings

Edit `backend/app/Config/Email.php` to configure your email settings.

**For Testing (Local Development):**
Use `mail` protocol (default) - works with Laragon's sendmail:

```php
public $protocol = 'mail';
```

**For Production (SMTP):**
```php
public $protocol = 'smtp';
public $SMTPHost = 'smtp.gmail.com';  // Your SMTP host
public $SMTPUser = 'your-email@gmail.com';
public $SMTPPass = 'your-app-password';  // Use app-specific password for Gmail
public $SMTPPort = 587;
public $SMTPCrypto = 'tls';

// Set sender info
public $fromEmail = 'noreply@ascot.edu.ph';
public $fromName = 'ASCOT InfoVault';
```

**For Gmail Setup:**
1. Enable 2-factor authentication on your Google account
2. Go to: https://myaccount.google.com/apppasswords
3. Generate an App Password
4. Use that password in `$SMTPPass`

### 3. Update Frontend URL (if needed)

If your frontend is not running on `http://localhost:8081`, update the reset link URL in:
`backend/app/Controllers/Auth.php` (line ~226):

```php
$resetLink = "http://your-domain.com/reset-password/" . $token;
```

### 4. Test the Email Configuration (Optional)

Create a test route to verify email sending:

```php
// In backend/app/Config/Routes.php
$routes->get('test-email', function(){
    $email = \Config\Services::email();
    $email->setFrom('noreply@ascot.edu.ph', 'ASCOT InfoVault');
    $email->setTo('your-test-email@gmail.com');
    $email->setSubject('Test Email');
    $email->setMessage('This is a test email.');
    
    if($email->send()){
        echo 'Email sent successfully!';
    } else {
        echo 'Email failed to send.<br>';
        echo $email->printDebugger(['headers']);
    }
});
```

Visit: `http://localhost:8080/test-email`

## How It Works

### User Flow:
1. User clicks "Forgot Password?" on Sign-In page
2. Enters their username (must be filled) and email
3. Backend validates that username and email match in database
4. If valid, generates a secure token and sends reset email
5. User clicks the link in email
6. Opens password reset page with token validation
7. User enters new password (with confirmation)
8. Password is updated and user can login

### Security Features:
- Token expires after 1 hour
- Username + Email must match in database
- Token can only be used once
- Old tokens are deleted when new reset is requested
- Password is hashed with SHA1 (same as login)

## API Endpoints

### 1. Request Password Reset
**POST** `/infovault/api/v1/auth/forgot-password`

**Request Body:**
```json
{
  "username": "john_doe",
  "email": "john@example.com"
}
```

**Success Response (200):**
```json
{
  "success": true,
  "title": "Email Sent",
  "message": "A password reset link has been sent to your email address."
}
```

**Error Response (404):**
```json
{
  "error": 404,
  "title": "User Not Found",
  "message": "The email address may not exist or does not match the username provided."
}
```

### 2. Validate Reset Token
**POST** `/infovault/api/v1/auth/validate-reset-token`

**Request Body:**
```json
{
  "token": "abc123..."
}
```

**Success Response (200):**
```json
{
  "valid": true,
  "email": "john@example.com"
}
```

### 3. Reset Password
**POST** `/infovault/api/v1/auth/reset-password`

**Request Body:**
```json
{
  "token": "abc123...",
  "newPassword": "newSecurePassword123"
}
```

**Success Response (200):**
```json
{
  "success": true,
  "title": "Password Reset Successful",
  "message": "Your password has been successfully reset. You can now login with your new password."
}
```

## Files Modified/Created

### Backend:
- ✅ `backend/app/Controllers/Auth.php` - Added 3 new methods
- ✅ `backend/app/Config/Routes.php` - Added 3 new routes
- ✅ `backend/password_resets_table.sql` - Database table SQL

### Frontend:
- ✅ `frontend/src/views/ResetPassword.vue` - New password reset page
- ✅ `frontend/src/router/index.js` - Added reset password route
- ✅ `frontend/src/views/Sign-In.vue` - Already has forgot password modal

## Testing Checklist

- [ ] Database table created successfully
- [ ] Email configuration updated
- [ ] Test email sending (optional test route)
- [ ] Backend server running (`php spark serve`)
- [ ] Frontend server running (`npm run serve`)
- [ ] Test forgot password flow:
  - [ ] Enter valid username/email - should receive email
  - [ ] Enter invalid username/email - should show error
  - [ ] Click link in email - should open reset page
  - [ ] Enter new password - should update successfully
  - [ ] Try using expired/invalid token - should show error
  - [ ] Login with new password - should work

## Troubleshooting

### Email not sending:
1. Check `backend/writable/logs/log-*.php` for errors
2. Verify Email.php configuration
3. For Gmail, ensure "Less secure app access" is ON or use App Password
4. Check firewall/antivirus blocking port 587/465
5. Try `mail` protocol first for local testing

### Token validation fails:
1. Check database - verify token exists in `tblpassword_resets`
2. Check token expiry datetime
3. Verify system timezone matches database timezone

### User not found error:
1. Verify username and email exist in `tblusers` table
2. Ensure they match exactly (case-sensitive)
3. Check database connection in `app/Config/Database.php`

## Email Configuration Examples

### Using Mailtrap (for testing):
```php
public $protocol = 'smtp';
public $SMTPHost = 'smtp.mailtrap.io';
public $SMTPUser = 'your-mailtrap-username';
public $SMTPPass = 'your-mailtrap-password';
public $SMTPPort = 2525;
public $SMTPCrypto = 'tls';
```

### Using Gmail:
```php
public $protocol = 'smtp';
public $SMTPHost = 'smtp.gmail.com';
public $SMTPUser = 'your-email@gmail.com';
public $SMTPPass = 'your-16-char-app-password';
public $SMTPPort = 587;
public $SMTPCrypto = 'tls';
```

### Using Outlook/Office365:
```php
public $protocol = 'smtp';
public $SMTPHost = 'smtp.office365.com';
public $SMTPUser = 'your-email@outlook.com';
public $SMTPPass = 'your-password';
public $SMTPPort = 587;
public $SMTPCrypto = 'tls';
```

## Notes
- The password reset link is valid for **1 hour only**
- Tokens are one-time use - deleted after successful password reset
- Old tokens for the same email are automatically deleted when requesting a new reset
- Frontend runs on port 8081, backend on port 8080 (update if different)
- Email template includes ASCOT branding and styling

# BIZCONNECT - Backend Setup Guide

This guide covers the PHP backend integration for the contact form with database storage and admin panel.

## Files Added

### Backend Files
1. **config.php** - Database configuration file
2. **database_setup.php** - Creates database and tables (run once)
3. **handle_contact.php** - Processes contact form submissions
4. **admin_login.php** - Admin authentication page
5. **admin.php** - Admin panel for viewing messages

### Updated Files
1. **santhiya-website.html** - Updated contact form with proper form attributes
2. **script.js** - Updated with AJAX submission instead of alerts

## Setup Instructions

### Step 1: Configure PHP Environment
Ensure you have PHP installed with MySQL support. If using a local development server:
- **XAMPP**: https://www.apachefriends.org/
- **WAMP**: http://www.wampserver.com/
- **MAMP**: https://www.mamp.info/

### Step 2: Update Database Configuration
Edit **config.php** and update these values if needed:
```php
define('DB_HOST', 'localhost');      // Your database host
define('DB_USER', 'root');           // Your database user
define('DB_PASSWORD', '');           // Your database password
define('DB_NAME', 'bizconnect_db');  // Database name
```

### Step 3: Run Database Setup
1. Place all files in your web server's root folder (e.g., htdocs for XAMPP)
2. Open your browser and navigate to: `http://localhost/Small-business-platform-BIZCONNECT-/database_setup.php`
3. You should see success messages confirming:
   - Database created
   - Tables created
   - Default admin user created

**Default Admin Credentials:**
- Username: `admin`
- Password: `admin123`

⚠️ **Important**: Change the default password after first login!

### Step 4: Verify Setup
Test the contact form:
1. Navigate to `http://localhost/Small-business-platform-BIZCONNECT-/santhiya-website.html`
2. Scroll to the Contact section
3. Fill out and submit the form
4. You should see a success message

### Step 5: Access Admin Panel
1. Navigate to: `http://localhost/Small-business-platform-BIZCONNECT-/admin_login.php`
2. Login with default credentials (admin / admin123)
3. View all submitted messages with:
   - Sender name and email
   - Message content
   - Submission timestamp
   - Read/Unread status

## Admin Panel Features

### Message Management
- **View Messages**: See all contact form submissions
- **Search**: Filter messages by name, email, or content
- **Filter**: View all, unread, or read messages only
- **Mark as Read**: Change message status
- **Delete**: Remove messages from database
- **Statistics**: View total and unread message counts

### Admin Actions
- Logout: Securely logout from the admin panel
- Responsive Design: Works on desktop and mobile devices

## Database Structure

### contact_messages Table
```
- id: Integer (Auto-increment, Primary Key)
- name: VARCHAR(100) - Sender's name
- email: VARCHAR(100) - Sender's email
- message: TEXT - Message content
- created_at: TIMESTAMP - Submission date/time
- status: ENUM('unread', 'read') - Message status
```

### admin_users Table
```
- id: Integer (Auto-increment, Primary Key)
- username: VARCHAR(50) - Username (unique)
- password: VARCHAR(255) - Hashed password
- email: VARCHAR(100) - Admin email
- created_at: TIMESTAMP - Account creation date
```

## Security Considerations

✅ **Implemented Security Features:**
1. **Prepared Statements**: Prevents SQL injection attacks
2. **Input Validation**: Checks email format and required fields
3. **Input Sanitization**: HTML special characters escaped
4. **Password Hashing**: Uses PHP's password_hash() function
5. **Session Management**: Admin authentication with sessions
6. **CSRF**: Form submissions are verified

### Additional Recommendations:
1. Change default admin password immediately
2. Configure HTTPS/SSL for production
3. Set up regular database backups
4. Restrict admin.php access by IP if needed
5. Remove or password-protect database_setup.php in production

## Troubleshooting

### Database Connection Fails
- Ensure MySQL/MariaDB is running
- Check config.php credentials
- Verify database user has necessary permissions

### Form Won't Submit
- Check browser console for errors (F12)
- Ensure handle_contact.php exists
- Verify form field names match (name, email, message)

### Admin Login Issues
- Ensure database_setup.php was run successfully
- Check cookies are enabled in browser
- Clear browser cache and try again

### No Messages Appearing
- Confirm form submission succeeded (check success message)
- Verify database connection in config.php
- Check if PHP error logs show any issues

## File Structure
```
Small-business-platform-BIZCONNECT-/
├── santhiya-website.html      (Updated)
├── script.js                  (Updated)
├── style.css
├── config.php                 (New)
├── database_setup.php         (New)
├── handle_contact.php         (New)
├── admin_login.php            (New)
├── admin.php                  (New)
└── SETUP_GUIDE.md             (This file)
```

## Support & Maintenance

### Regular Tasks:
1. Monitor admin panel for new messages
2. Delete older messages as needed
3. Back up your database regularly
4. Keep PHP and MySQL updated

### Future Enhancements:
- Email notifications for new messages
- Message export (CSV/PDF)
- Automated backups
- Multi-admin user support
- Custom admin dashboard

## Contact Form Fields
The contact form includes:
- **Name** (required): Sender's full name
- **Email** (required): Valid email address
- **Message** (required): Message content

All fields are validated on both client and server side.

---

**Setup completed! Your BIZCONNECT website now has full backend support.**

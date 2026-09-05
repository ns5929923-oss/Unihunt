# UniHunt Backend Documentation

## Overview
UniHunt is a comprehensive university finder platform specifically designed for Gujarat, India. This backend provides a complete REST API, admin panel, and database management system for the platform.

## Features

### Core Functionality
- **University Management**: Complete CRUD operations for universities
- **Blog System**: Content management for educational blogs
- **Counselling Sessions**: Booking and management system
- **Enquiry System**: Handle user enquiries and applications
- **Career Quiz**: Interactive quiz for stream recommendation
- **Newsletter**: Subscription management
- **Admin Panel**: Complete administrative interface

### Security Features
- **Input Validation**: Comprehensive validation for all inputs
- **SQL Injection Prevention**: Parameterized queries and input sanitization
- **CSRF Protection**: Token-based protection
- **Rate Limiting**: Prevents abuse and spam
- **Security Headers**: XSS, clickjacking, and MIME type protection
- **File Upload Security**: Secure file handling with validation

## Directory Structure

```
backend/
├── config/
│   ├── config.php          # Application configuration
│   └── database.php        # Database connection class
├── database/
│   ├── schema.php          # Database schema creation
│   └── seed.php            # Sample data insertion
├── api/
│   ├── universities.php    # Universities API endpoints
│   ├── blogs.php           # Blogs API endpoints
│   ├── counselling.php     # Counselling sessions API
│   └── enquiries.php       # Enquiries API endpoints
├── handlers/
│   ├── counselling_handler.php  # Counselling form handler
│   ├── enquiry_handler.php      # Enquiry form handler
│   ├── quiz_handler.php         # Quiz submission handler
│   └── newsletter_handler.php   # Newsletter subscription handler
├── admin/
│   ├── index.php           # Admin dashboard
│   ├── login.php           # Admin login
│   └── logout.php          # Admin logout
├── utils/
│   ├── SecurityHelper.php  # Security utilities
│   └── InputValidator.php  # Input validation class
└── README.md               # This file
```

## Installation & Setup

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Composer (optional, for dependency management)

### Installation Steps

1. **Clone/Download the backend files**
   ```bash
   # Place all backend files in your web server directory
   # e.g., /var/www/html/backend/ or C:\xampp\htdocs\backend\
   ```

2. **Create Database**
   ```sql
   CREATE DATABASE unihunt CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

3. **Configure Database Connection**
   Edit `config/database.php` and update the connection parameters:
   ```php
   private $host = "localhost";
   private $username = "your_username";
   private $password = "your_password";
   private $database = "unihunt";
   ```

4. **Run Database Setup**
   ```bash
   # Navigate to your backend directory
   cd backend/database/
   
   # Run schema creation
   php schema.php
   
   # Run data seeding
   php seed.php
   ```

5. **Set Permissions**
   ```bash
   # Create logs directory
   mkdir logs
   chmod 755 logs
   
   # Set appropriate permissions for uploads (if needed)
   mkdir uploads
   chmod 755 uploads
   ```

6. **Configure Web Server**
   - Ensure PHP is enabled
   - Set document root to include the backend directory
   - Enable mod_rewrite for clean URLs (optional)

## API Endpoints

### Universities API (`/api/universities.php`)

#### GET Requests
- **Get All Universities**: `GET /api/universities.php`
- **Get University by ID**: `GET /api/universities.php?id=U1`
- **Search Universities**: `GET /api/universities.php?search=engineering`
- **Filter by City**: `GET /api/universities.php?city=Ahmedabad`
- **Get Featured Universities**: `GET /api/universities.php?featured=1`

#### Query Parameters
- `stream`: Filter by stream (Science (PCM), Commerce, etc.)
- `course`: Filter by course (B.Tech, BBA, etc.)
- `exam`: Filter by entrance exam (JEE, GUJCET, etc.)
- `type`: Filter by type (Government, Private)
- `city`: Filter by city
- `budget`: Filter by maximum budget (in thousands)
- `page`: Page number for pagination
- `limit`: Number of results per page

### Blogs API (`/api/blogs.php`)

#### GET Requests
- **Get All Blogs**: `GET /api/blogs.php`
- **Get Blog by ID/Slug**: `GET /api/blogs.php?id=B1` or `GET /api/blogs.php?slug=blog-slug`
- **Search Blogs**: `GET /api/blogs.php?search=engineering`
- **Get Blogs by Tag**: `GET /api/blogs.php?tag=Engineering`
- **Get Popular Blogs**: `GET /api/blogs.php?popular=1`

### Counselling API (`/api/counselling.php`)

#### POST Request - Book Session
```json
POST /api/counselling.php
{
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "+91-9876543210",
    "preferred_date": "2024-02-15",
    "preferred_time": "14:00:00",
    "session_type": "Zoom",
    "stream": "Science (PCM)",
    "interests": ["Engineering", "Technology"],
    "budget_range": "1-2 Lakhs",
    "location_preference": "Ahmedabad"
}
```

#### GET Requests
- **Get Sessions** (Admin): `GET /api/counselling.php`
- **Get Statistics**: `GET /api/counselling.php?stats=1`

### Enquiries API (`/api/enquiries.php`)

#### POST Request - Submit Enquiry
```json
POST /api/enquiries.php
{
    "type": "Enquire",
    "university_id": "U1",
    "name": "Jane Smith",
    "email": "jane@example.com",
    "phone": "+91-9876543211",
    "subject": "B.Tech Admission Query",
    "message": "I am interested in B.Tech Computer Science...",
    "priority": "medium"
}
```

## Form Handlers

### Counselling Handler (`/handlers/counselling_handler.php`)
Processes counselling session booking forms from the frontend.

**Usage**: Submit POST data with counselling session details.

### Enquiry Handler (`/handlers/enquiry_handler.php`)
Processes enquiry, application, and general contact forms.

**Usage**: Submit POST data with enquiry details.

### Quiz Handler (`/handlers/quiz_handler.php`)
Processes career guidance quiz submissions and calculates recommendations.

**Usage**: Submit POST data with quiz answers.

### Newsletter Handler (`/handlers/newsletter_handler.php`)
Handles newsletter subscription and unsubscription.

**Usage**: Submit POST data with email and subscription preferences.

## Admin Panel

### Access
- **URL**: `/admin/`
- **Default Credentials**: 
  - Username: `admin`
  - Password: `admin123`

### Features
- **Dashboard**: Overview of system statistics
- **University Management**: Add, edit, delete universities
- **Blog Management**: Create and manage blog posts
- **Enquiry Management**: View and respond to enquiries
- **Counselling Management**: Manage counselling sessions
- **Statistics**: View system analytics

## Database Schema

### Core Tables

#### universities
- Stores university information including courses, fees, ratings
- Uses JSON fields for arrays (streams, courses, exams, etc.)

#### blogs
- Blog posts and articles
- Includes SEO-friendly slugs and status management

#### enquiries
- User enquiries, applications, and general contact forms
- Tracks status and priority levels

#### counselling_sessions
- Counselling session bookings
- Includes preferences and scheduling information

#### testimonials
- User testimonials and reviews
- Linked to universities

#### users
- Admin and staff user accounts
- Role-based access control

#### quiz_results
- Career quiz submissions and results
- Stores recommendations and university suggestions

#### newsletter_subscribers
- Newsletter subscription management
- Includes interests and preferences

## Security Features

### Input Validation
- Comprehensive validation for all input types
- Email, phone, date, time, and JSON validation
- Length and range validation
- Custom validation rules for specific fields

### SQL Injection Prevention
- Parameterized queries throughout
- Input sanitization and escaping
- SQL injection pattern detection
- Security event logging

### CSRF Protection
- Token generation and validation
- Session-based protection
- Form integration ready

### Rate Limiting
- Configurable rate limits per endpoint
- Session-based tracking
- Automatic reset after time window

### Security Headers
- X-Frame-Options: DENY
- X-Content-Type-Options: nosniff
- X-XSS-Protection: 1; mode=block
- Content Security Policy
- Strict Transport Security (HTTPS)

## Configuration

### Application Settings (`config/config.php`)
```php
// Database Settings
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'unihunt');

// Security Settings
define('ENCRYPTION_KEY', 'your_encryption_key');
define('SESSION_TIMEOUT', 3600);

// Email Settings
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your_email');
define('SMTP_PASS', 'your_password');

// File Upload Settings
define('UPLOAD_PATH', 'uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB

// API Settings
define('API_VERSION', 'v1');
define('API_BASE_URL', '/api/' . API_VERSION);
```

## Error Handling

### API Responses
All API endpoints return JSON responses with consistent format:

**Success Response**:
```json
{
    "success": true,
    "data": {...},
    "message": "Operation completed successfully"
}
```

**Error Response**:
```json
{
    "success": false,
    "error": "Error message",
    "code": "ERROR_CODE"
}
```

### HTTP Status Codes
- `200`: Success
- `400`: Bad Request (validation errors)
- `401`: Unauthorized
- `403`: Forbidden
- `404`: Not Found
- `405`: Method Not Allowed
- `500`: Internal Server Error

## Logging

### Security Logs
Security events are logged to `logs/security.log`:
- SQL injection attempts
- Rate limit violations
- Authentication failures
- Suspicious activities

### Application Logs
General application logs can be added to `logs/app.log` for debugging and monitoring.

## Deployment

### Production Checklist
1. **Security**
   - Change default admin credentials
   - Use strong database passwords
   - Enable HTTPS
   - Configure proper file permissions
   - Set up firewall rules

2. **Performance**
   - Enable PHP OPcache
   - Configure database connection pooling
   - Set up CDN for static assets
   - Implement caching strategies

3. **Monitoring**
   - Set up error logging
   - Monitor database performance
   - Track API usage
   - Monitor security events

4. **Backup**
   - Regular database backups
   - File system backups
   - Configuration backups
   - Test restore procedures

## Troubleshooting

### Common Issues

1. **Database Connection Errors**
   - Check database credentials in `config/database.php`
   - Ensure MySQL service is running
   - Verify database exists

2. **Permission Errors**
   - Check file permissions for logs directory
   - Ensure web server has read/write access
   - Verify upload directory permissions

3. **API Not Responding**
   - Check PHP error logs
   - Verify web server configuration
   - Ensure mod_rewrite is enabled (if using clean URLs)

4. **Admin Panel Access Issues**
   - Verify session configuration
   - Check PHP session settings
   - Clear browser cookies/cache

### Debug Mode
Enable debug mode in `config/config.php`:
```php
define('DEBUG_MODE', true);
```

This will display detailed error messages and enable additional logging.

## Support

For technical support or questions:
- Check the error logs first
- Review the configuration settings
- Test with sample data
- Verify database schema matches the code

## License

This project is proprietary software. All rights reserved.

## Version History

- **v1.0.0**: Initial release with core functionality
  - University management
  - Blog system
  - Counselling sessions
  - Admin panel
  - Security features
  - API endpoints

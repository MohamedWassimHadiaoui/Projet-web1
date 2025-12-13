# Module 4: Users

## Setup

1. Create database `peaceconnect_users` in phpMyAdmin
2. Import `database/schema.sql`
3. Update `config.php` with your database credentials
4. Update `mail_config.php` for password reset emails
5. Access via `http://localhost/Git/4_Users/`

## Structure

```
├── config.php              # Database configuration
├── mail_config.php         # Email configuration
├── index.php               # Entry point
├── Controller/
│   └── userController.php
├── Model/
│   └── User.php
├── View/
│   ├── frontoffice/        # Public pages
│   ├── backoffice/         # Admin pages
│   └── assets/             # CSS, JS, images
└── database/
    └── schema.sql          # Database tables
```

## Features

- User registration
- User login
- Password reset
- Profile management
- Two-Factor Authentication (2FA)
- User management (admin)


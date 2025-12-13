# Module 1: Help Requests & Organisations

## Setup

1. Create database `peaceconnect_help` in phpMyAdmin
2. Import `database/schema.sql`
3. Update `config.php` with your database credentials
4. Access via `http://localhost/Git/1_HelpRequests_Organisations/`

## Structure

```
├── config.php              # Database configuration
├── index.php               # Entry point
├── Controller/
│   ├── helpRequestController.php
│   └── organisationController.php
├── Model/
│   ├── HelpRequest.php
│   └── Organisation.php
├── View/
│   ├── frontoffice/        # Public pages
│   ├── backoffice/         # Admin pages
│   └── assets/             # CSS, JS, images
└── database/
    └── schema.sql          # Database tables
```

## Features

- Submit help requests
- Browse organisations
- Manage help requests (admin)
- Manage organisations (admin)


# Module 3: Events & Resources

## Setup

1. Create database `peaceconnect_events` in phpMyAdmin
2. Import `database/schema.sql`
3. Update `config.php` with your database credentials
4. Access via `http://localhost/Git/3_Events_Resources/`

## Structure

```
├── config.php              # Database configuration
├── index.php               # Entry point
├── Controller/
│   ├── eventController.php
│   └── contenuController.php
├── Model/
│   ├── Event.php
│   └── Contenu.php
├── View/
│   ├── frontoffice/        # Public pages
│   ├── backoffice/         # Admin pages
│   └── assets/             # CSS, JS, images
└── database/
    └── schema.sql          # Database tables
```

## Features

- Browse events
- Subscribe to events
- Read resources
- Like resources
- Translate content
- Manage events (admin)
- Manage resources (admin)


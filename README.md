# TravelNest — PHP Travel Booking Platform

## Quick Start (3 steps)

### Step 1 — Import Database
Open phpMyAdmin or MySQL CLI and run:
```sql
source /path/to/travelnest/database.sql
```

### Step 2 — Configure DB
Edit `includes/config.php`:
```php
define('DB_USER', 'root');      // your MySQL username
define('DB_PASS', '');          // your MySQL password
```

### Step 3 — Run
Place folder in XAMPP `htdocs/travelnest/` and visit:
- **Site:** http://localhost/travelnest/
- **Admin:** http://localhost/travelnest/admin/login.php

---

## Login Credentials

| Role  | URL                          | Email                    | Password  |
|-------|------------------------------|--------------------------|-----------|
| User  | /login.php                   | user@demo.com            | demo123   |
| Admin | **/admin/login.php**         | admin@travelnest.com     | admin123  |

> ⚠️ Admin login is at `/admin/login.php` — NOT `/login.php`

---

## File Structure
```
travelnest/
├── index.php          Homepage
├── flights.php        Flights listing
├── hotels.php         Hotels listing
├── packages.php       Holiday packages
├── trains.php         Train tickets
├── buses.php          Bus tickets
├── cabs.php           Cab booking
├── cruises.php        Cruise listing
├── book.php           Universal booking form
├── invoice.php        Printable invoice + map
├── bookings.php       My bookings
├── wishlist.php       Saved items
├── login.php          User login
├── register.php       User registration
├── logout.php         Logout
├── api.php            AJAX endpoint
├── database.sql       Database schema + seed data
│
├── admin/
│   ├── login.php      ← ADMIN LOGIN PAGE
│   ├── index.php      Admin dashboard router
│   └── sections/
│       ├── dashboard.php
│       ├── bookings.php
│       ├── users.php
│       ├── flights.php   (Add/Edit/Delete)
│       ├── hotels.php
│       ├── packages.php
│       ├── trains.php
│       ├── buses.php
│       ├── cabs.php
│       ├── cruises.php
│       ├── promos.php    (Add/Edit promo codes)
│       ├── reviews.php
│       ├── support.php
│       └── revenue.php
│
├── includes/
│   ├── bootstrap.php   Auto-detects BASE URL
│   ├── config.php      DB credentials
│   ├── db.php          PDO wrapper
│   ├── functions.php   Auth, helpers
│   ├── header.php      Navigation
│   └── footer.php      Footer
│
└── assets/
    ├── css/style.css   Dark luxury theme
    └── js/app.js       Click events, modals, AJAX

```

## Working Promo Codes
| Code       | Discount        |
|------------|-----------------|
| FIRST50    | 50% (max ₹500)  |
| SUMMER25   | 25% (max ₹750)  |
| HOLI2026   | 40% (max ₹1,500)|
| HOTEL20    | 20% (max ₹2,000)|
| FLIGHT15   | 15% (max ₹1,000)|
| NEWUSER    | ₹250 flat off   |
| TRAINPASS  | 10% (max ₹200)  |
| PLATINUM40 | 40% (max ₹5,000)|

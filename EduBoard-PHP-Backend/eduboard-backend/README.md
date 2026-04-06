# EduBoard — PHP + MySQL Backend Setup Guide

## 📁 Folder Structure
```
eduboard-backend/
├── api/
│   ├── login.php        → POST: authenticate user
│   ├── logout.php       → POST: destroy session
│   ├── me.php           → GET: check current session
│   ├── notices.php      → GET/POST/PUT/DELETE: full notice CRUD
│   └── users.php        → GET/POST/PUT/DELETE: admin user management
├── config/
│   └── db.php           → Database connection + settings
├── includes/
│   └── auth.php         → Auth helpers, session, response utilities
├── js/
│   └── api.js           → Shared frontend API helper
├── pages/
│   ├── admin-login.html
│   ├── faculty-login.html
│   └── student.html
├── database.sql          → Run this first to create the database
└── README.md             → This file
```

---

## 🚀 Step-by-Step Setup

### Step 1 — Install a local server (if testing locally)
Download and install **XAMPP** (recommended for beginners):
- https://www.apachefriends.org/download.html
- Install it → Open XAMPP Control Panel → Start **Apache** and **MySQL**

### Step 2 — Copy files to the server folder
- Open the XAMPP installation folder (usually `C:\xampp\`)
- Go to the `htdocs` folder
- Create a new folder called `eduboard`
- Copy ALL files from this zip into `C:\xampp\htdocs\eduboard\`

Your structure should look like:
```
C:\xampp\htdocs\eduboard\
├── api\
├── config\
├── includes\
├── js\
├── pages\
├── index.html
└── database.sql
```

### Step 3 — Create the database
1. Open your browser and go to: http://localhost/phpmyadmin
2. Click **"New"** in the left sidebar
3. Type `eduboard` as the database name → Click **Create**
4. Click on the `eduboard` database
5. Click the **"Import"** tab at the top
6. Click **"Choose File"** → select `database.sql` from your folder
7. Click **"Go"** at the bottom
8. You should see "Import has been successfully finished"

### Step 4 — Configure database connection
Open `config/db.php` and update these lines:
```php
define('DB_HOST', 'localhost');   // usually localhost
define('DB_NAME', 'eduboard');    // name you created
define('DB_USER', 'root');        // XAMPP default is root
define('DB_PASS', '');            // XAMPP default is empty
```

### Step 5 — Open the website
Go to: **http://localhost/eduboard/index.html**

That's it! 🎉

---

## 🔐 Login Credentials (from database seed)
| Role    | Email                  | Password   |
|---------|------------------------|------------|
| Admin   | admin@college.edu      | admin123   |
| Faculty | faculty@college.edu    | faculty123 |
| Student | (no login required)    | —          |

---

## 🌐 API Endpoints Reference

### Auth
| Method | Endpoint        | Description         | Auth Required |
|--------|-----------------|---------------------|---------------|
| POST   | api/login.php   | Login               | No            |
| POST   | api/logout.php  | Logout              | No            |
| GET    | api/me.php      | Get current user    | Yes           |

### Notices
| Method | Endpoint             | Description         | Auth Required |
|--------|----------------------|---------------------|---------------|
| GET    | api/notices.php      | List notices        | No            |
| GET    | api/notices.php?id=5 | Single notice       | No            |
| POST   | api/notices.php      | Create notice       | Admin/Faculty |
| PUT    | api/notices.php      | Update notice       | Admin/Faculty |
| DELETE | api/notices.php?id=5 | Delete notice       | Admin/Faculty |

**GET query parameters:**
- `?dept=CSE` — filter by department
- `?category=Exam` — filter by category
- `?status=Active` — filter by status
- `?search=keyword` — full-text search

### Users (Admin only)
| Method | Endpoint            | Description   |
|--------|---------------------|---------------|
| GET    | api/users.php       | List users    |
| POST   | api/users.php       | Create user   |
| PUT    | api/users.php       | Update user   |
| DELETE | api/users.php?id=5  | Delete user   |

---

## 🌍 Deploying to a Hosting Provider

### Option A — Free hosting (000webhost / InfinityFree)
1. Sign up at https://www.000webhost.com
2. Create a new website
3. Upload all files via File Manager
4. Create a MySQL database in the control panel
5. Import `database.sql`
6. Update `config/db.php` with the host's DB credentials

### Option B — Paid hosting (Hostinger / GoDaddy / SiteGround)
Same steps as above — all PHP shared hosts support this out of the box.

---

## 🔒 Security Checklist (Before Going Live)
- [ ] Change `JWT_SECRET` in `config/db.php` to a long random string
- [ ] Change default passwords in the database
- [ ] Set proper DB credentials (not root with empty password)
- [ ] Enable HTTPS on your hosting
- [ ] Remove the `input-hint` lines from login pages

---

## 🛠 Tech Stack
- **Frontend:** HTML5, CSS3, Vanilla JavaScript
- **Backend:** PHP 8.x
- **Database:** MySQL 8.x (or MariaDB)
- **Session:** PHP native sessions
- **Password hashing:** bcrypt via `password_hash()`
- **Database access:** PDO with prepared statements (SQL injection safe)

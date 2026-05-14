# مجلة العرب — AL ARAB Magazine
### Laravel 11 + MySQL Full Stack Platform

---

## 🚀 Quick Start

### Requirements
- PHP >= 8.2
- Composer
- MySQL 8.0+
- Node.js (optional, for assets)

### Setup Steps

```bash
# 1. Install PHP dependencies
composer install

# 2. Copy environment file
cp .env.example .env

# 3. Generate application key
php artisan key:generate

# 4. Configure your database in .env
# Edit DB_DATABASE, DB_USERNAME, DB_PASSWORD

# 5. Create the MySQL database
mysql -u root -p -e "CREATE DATABASE alarab_magazine CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 6. Run migrations (creates all tables)
php artisan migrate

# 7. Seed with demo data (Arabic content)
php artisan db:seed

# 8. Start the server
php artisan serve
```

Then open:
- **Website:** http://localhost:8000
- **Dashboard:** http://localhost:8000/dashboard
- **Login:** `admin` / `alarab2026`

---

## 📁 Project Structure

```
alarab-laravel/
│
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── ArticleController.php    ← Articles CRUD
│   │   │   ├── BlogController.php       ← Blogs CRUD
│   │   │   ├── PersonController.php     ← People CRUD
│   │   │   ├── SettingController.php    ← Site settings
│   │   │   ├── StatsController.php      ← Dashboard stats
│   │   │   ├── UploadController.php     ← Image upload
│   │   │   └── AuthController.php       ← Admin login
│   │   └── Middleware/
│   │       └── AdminToken.php           ← Token-based auth
│   │
│   └── Models/
│       ├── Article.php
│       ├── Blog.php
│       ├── Person.php
│       └── Setting.php
│
├── database/
│   ├── migrations/                      ← MySQL table definitions
│   └── seeders/
│       └── DatabaseSeeder.php           ← Arabic demo content
│
├── routes/
│   ├── api.php                          ← All API endpoints
│   └── web.php                          ← Website + Dashboard routes
│
├── public/
│   ├── index.php                        ← Laravel entry point
│   ├── site/index.html                  ← Arabic magazine website
│   ├── dashboard/index.html             ← CMS dashboard
│   └── uploads/                         ← Uploaded images
│
└── .env                                 ← Configuration
```

---

## 🌐 API Reference

### Authentication
```
POST /api/login
Body: { "username": "admin", "password": "alarab2026" }
Returns: { "success": true, "token": "..." }
```

All write operations require the header:
```
x-admin-token: <token from login>
```

### Endpoints

| Method | URL | Auth | Description |
|--------|-----|------|-------------|
| GET | `/api/articles` | ❌ | List articles (`?category=`, `?featured=1`, `?status=all`) |
| GET | `/api/articles/{id}` | ❌ | Get single article |
| POST | `/api/articles` | ✅ | Create article |
| PUT | `/api/articles/{id}` | ✅ | Update article |
| DELETE | `/api/articles/{id}` | ✅ | Delete article |
| GET | `/api/blogs` | ❌ | List blogs |
| GET | `/api/blogs/{id}` | ❌ | Get single blog |
| POST | `/api/blogs` | ✅ | Create blog |
| PUT | `/api/blogs/{id}` | ✅ | Update blog |
| DELETE | `/api/blogs/{id}` | ✅ | Delete blog |
| GET | `/api/people` | ❌ | List people (`?category=influencer|artist|doctor|business`) |
| GET | `/api/people/{id}` | ❌ | Get single person |
| POST | `/api/people` | ✅ | Create person |
| PUT | `/api/people/{id}` | ✅ | Update person |
| DELETE | `/api/people/{id}` | ✅ | Delete person |
| GET | `/api/settings` | ❌ | Get site settings |
| PUT | `/api/settings` | ✅ | Update settings |
| POST | `/api/upload` | ✅ | Upload image (form-data, field: `image`) |
| GET | `/api/stats` | ❌ | Dashboard statistics |

---

## 🗄️ Database Schema

### articles
| Column | Type | Notes |
|--------|------|-------|
| id | bigint | PK, auto-increment |
| title | varchar(1000) | Arabic title |
| subtitle | varchar(500) | |
| excerpt | text | |
| body | longtext | Full HTML content |
| category | varchar(100) | أعمال, فن, رياضة, موضة... |
| author | varchar(200) | |
| image_url | varchar(1000) | |
| read_time | varchar(50) | e.g. "5 دقائق" |
| featured | boolean | |
| status | enum | published, draft |
| region | varchar(100) | Country/region |
| views | int | View counter |
| created_at / updated_at | timestamp | |

### blogs
| Column | Type |
|--------|------|
| id, title, excerpt, body, author, author_bio, author_img, image_url, tags, featured, status, views, created_at, updated_at |

### people
| Column | Type | Notes |
|--------|------|-------|
| id, name, name_en, role | | |
| category | enum | influencer, artist, doctor, business |
| country, flag, image_url, excerpt, bio | | |
| stat, stat_label | | Generic stat display |
| handle, platform, followers | | Influencer fields |
| hospital, specialty, badge | | Doctor fields |
| company, net_worth | | Business fields |
| featured | boolean | |

### settings
| Column | Type |
|--------|------|
| key (PK), value |

---

## ☁️ Deploy on Shared Hosting (cPanel)

1. Upload all files to `public_html/alarab/`
2. Point domain to `public_html/alarab/public/`
3. Create MySQL database in cPanel
4. Edit `.env` with your DB credentials
5. Run `php artisan migrate --seed` via SSH

## ☁️ Deploy on Railway / Render

```bash
# Add to your repo and connect to Railway
railway login
railway init
railway up
```

Set these environment variables:
```
APP_KEY=base64:...      (from php artisan key:generate)
APP_ENV=production
DB_CONNECTION=mysql
DB_HOST=your-mysql-host
DB_DATABASE=alarab_magazine
DB_USERNAME=...
DB_PASSWORD=...
ADMIN_USER=admin
ADMIN_PASS=your_secure_password
```

---

## 🔧 Artisan Commands

```bash
# Reset and re-seed database
php artisan migrate:fresh --seed

# Clear all caches
php artisan optimize:clear

# Create storage symlink
php artisan storage:link
```

---

## 🔒 Security

- Change `ADMIN_PASS` in `.env` before going live
- Set `APP_DEBUG=false` in production
- Set `APP_ENV=production` in production

---

*مجلة العرب · دبي، الإمارات العربية المتحدة · 2026*

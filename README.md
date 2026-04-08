# CodeIgniter 3 Backend (PHP 5.6)

This project is a backend setup using CodeIgniter 3.1.13, compatible with PHP 5.6.

## Requirements
- Apache
- PHP 5.6
- MySQL
- OR **Docker** (Recommended)

## Running with Docker
If you have Docker installed, you can simply run:
```bash
docker-compose up -d
```
The application will be available at `http://localhost:8080`.

## Manual Setup (XAMPP/WAMP)
1. Move the files to your web server root (e.g., `htdocs`).
2. Import your database and update `application/config/database.php`.
3. Ensure `mod_rewrite` is enabled in Apache.

## Tech Stack
- **Framework:** CodeIgniter 3.1.13
- **PHP:** 5.6
- **Database:** MySQL 5.7
- **Web Server:** Apache (with mod_rewrite)

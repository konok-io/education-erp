# Education ERP & CMS - Local Setup Guide

## Version 1.0 LTS - Complete Installation

এই গাইড অনুসরণ করে আপনি আপনার লোকাল মেশিনে Education ERP সম্পূর্ণ সেটআপ করতে পারবেন।

---

## Prerequisites (পূর্বশর্ত)

### ১. PHP 8.2+ ইনস্টল করুন

**Windows:**
- [XAMPP](https://www.apachefriends.org/download.html) ডাউনলোড করুন (PHP 8.2+ সহ)
- অথবা [WampServer](https://www.wampserver.com/en/) ব্যবহার করুন

**macOS:**
```bash
brew install php@8.2
```

**Linux (Ubuntu/Debian):**
```bash
sudo apt update
sudo apt install php8.2 php8.2-cli php8.2-common php8.2-mysql php8.2-xml php8.2-mbstring php8.2-curl php8.2-zip php8.2-gd php8.2-bcmath
```

### ২. Composer ইনস্টল করুন

```bash
# Windows
# https://getcomposer.org/download/ থেকে Composer-Setup.exe ডাউনলোড করুন

# macOS/Linux
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### ৩. MySQL/MariaDB ইনস্টল করুন

**Windows:** XAMPP/WampServer এর সাথে আসে

**macOS:**
```bash
brew install mysql
```

**Linux:**
```bash
sudo apt install mysql-server
```

### ৪. Node.js 18+ ইনস্টল করুন

```bash
# macOS
brew install node

# Linux
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install nodejs
```

---

## Installation Steps (ইনস্টলেশন ধাপসমূহ)

### Step 1: প্রজেক্ট ক্লোন করুন

```bash
git clone https://github.com/konok-io/education-erp.git
cd education-erp
```

### Step 2: Backend সেটআপ

```bash
cd backend

# Composer dependencies ইনস্টল করুন
composer install

# .env ফাইল কপি করুন
cp .env.example .env

# Application key জেনারেট করুন
php artisan key:generate

# যদি key কাজ না করে, Manually যোগ করুন:
# APP_KEY=base64:YOUR_GENERATED_KEY
```

### Step 3: ডাটাবেস সেটআপ

```bash
# MySQL এ নতুন ডাটাবেস তৈরি করুন
mysql -u root -p
```

```sql
CREATE DATABASE education_erp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'erp_user'@'localhost' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON education_erp.* TO 'erp_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

`.env` ফাইলে আপডেট করুন:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=education_erp
DB_USERNAME=erp_user
DB_PASSWORD=your_password
```

### Step 4: মাইগ্রেশন চালান

```bash
php artisan migrate
php artisan db:seed --class=SuperAdminSeeder
```

### Step 5: Storage Link

```bash
php artisan storage:link
```

### Step 6: Frontend সেটআপ

```bash
cd ../frontend

# Dependencies ইনস্টল করুন
npm install

# .env ফাইল তৈরি করুন
cp .env.example .env
```

### Step 7: ডেভেলপমেন্ট সার্ভার চালান

**Terminal 1 - Backend:**
```bash
cd backend
php artisan serve
# http://localhost:8000 এ API চালু হবে
```

**Terminal 2 - Frontend:**
```bash
cd frontend
npm run dev
# http://localhost:5173 এ UI চালু হবে
```

---

## Docker Compose দিয়ে সেটআপ (বিকল্প পদ্ধতি)

```bash
# সম্পূর্ণ প্রজেক্ট ডিরেক্টরিতে
docker-compose up -d

# সার্ভিস চালু হলে
docker exec -it education-erp-backend-1 php artisan migrate
docker exec -it education-erp-backend-1 php artisan db:seed --class=SuperAdminSeeder
```

---

## প্রথম ব্যবহার

### Admin Login
- **URL:** http://localhost:5173
- **Email:** admin@education-erp.com
- **Password:** password

### API Documentation
- **Swagger:** http://localhost:8000/api/documentation

---

## সাধারণ সমস্যা সমাধান

### সমস্যা ১: Composer Memory Error
```bash
php -d memory_limit=-1 composer.phar install
```

### সমস্যা ২: Permission Error (Linux)
```bash
sudo chown -R $USER:$USER .
chmod -R 775 storage bootstrap/cache
```

### সমস্যা ৩: Node Modules Error
```bash
rm -rf node_modules package-lock.json
npm install
```

### সমস্যা ৪: Port Already in Use
```bash
# Backend
php artisan serve --port=8080

# Frontend
npm run dev -- --port=3000
```

---

## Features Overview (ফিচার সমূহ)

### ✅ Phase 001-045 Completed

- **Academic Management** - Students, Teachers, Classes, Subjects
- **Admission System** - Online/Offline Admission
- **Examination** - MCQ, Written, Practical
- **Result Management** - GPA, CGPA, Rank
- **Finance** - Fees, Payments, Invoices
- **HR & Payroll** - Employees, Salary
- **Library** - Books, Issue/Return
- **Inventory** - Items, Stock
- **Hostel** - Rooms, Allotment
- **Transport** - Routes, Vehicles
- **Certificate** - Degree, Transcript, TC
- **Alumni** - Directory, Events
- **AI Integration** - Chatbot, Predictions
- **Multi-Tenant** - SaaS Ready
- **API Platform** - REST API, OAuth

### 🚀 Phase 046-060 (Ready for Development)

- LMS & Virtual Classroom
- Research Management
- Blockchain Credentials
- Smart Campus
- AI Agents
- Data Lake
- Microservices
- Event-Driven Architecture
- Globalization (i18n)
- Super App
- DevSecOps
- Observability
- Backup & DR
- Identity Platform

---

## Project Structure

```
education-erp/
├── backend/              # Laravel API Backend
│   ├── app/
│   │   ├── Http/        # Controllers, Middleware
│   │   ├── Models/      # Eloquent Models
│   │   ├── Services/    # Business Logic
│   │   └── Enums/       # Enum Classes
│   ├── database/
│   │   └── migrations/  # Database Migrations
│   └── routes/           # API Routes
├── frontend/             # React + Vite Frontend
│   └── src/
│       ├── features/    # Feature Modules
│       ├── components/  # Reusable Components
│       └── pages/       # Page Components
├── docker-compose.yml    # Docker Setup
├── docs/                # Documentation
└── README.md
```

---

## Support

কোনো সমস্যা হলে:
1. GitHub Issues: https://github.com/konok-io/education-erp/issues
2. Email: support@education-erp.com

---

**🎓 Education ERP Version 1.0 LTS**
*Empowering Digital Education*

# Education ERP - Laragon Setup Guide

## Quick Setup (Windows)

### Step 1: Copy Project to Laragon
```
C:\laragon\www\education-erp\
```

### Step 2: Run Setup Script
Right-click on `setup-laragon.bat` and select **"Run as Administrator"**

This will:
- ✅ Copy Nginx configuration
- ✅ Add hosts entry
- ✅ Build frontend
- ✅ Setup Laravel

### Step 3: Restart Laragon
1. Open Laragon
2. Click **"Stop All"**
3. Click **"Start All"**

### Step 4: Open in Browser
```
http://auto.education-erp.test
```

---

## Manual Setup (if script doesn't work)

### Step 1: Copy Nginx Config
Copy `auto.education-erp.test.conf` to:
```
C:\laragon\etc\nginx\sites-enabled\
```

### Step 2: Add Hosts Entry
Open Notepad as Administrator and edit:
```
C:\Windows\System32\drivers\etc\hosts
```

Add this line:
```
127.0.0.1    auto.education-erp.test
```

### Step 3: Build Frontend
```cmd
cd C:\laragon\www\education-erp\frontend
npm install
npm run build
```

### Step 4: Setup Backend
```cmd
cd C:\laragon\www\education-erp\backend
copy .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed --class=SuperAdminSeeder
php artisan storage:link
```

### Step 5: Restart Services
```
Laragon Menu > Nginx > Restart
```

---

## Troubleshooting

### Blank Page
1. Check if Laravel is working:
   ```
   http://auto.education-erp.test/api
   ```
2. Check frontend build:
   ```
   C:\laragon\www\education-erp\frontend\dist\
   ```
   Should contain `index.html` and `assets/` folder

### 404 Not Found
- Make sure Nginx config is in `sites-enabled` (not just `sites-available`)
- Restart Laragon after adding config

### 500 Internal Server Error
1. Check Laravel logs:
   ```
   C:\laragon\www\education-erp\backend\storage\logs\
   ```
2. Check PHP error log in Laragon

### CORS Error
Frontend and backend are on same domain, so CORS shouldn't be an issue.
If you see CORS errors, check `.env`:
```env
FRONTEND_URL=http://auto.education-erp.test
```

---

## Project Structure
```
C:\laragon\www\education-erp\
├── backend/
│   ├── app/
│   ├── public/
│   │   └── index.php
│   ├── storage/
│   └── .env
├── frontend/
│   ├── dist/           ← Built files
│   ├── src/
│   └── node_modules/
├── auto.education-erp.test.conf
└── setup-laragon.bat
```

---

## Login Credentials
- **URL:** http://auto.education-erp.test
- **Email:** admin@education-erp.com
- **Password:** password

---

## Development Mode (Live Reload)

To enable live reload while developing:

**Terminal 1 - Backend:**
```cmd
cd C:\laragon\www\education-erp\backend
php artisan serve --host=auto.education-erp.test --port=8000
```

**Terminal 2 - Frontend:**
```cmd
cd C:\laragon\www\education-erp\frontend
npm run dev
```

Then access:
- Frontend: http://auto.education-erp.test:5173
- Backend API: http://auto.education-erp.test:8000/api

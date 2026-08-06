# Education ERP - Git Bash Setup Commands

## এই সব কমান্ড Git Bash তে paste করুন (line by line)

### 📁 Step 1: Project Directory তে যান
```bash
cd /c/laragon/www/education-erp
pwd
```

### 📋 Step 2: Apache Config Copy করুন
```bash
# Apache config copy
cp auto.education-erp.test.apache.conf /c/laragon/etc/apache/sites-enabled/

# Verify
ls -la /c/laragon/etc/apache/sites-enabled/ | grep education
```

### 🌐 Step 3: Nginx Configs Copy করুন
```bash
# Nginx configs copy
cp education-erp.test.conf /c/laragon/etc/nginx/sites-enabled/
cp api.education-erp.test.conf /c/laragon/etc/nginx/sites-enabled/

# Verify
ls -la /c/laragon/etc/nginx/sites-enabled/ | grep education
```

### 🏠 Step 4: Hosts File এ যোগ করুন
```bash
# Add hosts entries (if not exists)
echo "127.0.0.1    education-erp.test" >> /c/Windows/System32/drivers/etc/hosts
echo "127.0.0.1    api.education-erp.test" >> /c/Windows/System32/drivers/etc/hosts

# Verify
grep education /c/Windows/System32/drivers/etc/hosts
```

### ⚙️ Step 5: Backend Setup করুন
```bash
cd /c/laragon/www/education-erp/backend

# Create .env if not exists
[ ! -f .env ] && cp .env.example .env

# Generate APP_KEY
php artisan key:generate --no-interaction

# Set permissions
chmod -R 775 storage bootstrap/cache

# Verify
ls -la .env
```

### 📦 Step 6: Frontend Setup করুন
```bash
cd /c/laragon/www/education-erp/frontend

# Install dependencies
npm install

# Create .env if not exists
[ ! -f .env ] && cp .env.example .env

# Verify
ls -la node_modules | head -5
```

---

## 🚀 একসাথে সব কমান্ড (Copy-Paste করুন)

```bash
# ============================================
# Education ERP - One Command Setup
# ============================================

cd /c/laragon/www/education-erp

echo "[1/6] Copying Apache config..."
cp auto.education-erp.test.apache.conf /c/laragon/etc/apache/sites-enabled/

echo "[2/6] Copying Nginx configs..."
cp education-erp.test.conf /c/laragon/etc/nginx/sites-enabled/
cp api.education-erp.test.conf /c/laragon/etc/nginx/sites-enabled/

echo "[3/6] Adding hosts entries..."
grep -q "education-erp.test" /c/Windows/System32/drivers/etc/hosts || \
    echo "127.0.0.1    education-erp.test" >> /c/Windows/System32/drivers/etc/hosts
grep -q "api.education-erp.test" /c/Windows/System32/drivers/etc/hosts || \
    echo "127.0.0.1    api.education-erp.test" >> /c/Windows/System32/drivers/etc/hosts

echo "[4/6] Setting up Backend..."
cd backend
[ ! -f .env ] && cp .env.example .env
php artisan key:generate --no-interaction 2>/dev/null || echo "Run: php artisan key:generate"
chmod -R 775 storage bootstrap/cache

echo "[5/6] Setting up Frontend..."
cd ../frontend
[ ! -f .env ] && cp .env.example .env
[ ! -d node_modules ] && npm install

echo "[6/6] Done!"
echo ""
echo "=========================================="
echo "✅ Setup Complete!"
echo "=========================================="
echo ""
echo "Next steps:"
echo "1. Restart Laragon (Apache > Restart)"
echo ""
echo "2. Terminal 1 - Backend:"
echo "   cd /c/laragon/www/education-erp/backend"
echo "   php artisan serve --host=api.education-erp.test --port=8000"
echo ""
echo "3. Terminal 2 - Frontend:"
echo "   cd /c/laragon/www/education-erp/frontend"
echo "   npm run dev"
echo ""
echo "4. Browser:"
echo "   http://education-erp.test"
echo "   http://api.education-erp.test"
echo ""
echo "Login: admin@education-erp.com / password"
echo "=========================================="
```

---

## 🛑 Server বন্ধ করার কমান্ড

```bash
# Stop all services
cd /c/laragon/www/education-erp

# Kill PHP servers
pkill -f "artisan serve" || true

# Kill Node/Vite servers
pkill -f "vite" || true

echo "✅ All services stopped"
```

---

## 🔄 Server চালু করার কমান্ড

```bash
# Terminal 1 - Backend
cd /c/laragon/www/education-erp/backend
php artisan serve --host=api.education-erp.test --port=8000

# Terminal 2 - Frontend
cd /c/laragon/www/education-erp/frontend
npm run dev
```

---

## ✅ Verification Commands

```bash
# Check if configs are in place
ls -la /c/laragon/etc/apache/sites-enabled/ | grep education
ls -la /c/laragon/etc/nginx/sites-enabled/ | grep education

# Check hosts file
grep education /c/Windows/System32/drivers/etc/hosts

# Check .env files
cat /c/laragon/www/education-erp/backend/.env | grep APP_KEY
cat /c/laragon/www/education-erp/frontend/.env | grep VITE_API
```

---

## ❌ Error হলে

```bash
# Permission error হলে:
cd /c/laragon/www/education-erp/backend
chmod -R 777 storage bootstrap/cache

# Hosts file edit করতে না পারলে:
# Notepad++ বা VS Code দিয়ে edit করুন (Admin তে)
# Path: C:\Windows\System32\drivers\etc\hosts

# Apache config error হলে:
# Laragon Menu > Apache > Error Log দেখুন
```

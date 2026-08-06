#!/bin/bash
# Education ERP - Laragon Setup Script
# Run this in Git Bash or WSL on Windows

echo "=========================================="
echo "   Education ERP - Laragon Setup"
echo "=========================================="

LARAGON_WWW="C:/laragon/www/education-erp"
LARAGON_NGINX="C:/laragon/etc/nginx/sites-enabled"
LARAGON_HOSTS="C:/Windows/System32/drivers/etc/hosts"

# Check if project exists
if [ ! -d "$LARAGON_WWW" ]; then
    echo "❌ Project not found at $LARAGON_WWW"
    echo "   Please copy your project to C:/laragon/www/education-erp"
    exit 1
fi

echo "✅ Project found at $LARAGON_WWW"

# 1. Copy Nginx config
echo ""
echo "📝 Step 1: Copying Nginx configuration..."
if [ -f "$(dirname "$0")/auto.education-erp.test.conf" ]; then
    cp "$(dirname "$0")/auto.education-erp.test.conf" "$LARAGON_NGINX/"
    echo "✅ Nginx config copied"
else
    echo "❌ Nginx config file not found"
fi

# 2. Add hosts entry
echo ""
echo "📝 Step 2: Adding hosts entry..."
if grep -q "auto.education-erp.test" "$LARAGON_HOSTS" 2>/dev/null; then
    echo "✅ Hosts entry already exists"
else
    echo "127.0.0.1    auto.education-erp.test" >> "$LARAGON_HOSTS"
    echo "✅ Hosts entry added"
fi

# 3. Build frontend
echo ""
echo "📝 Step 3: Building frontend..."
cd "$LARAGON_WWW/frontend"
if [ -d "node_modules" ]; then
    npm run build
    echo "✅ Frontend built"
else
    echo "⚠️  node_modules not found. Run: npm install"
fi

# 4. Setup Laravel
echo ""
echo "📝 Step 4: Laravel setup..."
cd "$LARAGON_WWW/backend"
if [ -f ".env" ]; then
    echo "✅ .env file exists"
else
    echo "⚠️  .env file not found. Please create it:"
    echo "    cp .env.example .env"
    echo "    php artisan key:generate"
fi

echo ""
echo "=========================================="
echo "   Setup Complete!"
echo "=========================================="
echo ""
echo "Next steps:"
echo "1. Restart Laragon (Stop All → Start All)"
echo "2. Open browser: http://auto.education-erp.test"
echo ""
echo "If you see blank page, run in terminal:"
echo "  cd C:/laragon/www/education-erp/frontend"
echo "  npm install && npm run build"
echo ""

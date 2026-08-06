#!/bin/bash
# ===========================================
# Education ERP - Laragon Setup (Git Bash)
# ===========================================

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo "=========================================="
echo "   Education ERP - Laragon Setup"
echo "=========================================="

# Paths
LARAGON_WWW="C:/laragon/www/education-erp"
LARAGON_APACHE="C:/laragon/etc/apache/sites-enabled"
LARAGON_NGINX="C:/laragon/etc/nginx/sites-enabled"
HOSTS_FILE="C:/Windows/System32/drivers/etc/hosts"

# ===========================================
# Step 1: Check project location
# ===========================================
echo ""
echo -e "${YELLOW}[1/7]${NC} Checking project location..."

if [ -d "$LARAGON_WWW" ]; then
    echo -e "${GREEN}✅${NC} Project found at $LARAGON_WWW"
else
    echo -e "${RED}❌${NC} Project not found at $LARAGON_WWW"
    echo "   Please copy your project to C:/laragon/www/education-erp"
    exit 1
fi

# ===========================================
# Step 2: Copy Apache config
# ===========================================
echo ""
echo -e "${YELLOW}[2/7]${NC} Copying Apache configuration..."

if [ -f "$(dirname "$0")/auto.education-erp.test.apache.conf" ]; then
    cp "$(dirname "$0")/auto.education-erp.test.apache.conf" "$LARAGON_APACHE/"
    echo -e "${GREEN}✅${NC} Apache config copied to sites-enabled/"
else
    echo -e "${RED}❌${NC} Apache config file not found"
fi

# ===========================================
# Step 3: Copy Nginx configs
# ===========================================
echo ""
echo -e "${YELLOW}[3/7]${NC} Copying Nginx configurations..."

[ -f "$(dirname "$0")/education-erp.test.conf" ] && \
    cp "$(dirname "$0")/education-erp.test.conf" "$LARAGON_NGINX/" && \
    echo -e "${GREEN}✅${NC} education-erp.test.conf copied"

[ -f "$(dirname "$0")/api.education-erp.test.conf" ] && \
    cp "$(dirname "$0")/api.education-erp.test.conf" "$LARAGON_NGINX/" && \
    echo -e "${GREEN}✅${NC} api.education-erp.test.conf copied"

# ===========================================
# Step 4: Add hosts entries
# ===========================================
echo ""
echo -e "${YELLOW}[4/7]${NC} Adding hosts entries..."

# Function to add host if not exists
add_host() {
    if grep -q "$1" "$HOSTS_FILE" 2>/dev/null; then
        echo -e "${GREEN}✅${NC} $1 already exists"
    else
        echo "127.0.0.1    $1" >> "$HOSTS_FILE"
        echo -e "${GREEN}✅${NC} Added $1"
    fi
}

add_host "education-erp.test"
add_host "api.education-erp.test"

# ===========================================
# Step 5: Setup Backend .env
# ===========================================
echo ""
echo -e "${YELLOW}[5/7]${NC} Setting up Laravel backend..."

cd "$LARAGON_WWW/backend"

if [ -f ".env" ]; then
    echo -e "${GREEN}✅${NC} .env file already exists"
else
    echo "   Creating .env from .env.example..."
    cp .env.example .env
    echo -e "${GREEN}✅${NC} .env file created"
fi

if grep -q "APP_KEY=base64:" .env 2>/dev/null; then
    echo -e "${GREEN}✅${NC} APP_KEY already set"
else
    echo "   Generating APP_KEY..."
    php artisan key:generate --no-interaction 2>/dev/null && \
        echo -e "${GREEN}✅${NC} APP_KEY generated" || \
        echo -e "${YELLOW}⚠️${NC} Could not generate key. Run: php artisan key:generate"
fi

# ===========================================
# Step 6: Setup Frontend
# ===========================================
echo ""
echo -e "${YELLOW}[6/7]${NC} Setting up Frontend..."

cd "$LARAGON_WWW/frontend"

if [ -d "node_modules" ]; then
    echo -e "${GREEN}✅${NC} node_modules already exists"
else
    echo "   Installing npm dependencies..."
    npm install && echo -e "${GREEN}✅${NC} Dependencies installed" || \
        echo -e "${RED}❌${NC} npm install failed"
fi

# Create .env if not exists
if [ ! -f ".env" ]; then
    cp .env.example .env
    echo -e "${GREEN}✅${NC} Frontend .env created"
fi

# ===========================================
# Step 7: Permissions
# ===========================================
echo ""
echo -e "${YELLOW}[7/7]${NC} Setting permissions..."

cd "$LARAGON_WWW/backend"
[ -d "storage" ] && chmod -R 775 storage 2>/dev/null && echo -e "${GREEN}✅${NC} storage permissions set"
[ -d "bootstrap/cache" ] && chmod -R 775 bootstrap/cache 2>/dev/null && echo -e "${GREEN}✅${NC} bootstrap/cache permissions set"

# ===========================================
# Summary
# ===========================================
echo ""
echo "=========================================="
echo -e "   ${GREEN}Setup Complete!${NC}"
echo "=========================================="
echo ""
echo "📋 What to do next:"
echo ""
echo "1. Restart Laragon:"
echo "   Laragon Menu > Apache > Restart"
echo "   (or click Stop All > Start All)"
echo ""
echo "2. Open Terminal 1 - Backend API:"
echo "   cd $LARAGON_WWW/backend"
echo "   php artisan serve --host=api.education-erp.test --port=8000"
echo ""
echo "3. Open Terminal 2 - Frontend Dev:"
echo "   cd $LARAGON_WWW/frontend"
echo "   npm run dev"
echo ""
echo "4. Open Browser:"
echo "   🌐 Frontend: http://education-erp.test"
echo "   🔌 API:      http://api.education-erp.test"
echo ""
echo "=========================================="
echo -e "${GREEN}Login Credentials:${NC}"
echo "   Email:    admin@education-erp.com"
echo "   Password: password"
echo "=========================================="
echo ""

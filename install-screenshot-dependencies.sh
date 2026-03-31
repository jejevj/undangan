#!/bin/bash

# Install Screenshot Dependencies for Ubuntu/Debian
# Run this script on your production server

echo "🚀 Installing screenshot dependencies..."

# Get current directory
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

# Update package list
echo "📦 Updating package list..."
sudo apt-get update

# Install Node.js if not installed
if ! command -v node &> /dev/null; then
    echo "📦 Installing Node.js..."
    curl -fsSL https://deb.nodesource.com/setup_18.x | sudo bash -
    sudo apt-get install -y nodejs
else
    echo "✅ Node.js already installed: $(node --version)"
fi

# Install npm if not installed
if ! command -v npm &> /dev/null; then
    echo "📦 Installing npm..."
    sudo apt-get install -y npm
else
    echo "✅ npm already installed: $(npm --version)"
fi

# Install Puppeteer globally
echo "📦 Installing Puppeteer globally..."
sudo npm install -g puppeteer

# Also install locally in project
echo "📦 Installing Puppeteer locally..."
cd "$SCRIPT_DIR"
npm install puppeteer

# Install Chrome/Chromium dependencies
echo "📦 Installing Chrome dependencies..."
sudo apt-get install -y \
    chromium-browser \
    libx11-xcb1 \
    libxcomposite1 \
    libxcursor1 \
    libxdamage1 \
    libxi6 \
    libxtst6 \
    libnss3 \
    libcups2 \
    libxss1 \
    libxrandr2 \
    libasound2 \
    libpangocairo-1.0-0 \
    libatk1.0-0 \
    libatk-bridge2.0-0 \
    libgtk-3-0 \
    libgbm1 \
    fonts-liberation \
    libappindicator3-1 \
    xdg-utils

# Set permissions
echo "🔐 Setting permissions..."
if [ -d "$SCRIPT_DIR/node_modules" ]; then
    chmod -R 755 "$SCRIPT_DIR/node_modules"
fi

echo ""
echo "✅ Installation completed!"
echo ""
echo "🧪 Test the installation:"
echo "   cd $SCRIPT_DIR"
echo "   php artisan templates:generate-thumbnails --template=basic-preview"
echo ""

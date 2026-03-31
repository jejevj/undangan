#!/bin/bash

# Install Screenshot Dependencies for Rocky Linux / RHEL / CentOS
# Run this script on your production server

echo "🚀 Installing screenshot dependencies for Rocky Linux..."

# Get current directory
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

# Update package list
echo "📦 Updating package list..."
sudo dnf update -y

# Install Node.js if not installed
if ! command -v node &> /dev/null; then
    echo "📦 Installing Node.js 18..."
    sudo dnf module reset nodejs -y
    sudo dnf module enable nodejs:18 -y
    sudo dnf install nodejs -y
else
    echo "✅ Node.js already installed: $(node --version)"
fi

# Install npm if not installed
if ! command -v npm &> /dev/null; then
    echo "📦 Installing npm..."
    sudo dnf install npm -y
else
    echo "✅ npm already installed: $(npm --version)"
fi

# Install Puppeteer globally
echo "📦 Installing Puppeteer globally..."
sudo npm install -g puppeteer --unsafe-perm=true

# Also install locally in project
echo "📦 Installing Puppeteer locally..."
cd "$SCRIPT_DIR"
npm install puppeteer --unsafe-perm=true

# Install Chrome/Chromium dependencies for Rocky Linux
echo "📦 Installing Chrome dependencies..."
sudo dnf install -y \
    chromium \
    alsa-lib \
    atk \
    cups-libs \
    gtk3 \
    libXcomposite \
    libXcursor \
    libXdamage \
    libXext \
    libXi \
    libXrandr \
    libXScrnSaver \
    libXtst \
    pango \
    xorg-x11-fonts-100dpi \
    xorg-x11-fonts-75dpi \
    xorg-x11-fonts-cyrillic \
    xorg-x11-fonts-misc \
    xorg-x11-fonts-Type1 \
    xorg-x11-utils \
    liberation-fonts \
    nss \
    nspr \
    mesa-libgbm

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

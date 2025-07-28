#!/bin/bash

echo "🔧 Fixing build issues..."

# Clean npm cache and reinstall with compatible versions
echo "Cleaning npm cache and node_modules..."
rm -rf node_modules package-lock.json
npm cache clean --force

echo "Installing compatible dependencies..."
npm install

echo "Building assets..."
npm run build

echo "✅ Build fix complete!"
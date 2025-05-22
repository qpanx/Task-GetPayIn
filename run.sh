#!/bin/bash

# Content Scheduler startup script

echo "Starting Content Scheduler Application..."

# Install dependencies
echo "Installing PHP dependencies..."
composer install

echo "Installing NPM dependencies..."
npm install

# Environment setup
echo "Setting up environment..."
if [ ! -f .env ]; then
    echo "Creating .env file..."
    cp .env.example .env
    php artisan key:generate
fi

# Database setup
echo "Setting up database..."
php artisan migrate --seed

# Build frontend assets
echo "Building frontend assets..."
npm run build

# Run the application
echo "Starting the server..."
php artisan serve

echo "Content Scheduler is running!" 
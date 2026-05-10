@echo off
REM Deployment script for Vending Machine App
REM Run this on the server after uploading files

echo Installing production dependencies...
composer install --no-dev --optimize-autoloader

echo Copying environment file...
if not exist .env copy .env.example .env

echo Please edit .env with your production settings before continuing.
pause

echo Running migrations...
php scripts/migrate.php

echo Seeding database...
php scripts/seed.php

echo Deployment complete. Set document root to public/ and enable mod_rewrite.
# GESTACAD Routing System - Troubleshooting Guide

## Current Issue: Internal Server Error

### Step 1: Test Basic PHP Functionality
1. Visit: `http://localhost/GESTACAD/test.php`
2. This will show you if PHP is working and if the database connection is OK

### Step 2: Test Direct Access
1. Visit: `http://localhost/GESTACAD/index.php`
2. This should show the login page directly

### Step 3: Test Routing
1. Visit: `http://localhost/GESTACAD/` (should redirect to login)
2. Visit: `http://localhost/GESTACAD/login` (should show login page)

## Common Issues and Solutions

### Issue 1: mod_rewrite Not Enabled
**Symptoms**: Direct access to index.php works, but clean URLs don't work

**Solution**: Enable mod_rewrite in XAMPP
1. Open XAMPP Control Panel
2. Click "Config" next to Apache
3. Select "Apache (httpd.conf)"
4. Find the line: `#LoadModule rewrite_module modules/mod_rewrite.so`
5. Remove the # to uncomment it
6. Restart Apache

### Issue 2: .htaccess Not Working
**Symptoms**: 500 Internal Server Error

**Solution**: Check .htaccess syntax
1. The current .htaccess uses Apache 2.4+ syntax
2. If you have an older Apache version, use this instead:

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]

<Files "composer.json">
    Order Allow,Deny
    Deny from all
</Files>

<Files "composer.lock">
    Order Allow,Deny
    Deny from all
</Files>

<Files ".htaccess">
    Order Allow,Deny
    Deny from all
</Files>
```

### Issue 3: File Path Issues
**Symptoms**: CSS/JS not loading, includes failing

**Solution**: Check file paths
1. All asset paths should be relative to the root directory
2. Include paths should be relative to the current file

### Issue 4: Database Connection Issues
**Symptoms**: Database errors in test.php

**Solution**: Check database configuration
1. Verify database exists: `gestacadv2`
2. Check credentials in `config/db.php`
3. Ensure MySQL is running in XAMPP

## Testing Steps

### 1. Basic Test
```
http://localhost/GESTACAD/test.php
```
Should show:
- PHP is working!
- Database connection: OK
- Current URL information

### 2. Direct Access Test
```
http://localhost/GESTACAD/index.php
```
Should show the login page

### 3. Routing Test
```
http://localhost/GESTACAD/
http://localhost/GESTACAD/login
http://localhost/GESTACAD/dashboard
```
Should work with clean URLs

## Fallback Solutions

### If mod_rewrite doesn't work:
1. Use direct access: `http://localhost/GESTACAD/index.php`
2. The application will still work, just with less clean URLs

### If .htaccess causes issues:
1. Rename `.htaccess` to `.htaccess.bak`
2. Use direct access to `index.php`
3. Fix the .htaccess syntax and rename back

## Files Created for Testing
- `test.php` - Basic functionality test
- `index_simple.php` - Simplified routing version
- `index_backup.php` - Backup of original index.php
- `index.html` - Fallback redirect

## Next Steps
1. Test the basic functionality first
2. If that works, test the routing
3. If routing doesn't work, check mod_rewrite
4. If everything works, you can delete the test files

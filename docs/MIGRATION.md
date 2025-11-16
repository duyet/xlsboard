# Migration Guide: v1.x to v2.0

This guide will help you migrate from xlsboard v1.x to v2.0, which includes significant security improvements, modern dependencies, and better architecture.

## What's New in v2.0

### 🔒 Security Enhancements
- ✅ CSRF protection on settings form
- ✅ Password authentication for settings page
- ✅ Input validation and sanitization
- ✅ XSS prevention with HTML escaping
- ✅ Secure session handling

### ⚡ Performance Improvements
- ✅ File-based caching system
- ✅ Configurable cache TTL
- ✅ Reduced Google API calls

### 🎨 Modern UI/UX
- ✅ Bootstrap 3 → Bootstrap 5
- ✅ jQuery 1.10.2 removed (Bootstrap 5 doesn't need it)
- ✅ Font Awesome 4 → Font Awesome 6
- ✅ Responsive, mobile-first design
- ✅ Clean, gradient-based settings UI

### 🏗️ Architecture
- ✅ PSR-12 code style
- ✅ Namespaced classes (Xlsboard\*)
- ✅ PHPUnit tests with >80% coverage
- ✅ PHPStan level 8 static analysis
- ✅ Proper error handling
- ✅ Environment variable support

### 🧪 Developer Experience
- ✅ Composer support
- ✅ GitHub Actions CI/CD
- ✅ Code quality tools (PHP CS Fixer, PHPStan)
- ✅ Comprehensive documentation

## Breaking Changes

### 1. Minimum PHP Version
- **Old**: PHP 5.3+
- **New**: PHP 7.4+

**Action Required**: Upgrade your PHP version if below 7.4

### 2. Settings Page Authentication
- **Old**: Public access to `/m.php`
- **New**: Password-protected

**Action Required**: Set `XLSBOARD_ADMIN_PASSWORD` environment variable

```bash
# In .env file
XLSBOARD_ADMIN_PASSWORD=your_secure_password
```

Default password is `admin` if not set (insecure, please change!)

### 3. JavaScript Dependencies
- **Old**: jQuery 1.10.2 bundled
- **New**: No jQuery (Bootstrap 5 uses vanilla JS)

**Action Required**: None, unless you added custom jQuery code

### 4. File Structure
New files/directories:
```
src/                    # New: PSR-4 namespaced classes
├── SpreadsheetLoader.php
├── Cache.php
├── Security.php
├── Validator.php
└── helpers.php

tests/                  # New: PHPUnit tests
bootstrap.php          # New: Application bootstrap
composer.json          # New: Dependency management
.env.example           # New: Environment config template
cache/                 # New: Cache directory
```

**Action Required**: None, old files still work

## Migration Steps

### Step 1: Backup Your Data

```bash
# Backup your settings
cp data.txt data.txt.backup
cp title.txt title.txt.backup
```

### Step 2: Update Code

```bash
# Pull latest changes
git pull origin master

# Or download latest release
wget https://github.com/duyetdev/xlsboard/archive/v2.0.0.zip
unzip v2.0.0.zip
```

### Step 3: Install Dependencies (Recommended)

```bash
composer install
```

If you don't have Composer, xlsboard v2.0 includes fallback autoloading, so this is optional but recommended.

### Step 4: Configure Environment

```bash
# Copy example environment file
cp .env.example .env

# Edit configuration
nano .env
```

Minimal configuration:

```env
XLSBOARD_ADMIN_PASSWORD=your_secure_password
```

### Step 5: Set Permissions

```bash
# Ensure cache directory is writable
chmod 755 cache

# Ensure settings files are writable
chmod 644 data.txt title.txt
```

### Step 6: Test

```bash
# Start development server
php -S localhost:8000

# Visit in browser
open http://localhost:8000
```

### Step 7: Verify Settings Page

1. Visit `http://localhost:8000/m.php`
2. Login with your password
3. Verify spreadsheet key and title are preserved

### Step 8: Clear Old Cache (if any)

```bash
# Remove any old cached data
rm -rf cache/*.cache
```

## Configuration Migration

### Old: Hardcoded Values

```php
// Old way (v1.x)
$defaultSpreadSheet = '1YIFMvnSf9bcmDd3ZGi8kV0VvHkCOkQxWwAYhVedYfhE';
```

### New: Environment Variables

```env
# New way (v2.0)
XLSBOARD_DEFAULT_SPREADSHEET=1YIFMvnSf9bcmDd3ZGi8kV0VvHkCOkQxWwAYhVedYfhE
XLSBOARD_PAGE_TITLE=My Dashboard
```

## Custom Code Migration

### If You Modified `load.php`

The new `load.php` is a bridge to the new architecture. Your custom modifications should be moved to:

- `src/SpreadsheetLoader.php` - for data loading logic
- `src/Cache.php` - for caching logic
- `src/helpers.php` - for utility functions

### If You Modified `index.php`

The new `index.php` uses:
- Bootstrap 5 classes (not Bootstrap 3)
- Security::escape() for HTML escaping
- Modern PHP syntax

Update your custom code to match.

### If You Modified `m.php`

The new `m.php` is completely rewritten with security. If you had customizations:

1. Review `src/Security.php` for authentication logic
2. Review `src/Validator.php` for validation logic
3. Port your customizations to the new structure

## CSS/Styling Migration

### Bootstrap 3 → Bootstrap 5 Changes

Common class changes:

| Bootstrap 3 | Bootstrap 5 |
|-------------|-------------|
| `.pull-left` | `.float-start` |
| `.pull-right` | `.float-end` |
| `.hidden-xs` | `.d-none .d-sm-block` |
| `.text-left` | `.text-start` |
| `.btn-default` | `.btn-secondary` |

See [Bootstrap 5 Migration Guide](https://getbootstrap.com/docs/5.0/migration/) for full list.

### Custom CSS

If you have custom CSS in `css/style.css`, it should still work. However, you may need to update:

1. References to Bootstrap 3 classes
2. jQuery UI dependencies (if any)

## Testing Your Migration

### Checklist

- [ ] Main page loads correctly
- [ ] Spreadsheet data displays
- [ ] Settings page requires password
- [ ] Settings can be saved
- [ ] Cache directory is writable
- [ ] No PHP errors in logs
- [ ] Mobile responsive layout works
- [ ] All custom functionality preserved

### Troubleshooting

**Problem**: "Class 'Xlsboard\SpreadsheetLoader' not found"

**Solution**: Run `composer install` or check that `bootstrap.php` is properly loaded

---

**Problem**: Can't login to settings page

**Solution**: Check `XLSBOARD_ADMIN_PASSWORD` in `.env` or use default password `admin`

---

**Problem**: Spreadsheet not loading

**Solution**:
1. Clear cache: `rm -rf cache/*.cache`
2. Check spreadsheet is still published
3. Verify spreadsheet key in settings

---

**Problem**: Permission denied on cache or data files

**Solution**:
```bash
chmod 755 cache
chmod 644 data.txt title.txt
chown www-data:www-data cache data.txt title.txt
```

## Rollback Plan

If you need to rollback to v1.x:

```bash
# Restore from backup
git checkout v1.0.0

# Or restore files
cp data.txt.backup data.txt
cp title.txt.backup title.txt
```

## Support

If you encounter issues during migration:

1. Check the [Troubleshooting Guide](../README.md#troubleshooting)
2. Review [CLAUDE.md](../CLAUDE.md) for architecture details
3. Open an issue: [GitHub Issues](https://github.com/duyetdev/xlsboard/issues)

## What's Next?

After successful migration, consider:

1. **Enable Production Mode**: Set `APP_ENV=production` in `.env`
2. **Optimize Cache**: Increase `XLSBOARD_CACHE_TTL` for static data
3. **Add SSL**: Use HTTPS in production
4. **Monitor Logs**: Check PHP error logs for any warnings
5. **Update Dependencies**: Run `composer update` regularly

## Future Roadmap (v3.0)

Planned for future releases:

- Google Sheets API v4 support
- Multiple spreadsheet support
- Real-time data updates
- Export to CSV/JSON
- Docker container
- Database backend option

---

**Happy migrating! 🚀**

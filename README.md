# xlsboard

[![CI](https://github.com/duyetdev/xlsboard/workflows/CI/badge.svg)](https://github.com/duyetdev/xlsboard/actions)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D7.4-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

**Elegant Google Spreadsheet Display** - Transform your public Google Spreadsheets into beautiful, responsive web tables with zero complexity.

## ✨ Features

- 🚀 **Zero Configuration** - Clone and run in seconds
- 🔒 **Security First** - CSRF protection, authentication, input validation
- ⚡ **Smart Caching** - Automatic spreadsheet data caching
- 📱 **Responsive** - Built with Bootstrap 5
- 🎨 **Modern UI** - Clean, professional interface
- 🧪 **Well Tested** - Comprehensive unit test coverage
- 📊 **Real-time Updates** - Configurable cache TTL
- 🔐 **Password Protected** - Secure settings page

## 🚀 Quick Start

### Requirements

- PHP >= 7.4
- Extensions: `simplexml`, `json`
- Composer (optional, but recommended)

### Installation

1. **Clone the repository**

```bash
git clone https://github.com/duyetdev/xlsboard
cd xlsboard
```

2. **Install dependencies** (optional)

```bash
composer install
```

If you skip this step, the app will use built-in autoloading.

3. **Configure environment** (optional)

```bash
cp .env.example .env
```

Edit `.env` to customize:

```env
XLSBOARD_ADMIN_PASSWORD=your_secure_password
XLSBOARD_CACHE_TTL=300
XLSBOARD_DEFAULT_SPREADSHEET=your_spreadsheet_key
XLSBOARD_PAGE_TITLE=My Dashboard
```

4. **Start the server**

```bash
php -S localhost:8000
```

5. **Configure your spreadsheet**

Visit `http://localhost:8000/m.php` and login with your password (default: `admin`)

## 📊 Setting Up Google Spreadsheet

1. Open your Google Spreadsheet
2. Go to **File** → **Share** → **Publish to web**
3. Click **Publish**
4. Copy the spreadsheet key from the URL:
   ```
   https://docs.google.com/spreadsheets/d/YOUR_KEY_HERE/pubhtml
   ```
5. Paste the key in the settings page

## 🔧 Configuration

### Environment Variables

| Variable | Default | Description |
|----------|---------|-------------|
| `XLSBOARD_ADMIN_PASSWORD` | `admin` | Admin password for settings page |
| `XLSBOARD_CACHE_TTL` | `300` | Cache duration in seconds |
| `XLSBOARD_DEFAULT_SPREADSHEET` | - | Default spreadsheet key |
| `XLSBOARD_PAGE_TITLE` | `xlsboard` | Page title |
| `APP_ENV` | `development` | Environment (development/production) |
| `APP_DEBUG` | `false` | Enable debug mode |

### File-based Configuration

If you don't use `.env`, settings are stored in:
- `data.txt` - Spreadsheet key
- `title.txt` - Page title

## 🏗️ Architecture

xlsboard follows clean architecture principles:

```
xlsboard/
├── src/
│   ├── SpreadsheetLoader.php  # Core data loading
│   ├── Cache.php              # File-based caching
│   ├── Security.php           # Authentication & CSRF
│   ├── Validator.php          # Input validation
│   └── helpers.php            # Utility functions
├── tests/
│   └── Unit/                  # PHPUnit tests
├── index.php                  # Main display page
├── m.php                      # Settings page
├── load.php                   # Data loading bridge
└── bootstrap.php              # Application initialization
```

See [CLAUDE.md](CLAUDE.md) for detailed architecture documentation.

## 🧪 Development

### Code Quality

```bash
# Run tests
composer test

# Run tests with coverage
composer test:coverage

# Static analysis
composer analyse

# Code style check
composer lint

# Auto-fix code style
composer fix

# Run all quality checks
composer quality
```

### Testing

All core functionality is covered by unit tests:

```bash
# Run PHPUnit tests
./vendor/bin/phpunit

# With coverage
./vendor/bin/phpunit --coverage-html coverage
```

## 🔒 Security Features

### v2.0 Security Improvements

- ✅ **CSRF Protection** - Token-based form protection
- ✅ **Authentication** - Password-protected settings
- ✅ **Input Validation** - Sanitization of all user inputs
- ✅ **XSS Prevention** - HTML escaping on output
- ✅ **Secure Sessions** - Properly configured session handling
- ✅ **No SQL Injection** - No database, file-based storage
- ✅ **Updated Dependencies** - Bootstrap 5, Font Awesome 6

### Changing Admin Password

**Option 1: Environment Variable (Recommended)**

```bash
# In .env file
XLSBOARD_ADMIN_PASSWORD=your_secure_password_here
```

**Option 2: Server Environment**

```bash
export XLSBOARD_ADMIN_PASSWORD=your_secure_password_here
```

## 🎨 Customization

### Custom Styling

Edit `css/style.css` or add your own CSS file:

```html
<!-- In index.php -->
<link rel="stylesheet" href="css/custom.css">
```

### Bootstrap Themes

xlsboard uses Bootstrap 5. You can easily swap themes:

```html
<!-- Replace Bootstrap CDN with your theme -->
<link href="https://bootswatch.com/5/darkly/bootstrap.min.css" rel="stylesheet">
```

## 📈 Performance

### Caching

xlsboard automatically caches spreadsheet data to reduce Google API calls:

- Default cache TTL: 5 minutes (300 seconds)
- Configurable via `XLSBOARD_CACHE_TTL`
- File-based cache in `cache/` directory

### Optimization Tips

1. **Increase Cache TTL** for static data
2. **Use Environment Variables** instead of file reads
3. **Enable OPcache** in production
4. **Use a CDN** for Bootstrap/Font Awesome

## 🐛 Troubleshooting

### "Empty data!" Error

- Check if spreadsheet is published to web
- Verify the spreadsheet key is correct
- Ensure `simplexml` PHP extension is enabled

### "Failed to load spreadsheet"

- Check internet connectivity
- Verify Google Sheets API is accessible
- Check if spreadsheet is public

### Settings Not Saving

- Check file permissions on `data.txt` and `title.txt`
- Ensure web server has write permissions to the directory

## 🔄 Migration from v1.x

xlsboard v2.0 is backward compatible, but includes major improvements:

1. **Update dependencies**: Run `composer install`
2. **Set admin password**: Add `XLSBOARD_ADMIN_PASSWORD` to `.env`
3. **Clear cache**: Delete `cache/` directory contents
4. **Test settings**: Visit `/m.php` and verify authentication works

See detailed migration guide in [MIGRATION.md](docs/MIGRATION.md)

## 🚨 Google Sheets API Deprecation

⚠️ **Important**: xlsboard currently uses Google Sheets XML feed, which is deprecated.

**Current API**: `https://spreadsheets.google.com/feeds/cells/{KEY}/1/public/values`

**Future**: Migration to Google Sheets API v4 is planned for v3.0

For now, the XML feed still works for public spreadsheets.

## 📝 License

MIT License - see [LICENSE](LICENSE) file for details

## 🤝 Contributing

Contributions are welcome! Please follow these guidelines:

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/amazing-feature`
3. Follow PSR-12 code style
4. Add tests for new features
5. Ensure all tests pass: `composer quality`
6. Submit a pull request

## 📚 Resources

- [Architecture Documentation](CLAUDE.md)
- [Google Sheets Publishing Guide](https://support.google.com/docs/answer/183965)
- [PHP SimpleXML Documentation](https://www.php.net/manual/en/book.simplexml.php)

## 🙏 Acknowledgments

- Original concept: Simple spreadsheet display
- Rebuilt with modern practices and security
- Powered by Bootstrap 5 and PHP

## 📧 Support

- **Issues**: [GitHub Issues](https://github.com/duyetdev/xlsboard/issues)
- **Discussions**: [GitHub Discussions](https://github.com/duyetdev/xlsboard/discussions)
- **Author**: [Duyet Le](https://github.com/duyetdev)

---

**Made with ❤️ by the open source community**

# xlsboard Architecture & Philosophy

## Vision

xlsboard is built on a simple, powerful idea: **democratize data presentation**. Anyone with a Google Spreadsheet should be able to publish it as a beautiful, responsive web table without complexity, frameworks, or overhead.

This isn't about building the most feature-rich dashboard. It's about **elegance through simplicity**.

## Core Philosophy

### 1. Zero Friction
- No database setup
- No complex configuration
- Clone, run, done

### 2. Embrace Constraints
- Pure PHP (no framework overhead)
- Flat file storage (data.txt, title.txt)
- Single-purpose: display spreadsheet data beautifully

### 3. Progressive Enhancement
- Works without JavaScript
- Responsive by default
- Fast, minimal dependencies

## Architecture

### Data Flow

```
Google Sheets (Public XML Feed)
         ↓
    load.php (Fetches & Parses)
         ↓
    $finalData (Cell Array)
         ↓
    index.php (Renders Table)
         ↓
    Browser (Responsive Display)
```

### File Structure

```
xlsboard/
├── index.php       # Main display (presentation layer)
├── load.php        # Data layer (fetch & parse logic)
├── m.php           # Settings (configuration interface)
├── data.txt        # Spreadsheet key storage
├── title.txt       # Page title storage
├── css/            # Styles (Bootstrap + custom)
├── js/             # Client-side enhancements
└── images/         # Icons & assets
```

### Core Components

#### 1. Data Layer (`load.php`)
- **loadSpreadSheetKey()**: Retrieves spreadsheet key from data.txt
- **loadSpreadsheets()**: Fetches Google Sheets XML feed via SimpleXML
- **getMaxColumn()**: Calculates table dimensions (columns)
- **getMaxRow()**: Calculates table dimensions (rows)

**Design Decision**: Global variables (`$finalData`, `$maxRow`, `$maxCol`) for simplicity. In a larger app, we'd use proper OOP, but here it reduces complexity for the use case.

#### 2. Presentation Layer (`index.php`)
- Minimal logic: just render the table
- Bootstrap 3 for responsive grid
- Dynamic column width calculation
- Graceful degradation for empty cells

**Design Decision**: Inline PHP in HTML. For this scale, separation of concerns would add unnecessary complexity. The entire rendering logic is ~40 lines.

#### 3. Configuration (`m.php`)
- Settings form for spreadsheet key & title
- Direct file writes (no database)
- Immediate persistence

**Security Note**: Original implementation lacked CSRF protection and input validation. This has been addressed in the modern refactor.

## Technology Choices

### Backend: Vanilla PHP
**Why?** Universal hosting support. No composer, no framework, no dependencies. Drop it on any PHP server and it works.

**Trade-off**: No modern PHP patterns (namespaces, autoloading, DI). Acceptable for this scope.

### Frontend: Bootstrap 3 → Bootstrap 5
**Original**: Bootstrap 3 (lightweight, CDN)
**Modern**: Bootstrap 5 (improved accessibility, dropped jQuery dependency)

### Data Source: Google Sheets XML API
**Why?** Public, free, no authentication required for public sheets.

**Limitation**: Google deprecated this API. Migration path documented for Google Sheets API v4.

### Storage: Flat Files
**Why?**
- Only 2 config values to persist
- No user-generated content
- File locking handles concurrency for this scale

**Trade-off**: Not suitable for high-traffic sites. Perfect for internal dashboards and low-volume use cases.

## Design Patterns

### 1. Configuration Over Code
Spreadsheet key and title stored in files, not hardcoded. Change data source without touching code.

### 2. Fail Fast
```php
if (!$datas OR empty($datas)) {
    die('Empty data!');
}
```
Clear error messages. No silent failures.

### 3. Defensive Programming
```php
isset($finalData["$j$i"]) ? $finalData["$j$i"] : '&nbsp;'
```
Always assume data might be missing. Render empty cells gracefully.

### 4. Progressive Complexity
- Basic version: just table rendering
- Enhanced: animations, scroll effects
- Future: caching, rate limiting, auth

## Security Model

### Original Vulnerabilities
1. **CSRF**: No token protection on settings form
2. **Input Validation**: Raw POST data written to files
3. **Authentication**: Public settings page
4. **Old Dependencies**: jQuery 1.10.2 (2013) with known CVEs

### Modern Security Measures
1. **CSRF Protection**: Token-based validation
2. **Input Sanitization**: Validate spreadsheet key format
3. **Authentication**: Password-protected settings
4. **Rate Limiting**: Prevent abuse of Google API
5. **Content Security Policy**: Restrict inline scripts
6. **Secure Headers**: X-Frame-Options, X-Content-Type-Options

## Performance Considerations

### Caching Strategy
- **Problem**: Every page load fetches from Google
- **Solution**: Cache spreadsheet data with TTL
- **Implementation**: File-based cache (cache/sheet_{key}.json)

### Rate Limiting
- Google Sheets API has quota limits
- Implement request throttling
- Graceful degradation on quota exceeded

## Testing Philosophy

### What We Test
- ✅ Spreadsheet parsing logic
- ✅ Input validation
- ✅ Security measures (CSRF tokens)
- ✅ Error handling

### What We Don't Test
- ❌ Google API availability (external dependency)
- ❌ Browser rendering (visual testing out of scope)

### Test Coverage Goals
- Unit tests: 80%+ coverage
- Integration tests: Critical paths
- CI runs tests on every push

## Code Quality Standards

### PHP
- **PSR-12**: Code style standard
- **PHPStan Level 8**: Maximum static analysis
- **PHP CS Fixer**: Automated formatting

### JavaScript
- **ESLint**: Airbnb config (strict but pragmatic)
- **Prettier**: Consistent formatting
- **Modern ES6+**: Use const/let, arrow functions, template literals

### CSS
- **BEM Methodology**: Block-Element-Modifier naming
- **Mobile-First**: Responsive breakpoints
- **Minimal Specificity**: Avoid !important, deep nesting

## Development Workflow

### Local Development
```bash
php -S localhost:8000
```

### Testing
```bash
composer test          # Run PHPUnit tests
composer lint          # Check code style
composer fix           # Auto-fix style issues
composer analyse       # Run PHPStan
```

### CI/CD Pipeline
1. **Lint**: PHP CS Fixer, ESLint
2. **Test**: PHPUnit with coverage
3. **Analyze**: PHPStan static analysis
4. **Build**: Validate no errors
5. **Deploy**: Manual (this is a library, not SaaS)

## API Migration Path

### Current: Google Sheets XML Feed (Deprecated)
```
https://spreadsheets.google.com/feeds/cells/{KEY}/1/public/values
```

### Future: Google Sheets API v4
Requires:
- API key or OAuth
- JSON parsing (replace SimpleXML)
- More complex auth flow

**Migration Strategy**: Support both APIs with feature flag. Deprecation notice in README.

## Extensibility Points

### Custom Data Sources
Replace `loadSpreadsheets()` to fetch from:
- Airtable
- CSV files
- Database tables
- Other APIs

### Custom Rendering
Replace table in `index.php` with:
- Charts (Chart.js)
- Cards/tiles
- Interactive filters

### Custom Styling
Override CSS variables or swap Bootstrap for:
- Tailwind CSS
- Material Design
- Custom design system

## Lessons & Trade-offs

### What Worked
✅ **Simplicity**: 3 PHP files, ~200 LOC
✅ **Zero setup**: No database, no config files
✅ **Universal hosting**: Works everywhere PHP runs

### What We'd Change
❌ **Global variables**: Should use a class
❌ **No caching**: Every request hits Google
❌ **No auth**: Settings page publicly accessible
❌ **Outdated deps**: jQuery 1.10.2, Bootstrap 3

### What We Won't Change
- **No framework**: Deliberate choice for portability
- **Flat files**: Appropriate for this scale
- **Inline PHP**: Readability over architecture purity

## Future Roadmap

### v2.0 (Current Refactor)
- ✅ Security hardening
- ✅ Modern dependencies
- ✅ Test coverage
- ✅ CI/CD pipeline
- ✅ Code quality tools

### v3.0 (Future)
- 🔮 Google Sheets API v4 support
- 🔮 Multiple sheet support
- 🔮 Data refresh intervals
- 🔮 Export to CSV/JSON
- 🔮 Webhook updates

### Beyond
- Docker container
- Caching layer (Redis/Memcached)
- Real-time updates (WebSockets)
- Chart visualizations

## Contributing Guidelines

### Code Contributions
1. Follow existing code style (PSR-12)
2. Add tests for new features
3. Update this document for architectural changes
4. Keep it simple (resist feature creep)

### Bug Reports
- Include PHP version
- Spreadsheet key format
- Error messages
- Steps to reproduce

### Philosophy Contributions
This project has a strong opinion: **simplicity over features**. Proposed changes should align with this philosophy or make a compelling case for evolution.

---

## The Essence

xlsboard is not trying to be everything to everyone. It's a sharp tool for a specific job: **display Google Spreadsheet data beautifully, with zero hassle**.

Every line of code should serve this purpose. Every dependency should justify its weight. Every feature should ask: "Does this make the core use case better, or just more complex?"

That's the xlsboard way.

---

*Last updated: 2025-11-16*
*Document maintained by: Claude (AI Assistant)*

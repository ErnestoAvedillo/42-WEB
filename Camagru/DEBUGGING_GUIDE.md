# Complete Web Development Debugging Guide

## 1. PHP Debugging with Xdebug

### Setup Steps:
1. **Rebuild containers** (Xdebug is now included):
   ```bash
   cd /home/ernesto/Desktop/WEB/Camagru
   docker-compose down
   docker-compose build
   docker-compose up -d
   ```

2. **Start debugging in VS Code**:
   - Open the debug panel (Ctrl+Shift+D)
   - Select "Listen for Xdebug"
   - Click the green play button
   - Set breakpoints by clicking on line numbers

3. **Test debugging**:
   - Go to http://localhost:8080/debug.php
   - The debugger will pause at your breakpoints
   - You can inspect variables, step through code, etc.

### PHP Debugging Features:
- **Breakpoints**: Click on line numbers in VS Code
- **Variable inspection**: Hover over variables or check the Variables panel
- **Step through code**: F10 (step over), F11 (step into), F5 (continue)
- **Call stack**: See the execution path
- **Watch expressions**: Monitor specific variables

## 2. JavaScript Debugging

### Browser Dev Tools:
1. **Open browser dev tools** (F12)
2. **Go to Sources tab**
3. **Set breakpoints** in JavaScript code
4. **Use console.log()** for simple debugging

### JavaScript Debugging Features:
- **Console logging**: `console.log()`, `console.error()`, `console.warn()`
- **Breakpoints**: Set in browser dev tools
- **Variable inspection**: Hover over variables in dev tools
- **Network tab**: Monitor AJAX requests
- **Application tab**: Check localStorage, sessionStorage, cookies

## 3. HTML/CSS Debugging

### Browser Inspector:
1. **Right-click** on any element → "Inspect"
2. **Elements tab**: View/edit HTML structure
3. **Styles tab**: View/edit CSS properties
4. **Computed tab**: See final computed styles
5. **Console tab**: Check for JavaScript errors

### CSS Debugging Tips:
- **Box model**: Check margins, padding, borders
- **Flexbox/Grid**: Use browser grid/flexbox overlays
- **Responsive design**: Test different screen sizes
- **Lighthouse**: Performance and accessibility audits

## 4. Database Debugging

### MongoDB:
- **Mongo Express**: http://localhost:8081
- **View collections**: Check users, files collections
- **Query data**: Use the web interface
- **Monitor connections**: Check logs

### Connection debugging:
```php
try {
    $database = new Database();
    $manager = $database->connect();
    var_dump($manager); // Debug connection
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

## 5. Docker Debugging

### Container logs:
```bash
docker-compose logs php      # PHP container logs
docker-compose logs mongodb  # MongoDB logs
docker-compose logs nginx    # Nginx logs
```

### Container shell access:
```bash
docker-compose exec php bash    # Access PHP container
docker-compose exec mongodb bash # Access MongoDB container
```

## 6. Error Logging

### PHP Error Logging:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', '/tmp/php_errors.log');
```

### Custom logging:
```php
error_log("Debug message: " . print_r($variable, true));
```

## 7. Network Debugging

### Check HTTP requests:
- **Browser Network tab**: Monitor all requests
- **Response status codes**: 200, 404, 500, etc.
- **Request headers**: Check Content-Type, Authorization
- **Response times**: Performance analysis

### cURL debugging:
```bash
curl -v http://localhost:8080/register.php
```

## 8. VS Code Extensions for Debugging

### Recommended extensions:
- **PHP Debug**: Already installed
- **HTML CSS Support**: Auto-completion
- **JavaScript Debugger**: Built-in
- **REST Client**: Test API endpoints
- **Live Server**: Preview HTML changes

## 9. Common Debugging Scenarios

### 1. Form submission not working:
- Check browser Network tab
- Verify form method and action
- Check PHP error logs
- Inspect POST data

### 2. Database connection issues:
- Check container logs
- Verify credentials
- Test connection manually
- Check network connectivity

### 3. JavaScript not executing:
- Check browser console for errors
- Verify script tags
- Check syntax errors
- Ensure DOM is loaded

### 4. Styling issues:
- Use browser inspector
- Check CSS specificity
- Verify file paths
- Check for syntax errors

## 10. Debugging Best Practices

1. **Use version control**: Git for tracking changes
2. **Write tests**: Unit tests for functions
3. **Log everything**: Use proper logging levels
4. **Use breakpoints**: Instead of print statements
5. **Check one thing at a time**: Isolate issues
6. **Read error messages**: They usually tell you what's wrong
7. **Use debugging tools**: Don't guess, investigate

## Quick Start:
1. Start containers: `docker-compose up -d`
2. Open VS Code debug panel (Ctrl+Shift+D)
3. Select "Listen for Xdebug"
4. Set breakpoints in your PHP code
5. Visit http://localhost:8080/debug.php
6. Debug step by step!

Happy debugging! 🐛

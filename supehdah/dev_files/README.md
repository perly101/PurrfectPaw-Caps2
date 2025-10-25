# Development Files

This folder contains test files, debugging scripts, and development utilities that are not part of the core application but may be useful for development and troubleshooting.

## Contents:

### Test Files
- `test_email_*.php` - Email testing scripts
- `test_gmail.php` - Gmail integration testing
- `test_google_auth_endpoint.php` - Google OAuth testing
- `test_specific_sms.php` - SMS testing script
- `test-sms-direct.php` - Direct SMS testing

### Debugging & Development Scripts
- `check_columns.php` - Database column checker
- `check_password_nullable.php` - Password field validation checker
- `check_users_table.php` - User table structure checker
- `debug-clinic-fields.php` - Clinic fields debugging
- `diagnose_status.php` - Status diagnosis script
- `temp_check.php` - Temporary debugging script

### Installation & Configuration
- `disable_composer_ssl.php` - SSL configuration for Composer
- `disable_ssl_verify.php` - SSL verification disabling script
- `install_socialite.php` - Laravel Socialite installation script
- `add_migration_record.php` - Migration record management

### Development Resources
- `suggested_code/` - Code suggestions and templates
- `temp_archived.blade.php` - Temporary archived template

### Documentation
- `NOTIFICATION-SYSTEM-GUIDE.md` - Notification system documentation
- `PRODUCTION-EMAIL-SETUP.md` - Email configuration guide
- `README-PRODUCTION-SETUP.md` - Production deployment guide
- `SMS_INTEGRATION_README.md` - SMS integration documentation

## Usage Notes

These files are kept for:
1. **Development debugging** - When issues arise during development
2. **Testing integrations** - Email, SMS, OAuth testing
3. **Database troubleshooting** - Column and table validation
4. **Reference** - Code examples and suggestions

## Safety

These files are safe to keep and won't interfere with production deployment as they're not included in the main application flow.

---
*Organized on: October 25, 2025*
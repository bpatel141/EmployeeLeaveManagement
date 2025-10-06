# Quick Setup Guide - Employee Leave Management System

## 🚀 Quick Start (5 Minutes)

### Prerequisites Check
```bash
# Check PHP version (should be 8.2+)
php --version

# Check Composer
composer --version

# Check Node.js
node --version
npm --version
```

### 1. Clone and Install
```bash
# Clone repository
git clone <repository-url>
cd EmployeeLeaveManagement

# Install dependencies
composer install
npm install
```

### 2. Environment Setup
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Update database credentials in .env
DB_DATABASE=employee_leave_management
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 3. Database Setup
```bash
# Create database
mysql -u root -p
CREATE DATABASE employee_leave_management;
exit

# Run migrations
php artisan migrate

# Run seeders
php artisan db:seed
```

### 4. Start Application
```bash
# Compile assets
npm run dev

# Start server
php artisan serve

# Visit: http://127.0.0.1:8000
```

## 🔑 Default Login Credentials

### Admin Access
```
Email: admin@example.com
Password: password
```

### Employee Access
```
Email: employee@example.com
Password: password
```

## 📊 Sample Data Created

### Users (5 total)
- **1 Admin**: admin@example.com
- **4 Employees**: employee@example.com, user2@example.com, user3@example.com, user4@example.com

### Departments (3 total)
- HR Department
- IT Department
- Finance Department

### Leave Types (2 total)
- Sick Leave (7 days annually)
- Casual Leave (30 days annually)

### Leave Allocations
- Each user gets annual leave allocations
- Sick Leave: 7 days
- Casual Leave: 30 days

## 🛠️ Development Commands

### Database Operations
```bash
# Fresh migration with seeding
php artisan migrate:fresh --seed

# Run specific seeder
php artisan db:seed --class=UserSeeder

# Reset database
php artisan migrate:reset
```

### Cache Management
```bash
# Clear all caches
php artisan optimize:clear

# Clear specific cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Asset Management
```bash
# Development build
npm run dev

# Production build
npm run build

# Watch for changes
npm run watch
```

## 🔧 Common Issues & Solutions

### Issue 1: Livewire Service Provider Error
```bash
# Solution
rm bootstrap/cache/packages.php
rm bootstrap/cache/services.php
php artisan optimize:clear
```

### Issue 2: Asset Loading Issues
```bash
# Solution
php artisan view:clear
npm run build
chmod -R 755 public/
```

### Issue 3: Database Connection Error
```bash
# Check database configuration
php artisan config:show database

# Test connection
php artisan tinker
>>> DB::connection()->getPdo();
```

### Issue 4: Permission Denied
```bash
# Fix permissions
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

## 📁 Project Structure

```
EmployeeLeaveManagement/
├── app/
│   ├── Http/Controllers/
│   │   ├── Admin/          # Admin controllers
│   │   ├── Employee/       # Employee controllers
│   │   └── Auth/          # Authentication controllers
│   ├── Models/            # Eloquent models
│   ├── Repositories/      # Repository pattern
│   └── Http/Traits/       # Reusable traits
├── database/
│   ├── migrations/        # Database migrations
│   └── seeders/          # Database seeders
├── resources/
│   ├── views/            # Blade templates
│   ├── css/              # Custom CSS
│   └── js/               # Custom JavaScript
├── public/
│   ├── css/              # Compiled CSS
│   └── js/               # Compiled JS
└── routes/
    ├── web.php           # Web routes
    └── auth.php          # Auth routes
```

## 🎯 Key Features to Test

### Employee Features
1. **Login** → Dashboard → View leave allocations
2. **Submit Leave Request** → Fill form → Submit
3. **View Leave Requests** → Check status → Delete pending requests
4. **Profile Management** → Update information

### Admin Features
1. **Login** → Dashboard → View system statistics
2. **Employee Management** → Add/Edit/Delete employees
3. **Leave Requests** → Approve/Reject with comments
4. **Filtering** → Test various filters

## 🔍 Testing Checklist

### Basic Functionality
- [ ] User registration and login
- [ ] Role-based access control
- [ ] Leave request submission
- [ ] Leave request approval/rejection
- [ ] Employee management
- [ ] DataTable functionality
- [ ] AJAX operations
- [ ] Error handling

### UI/UX Testing
- [ ] Responsive design
- [ ] Modal functionality
- [ ] Form validation
- [ ] Success/error messages
- [ ] Loading states
- [ ] Navigation

### Performance Testing
- [ ] Page load times
- [ ] Database query optimization
- [ ] Asset loading
- [ ] Caching effectiveness

## 🚀 Production Deployment

### Environment Setup
```bash
# Update .env for production
APP_ENV=production
APP_DEBUG=false
DB_DATABASE=production_db
```

### Asset Compilation
```bash
# Install production dependencies
composer install --optimize-autoloader --no-dev

# Compile assets
npm run build

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Database Migration
```bash
# Run migrations
php artisan migrate --force

# Seed production data
php artisan db:seed --class=ProductionSeeder
```

## 📞 Support

### Getting Help
1. Check the README.md for detailed documentation
2. Review PROJECT_FLOW.md for system architecture
3. Check Laravel documentation for framework-specific issues
4. Create an issue in the repository

### Development Resources
- [Laravel Documentation](https://laravel.com/docs)
- [Tailwind CSS Documentation](https://tailwindcss.com/docs)
- [DataTables Documentation](https://datatables.net/manual/)
- [jQuery Documentation](https://api.jquery.com/)

---

**Happy Coding! 🎉**

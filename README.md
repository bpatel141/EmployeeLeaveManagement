# Employee Leave Management System

A comprehensive Laravel-based web application for managing employee leave requests, approvals, and tracking. This system provides role-based access control with separate interfaces for employees and administrators.

## 🚀 Features

### Employee Features
- **Leave Request Management**: Submit, view, and delete pending leave requests
- **Leave Allocations**: View available leave balances by type
- **Dashboard**: Personal dashboard with leave statistics
- **Profile Management**: Update personal information

### Admin Features
- **Employee Management**: Create, edit, and delete employee records
- **Leave Request Approval**: Approve or reject leave requests with comments
- **Leave Allocations**: Manage employee leave allocations
- **Advanced Filtering**: Filter employees and leave requests by various criteria
- **Dashboard**: Administrative dashboard with system statistics

### System Features
- **Role-Based Access Control**: Separate interfaces for employees and admins
- **DataTables Integration**: Advanced table functionality with server-side processing
- **Responsive Design**: Mobile-friendly interface
- **Real-time Updates**: AJAX-powered interactions without page reloads
- **Error Handling**: Comprehensive error handling with user-friendly messages

## 📋 Prerequisites

- PHP 8.2 or higher
- Composer
- MySQL 5.7 or higher
- Node.js and NPM (for asset compilation)
- Git

## 🛠️ Installation

### 1. Clone the Repository
```bash
git clone <repository-url>
cd EmployeeLeaveManagement
```

### 2. Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### 3. Environment Setup
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Database Configuration
Update your `.env` file with database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=employee_leave_management
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 5. Database Setup
```bash
# Run migrations
php artisan migrate

# Run seeders
php artisan db:seed
```

### 6. Asset Compilation
```bash
# Compile assets for development
npm run dev

# Or for production
npm run build
```

### 7. Start the Application
```bash
# Start Laravel development server
php artisan serve
Npm Run Dev

# The application will be available at http://127.0.0.1:8000
```

## 🗄️ Database Structure

### Core Tables

#### Users Table
- **Purpose**: Stores employee and admin user information
- **Key Fields**: name, email, password, department_id, join_date
- **Soft Deletes**: Enabled for data retention

#### Departments Table
- **Purpose**: Organizational departments
- **Key Fields**: name

#### Roles Table
- **Purpose**: User roles (admin, employee)
- **Key Fields**: name

#### Role User Table (Pivot)
- **Purpose**: Many-to-many relationship between users and roles

#### Leave Types Table
- **Purpose**: Types of leave (Sick, Casual, etc.)
- **Key Fields**: name, description

#### Leave Allocations Table
- **Purpose**: Annual leave allocations for employees
- **Key Fields**: user_id, leave_type_id, year, total_allocated, remaining

#### Leave Requests Table
- **Purpose**: Employee leave requests
- **Key Fields**: user_id, leave_type_id, start_date, end_date, reason, status, approved_by, approved_at, days, admin_comment
- **Soft Deletes**: Enabled

## 📊 Migrations

### Available Migrations
```bash
# List all migrations
php artisan migrate:status

# Run specific migration
php artisan migrate --path=/database/migrations/2024_01_01_000000_create_users_table.php

# Rollback last migration
php artisan migrate:rollback

# Rollback all migrations
php artisan migrate:reset

# Fresh migration (drop all tables and re-run)
php artisan migrate:fresh
```

### Migration Files
- `create_users_table.php` - User management
- `create_departments_table.php` - Department structure
- `create_roles_table.php` - Role definitions
- `create_role_user_table.php` - User-role relationships
- `create_leave_types_table.php` - Leave type definitions
- `create_leave_allocations_table.php` - Leave allocation management
- `create_leave_requests_table.php` - Leave request tracking

## 🌱 Seeders

### Available Seeders
```bash
# Run all seeders
php artisan db:seed

# Run specific seeder
php artisan db:seed --class=DatabaseSeeder

# Run individual seeders
php artisan db:seed --class=DepartmentSeeder
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=LeaveTypeSeeder
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=LeaveAllocationSeeder
```

### Seeder Classes
- **DatabaseSeeder**: Main seeder that calls all other seeders
- **DepartmentSeeder**: Creates sample departments
- **RoleSeeder**: Creates admin and employee roles
- **LeaveTypeSeeder**: Creates leave types (Sick, Casual, etc.)
- **UserSeeder**: Creates sample users (admin and employees)
- **LeaveAllocationSeeder**: Creates leave allocations for users

### Sample Data Created
- **1 Admin User**: admin@example.com / password
- **5 Employee Users**: Various departments and roles
- **3 Departments**: HR, IT, Finance
- **2 Leave Types**: Sick Leave, Casual Leave
- **Leave Allocations**: Annual allocations for all users

## 🔐 Authentication & Authorization

### Default Login Credentials
```
Admin:
Email: admin@example.com
Password: password
```

### Role-Based Access
- **Admin**: Full system access, can manage employees and approve/reject leave requests
- **Employee**: Limited access to personal leave management

### Authorization Policies
- **UserPolicy**: Controls user management permissions
- **Route Middleware**: Protects admin routes with 'can:admin' middleware

## 🎯 Project Flow

### 1. User Registration & Authentication
```
User Registration → Email Verification → Login → Role Assignment
```

### 2. Employee Workflow
```
Login → Dashboard → Leave Management → Submit Request → View Status
```

### 3. Admin Workflow
```
Login → Dashboard → Employee Management → Leave Requests → Approve/Reject
```

### 4. Leave Request Lifecycle
```
Submit Request → Pending Status → Admin Review → Approved/Rejected → Notification
```

## 🏗️ Architecture

### MVC Pattern
- **Models**: User, Department, Role, LeaveType, LeaveAllocation, LeaveRequest
- **Views**: Blade templates with responsive design
- **Controllers**: Admin and Employee controllers with proper separation

### Repository Pattern
- **EmployeeRepository**: Employee data management
- **LeaveRequestRepository**: Leave request operations
- **UserRepository**: User management operations

### Service Layer
- **ApiResponseTrait**: Standardized API responses
- **Request Validation**: Form request classes for validation
- **Policy Classes**: Authorization logic

## 📱 Frontend Technologies

### CSS Framework
- **Tailwind CSS**: Utility-first CSS framework
- **Custom Styles**: Modal and component-specific styling

### JavaScript Libraries
- **jQuery**: DOM manipulation and AJAX requests
- **DataTables**: Advanced table functionality
- **jQuery UI**: Date picker components

### Asset Management
- **Vite**: Modern build tool for asset compilation
- **Laravel Mix**: Asset compilation and optimization

## 🔧 Configuration

### Environment Variables
```env
APP_NAME="Employee Leave Management"
APP_ENV=local
APP_KEY=base64:your-app-key
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=employee_leave_management
DB_USERNAME=your_username
DB_PASSWORD=your_password

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### Cache Configuration
```bash
# Clear application cache
php artisan cache:clear

# Clear configuration cache
php artisan config:clear

# Clear route cache
php artisan route:clear

# Clear view cache
php artisan view:clear

# Clear all caches
php artisan optimize:clear
```

## 🚀 Deployment

### Production Setup
```bash
# Install dependencies
composer install --optimize-autoloader --no-dev

# Compile assets
npm run build

# Run migrations
php artisan migrate --force

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Server Requirements
- PHP 8.2+
- MySQL 5.7+
- Web server (Apache/Nginx)
- SSL certificate (recommended)

## 🧪 Testing

### Running Tests
```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter=UserTest

# Run with coverage
php artisan test --coverage
```

### Test Structure
- **Feature Tests**: End-to-end functionality testing
- **Unit Tests**: Individual component testing
- **Browser Tests**: Laravel Dusk for browser automation

## 📈 Performance Optimization

### Database Optimization
- **Indexes**: Proper indexing on frequently queried columns
- **Eager Loading**: Prevents N+1 query problems
- **Query Optimization**: Efficient database queries

### Caching Strategy
- **Route Caching**: Cached routes for better performance
- **View Caching**: Compiled views for faster rendering
- **Configuration Caching**: Cached configuration for faster boot

### Frontend Optimization
- **Asset Minification**: Compressed CSS and JavaScript
- **Image Optimization**: Optimized images for web
- **CDN Integration**: Content delivery network support

## 🔒 Security Features

### Authentication Security
- **Password Hashing**: Bcrypt password hashing
- **CSRF Protection**: Cross-site request forgery protection
- **Session Security**: Secure session management

### Authorization Security
- **Role-Based Access**: Granular permission system
- **Route Protection**: Middleware-based route protection
- **Policy-Based Authorization**: Laravel policies for authorization

### Data Security
- **Input Validation**: Comprehensive input validation
- **SQL Injection Prevention**: Eloquent ORM protection
- **XSS Protection**: Cross-site scripting prevention

## 🐛 Troubleshooting

### Common Issues

#### 1. Livewire Service Provider Error
```bash
# Clear all caches
php artisan optimize:clear

# Remove problematic cache files
rm bootstrap/cache/packages.php
rm bootstrap/cache/services.php

# Regenerate caches
php artisan package:discover
```

#### 2. Asset Loading Issues
```bash
# Clear view cache
php artisan view:clear

# Recompile assets
npm run build

# Check file permissions
chmod -R 755 public/
```

#### 3. Database Connection Issues
```bash
# Check database configuration
php artisan config:show database

# Test database connection
php artisan tinker
>>> DB::connection()->getPdo();
```

### Debug Mode
```env
APP_DEBUG=true
LOG_LEVEL=debug
```

## 📚 API Documentation

### Available Routes

#### Authentication Routes
- `GET /login` - Login page
- `POST /login` - Process login
- `POST /logout` - Logout user
- `GET /register` - Registration page
- `POST /register` - Process registration

#### Employee Routes
- `GET /employee/dashboard` - Employee dashboard
- `GET /employee/leaves` - Leave management
- `POST /employee/leaves` - Submit leave request
- `DELETE /employee/leaves/{id}` - Delete leave request

#### Admin Routes
- `GET /admin/dashboard` - Admin dashboard
- `GET /admin/employees` - Employee management
- `POST /admin/employees` - Create employee
- `PUT /admin/employees/{id}` - Update employee
- `DELETE /admin/employees/{id}` - Delete employee
- `GET /admin/leave-requests` - Leave request management
- `POST /admin/leave-requests/{id}/approve` - Approve leave request
- `POST /admin/leave-requests/{id}/reject` - Reject leave request

## 🤝 Contributing

### Development Setup
1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Run tests
5. Submit a pull request

### Code Standards
- Follow PSR-12 coding standards
- Write comprehensive tests
- Document your code
- Use meaningful commit messages

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 📞 Support

For support and questions:
- Create an issue in the repository
- Contact the development team
- Check the documentation

## 🔄 Version History

### v1.0.0 (Current)
- Initial release
- Basic leave management functionality
- Admin and employee interfaces
- Role-based access control
- DataTables integration
- Responsive design

### Future Releases
- Email notifications
- Calendar integration
- Advanced reporting
- Mobile app
- API endpoints

---

**Built with ❤️ using Laravel, Tailwind CSS, and modern web technologies.**
# Employee Leave Management - Architecture Documentation

## 🏗️ Architecture Overview

This application follows **Clean Architecture** principles with clear separation of concerns:

- **Controllers**: Thin layer that only coordinates requests/responses
- **Form Requests**: Handle validation and authorization logic
- **Repositories**: Abstract data access and business logic
- **Models**: Eloquent models with minimal business logic
- **Policies**: Authorization rules (Gate/Policy pattern)

---

## 📁 Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/
│   │   │   └── EmployeeController.php          # Employee management
│   │   ├── Auth/
│   │   │   └── RegisteredUserController.php    # User registration
│   │   └── ProfileController.php               # User profile
│   └── Requests/
│       ├── Auth/
│       │   ├── LoginRequest.php                # Login validation
│       │   └── RegisterRequest.php             # Registration validation
│       ├── Employee/
│       │   ├── StoreEmployeeRequest.php        # Create employee validation
│       │   ├── UpdateEmployeeRequest.php       # Update employee validation
│       │   └── DeleteEmployeeRequest.php       # Delete employee authorization
│       └── ProfileUpdateRequest.php            # Profile update validation
│
├── Repositories/
│   ├── Contracts/
│   │   ├── EmployeeRepositoryInterface.php     # Employee repository contract
│   │   └── UserRepositoryInterface.php         # User repository contract
│   ├── EmployeeRepository.php                  # Employee business logic
│   └── UserRepository.php                      # User business logic
│
├── Models/
│   ├── User.php                                # User model
│   ├── Department.php                          # Department model
│   ├── Role.php                                # Role model
│   ├── LeaveType.php                           # Leave type model
│   ├── LeaveRequest.php                        # Leave request model
│   └── LeaveAllocation.php                     # Leave allocation model
│
├── Policies/
│   └── UserPolicy.php                          # User authorization policies
│
└── Providers/
    ├── AppServiceProvider.php                  # Application service provider
    ├── AuthServiceProvider.php                 # Auth policies provider
    └── RepositoryServiceProvider.php           # Repository bindings
```

---

## 🔄 Request Flow

### Example: Creating an Employee

```
1. HTTP Request → EmployeeController@store
                    ↓
2. StoreEmployeeRequest validates & authorizes
                    ↓
3. Controller calls EmployeeRepository->createEmployee()
                    ↓
4. Repository handles business logic:
   - Creates user with hashed password
   - Attaches employee role
   - Wraps in DB transaction
                    ↓
5. Controller returns JSON response
```

---

## 📋 Form Requests (Validation & Authorization)

### Purpose
- Validate incoming request data
- Authorize the action before it reaches the controller
- Provide custom error messages
- Keep controllers clean

### Example: StoreEmployeeRequest

```php
class StoreEmployeeRequest extends FormRequest
{
    // Authorization logic
    public function authorize(): bool
    {
        return $this->user()->can('create', User::class);
    }

    // Validation rules
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'department_id' => 'nullable|exists:departments,id',
        ];
    }

    // Custom error messages
    public function messages(): array
    {
        return [
            'email.unique' => 'This email address is already registered.',
        ];
    }
}
```

### Available Form Requests

#### Employee Management
- `StoreEmployeeRequest` - Create new employee
- `UpdateEmployeeRequest` - Update employee
- `DeleteEmployeeRequest` - Delete employee

#### Authentication
- `LoginRequest` - User login
- `RegisterRequest` - User registration

#### Profile
- `ProfileUpdateRequest` - Update user profile

---

## 🗄️ Repository Pattern

### Purpose
- Abstract database queries from controllers
- Centralize business logic
- Make code testable (easy to mock)
- Promote code reuse

### Interface First Approach

```php
interface EmployeeRepositoryInterface
{
    public function createEmployee(array $data): User;
    public function updateEmployee(User $user, array $data): User;
    public function deleteEmployee(User $user): bool;
    public function getAllEmployeesQuery(): Builder;
    public function calculateHighestLeaves(User $user, string $filter, ?string $period): int;
}
```

### Implementation

```php
class EmployeeRepository implements EmployeeRepositoryInterface
{
    public function createEmployee(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $data['password'] = Hash::make('password');
            $user = User::create($data);
            $this->attachEmployeeRole($user);
            return $user->load(['roles', 'department']);
        });
    }
}
```

### Available Repositories

#### EmployeeRepository
- `getAllEmployeesQuery()` - Get employees with relationships
- `createEmployee($data)` - Create new employee with role
- `updateEmployee($user, $data)` - Update employee
- `deleteEmployee($user)` - Delete employee
- `findEmployee($id)` - Find employee by ID
- `attachEmployeeRole($user)` - Attach employee role
- `calculateHighestLeaves($user, $filter, $period)` - Calculate leave statistics

#### UserRepository
- `createUser($data)` - Create user
- `registerUser($data)` - Register user with employee role
- `updateUser($user, $data)` - Update user
- `deleteUser($user)` - Delete user
- `findUser($id)` - Find user by ID
- `findUserByEmail($email)` - Find user by email
- `attachRole($user, $roleName)` - Attach role to user

---

## 🎯 Dependency Injection

### Registering Repositories

In `RepositoryServiceProvider`:

```php
public function register(): void
{
    $this->app->bind(
        EmployeeRepositoryInterface::class,
        EmployeeRepository::class
    );

    $this->app->bind(
        UserRepositoryInterface::class,
        UserRepository::class
    );
}
```

### Using in Controllers

```php
class EmployeeController extends Controller
{
    protected EmployeeRepositoryInterface $employeeRepository;

    public function __construct(EmployeeRepositoryInterface $employeeRepository)
    {
        $this->employeeRepository = $employeeRepository;
    }

    public function store(StoreEmployeeRequest $request): JsonResponse
    {
        $user = $this->employeeRepository->createEmployee($request->validated());
        
        return response()->json([
            'success' => true,
            'user' => $user
        ]);
    }
}
```

---

## ✅ Benefits of This Architecture

### 1. **Separation of Concerns**
- Controllers are thin (only coordinate)
- Business logic is in repositories
- Validation is in form requests
- Authorization is in policies and requests

### 2. **Testability**
- Easy to mock repositories
- Can test validation independently
- Business logic is isolated

### 3. **Maintainability**
- Changes to business logic don't affect controllers
- Validation changes are isolated
- Clear structure makes code easy to find

### 4. **Reusability**
- Repository methods can be called from anywhere
- Validation rules in form requests are reusable
- Business logic is centralized

### 5. **SOLID Principles**
- **S**ingle Responsibility: Each class has one job
- **O**pen/Closed: Easy to extend without modifying
- **L**iskov Substitution: Interfaces ensure substitutability
- **I**nterface Segregation: Small, focused interfaces
- **D**ependency Inversion: Depend on abstractions (interfaces)

---

## 🔐 Authorization Flow

### Policy-Based Authorization

Authorization happens at two levels:

1. **Form Request Level**
```php
public function authorize(): bool
{
    return $this->user()->can('create', User::class);
}
```

2. **Controller Level** (fallback)
```php
$this->authorize('viewAny', User::class);
```

### User Policy

Defined in `app/Policies/UserPolicy.php`:

```php
public function create(User $user): bool
{
    return $user->isAdmin();
}

public function update(User $user, User $model): bool
{
    return $user->isAdmin();
}

public function delete(User $user, User $model): bool
{
    return $user->isAdmin();
}
```

---

## 🚀 Adding New Features

### Step 1: Create Form Request

```bash
php artisan make:request Department/StoreDepartmentRequest
```

```php
class StoreDepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Department::class);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:departments,name',
        ];
    }
}
```

### Step 2: Create Repository Interface

```php
interface DepartmentRepositoryInterface
{
    public function getAllDepartments(): Collection;
    public function createDepartment(array $data): Department;
    public function updateDepartment(Department $dept, array $data): Department;
    public function deleteDepartment(Department $dept): bool;
}
```

### Step 3: Create Repository Implementation

```php
class DepartmentRepository implements DepartmentRepositoryInterface
{
    public function createDepartment(array $data): Department
    {
        return DB::transaction(function () use ($data) {
            return Department::create($data);
        });
    }
    // ... other methods
}
```

### Step 4: Register in Service Provider

```php
$this->app->bind(
    DepartmentRepositoryInterface::class,
    DepartmentRepository::class
);
```

### Step 5: Create Controller

```php
class DepartmentController extends Controller
{
    public function __construct(
        protected DepartmentRepositoryInterface $departmentRepository
    ) {}

    public function store(StoreDepartmentRequest $request): JsonResponse
    {
        $department = $this->departmentRepository->createDepartment(
            $request->validated()
        );
        
        return response()->json(['success' => true, 'data' => $department]);
    }
}
```

---

## 📚 Best Practices

### Controllers
- ✅ Keep them thin (< 50 lines per method)
- ✅ Use type hints for dependencies
- ✅ Return consistent response formats
- ❌ Don't put business logic here
- ❌ Don't use raw `Request $request->validate()`

### Form Requests
- ✅ One request per action (Store, Update, Delete)
- ✅ Include authorization logic
- ✅ Provide custom error messages
- ✅ Use attributes for better error messages
- ❌ Don't put business logic here

### Repositories
- ✅ Use database transactions for multi-step operations
- ✅ Return models with loaded relationships
- ✅ Use type hints for parameters and return values
- ✅ Keep methods focused (single responsibility)
- ❌ Don't inject Request objects
- ❌ Don't return Eloquent Builder (return Collection or Model)

### Models
- ✅ Define relationships
- ✅ Define casts and attributes
- ✅ Use accessors/mutators for simple transformations
- ❌ Don't put complex business logic here
- ❌ Don't make external API calls

---

## 🧪 Testing

### Testing Repositories

```php
class EmployeeRepositoryTest extends TestCase
{
    public function test_creates_employee_with_role()
    {
        $repo = new EmployeeRepository();
        
        $user = $repo->createEmployee([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'department_id' => 1,
        ]);
        
        $this->assertDatabaseHas('users', ['email' => 'john@example.com']);
        $this->assertTrue($user->roles()->where('name', 'employee')->exists());
    }
}
```

### Testing Controllers

```php
class EmployeeControllerTest extends TestCase
{
    public function test_admin_can_create_employee()
    {
        $admin = User::factory()->create();
        $admin->roles()->attach(Role::where('name', 'admin')->first());
        
        $response = $this->actingAs($admin)
            ->postJson('/admin/employees', [
                'name' => 'Jane Doe',
                'email' => 'jane@example.com',
            ]);
        
        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }
}
```

---

## 📖 Conclusion

This architecture provides:
- **Clean, maintainable code**
- **Easy to test**
- **Follows industry standards**
- **Scalable for future growth**
- **Clear separation of concerns**

All business logic is centralized in repositories, validation in form requests, and controllers remain thin coordinators.


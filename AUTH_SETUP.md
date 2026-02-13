# Authentication & Authorization Setup

This application has been set up with a complete authentication system including roles and permissions.

## 🔐 Login Credentials

**Super Admin Account:**
- Email: `janes.oosthuizen@gmail.com`
- Password: `password`

## 📝 Features

### Authentication
- Login page at `/login`
- Dashboard at `/dashboard` (requires authentication)
- Logout functionality

### Roles & Permissions System

#### Roles
- **super-admin**: Full access to all permissions
- **admin**: Most permissions (view/create/edit users, view roles, dashboard)
- **user**: Basic permissions (view dashboard only)

#### Permissions
- `view-users`: View users
- `create-users`: Create new users
- `edit-users`: Edit existing users
- `delete-users`: Delete users
- `view-roles`: View roles
- `manage-roles`: Manage roles and permissions
- `view-dashboard`: Access dashboard

### Database Structure
- `users` table: User accounts
- `roles` table: User roles
- `permissions` table: Granular permissions
- `role_user` pivot table: User-role relationships
- `permission_role` pivot table: Role-permission relationships

## 🚀 Getting Started

1. **Access the application:**
   ```
   http://127.0.0.1:8000
   ```

2. **Login:**
   - Navigate to http://127.0.0.1:8000/login
   - Use the super admin credentials above

3. **Dashboard:**
   - After login, you'll be redirected to the dashboard
   - The dashboard displays your roles and permissions

## 🛠️ Using Middleware

### Protect Routes by Role
```php
Route::middleware(['auth', 'role:super-admin'])->group(function () {
    // Only super-admins can access these routes
});
```

### Protect Routes by Permission
```php
Route::middleware(['auth', 'permission:manage-roles'])->group(function () {
    // Only users with 'manage-roles' permission can access
});
```

### Multiple Middleware
```php
Route::get('/admin/users', function () {
    // Your code
})->middleware(['auth', 'role:admin']);
```

## 📊 User Model Helper Methods

```php
// Check if user has a role
$user->hasRole('super-admin');

// Check if user has any of the given roles
$user->hasAnyRole(['admin', 'super-admin']);

// Check if user has a permission
$user->hasPermission('edit-users');

// Check if user is super admin
$user->isSuperAdmin();

// Get user's roles
$user->roles;

// Get all permissions through roles
$user->roles->flatMap->permissions;
```

## 🔄 Seeding

To re-seed the database with roles, permissions, and super admin:
```bash
php artisan db:seed --class=RolePermissionSeeder
```

To reset and re-seed everything:
```bash
php artisan migrate:fresh --seed
```

## 📁 Important Files

- **Models:**
  - `app/Models/User.php` - User model with role/permission methods
  - `app/Models/Role.php` - Role model
  - `app/Models/Permission.php` - Permission model

- **Controllers:**
  - `app/Http/Controllers/Auth/LoginController.php` - Login/logout logic

- **Middleware:**
  - `app/Http/Middleware/CheckRole.php` - Role-based access control
  - `app/Http/Middleware/CheckPermission.php` - Permission-based access control

- **Views:**
  - `resources/views/auth/login.blade.php` - Login page
  - `resources/views/dashboard.blade.php` - Dashboard page

- **Migrations:**
  - `database/migrations/2026_01_26_065011_create_roles_and_permissions_tables.php`

- **Seeders:**
  - `database/seeders/RolePermissionSeeder.php` - Seeds roles, permissions, and super admin

## 🎨 Views

Both the login and dashboard pages feature modern, responsive designs with:
- Gradient backgrounds
- Clean card-based layouts
- Form validation
- Error display
- Role and permission badges

## 🔒 Security Notes

- Passwords are hashed using Laravel's built-in hashing
- CSRF protection is enabled on all forms
- Session regeneration on login
- Session invalidation on logout
- Remember me functionality available

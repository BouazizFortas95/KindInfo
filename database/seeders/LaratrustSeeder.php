<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class LaratrustSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            // Create Permissions
            $permissions = $this->createPermissions();

            // Create Roles
            $roles = $this->createRoles();

            // Assign Permissions to Roles
            $this->assignPermissionsToRoles($roles, $permissions);

            // Create Super Admin User
            $this->createSuperAdminUser($roles['super_admin']);
        });
    }

    /**
     * Create all permissions.
     */
    private function createPermissions(): array
    {
        $permissionsData = [
            // User Management
            [
                'name' => 'view-users',
                'display_name' => ['en' => 'View Users', 'ar' => 'عرض المستخدمين'],
                'description' => ['en' => 'Can view users list', 'ar' => 'يمكن عرض قائمة المستخدمين']
            ],
            [
                'name' => 'create-users',
                'display_name' => ['en' => 'Create Users', 'ar' => 'إنشاء المستخدمين'],
                'description' => ['en' => 'Can create new users', 'ar' => 'يمكن إنشاء مستخدمين جدد']
            ],
            [
                'name' => 'edit-users',
                'display_name' => ['en' => 'Edit Users', 'ar' => 'تحرير المستخدمين'],
                'description' => ['en' => 'Can edit existing users', 'ar' => 'يمكن تحرير المستخدمين الموجودين']
            ],
            [
                'name' => 'delete-users',
                'display_name' => ['en' => 'Delete Users', 'ar' => 'حذف المستخدمين'],
                'description' => ['en' => 'Can delete users', 'ar' => 'يمكن حذف المستخدمين']
            ],
            [
                'name' => 'manage-users',
                'display_name' => ['en' => 'Manage Users', 'ar' => 'إدارة المستخدمين'],
                'description' => ['en' => 'Full control over users', 'ar' => 'سيطرة كاملة على المستخدمين']
            ],

            // Role Management
            [
                'name' => 'view-roles',
                'display_name' => ['en' => 'View Roles', 'ar' => 'عرض الأدوار'],
                'description' => ['en' => 'Can view roles list', 'ar' => 'يمكن عرض قائمة الأدوار']
            ],
            [
                'name' => 'create-roles',
                'display_name' => ['en' => 'Create Roles', 'ar' => 'إنشاء الأدوار'],
                'description' => ['en' => 'Can create new roles', 'ar' => 'يمكن إنشاء أدوار جديدة']
            ],
            [
                'name' => 'edit-roles',
                'display_name' => ['en' => 'Edit Roles', 'ar' => 'تحرير الأدوار'],
                'description' => ['en' => 'Can edit existing roles', 'ar' => 'يمكن تحرير الأدوار الموجودة']
            ],
            [
                'name' => 'delete-roles',
                'display_name' => ['en' => 'Delete Roles', 'ar' => 'حذف الأدوار'],
                'description' => ['en' => 'Can delete roles', 'ar' => 'يمكن حذف الأدوار']
            ],
            [
                'name' => 'manage-roles',
                'display_name' => ['en' => 'Manage Roles', 'ar' => 'إدارة الأدوار'],
                'description' => ['en' => 'Full control over roles', 'ar' => 'سيطرة كاملة على الأدوار']
            ],

            // Permission Management
            [
                'name' => 'view-permissions',
                'display_name' => ['en' => 'View Permissions', 'ar' => 'عرض الصلاحيات'],
                'description' => ['en' => 'Can view permissions list', 'ar' => 'يمكن عرض قائمة الصلاحيات']
            ],
            [
                'name' => 'create-permissions',
                'display_name' => ['en' => 'Create Permissions', 'ar' => 'إنشاء الصلاحيات'],
                'description' => ['en' => 'Can create new permissions', 'ar' => 'يمكن إنشاء صلاحيات جديدة']
            ],
            [
                'name' => 'edit-permissions',
                'display_name' => ['en' => 'Edit Permissions', 'ar' => 'تحرير الصلاحيات'],
                'description' => ['en' => 'Can edit existing permissions', 'ar' => 'يمكن تحرير الصلاحيات الموجودة']
            ],
            [
                'name' => 'delete-permissions',
                'display_name' => ['en' => 'Delete Permissions', 'ar' => 'حذف الصلاحيات'],
                'description' => ['en' => 'Can delete permissions', 'ar' => 'يمكن حذف الصلاحيات']
            ],
            [
                'name' => 'manage-permissions',
                'display_name' => ['en' => 'Manage Permissions', 'ar' => 'إدارة الصلاحيات'],
                'description' => ['en' => 'Full control over permissions', 'ar' => 'سيطرة كاملة على الصلاحيات']
            ],

            // Course Management
            [
                'name' => 'view-courses',
                'display_name' => ['en' => 'View Courses', 'ar' => 'عرض الدورات'],
                'description' => ['en' => 'Can view courses list', 'ar' => 'يمكن عرض قائمة الدورات']
            ],
            [
                'name' => 'create-courses',
                'display_name' => ['en' => 'Create Courses', 'ar' => 'إنشاء الدورات'],
                'description' => ['en' => 'Can create new courses', 'ar' => 'يمكن إنشاء دورات جديدة']
            ],
            [
                'name' => 'edit-courses',
                'display_name' => ['en' => 'Edit Courses', 'ar' => 'تحرير الدورات'],
                'description' => ['en' => 'Can edit existing courses', 'ar' => 'يمكن تحرير الدورات الموجودة']
            ],
            [
                'name' => 'delete-courses',
                'display_name' => ['en' => 'Delete Courses', 'ar' => 'حذف الدورات'],
                'description' => ['en' => 'Can delete courses', 'ar' => 'يمكن حذف الدورات']
            ],
            [
                'name' => 'manage-courses',
                'display_name' => ['en' => 'Manage Courses', 'ar' => 'إدارة الدورات'],
                'description' => ['en' => 'Full control over courses', 'ar' => 'سيطرة كاملة على الدورات']
            ],

            // Lesson Management
            [
                'name' => 'view-lessons',
                'display_name' => ['en' => 'View Lessons', 'ar' => 'عرض الدروس'],
                'description' => ['en' => 'Can view lessons list', 'ar' => 'يمكن عرض قائمة الدروس']
            ],
            [
                'name' => 'create-lessons',
                'display_name' => ['en' => 'Create Lessons', 'ar' => 'إنشاء الدروس'],
                'description' => ['en' => 'Can create new lessons', 'ar' => 'يمكن إنشاء دروس جديدة']
            ],
            [
                'name' => 'edit-lessons',
                'display_name' => ['en' => 'Edit Lessons', 'ar' => 'تحرير الدروس'],
                'description' => ['en' => 'Can edit existing lessons', 'ar' => 'يمكن تحرير الدروس الموجودة']
            ],
            [
                'name' => 'delete-lessons',
                'display_name' => ['en' => 'Delete Lessons', 'ar' => 'حذف الدروس'],
                'description' => ['en' => 'Can delete lessons', 'ar' => 'يمكن حذف الدروس']
            ],
            [
                'name' => 'manage-lessons',
                'display_name' => ['en' => 'Manage Lessons', 'ar' => 'إدارة الدروس'],
                'description' => ['en' => 'Full control over lessons', 'ar' => 'سيطرة كاملة على الدروس']
            ],

            // Work Management
            [
                'name' => 'view-works',
                'display_name' => ['en' => 'View Works', 'ar' => 'عرض الأعمال'],
                'description' => ['en' => 'Can view works list', 'ar' => 'يمكن عرض قائمة الأعمال']
            ],
            [
                'name' => 'create-works',
                'display_name' => ['en' => 'Create Works', 'ar' => 'إنشاء الأعمال'],
                'description' => ['en' => 'Can create new works', 'ar' => 'يمكن إنشاء أعمال جديدة']
            ],
            [
                'name' => 'edit-works',
                'display_name' => ['en' => 'Edit Works', 'ar' => 'تحرير الأعمال'],
                'description' => ['en' => 'Can edit existing works', 'ar' => 'يمكن تحرير الأعمال الموجودة']
            ],
            [
                'name' => 'delete-works',
                'display_name' => ['en' => 'Delete Works', 'ar' => 'حذف الأعمال'],
                'description' => ['en' => 'Can delete works', 'ar' => 'يمكن حذف الأعمال']
            ],
            [
                'name' => 'manage-works',
                'display_name' => ['en' => 'Manage Works', 'ar' => 'إدارة الأعمال'],
                'description' => ['en' => 'Full control over works', 'ar' => 'سيطرة كاملة على الأعمال']
            ],

            // Category Management
            [
                'name' => 'view-categories',
                'display_name' => ['en' => 'View Categories', 'ar' => 'عرض الفئات'],
                'description' => ['en' => 'Can view categories list', 'ar' => 'يمكن عرض قائمة الفئات']
            ],
            [
                'name' => 'create-categories',
                'display_name' => ['en' => 'Create Categories', 'ar' => 'إنشاء الفئات'],
                'description' => ['en' => 'Can create new categories', 'ar' => 'يمكن إنشاء فئات جديدة']
            ],
            [
                'name' => 'edit-categories',
                'display_name' => ['en' => 'Edit Categories', 'ar' => 'تحرير الفئات'],
                'description' => ['en' => 'Can edit existing categories', 'ar' => 'يمكن تحرير الفئات الموجودة']
            ],
            [
                'name' => 'delete-categories',
                'display_name' => ['en' => 'Delete Categories', 'ar' => 'حذف الفئات'],
                'description' => ['en' => 'Can delete categories', 'ar' => 'يمكن حذف الفئات']
            ],
            [
                'name' => 'manage-categories',
                'display_name' => ['en' => 'Manage Categories', 'ar' => 'إدارة الفئات'],
                'description' => ['en' => 'Full control over categories', 'ar' => 'سيطرة كاملة على الفئات']
            ],

            // Service Management
            [
                'name' => 'view-services',
                'display_name' => ['en' => 'View Services', 'ar' => 'عرض الخدمات'],
                'description' => ['en' => 'Can view services list', 'ar' => 'يمكن عرض قائمة الخدمات']
            ],
            [
                'name' => 'create-services',
                'display_name' => ['en' => 'Create Services', 'ar' => 'إنشاء الخدمات'],
                'description' => ['en' => 'Can create new services', 'ar' => 'يمكن إنشاء خدمات جديدة']
            ],
            [
                'name' => 'edit-services',
                'display_name' => ['en' => 'Edit Services', 'ar' => 'تحرير الخدمات'],
                'description' => ['en' => 'Can edit existing services', 'ar' => 'يمكن تحرير الخدمات الموجودة']
            ],
            [
                'name' => 'delete-services',
                'display_name' => ['en' => 'Delete Services', 'ar' => 'حذف الخدمات'],
                'description' => ['en' => 'Can delete services', 'ar' => 'يمكن حذف الخدمات']
            ],
            [
                'name' => 'manage-services',
                'display_name' => ['en' => 'Manage Services', 'ar' => 'إدارة الخدمات'],
                'description' => ['en' => 'Full control over services', 'ar' => 'سيطرة كاملة على الخدمات']
            ],

            // Testimonial Management
            [
                'name' => 'view-testimonials',
                'display_name' => ['en' => 'View Testimonials', 'ar' => 'عرض الشهادات'],
                'description' => ['en' => 'Can view testimonials list', 'ar' => 'يمكن عرض قائمة الشهادات']
            ],
            [
                'name' => 'create-testimonials',
                'display_name' => ['en' => 'Create Testimonials', 'ar' => 'إنشاء الشهادات'],
                'description' => ['en' => 'Can create new testimonials', 'ar' => 'يمكن إنشاء شهادات جديدة']
            ],
            [
                'name' => 'edit-testimonials',
                'display_name' => ['en' => 'Edit Testimonials', 'ar' => 'تحرير الشهادات'],
                'description' => ['en' => 'Can edit existing testimonials', 'ar' => 'يمكن تحرير الشهادات الموجودة']
            ],
            [
                'name' => 'delete-testimonials',
                'display_name' => ['en' => 'Delete Testimonials', 'ar' => 'حذف الشهادات'],
                'description' => ['en' => 'Can delete testimonials', 'ar' => 'يمكن حذف الشهادات']
            ],
            [
                'name' => 'manage-testimonials',
                'display_name' => ['en' => 'Manage Testimonials', 'ar' => 'إدارة الشهادات'],
                'description' => ['en' => 'Full control over testimonials', 'ar' => 'سيطرة كاملة على الشهادات']
            ],

            // Contact Message Management
            [
                'name' => 'view-contact-messages',
                'display_name' => ['en' => 'View Contact Messages', 'ar' => 'عرض رسائل الاتصال'],
                'description' => ['en' => 'Can view contact messages list', 'ar' => 'يمكن عرض قائمة رسائل الاتصال']
            ],
            [
                'name' => 'delete-contact-messages',
                'display_name' => ['en' => 'Delete Contact Messages', 'ar' => 'حذف رسائل الاتصال'],
                'description' => ['en' => 'Can delete contact messages', 'ar' => 'يمكن حذف رسائل الاتصال']
            ],
            [
                'name' => 'manage-contact-messages',
                'display_name' => ['en' => 'Manage Contact Messages', 'ar' => 'إدارة رسائل الاتصال'],
                'description' => ['en' => 'Full control over contact messages', 'ar' => 'سيطرة كاملة على رسائل الاتصال']
            ],

            // Settings Management
            [
                'name' => 'view-settings',
                'display_name' => ['en' => 'View Settings', 'ar' => 'عرض الإعدادات'],
                'description' => ['en' => 'Can view settings', 'ar' => 'يمكن عرض الإعدادات']
            ],
            [
                'name' => 'edit-settings',
                'display_name' => ['en' => 'Edit Settings', 'ar' => 'تحرير الإعدادات'],
                'description' => ['en' => 'Can edit settings', 'ar' => 'يمكن تحرير الإعدادات']
            ],
            [
                'name' => 'manage-settings',
                'display_name' => ['en' => 'Manage Settings', 'ar' => 'إدارة الإعدادات'],
                'description' => ['en' => 'Full control over settings', 'ar' => 'سيطرة كاملة على الإعدادات']
            ],

            // System Administration
            [
                'name' => 'access-admin-panel',
                'display_name' => ['en' => 'Access Admin Panel', 'ar' => 'الوصول إلى لوحة الإدارة'],
                'description' => ['en' => 'Can access the admin panel', 'ar' => 'يمكن الوصول إلى لوحة الإدارة']
            ],
            [
                'name' => 'manage-system',
                'display_name' => ['en' => 'Manage System', 'ar' => 'إدارة النظام'],
                'description' => ['en' => 'Full system administration access', 'ar' => 'وصول كامل لإدارة النظام']
            ],
        ];

        $permissions = [];
        foreach ($permissionsData as $permissionData) {
            $newPermission = Permission::firstOrCreate(['name' => $permissionData['name']]);

            // Set main table fields to default locale (en)
            $newPermission->display_name = $permissionData['display_name']['en'] ?? '';
            $newPermission->description = $permissionData['description']['en'] ?? '';

            foreach ($permissionData['display_name'] as $locale => $displayName) {
                $translation = $newPermission->translateOrNew($locale);
                $translation->display_name = $displayName;
                $translation->description = $permissionData['description'][$locale] ?? '';
            }

            $newPermission->save();
            $permissions[$permissionData['name']] = $newPermission;
        }

        return $permissions;
    }

    /**
     * Create all roles.
     */
    private function createRoles(): array
    {
        $rolesData = [
            [
                'name' => 'super_admin',
                'display_name' => [
                    'en' => 'Super Administrator',
                    'ar' => 'مدير عام'
                ],
                'description' => [
                    'en' => 'Total control over everything in the system',
                    'ar' => 'سيطرة كاملة على كل شيء في النظام'
                ],
            ],
            [
                'name' => 'admin',
                'display_name' => [
                    'en' => 'Administrator',
                    'ar' => 'مدير'
                ],
                'description' => [
                    'en' => 'Can manage content and settings but not system-level configurations',
                    'ar' => 'يمكن إدارة المحتوى والإعدادات ولكن ليس التكوينات على مستوى النظام'
                ],
            ],
            [
                'name' => 'editor',
                'display_name' => [
                    'en' => 'Editor',
                    'ar' => 'محرر'
                ],
                'description' => [
                    'en' => 'Can only manage Courses, Lessons, and Works',
                    'ar' => 'يمكن إدارة الدورات والدروس والأعمال فقط'
                ],
            ],
            [
                'name' => 'user',
                'display_name' => [
                    'en' => 'User',
                    'ar' => 'مستخدم'
                ],
                'description' => [
                    'en' => 'Front-end access only',
                    'ar' => 'الوصول إلى الواجهة الأمامية فقط'
                ],
            ],
        ];

        $roles = [];
        foreach ($rolesData as $roleData) {
            $newRole = Role::firstOrCreate(['name' => $roleData['name']]);

            foreach ($roleData['display_name'] as $locale => $displayName) {
                $translation = $newRole->translateOrNew($locale);
                $translation->display_name = $displayName;
                $translation->description = $roleData['description'][$locale] ?? '';
            }

            $newRole->save();
            $roles[$roleData['name']] = $newRole;
        }

        return $roles;
    }

    /**
     * Assign permissions to roles.
     */
    private function assignPermissionsToRoles(array $roles, array $permissions): void
    {
        // Super Admin - All permissions
        $roles['super_admin']->syncPermissions(array_values($permissions));

        // Admin - Content and settings management (no system-level)
        $adminPermissions = [
            // Users (view and edit only, no delete or manage)
            'view-users', 'create-users', 'edit-users',
            // Courses
            'view-courses', 'create-courses', 'edit-courses', 'delete-courses', 'manage-courses',
            // Lessons
            'view-lessons', 'create-lessons', 'edit-lessons', 'delete-lessons', 'manage-lessons',
            // Works
            'view-works', 'create-works', 'edit-works', 'delete-works', 'manage-works',
            // Categories
            'view-categories', 'create-categories', 'edit-categories', 'delete-categories', 'manage-categories',
            // Services
            'view-services', 'create-services', 'edit-services', 'delete-services', 'manage-services',
            // Testimonials
            'view-testimonials', 'create-testimonials', 'edit-testimonials', 'delete-testimonials', 'manage-testimonials',
            // Contact Messages
            'view-contact-messages', 'delete-contact-messages', 'manage-contact-messages',
            // Settings
            'view-settings', 'edit-settings',
            // Admin Panel Access
            'access-admin-panel',
        ];
        $roles['admin']->syncPermissions(
            array_filter($permissions, fn($key) => in_array($key, $adminPermissions), ARRAY_FILTER_USE_KEY)
        );

        // Editor - Only Courses, Lessons, and Works
        $editorPermissions = [
            // Courses
            'view-courses', 'create-courses', 'edit-courses', 'delete-courses',
            // Lessons
            'view-lessons', 'create-lessons', 'edit-lessons', 'delete-lessons',
            // Works
            'view-works', 'create-works', 'edit-works', 'delete-works',
            // Admin Panel Access
            'access-admin-panel',
        ];
        $roles['editor']->syncPermissions(
            array_filter($permissions, fn($key) => in_array($key, $editorPermissions), ARRAY_FILTER_USE_KEY)
        );

        // User - No admin permissions (front-end only)
        $roles['user']->syncPermissions([]);
    }

    /**
     * Create the super admin user.
     */
    private function createSuperAdminUser(Role $superAdminRole): void
    {
        $superAdmin = User::firstOrCreate(
            [
                'name' => 'Super Admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Attach super_admin role if not already attached
        if (!$superAdmin->hasRole('super_admin')) {
            $superAdmin->addRole($superAdminRole);
        }
    }
}

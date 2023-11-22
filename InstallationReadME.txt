Composer install
php artisan migrate
php artisan permission:create-permission-routes
php artisan db:seed

Order TO fill Data
1. Settings - Website Settings
2. Roles
3. properties
4. Units
5. Users
6. Utilities
7. Leases

Roles
Super Admin is the only one who can create and edit roles, permissions, properties
Administrator
Has control of every module and properties and cannot be edited
+ creates Units, Unitdetails, so by this he has access to all units

Agency Company
Has access to all modules but can only manage assigned properties and units

Property Owner
Has access to most modules but can only manage assigned properties and units


Rental Owner / Landlord
Also a landlord.Limited access to modules and can only access assigned units

Leasing Agent
Has access to the leasing workflow

Manager Staff
Has access to maintenance workflows

Staff
Has access to tasks only

Tenant
Has access to their information



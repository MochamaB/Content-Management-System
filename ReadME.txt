Composer install
php artisan migrate
php artisan permission:create-permission-routes
php artisan db:seed

Order TO fill Data
1. Settings
2. Roles
3. Users

Admin
Has control of every module and properties and cannot be edited

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

Tenant
Has access to their information



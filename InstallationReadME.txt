Composer install
cp .env.example .env
<------------- Configs ---------------------->
Change DB Name in .env file
change sync to QUEUE_CONNECTION=database in .env file

<!-------- IF running a new DB --------------->
Run php artisan queue:table  --Generate the jobs table for the message queue.
php artisan notifications:table -- Notifictions table ---if not generated
php artisan migrate
php artisan permission:create-permission-routes
php artisan db:seed  --individual php artisan db:seed --class=UserSeeder


<!-------  Run on alll new instances -------->
php artisan schedule-monitor:sync  ---Add all tasks to spatie monitor 
php artisan storage:link -- create link for spatie media library

php artisan queue:work ---Keep running all the time



Order TO fill Data
1. Website - Website Settings
2. Roles
3. properties
4. payment Methods
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


Added Packages

  barryvdh/laravel-debugbar 
  barryvdh/laravel-dompdf 
  fruitcake/laravel-cors  
  intervention/image 
  laravel-arcanist/arcanist 
  laravel/breeze 
  laravel/sail 
  laravel/sanctum 
  laravel/tinker 
  lorisleiva/laravel-actions 
  lorisleiva/lody 
  nesbot/carbon 
  nunomaduro/collision 
  nunomaduro/termwind 
  spatie/laravel-ignition 
  spatie/laravel-medialibrary 
  spatie/laravel-permission 
  spatie/laravel-schedule-monitor .



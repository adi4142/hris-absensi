<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "--- ROLES ---\n";
$roles = App\Role::all();
foreach ($roles as $role) {
    echo "ID: " . $role->roles_id . " | Name: [" . $role->name . "]\n";
}

echo "\n--- USERS ---\n";
$users = App\User::with('role')->get();
foreach ($users as $user) {
    echo "ID: " . $user->user_id . " | Email: " . $user->email . " | RoleID: " . $user->roles_id . " | RoleName: [" . ($user->role ? $user->role->name : "NULL") . "]\n";
}

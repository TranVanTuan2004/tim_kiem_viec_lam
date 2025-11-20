<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if roles table exists and has data
        if (Schema::hasTable('roles')) {
            $oldRoles = DB::table('roles')->get();
            
            foreach ($oldRoles as $oldRole) {
                // Only update if guard_name doesn't exist or is null
                if (!isset($oldRole->guard_name) || is_null($oldRole->guard_name)) {
                    DB::table('roles')->where('id', $oldRole->id)->update([
                        'guard_name' => 'web',
                    ]);
                }
            }
        }

        // Migrate data from role_user to model_has_roles (if role_user exists)
        if (Schema::hasTable('role_user')) {
            $roleUserData = DB::table('role_user')->get();
            
            foreach ($roleUserData as $roleUser) {
                // Check if record already exists to avoid duplicates
                $exists = DB::table('model_has_roles')
                    ->where('role_id', $roleUser->role_id)
                    ->where('model_type', 'App\\Models\\User')
                    ->where('model_id', $roleUser->user_id)
                    ->exists();
                
                if (!$exists) {
                    DB::table('model_has_roles')->insert([
                        'role_id' => $roleUser->role_id,
                        'model_type' => 'App\\Models\\User',
                        'model_id' => $roleUser->user_id,
                    ]);
                }
            }

            // Drop old pivot table after successful migration
            Schema::dropIfExists('role_user');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Only rollback if model_has_roles table exists
        if (Schema::hasTable('model_has_roles')) {
            // Recreate role_user table
            Schema::create('role_user', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('role_id');
                $table->timestamps();
                
                $table->unique(['user_id', 'role_id']);
                $table->index('user_id');
                $table->index('role_id');
            });

            // Migrate data back from model_has_roles to role_user
            $modelHasRoles = DB::table('model_has_roles')
                ->where('model_type', 'App\\Models\\User')
                ->get();
            
            foreach ($modelHasRoles as $modelRole) {
                // Check if record already exists to avoid duplicates
                $exists = DB::table('role_user')
                    ->where('user_id', $modelRole->model_id)
                    ->where('role_id', $modelRole->role_id)
                    ->exists();
                
                if (!$exists) {
                    DB::table('role_user')->insert([
                        'user_id' => $modelRole->model_id,
                        'role_id' => $modelRole->role_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // Delete migrated records from model_has_roles
            DB::table('model_has_roles')
                ->where('model_type', 'App\\Models\\User')
                ->delete();
        }

        // Remove guard_name from roles (if column exists)
        if (Schema::hasTable('roles') && Schema::hasColumn('roles', 'guard_name')) {
            // Drop unique index first if it exists (required before dropping column)
            try {
                DB::statement('ALTER TABLE `roles` DROP INDEX `roles_name_guard_name_unique`');
            } catch (\Exception $e) {
                // Index might not exist, ignore error
            }
            
            // Now drop the column
            Schema::table('roles', function (Blueprint $table) {
                $table->dropColumn('guard_name');
            });
        }
    }
};

<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CheckUserRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:check-roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check roles of all users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::with('roles')->get();

        if ($users->isEmpty()) {
            $this->info('No users found.');
            return Command::SUCCESS;
        }

        $this->info("Found {$users->count()} user(s).");
        $this->newLine();

        $headers = ['ID', 'Name', 'Email', 'Roles'];
        $data = [];

        foreach ($users as $user) {
            $roles = $user->roles->pluck('name')->implode(', ') ?: 'No roles';
            $data[] = [
                $user->id,
                $user->name,
                $user->email,
                $roles,
            ];
        }

        $this->table($headers, $data);

        // Check for users with Candidate role
        $candidates = User::role('Candidate')->get();
        $this->newLine();
        $this->info("Total users with Candidate role: {$candidates->count()}");

        // Check for users with Admin role
        $admins = User::role('Admin')->get();
        $this->info("Total users with Admin role: {$admins->count()}");

        // Check for users with Employer role
        $employers = User::role('Employer')->get();
        $this->info("Total users with Employer role: {$employers->count()}");

        return Command::SUCCESS;
    }
}


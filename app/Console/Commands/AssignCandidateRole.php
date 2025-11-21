<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class AssignCandidateRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:assign-candidate-role';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign Candidate role to users who do not have any role';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::doesntHave('roles')->get();

        if ($users->isEmpty()) {
            $this->info('No users without roles found.');
            return Command::SUCCESS;
        }

        $this->info("Found {$users->count()} user(s) without roles.");

        $bar = $this->output->createProgressBar($users->count());
        $bar->start();

        foreach ($users as $user) {
            $user->assignRole('Candidate');
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Successfully assigned Candidate role to {$users->count()} user(s).");

        return Command::SUCCESS;
    }
}

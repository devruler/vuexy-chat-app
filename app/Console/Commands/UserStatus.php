<?php

namespace App\Console\Commands;

use App\Services\UserService;
use Illuminate\Console\Command;

class UserStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'userstatus:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset the status of all users to offline state.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        UserService::setUsersStatusOffline();
        return true;
    }
}

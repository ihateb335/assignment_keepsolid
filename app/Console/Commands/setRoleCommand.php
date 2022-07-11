<?php
/**
 *
 * PHP version >= 7.0
 *
 * @category Console_Command
 * @package  App\Console\Commands
 */

namespace App\Console\Commands;


//use App\Post;

use Exception;
use Illuminate\Console\Command;

use Illuminate\Support\Facades\DB;

/**
 * Class deletePostsCommand
 *
 * @category Console_Command
 * @package  App\Console\Commands
 */
class SetRoleCommand extends Command
{

    public $current_roles = [
        'user',
        'admin'
    ];

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = "setrole {user : Id of a user} {--R|role=user : Role of a user}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Set role of a user";


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $id = intval( $this->argument('user') );
        $role = strtolower( $this->option('role') );
        if(!is_int($id)) {
            $this->error("Id of a user must be int");
            return;
        }
        if(!in_array($role, $this->current_roles)){
            $this->error("Role of a user must be user or admin");
            return;
        }

        try {
            DB::connection('admin')->table('users')->where('id', $id)->update(['role'=> $role ]);
            $this->info("Changed user's $id role to $role ");
        } catch (Exception $e) {
            $this->error("An error occurred");
        }
    }
}
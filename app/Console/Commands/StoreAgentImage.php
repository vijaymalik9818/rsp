<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Admin\Agent\AgentController;

class Storeagentimage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:store-agent-image';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $new = new AgentController();
        $new->moveAgentImagesToS3_bkup();
     
        return 0;
    }
}

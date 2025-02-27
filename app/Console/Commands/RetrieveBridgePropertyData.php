<?php

namespace App\Console\Commands;

use App\Http\Controllers\BridgePropertyController;
use Illuminate\Console\Command;
use App\Http\Controllers\RETSController;
use App\Http\Controllers\importListings\SoldImportlisting;

class RetrieveBridgePropertyData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:importingBridgeData';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $new = new BridgePropertyController();
        $new->fetchRaeListings();
        return 0;
    }
}

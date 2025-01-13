<?php

namespace App\Console\Commands;

use App\Http\Controllers\BridgePropertyController;
use Illuminate\Console\Command;
use App\Http\Controllers\RETSController;
use App\Http\Controllers\importListings\SoldImportlisting;

class RetrieveBridgePropertyDataImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:importingBridgeDataImages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description: This command will save all the images in the property images';

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
        $new->processBridgePropertyImages();
        return 0;
    }
}

<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admin\Agent\AgentController;
use Illuminate\Console\Command;

class ReplaceCityImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:replaceCityImages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command is used to replace the storage images with the s3 images';

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
        $new = new AgentController();
        $new->fetchAndUploadImagesToS3();
        return 0;
    }
}

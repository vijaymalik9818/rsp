<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected $commands = [
        Commands\RetrievePropertyData::class,
        Commands\RetrieveMemberPropertyData::class,
        Commands\UpdateSlug::class,
        Commands\UpdateSlugProperties::class,
        Commands\StoreImages::class,
        Commands\RetrievePropertySoldData::class,
        Commands\SendAlerts::class,
        Commands\StoreFirstImage::class,

        

    ];
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('command:importing')->everyFourHours();
        $schedule->command('command:removeSold')->everyFourHours();  
        $schedule->command('alerts:send')->twiceDaily(0, 12);
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}

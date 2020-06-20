<?php

namespace App\Console;
use App\CustomClass\UpdateNbaPlayersScores;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\CustomClass\StoreAllNbaTeams::class,
        \App\CustomClass\StoreNbaPlayerData::class,
        \App\CustomClass\StoreAllNbaPlayers::class,
        \App\CustomClass\StoreAllNbaPlayersInjuryData::class,
        \App\CustomClass\StoreNbaPlayerInjuryData::class,
        \App\CustomClass\UpdateNbaPlayersPrices::class,
        \App\CustomClass\UpdateNbaPlayersScores::class,
        \App\CustomClass\SaveDraftPick::class,
        \App\CustomClass\Matches\GenerateMatchesCalender::class,
        \App\CustomClass\Matches\PlayWeeklyMatches::class,
        \App\CustomClass\StoreNbaPlayerPhotoUrl::class,
        \App\CustomClass\League\EndOfLeague::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('StoreAllNbaTeams')->everyMinute();
        $schedule->command('StoreNbaPlayerData')->weeklyOn(1, '7:00');
        $schedule->command('StoreAllNbaPlayers')->weeklyOn(1, '8:00');
        $schedule->command('StoreAllNbaPlayersInjuryData')->weeklyOn(1, '8:00');
        $schedule->command('StoreNbaPlayerInjuryData')->weeklyOn(1, '8:00');
        $schedule->command('UpdateNbaPlayersPrices')->weeklyOn(1, '8:00');
        $schedule->command('SaveDraftPick')->everyMinute();


    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}

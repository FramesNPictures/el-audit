<?php

namespace Fnp\Audit;

use Fnp\Audit\Console\PurgeAuditCommand;
use Fnp\Module\Features\ModuleConfig;
use Fnp\Module\Features\ModuleConsole;
use Fnp\Module\Features\ModuleMigrations;
use Fnp\Module\Features\ModuleSchedule;
use Fnp\Module\ModuleProvider;
use Illuminate\Console\Scheduling\Schedule;

class AuditProvider extends ModuleProvider
{
    use ModuleConsole;
    use ModuleSchedule;
    use ModuleConfig;
    use ModuleMigrations;

    /**
     * Return an array of console command's class names
     *
     * @return array
     */
    public function consoleCommands(): array
    {
        return [
            PurgeAuditCommand::class,
        ];
    }

    public function schedule(Schedule $schedule)
    {
        $schedule->command(PurgeAuditCommand::class)
                 ->weeklyOn(1)
                 ->onOneServer();
    }

    /**
     * Return array of config files to be merged.
     * Namespace as key and config file path as value.
     *
     * @return array
     */
    function configFiles(): array
    {
        return [
            'audit' => __DIR__ . '/Config/audit.php',
        ];
    }

    /**
     * Return the location of migrations (folder)
     *
     * @return string
     */
    public function migrationsFolder(): string
    {
        return __DIR__ . '/Migrations';
    }
}
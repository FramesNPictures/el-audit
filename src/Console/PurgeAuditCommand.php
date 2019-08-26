<?php

namespace Fnp\Audit\Console;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class PurgeAuditCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'purge:audit {--months=12}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Purge Audit Entries older that 12 months';

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
     * @return mixed
     *
     * @throws \Exception
     */
    public function handle()
    {
        // Audit cleanup - keep only last month
        DB::table(Config::get('audit.config'))
          ->where('created_at', '<', Carbon::now()->subMonths(12))
          ->delete();

        return TRUE;
    }
}

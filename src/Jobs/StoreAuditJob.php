<?php

namespace Fnp\Audit\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class StoreAuditJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Carbon
     */
    protected $when;

    /**
     * @var int
     */
    protected $userId;

    /**
     * @var string
     */
    protected $session;

    /**
     * @var array
     */
    protected $payload;

    /**
     * @var array
     */
    protected $client;

    /**
     * @var string
     */
    protected $ip;

    /**
     * @var string
     */
    protected $handle;

    /**
     * Create a new job instance.
     *
     * @param          $handle
     * @param Carbon   $when
     * @param string   $session
     * @param          $ip
     * @param array    $payload
     * @param array    $client
     * @param int|null $userId
     */
    public function __construct(
        $handle,
        Carbon $when,
        $session,
        $ip,
        array $payload,
        array $client,
        $userId = NULL
    ) {
        $this->handle  = $handle;
        $this->when    = $when;
        $this->userId  = $userId;
        $this->session = $session;
        $this->payload = $payload;
        $this->client  = $client;
        $this->ip      = $ip;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        DB::table(Config::get('audit.table'))
          ->insert(
              [
                  'event'      => $this->handle,
                  'session'    => $this->session,
                  'ip'         => $this->ip,
                  'payload'    => json_encode($this->payload),
                  'user_id'    => optional($this->userId)->id,
                  'client'     => json_encode($this->client),
                  'created_at' => $this->when,
              ]
          );
    }
}

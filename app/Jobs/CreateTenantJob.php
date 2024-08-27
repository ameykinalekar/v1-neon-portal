<?php
// app/Jobs/CreateTenantJob.php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateTenantJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $tenant;

    public function __construct($tenant)
    {
        $this->tenant = $tenant;
    }

    public function handle()
    {
        // Extract subdomain and other tenant details
        $subdomain = $this->tenant->subdomain;
        // Add other details as needed

        $scriptPath = base_path('create_tenant.bat');
        // $scriptPath = base_path('scripts/create_tenant.bat');

        // Use shell_exec, exec, or similar to run the script
        shell_exec("{$scriptPath} {$subdomain}");
    }
}

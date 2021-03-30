<?php

namespace App\Observers;

use App\Models\Company;
use Illuminate\Support\Facades\Http;

class CompanyObserver
{
    /**
     * Handle events after all transactions are committed.
     *
     * @var bool
     */
    public $afterCommit = true;

    /**
     * Handle the company "updated" event.
     *
     * @param  \App\Company  $company
     * @return void
     */
    public function updated(Company $company)
    {
        $base_url = env('SOCKET_URL');

        Http::post("{$base_url}/user/publish", [
            'room' => 'mp-room-user-' . $company->user_id,
            'event' => 'updated-company', 
            'data' => $company
        ]);
    }
}

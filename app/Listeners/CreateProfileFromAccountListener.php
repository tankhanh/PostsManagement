<?php

namespace App\Listeners;

use App\Events\Admin\CreateUserFromAccount;
use App\Events\CreateProfileFromAccount;
use App\Models\Profile;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateProfileFromAccountListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(CreateProfileFromAccount $event)
    {
        //
        $account = $event->account;

        $profile = new Profile;
        $profile->firstname = $account->firstname;
        $profile->lastname = $account->lastname;
        $profile->save();
    }
}
<?php

// app/Events/CreateProfileFromAccount.php

namespace App\Events;

use App\Models\Account;
use Illuminate\Foundation\Events\Dispatchable;

class CreateProfileFromAccount
{
    use Dispatchable;

    public $account;

    public function __construct(Account $account)
    {
        $this->account = $account;
    }
}
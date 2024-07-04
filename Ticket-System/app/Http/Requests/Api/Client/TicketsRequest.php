<?php

namespace Pterodactyl\Http\Requests\Api\Client;

use Illuminate\Support\Facades\DB;
use Pterodactyl\Http\Requests\Api\Client\ClientApiRequest;

class TicketsRequest extends ClientApiRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        if (!parent::authorize()) {
            return false;
        }

        $user = DB::table('users')->select(['id'])->where('id', '=', $this->user()->id)->get();
        if (count($user) < 1) {
            return false;
        }

        return true;
    }
}

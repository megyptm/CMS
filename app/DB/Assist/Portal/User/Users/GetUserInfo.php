<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2023-03-24
 * Time: 3:27 AM
 * https://www.Maatify.dev
 */

namespace App\DB\Assist\Portal\User\Users;

use App\DB\Assist\Portal\User\AdminLoginToken;
use App\DB\Assist\Portal\User\Users;
use Maatify\Json\Json;

class GetUserInfo extends Users
{
    public function Get()
    {
        $this->PostedUserID();
        Json::Success($this->UserForEdit());
    }
}
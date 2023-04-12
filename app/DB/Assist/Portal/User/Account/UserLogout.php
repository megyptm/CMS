<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2023-03-20
 * Time: 11:04 AM
 */

namespace App\DB\Assist\Portal\User\Account;



use App\DB\Assist\Portal\User\AdminLoginToken;
use Maatify\Json\Json;

class UserLogout extends AdminLoginToken
{
    public function Do(){
        $this->LogoutSilent();
        Json::Success(line: debug_backtrace()[0]['line']);
    }
}
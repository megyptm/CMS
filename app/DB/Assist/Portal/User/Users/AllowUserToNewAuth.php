<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2023-03-24
 * Time: 5:18 AM
 * https://www.Maatify.dev
 */

namespace App\DB\Assist\Portal\User\Users;

use App\DB\Assist\Portal\User\Users;
use App\DB\Tables\Admin\Admin2FA;
use App\DB\Tables\Admin\AdminLogs;
use Maatify\Json\Json;

class AllowUserToNewAuth extends Users
{
    public function Do()
    {
        $this->PostedUserID();
        $user = $this->UserForEdit();
        if($user['auth']){
            Admin2FA::obj()->RemoveAuthCode($this->admin_id);
            AdminLogs::obj()->Record($this->admin_id, 'Remove Current Auth Code');
            Json::Success($this->UserForEdit());
        }else{
            Json::ErrorNoUpdate(__LINE__);
        }
    }

}
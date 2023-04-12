<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2023-03-24
 * Time: 5:21 AM
 * https://www.Maatify.dev
 */

namespace App\DB\Assist\Portal\User\Users;

use App\DB\Assist\Portal\User\Users;
use App\DB\Tables\Admin\AdminLogs;
use App\DB\Tables\Admin\AdminPassword;
use Maatify\Json\Json;

class SetUserNewPassword extends Users
{
    public function Do()
    {
        $this->PostedUserID();
        $user = $this->UserForEdit();
        $otp = AdminPassword::obj()->SetTemp($this->admin_id, $user['name'], $user['email']);
        AdminLogs::obj()->Record($this->admin_id, 'Generate new default password');
        $user['password'] = $otp;
        Json::Success($user);
    }
}
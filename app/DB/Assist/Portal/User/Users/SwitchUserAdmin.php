<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2023-03-24
 * Time: 4:54 AM
 * https://www.Maatify.dev
 */

namespace App\DB\Assist\Portal\User\Users;

use App\DB\Assist\Portal\User\Users;
use App\DB\Tables\Admin\AdminLogs;
use Maatify\Json\Json;

class SwitchUserAdmin extends Users
{
    public function Do()
    {
        $this->PostedUserID();
        $user = $this->UserForEdit();
        if($user['isAdmin']){
            $logs = 'Downgrade From Admin To User';
            $is_admin = 0;
        }else{
            $logs = 'Upgrade From User To Admin';
            $is_admin = 1;
        }
        $this->Edit(['isAdmin' => $is_admin], '`id` = ? ', [$this->admin_id]);
        AdminLogs::obj()->Record($this->admin_id, $logs);
        Json::Success($this->UserForEdit());
    }
}
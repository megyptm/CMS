<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2023-03-24
 * Time: 5:13 AM
 * https://www.Maatify.dev
 */

namespace App\DB\Assist\Portal\User\Users;

use App\DB\Assist\Portal\User\Users;
use App\DB\Tables\Admin\AdminLogs;
use Maatify\Json\Json;

class SwitchUserActive extends Users
{
    public function Do(){
        $this->PostedUserID();
        $user = $this->UserForEdit();
        if($user['isActive']){
            $logs = 'Deactivate';
            $is_admin = 0;
        }else{
            $logs = 'Activate';
            $is_admin = 1;
        }
        $this->Edit(['isActive' => $is_admin], '`id` = ? ', [$this->admin_id]);
        AdminLogs::obj()->Record($this->admin_id, $logs);
        Json::Success($this->UserForEdit());
    }

}
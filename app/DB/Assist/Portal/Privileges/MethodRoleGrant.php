<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2023-03-24
 * Time: 4:21 PM
 * https://www.Maatify.dev
 */

namespace App\DB\Assist\Portal\Privileges;

use App\DB\Assist\Portal\User\AdminLoginToken;
use App\DB\Tables\Admin\AdminEditsLogs;
use App\DB\Tables\Privileges\PrivilegeMethods;
use App\DB\Tables\Privileges\Privileges;
use Maatify\Json\Json;

class MethodRoleGrant extends Privileges
{
    public function Do()
    {
        $this->PostedID();
        if (! $rowPrivileges = $this->ById($this->id)) {
            Json::Invalid('id', line: __LINE__);
        } else {
            $granted = ! $rowPrivileges['granted'];
            $this->Edit(['granted' => (int)$granted], '`id` = ?', [$this->id]);
            $row = PrivilegeMethods::obj()->ById($rowPrivileges['role_id']);
            $row['privileges'] = $this->ByMethod($row['id']);
            AdminEditsLogs::obj()->Record(AdminLoginToken::obj()->GetAdminID(),
                'MethodRoleGrant: ' . PHP_EOL . ($granted ? 'Granted'
                    : 'not granted') . " to " . $row['method']);
            Json::Success($row);
        }
    }
}
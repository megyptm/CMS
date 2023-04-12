<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2023-03-20
 * Time: 10:53 AM
 */

namespace App\DB\Assist\Portal\User\Account;

use App\DB\Assist\Portal\User\AdminLoginToken;
use App\DB\Assist\Portal\User\Users;
use Maatify\Json\Json;

class UserInfo extends Users
{
    public function MyInfo(): void
    {
        $pre = $this->UsersTbsCols();

        Json::Success($this->Row(
            $pre['tb'],
            $pre['col'],
            "`$this->tableName`.`id` = ? GROUP BY `$this->tableName`.`id` ORDER BY `$this->tableName`.`id` ASC",
            [AdminLoginToken::obj()->GetAdminID()]));
    }
}
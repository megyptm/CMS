<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2023-03-24
 * Time: 3:20 AM
 * https://www.Maatify.dev
 */

namespace App\DB\Assist\Portal\User\Users;

use App\DB\Assist\Portal\User\AdminLoginToken;
use App\DB\Assist\Portal\User\Users;
use App\DB\Tables\Admin\AdminPrivileges;
use Maatify\Json\Json;

class ViewAllUsers extends Users
{

    public function Get()
    {
        $pre = $this->UsersTbsCols();
        $master_id = AdminPrivileges::obj()->MasterIds();
        if(AdminLoginToken::obj()->GetAdminID() <= $master_id){
            $where_val = [0];
        }else{
            $where_val = [$master_id];
        }
        Json::Success(
            $this->PaginationHandler(

                $this->CountThisTableRows(
                    'id',
                    '`id` >= ?',
                    $where_val),

                $this->PaginationRows(
                    $pre['tb'],
                    $pre['col'],
                    "`$this->tableName`.`id` > ? GROUP BY `$this->tableName`.`id` ORDER BY `$this->tableName`.`id` ASC",
                    $where_val)
            )
        );
    }

}
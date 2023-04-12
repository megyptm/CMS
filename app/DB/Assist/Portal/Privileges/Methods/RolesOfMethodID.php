<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2023-03-24
 * Time: 4:17 PM
 * https://www.Maatify.dev
 */

namespace App\DB\Assist\Portal\Privileges\Methods;

use App\DB\Tables\Privileges\PrivilegeMethods;
use App\DB\Tables\Privileges\Privileges;
use Maatify\Json\Json;

class RolesOfMethodID extends PrivilegeMethods
{
    public function Get()
    {
        $this->PostedID();
        if(!$row = $this->ById($this->id)){
            Json::Invalid('id', line: __LINE__);
        }else{
            $row['privileges'] = Privileges::obj()->ByMethod($this->id);
        }
        Json::Success($row);
    }
}
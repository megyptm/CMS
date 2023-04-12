<?php

namespace App\DB\Tables\Admin;

use App\DB\Tables\Privileges\PrivilegeMethods;
use App\DB\Tables\Privileges\PrivilegeRoles;
use App\DB\Tables\Privileges\Privileges;
use Maatify\DB\DbConnector;
use Maatify\Functions\GeneralFunctions;
use Maatify\Json\Json;

class AdminPrivileges extends DbConnector
{

    private static self $instance;
    public static function obj(): self
    {
        if(empty(self::$instance))
        {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function MasterIds(): int
    {
        return 3;
    }

    public function IsMaster(int $admin_id): bool
    {
        if($admin_id <= self::MasterIds()){
            return true;
        }
        return false;
    }

    private function CheckMyPrivilege(string $privilege_name, int $admin_id, int $is_admin): bool
    {
        $tb_privilege_roles = PrivilegeRoles::obj()->TableName();
        $tb_privilege = Privileges::obj()->TableName();
        $tb_privilege_methods = PrivilegeMethods::obj()->TableName();
        $tb_admin_role = AdminRoles::obj()->TableName();
        if(self::IsMaster($admin_id)){
            return true;
        }
        elseif(!empty($is_admin)) {
            return true;
        }
        elseif($this->RowISExist("`$tb_privilege_roles` 
                               INNER JOIN `$tb_admin_role` ON `$tb_privilege_roles`.`id` = `$tb_admin_role`.`role_id`
                               INNER JOIN `$tb_privilege` ON `$tb_privilege_roles`.`id` = `$tb_privilege`.`role_id` AND `$tb_privilege`.`granted` = '1'
                               INNER JOIN `$tb_privilege_methods` ON `$tb_privilege_methods`.`id` = `$tb_privilege`.`method_id`",
        "`$tb_admin_role`.`admin_id` = ? AND `$tb_privilege_methods`.`method` = ? ", [$admin_id, $privilege_name])){
                    return true;
        }
        return false;
    }

    public function IsAllowedMethod(int $admin_id, int $is_admin){
        if(empty($_GET['action'])){
            Json::Missing('action', line: __LINE__);
        }
        if(!str_contains($_GET['action'], 'Initialize') || !in_array($_GET['action'], ['UserTitle','CustomerTitle'])) {
            $privilege_name = GeneralFunctions::CurrentPage() . '_' . $_GET['action'];
            if (! self::CheckMyPrivilege($privilege_name, $admin_id, $is_admin)) {
                Json::Forbidden();
            }
        }
    }

    public function IsAllowedCustomerDownload(int $admin_id, int $is_admin){
        if(empty($_GET['action'])){
            Json::Missing('action', line: __LINE__);
        }
        $privilege_name = GeneralFunctions::CurrentPage() . '_' . $_GET['action'];
        if(!self::CheckMyPrivilege($privilege_name, $admin_id, $is_admin)) {
            Json::Forbidden();
        }
    }
}
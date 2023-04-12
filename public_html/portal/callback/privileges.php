<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2022-12-12
 * Time: 1:36 AM
 */

declare(strict_types=1);

use App\DB\Assist\Portal\Privileges\MethodRoleGrant;
use App\DB\Assist\Portal\Privileges\Methods\AddNewMethod;
use App\DB\Assist\Portal\Privileges\Methods\AllMethods;
use App\DB\Assist\Portal\Privileges\Methods\RolesOfMethodID;
use App\DB\Assist\Portal\Privileges\Methods\UpdateMethod;
use App\DB\Assist\Portal\Privileges\Roles\AddNewRole;
use App\DB\Assist\Portal\Privileges\Roles\AllRoles;
use App\DB\Assist\Portal\Privileges\Roles\MethodsOfRoleID;
use App\DB\Assist\Portal\Privileges\Roles\UpdateRole;
use App\DB\Assist\Portal\Privileges\Roles\UsersGrantedToRole;
use App\DB\Assist\Portal\User\AdminLoginToken;
use App\DB\Tables\Admin\AdminFailedLogin;
use App\DB\Tables\Admin\AdminPrivileges;

use Maatify\Json\Json;

require __DIR__ . '/../../../app/loader.php';

AdminFailedLogin::obj()->CheckFailed();
$admin = AdminLoginToken::obj()->ValidateAdminToken();

if(!empty($_GET['action'])) {
    AdminPrivileges::obj()->IsAllowedMethod($admin['id'], $admin['isAdmin']);
    switch ($_GET['action']) {
        case 'AllRoles';
            (new AllRoles())->Get();
            break;

        case 'AddNewRole';
            (new AddNewRole())->Do();
            break;

        case 'UpdateRole';
            (new UpdateRole())->Do();
            break;

        case 'AllMethods';
            (new AllMethods())->Get();
            break;

        case 'AddNewMethod';
            (new AddNewMethod())->Do();
            break;

        case 'UpdateMethod';
            (new UpdateMethod())->Do();
            break;

        case 'RolesOfMethodID';
            (new RolesOfMethodID())->Get();
            break;

        case 'MethodsOfRoleID';
            (new MethodsOfRoleID())->Get();
            break;

        case 'MethodRoleGrant';
            (new MethodRoleGrant())->Do();
            break;

        case 'UsersGrantedToRole';
            (new UsersGrantedToRole())->Get();
            break;

        default;
            Json::Invalid('action', line: __LINE__);

    }
}
Json::Missing('action', line: __LINE__);
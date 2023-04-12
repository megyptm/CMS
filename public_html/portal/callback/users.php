<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2022-12-12
 * Time: 1:36 AM
 */

declare(strict_types=1);


use App\DB\Assist\Portal\User\AdminLoginToken;
use App\DB\Assist\Portal\User\FieldLogins;
use App\DB\Assist\Portal\User\Privileges\UserPrivilegeRoleAdd;
use App\DB\Assist\Portal\User\Privileges\UserPrivilegeRoleRemove;
use App\DB\Assist\Portal\User\Privileges\UserPrivileges;
use App\DB\Assist\Portal\User\Users\AddNewUser;
use App\DB\Assist\Portal\User\Users\AllowUserToNewAuth;
use App\DB\Assist\Portal\User\Users\GetUserInfo;
use App\DB\Assist\Portal\User\Users\SetUserNewPassword;
use App\DB\Assist\Portal\User\Users\SwitchUserActive;
use App\DB\Assist\Portal\User\Users\SwitchUserAdmin;
use App\DB\Assist\Portal\User\Users\UpdateUserInfo;
use App\DB\Assist\Portal\User\Users\ViewAllUsers;
use App\DB\Tables\Admin\AdminFailedLogin;
use App\DB\Tables\Admin\AdminPrivileges;
use Maatify\Json\Json;

require __DIR__ . '/../../../app/loader.php';

AdminFailedLogin::obj()->CheckFailed();
$admin = AdminLoginToken::obj()->ValidateAdminToken();

if(!empty($_GET['action'])) {
    AdminPrivileges::obj()->IsAllowedMethod($admin['id'], $admin['isAdmin']);
    switch ($_GET['action']) {
        case 'ViewAllUsers';
            (new ViewAllUsers())->Get();
            break;

        case 'GetUserInfo';
            (new GetUserInfo())->Get();
            break;

        case 'UpdateUserInfo';
            (new UpdateUserInfo())->Do();
            break;

        case 'SwitchUserAdmin';
            if(!$admin['isAdmin']){
                Json::Forbidden();
            }else{
                (new SwitchUserAdmin())->Do();
            }
            break;

        case 'SwitchUserActive';
            if(!$admin['isAdmin']){
                Json::Forbidden();
            }else{
                (new SwitchUserActive())->Do();
            }
            break;

        case 'AllowUserToNewAuth';
            (new AllowUserToNewAuth())->Do();
            break;

        case 'SetUserNewPassword';
            (new SetUserNewPassword())->Do();
            break;

        case 'AddNewUser';
            (new AddNewUser())->Do();
            break;

        case 'UserPrivileges';
            (new UserPrivileges())->Get();
            break;

        case 'UserPrivilegeRoleAdd';
            (new UserPrivilegeRoleAdd())->Do();
            break;

        case 'UserPrivilegeRoleRemove';
            (new UserPrivilegeRoleRemove())->Do();
            break;

        case 'FieldLogins';
            (new FieldLogins())->Get();
            break;

        case 'FailedLoginRemove';
            (new FieldLogins())->Remove();
            break;

        default;
            Json::Invalid('action', line: __LINE__);

    }
}
Json::Missing('action', line: __LINE__);
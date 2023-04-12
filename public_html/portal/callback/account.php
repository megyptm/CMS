<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2022-12-12
 * Time: 1:36 AM
 */

declare(strict_types=1);

use App\DB\Assist\Portal\User\Account\UserAuth;
use App\DB\Assist\Portal\User\Account\UserEmailConfirm;
use App\DB\Assist\Portal\User\Account\UserInfo;
use App\DB\Assist\Portal\User\Account\UserLogin;
use App\DB\Assist\Portal\User\Account\UserLogout;
use App\DB\Assist\Portal\User\Account\UserPassword;
use App\DB\Assist\Portal\User\AdminLoginToken;
use App\DB\Tables\Admin\AdminFailedLogin;
use Maatify\Json\Json;

require __DIR__ . '/../../../app/loader.php';

AdminFailedLogin::obj()->CheckFailed();
$admin = array();
if(!empty($_GET['action'])) {
    if(!in_array($_GET['action'], ['Login', 'Logout', 'Auth', 'AuthRegister', 'CheckSession'])){
        $admin = AdminLoginToken::obj()->ValidateAdminToken();
    }
    switch ($_GET['action']) {
        case 'Login';
            (new UserLogin())->Do();
            break;

        case 'Logout';
            (new UserLogout())->Do();
            break;

        case 'EmailConfirm';
            if(!empty($admin)) {
                (new UserEmailConfirm())->EmailConfirm();
            }
            break;

        case 'EmailConfirmResend';
            if(!empty($admin)) {
                (new UserEmailConfirm())->EmailConfirmResend();
            }
            break;

        case 'Auth';
            (new UserAuth())->Auth();
            break;

        case 'AuthRegister';
            (new UserAuth())->AuthRegister();
            break;

        case 'ChangePassword';
            if(!empty($admin)) {
                (new UserPassword())->ChangePassword();
            }
            break;

        case 'ChangeEmail';
            if(!empty($admin)) {
                (new UserEmailConfirm())->ChangeEmail();
            }
            break;

        case 'GetMyInfo';
            (new UserInfo())->MyInfo();
            break;

        case 'CheckSession';
            AdminLoginToken::obj()->ValidateSilentAdminToken();
            break;

        default;
            Json::Invalid('action', line: __LINE__);

    }
}
if(!empty($admin)){
    Json::Success(AdminLoginToken::obj()->HandleAdminResponse($admin));
}
Json::Missing('action', line: __LINE__);
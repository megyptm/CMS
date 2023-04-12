<?php

namespace App\DB\Assist\Portal\User;

use App\Assist\Encryptions\AdminTokenEncryption;
use App\Assist\Jwt\JWTAssistance;
use App\DB\Tables\Admin\Admin;
use App\DB\Tables\Admin\Admin2FA;
use App\DB\Tables\Admin\AdminEmail;
use App\DB\Tables\Admin\AdminFailedLogin;
use App\DB\Tables\Admin\AdminToken;
use Maatify\Functions\GeneralFunctions;
use Maatify\Json\Json;

class AdminLoginToken extends AdminToken
{

    private static self $instance;
    private string $admin_name = '';
    private string $admin_username = '';
    private string $admin_email;

    public static function obj(): self
    {
        if(empty(self::$instance))
        {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function LogoutSilent(){
        if (! empty($_SESSION['token']) && $token = JWTAssistance::obj()->JwtValidation(__LINE__)) {
            if (! empty($token->token)) {
                if ($admin = $this->ByToken($token->token, debug_backtrace()[0]['line'])) {
                    $this->Edit(['token'=>''], '`admin_id` = ? ', [$admin['id']]);
                }
            }
        }
        session_destroy();
    }

    public function ValidateAdminToken(): array
    {
        if(!empty($_GET['action']) && !in_array($_GET['action'], ['login', 'logout'])) {
            if (! empty($_SESSION['token']) && $token = JWTAssistance::obj()->JwtValidation(__LINE__)) {
                if (! empty($token->token)) {
                    if ($admin = $this->ByToken($token->token, debug_backtrace()[0]['line'])) {
                        JWTAssistance::obj()->JwtTokenHash($admin['id'], $admin['username']);
                        $this->admin_id = $admin['id'];
                        $this->admin_name = $admin['name'];
                        $this->admin_username = $admin['username'];
                        $this->admin_email = $admin['email'];

                        return $admin;
                    }
                }
            }
            AdminFailedLogin::obj()->Failed('');
            Json::ReLogin(debug_backtrace()[0]['line']);
        }
        return [];
    }

    public function ValidateSilentAdminToken(): void
    {
        $auth_pages = ['AuthRegister', 'Auth', 'ChangePassword', 'EmailConfirm', 'CheckSession'];
        if(!empty($_GET['action'])) {
            if (! empty($_SESSION['token']) && $tokens = JWTAssistance::obj()->JwtValidationForSessionLogin(__LINE__)) {
                if (isset($tokens->token)) {
                    if (AdminLoginToken::obj()->ByToken($tokens->token, debug_backtrace()[0]['line'])) {
                        $type['type'] = (isset($tokens->next) && in_array($tokens->next, $auth_pages) ? 'login' : 'main');
                        Json::Success($type);
                    }
                }
            }
        }
        Json::ReLogin(debug_backtrace()[0]['line']);
    }

    public function ByToken(string $hashed_token, int $line = 0): array
    {
        $tb_admin = Admin::obj()->TableName();
        $tb_admin_email = AdminEmail::obj()->TableName();
        $tb_admin_auth = Admin2FA::obj()->TableName();
        $admin = $this->Row("`$this->tableName` 
        INNER JOIN `$tb_admin` ON `$tb_admin`.`id` = `$this->tableName`.`admin_id` 
        INNER JOIN `$tb_admin_email` ON `$tb_admin_email`.`admin_id` = `$this->tableName`.`admin_id` 
        INNER JOIN `$tb_admin_auth` ON `$tb_admin_auth`.`admin_id` = `$this->tableName`.`admin_id` 
        ",
            "`$tb_admin`.*, `$tb_admin_email`.`email`, `$tb_admin_email`.`confirmed`, `$tb_admin_auth`.`auth`, `$tb_admin_auth`.`isAuthRequired`",
            "`$this->tableName`.`token` = ? AND `$this->tableName`.`token` <> ''",
            [self::TokenSecretKeyEncode($hashed_token)]);
        if($admin){
            if(empty($admin['isActive'])){
                Json::SuspendedAccount($line ?:debug_backtrace()[0]['line']);
            }
            $this->admin_id = $admin['id'];
            $this->admin_name = $admin['name'];
            $this->admin_username = $admin['username'];
            $this->admin_email = $admin['email'];
        }
        return $admin;
    }

    public function RemoveToken(int $admin_id)
    {
        $this->Edit(['token' => ''],
            '`id` = ? ',
            [$admin_id]);
    }
    public function GenerateToken(int $admin_id, string $username): string
    {
        $token = time() . $admin_id . md5(time() . $username) . "_" . $this->MD5IP() . "_" . $this->MD5AgentUser();
        $this->SetToken($admin_id, $token);
        return $token;
    }

    private function MD5AgentUser(): string
    {
        return md5(GeneralFunctions::UserAgent());
    }

    private function MD5IP(): string
    {
        return md5(GeneralFunctions::IP());
    }


    private static function TokenSecretKeyEncode($code): string
    {
        $code = base64_encode($code);
        return (new AdminTokenEncryption())->Hash($code);
    }
    private static function TokenSecretKeyDecode($code): string
    {
        $code = (new AdminTokenEncryption())->DeHashed($code);
        return (string)base64_decode($code);
    }

    private function SetToken(int $admin_id, string $token): void
    {
        $this->Edit(['token' => $this->TokenSecretKeyEncode($token)],
            '`id` = ? ',
            [$admin_id]);
    }

    public function HandleAdminResponse(array $admin): array
    {
        $this->admin_id = $admin['id'];
//        if(isset($admin['id'])) unset($admin['id']);
        if(isset($admin['username'])) unset($admin['username']);
//        if(isset($admin['isAdmin'])) unset($admin['isAdmin']);
        if(isset($admin['isActive'])) unset($admin['isActive']);
        if(isset($admin['lang'])) unset($admin['lang']);
        if(isset($admin['confirmed'])) $admin['confirmed'] = (bool) $admin['confirmed'];
        if(isset($admin['isAuthRequired'])) unset($admin['isAuthRequired']);
        if(isset($admin['auth'])) unset($admin['auth']);
        return $admin;
    }

    public function GetAdminID(): int
    {
        return $this->admin_id;
    }

    public function GetAdminName(): string
    {
        return $this->admin_name;
    }

    public function GetAdminUsername(): string
    {
        return $this->admin_username;
    }

    public function GetAdminEmail(): string
    {
        return $this->admin_email;
    }
}
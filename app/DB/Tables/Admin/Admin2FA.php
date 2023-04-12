<?php

namespace App\DB\Tables\Admin;

use App\Assist\Encryptions\AdminAuthEncryption;
use App\Assist\Jwt\JWTAssistance;
use App\DB\Assist\Portal\User\AdminLoginToken;
use Maatify\DB\DbConnector;
use Maatify\GoogleAuth\GoogleAuth;
use Maatify\Json\Json;

class Admin2FA extends DbConnector
{
    protected string $tableName = 'a_2fa';

    protected static int|string $line;

    protected static self $instance;
    public static function obj(): self
    {
        if(empty(self::$instance))
        {
            self::$instance = new self();
        }
        self::$line = debug_backtrace()[0]['line'];
        return self::$instance;
    }

    public function __construct()
    {
        parent::__construct();
        self::$line = debug_backtrace()[0]['line'];
    }

    public function ShowAuthCode(array $admin)
    {
        $this->AuthDecode($admin['auth']);
        Json::Success([$this->AuthDecode($admin['auth'])]);
    }

    public function ResponseAuthMov(array $admin){
        if(!empty($admin)) {
            if (! empty($admin['auth'])) {
                JWTAssistance::obj()->TokenAuth($admin['id'],
                    $admin['username'],
                    ['next' => 'Auth']);
                Json::GoToMethod('Auth',
                    'Please Confirm Your Google Authenticator',
                    line: self::$line);
            } else {
                $g_2fa_code = GoogleAuth::obj()->GenerateSecret();
                JWTAssistance::obj()->TokenAuth($admin['id'],
                    $admin['username'],
                    ['secret' => $g_2fa_code, 'next' => 'AuthRegister']);
                Json::GoToMethod('AuthRegister',
                    'Please Set Your Google Authenticator',
                    [
                        'g_auth_code' => $g_2fa_code,
                        'g_auth_base64' => base64_encode(file_get_contents(GoogleAuth::obj()
                            ->GetImg(trim($admin['username']),
                                trim($g_2fa_code),
                                trim($_ENV['SITE_PORTAL_NAME'])))),
                    ],
                    self::$line);
            }
        }
    }

    public function ValidateAdminCode(int $admin_id): bool
    {
        $code = $this->postValidator->Require('code', 'code');
        $tb_admin = Admin::obj()->TableName();
        $admin = $this->Row("`$this->tableName` INNER JOIN `$tb_admin` ON `$tb_admin`.`id` = `$this->tableName`.`admin_id` ",
            "`$tb_admin`.`username`, `$this->tableName`.`auth`",
        "`$this->tableName`.`admin_id` = ? ",
        [$admin_id]);
        if(empty($admin['auth'])){
            Json::NotAllowedToUse('code', 'account not allowed to use 2fa authentication');
        }
        return $this->ValidateCode($code, $this->AuthDecode($admin['auth']), $admin['username']);
    }

    protected function ValideToken(): array
    {
        $auth_pages = ['AuthRegister', 'Auth'];
        if(!empty($_GET['action']) && in_array($_GET['action'], $auth_pages)) {
            if (! empty($_SESSION['token']) && $tokens = JWTAssistance::obj()->JwtValidation(__LINE__)) {
                if (isset($tokens->token, $tokens->next)) {
                    if(in_array($tokens->next, $auth_pages)) {
                        if ($admin = AdminLoginToken::obj()->ByToken($tokens->token, self::$line)) {
                            if(isset($tokens->secret) && !empty($tokens->secret)){
                                $admin['secret'] = $tokens->secret;
                            }
                            return $admin;
                        }
                    }
                }
            }
        }
        AdminFailedLogin::obj()->Failed('');
        Json::ReLogin(self::$line);
        return [];
    }

    protected function ValidateCode(string $code, string $auth_code, string $username): bool
    {
        if (GoogleAuth::obj()->checkCode($auth_code, $code)) {
            AdminFailedLogin::obj()->Success($username);
            return true;
        } else {
            AdminFailedLogin::obj()->Failed($username);
            Json::Incorrect('code', line: self::$line);
            return false;
        }
    }

    public function RemoveAuthCode(int $admin_id): bool
    {
        return $this->Edit(['auth'=>0], '`admin_id` = ? ', [$admin_id]);
    }

    /**
    ========================================= GoogleAuthenticator =========================================
     **/
    protected function AuthEncode($auth): string
    {
        $auth = base64_encode($auth);
        return (new AdminAuthEncryption())->Hash($auth);
    }
    protected function AuthDecode($auth): string
    {
        $auth = (new AdminAuthEncryption())->DeHashed($auth);
        return (string)base64_decode($auth);
    }


}
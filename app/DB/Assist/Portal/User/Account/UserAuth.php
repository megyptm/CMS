<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2023-03-20
 * Time: 11:34 AM
 */

namespace App\DB\Assist\Portal\User\Account;

use App\Assist\Jwt\JWTAssistance;
use App\DB\Assist\Portal\User\AdminLoginToken;
use App\DB\Tables\Admin\Admin2FA;
use App\DB\Tables\Admin\AdminLogs;
use App\DB\Tables\Admin\AdminPassword;
use Maatify\Json\Json;

class UserAuth extends Admin2FA
{
    public function Auth()
    {
        $code = $this->postValidator->Require('code', 'code');
        $admin = $this->CheckCode($code);
        AdminLogs::obj()->RecordMyLog('success Auth Login');
        AdminPassword::obj()->ValidateTempPass($admin['id']);
        Json::Success(AdminLoginToken::obj()->HandleAdminResponse($admin));
    }

    public function AuthRegister()
    {
        $code = $this->postValidator->Require('code', 'code');
        $admin = $this->RegisterNewCode($code);
        AdminLogs::obj()->RecordMyLog('success Auth Register');
        $admin = AdminLoginToken::obj()->ValidateAdminToken();
        AdminPassword::obj()->ValidateTempPass($admin['id']);
        Json::Success(AdminLoginToken::obj()->HandleAdminResponse($admin));
    }

    private function CheckCode(string $code): array
    {
        if($admin = $this->ValideToken()){
            if($this->ValidateCode($code, $this->AuthDecode($admin['auth']), $admin['username'])){
                JWTAssistance::obj()->JwtTokenHash($admin['id'], $admin['username']);
                return $admin;
            }
        }
        return [];
    }

    private function RegisterNewCode(string $code): array
    {
        if($admin = $this->ValideToken()){
            if(empty($admin['auth'])) {
                if (! empty($admin['secret'])) {
                    if ($this->ValidateCode($code, $admin['secret'], $admin['username'])) {
                        if ($this->NewAuthRecord($admin['id'],
                            $admin['secret'])) {
                            JWTAssistance::obj()->JwtTokenHash($admin['id'], $admin['username']);
                            return $admin;
                        }
                    }
                }
            }else{
                Json::Exist('code', line: self::$line);
            }
        }
        Json::ReLogin(self::$line);
        return [];
    }


    private function NewAuthRecord(int $admin_id, string $authCode): bool
    {
        if ($this->AuthCanUse($authCode)) {
            if ($this->Edit(['auth' => $this->AuthEncode($authCode)], " `id` = ?", [$admin_id])) {
                return true;
            }
        }
        return false;
    }

    private function AuthCanUse($authCode): bool
    {
        $authCode = $this->AuthEncode($authCode);
        if (self::RowThisTable("`id`",
            "`auth` = ? LIMIT 1", [$authCode])) {
            return false;
        }
        else return $authCode;
    }
}
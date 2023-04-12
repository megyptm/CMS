<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2023-01-09
 * Time: 10:20 AM
 */

namespace App\Assist\Jwt;

use Maatify\Functions\GeneralFunctions;
use Maatify\Json\Json;
use stdClass;

class JWTAssistance extends JWTAssist
{
    //    protected $secretKey ;
    protected string $algo = 'HS256';
    //    protected $algo = 'HS384';
    //    protected $algo = 'HS512';

    protected static self $instance;
    public static function obj(): self
    {
        if(empty(self::$instance))
        {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct(){
        $this->secretKey = $this->HashSecret($this->ssl_secret);
    }

    public function Hash($issuer, $timeout, array $data): string
    {
        return $this->Encode($issuer, $timeout, $data);
    }

    public function DeHash($jwt): stdClass
    {
        return $this->Decode($jwt);
    }

    protected function HashSecret(string $code): string
    {
        return openssl_encrypt($code, $this->ssl_cipher_algo, $this->ssl_key);
    }

    public function JwtHash(array|string $array, int|float $timout_minutes = 0): string
    {
        if(!is_array($array)) $arr['token'] = $array;
        else $arr = $array;
        return $this->Hash(GeneralFunctions::MainUrl(),($timout_minutes ? : $_ENV['SESSION_TIMEOUT'])*60, $arr);
    }

    public function JwtTokenHash(int $admin_id, string $username, array|string $array = '', int|float $timout_minutes = 0): void
    {
        $token = \App\DB\Assist\Portal\User\AdminLoginToken::obj()->GenerateToken($admin_id, $username);
        if(!empty($array) && is_array($array)){
                $array['token'] = $token;
        }else{
            $array = $token;
        }
        $_SESSION['token'] = $this->JwtHash($array, $timout_minutes);
    }

    public function TokenConfirmMail(int $admin_id, string $username, array|string $array = ''): void
    {
        $this->JwtTokenHash($admin_id, $username, $array, 60*24);
    }

    public function TokenAuth(int $admin_id, string $username, array|string $array = ''): void
    {
        $this->JwtTokenHash($admin_id, $username, $array, 10);
    }

    public function JwtValidation(int|string $line = ''): stdClass
    {
        if($token = $this->DeHash($_SESSION['token'])){
            if(!empty($token->iss)) {
                if ($token->iss == GeneralFunctions::MainUrl()
                    && $token->nbf < GeneralFunctions::CurrentTimeStamp()
                    && $token->exp > GeneralFunctions::CurrentTimeStamp()) {
                    if (isset($token->next)) {
                        if ($token->next == GeneralFunctions::CurrentAction()) {
                            return $token;
                        }
                    } else {
                        return $token;
                    }
                }
            }
        }
        Json::ReLogin($line);
        return new stdClass();
    }

    public function JwtValidationForSessionLogin(int|string $line = ''): stdClass
    {
        if($token = $this->DeHash($_SESSION['token'])){
            if(!empty($token->iss)) {
                if ($token->iss == GeneralFunctions::MainUrl()
                    && $token->nbf < GeneralFunctions::CurrentTimeStamp()
                    && $token->exp > GeneralFunctions::CurrentTimeStamp()) {
                    return $token;
                }
            }
        }
        Json::ReLogin($line);
        return new stdClass();
    }

}
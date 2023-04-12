<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2023-01-09
 * Time: 10:20 AM
 */

namespace App\Assist\Jwt;

use Maatify\Logger\Logger;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use stdClass;

abstract class JWTAssist extends JWTKeys
{
    protected string $secretKey;

    protected string $algo = 'HS256';

    /**

        use Firebase\JWT\JWT;
        use Firebase\JWT\Key;

        $key = 'example_key';
        $payload = [
        'iss' => 'http://example.org',
        'aud' => 'http://example.com',
        'iat' => 1356999524,
        'nbf' => 1357000000
        ];

        /*
         * IMPORTANT:
         * You must specify supported algorithms for your application. See
         * https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40
         * for a list of spec-compliant algorithms.

        $jwt = JWT::encode($payload, $key, 'HS256');
        $decoded = JWT::decode($jwt, new Key($key, 'HS256'));

        print_r($decoded);


         NOTE: This will now be an object instead of an associative array. To get
         an associative array, you will need to cast it as such:


        $decoded_array = (array) $decoded;

        /*
         * You can add a leeway to account for when there is a clock skew times between
         * the signing and verifying servers. It is recommended that this leeway should
         * not be bigger than a few minutes.
         *
         * Source: http://self-issued.info/docs/draft-ietf-oauth-json-web-token.html#nbfDef

        JWT::$leeway = 60; // $leeway in seconds
        $decoded = JWT::decode($jwt, new Key($key, 'HS256'));

     */

    protected function Encode($issuer, $timeout, array $data): string
    {
        $data['iat'] = time();
        $data['nbf'] = $data['iat']-1;
        $data['exp'] = $data['iat']+$timeout;
        $data['iss'] = $issuer;
        $data['ip'] = $_SERVER['REMOTE_ADDR'] ?? "";
        if($timeout == 60*60*24*30) {
            $data['remember'] = true;
        }

        try {
            $jwt = JWT::encode($data, $this->secretKey,$this->algo);
        } catch (\Exception $e) { // Also tried JwtException
            Logger::RecordLog($e, 'jwtEncode_error_'.$issuer);
            $jwt = false;
        }
        return $jwt;
    }

    protected function Decode($jwt): stdClass
    {
        try {
            $arr = JWT::decode($jwt, new Key($this->secretKey, $this->algo));
        } catch (\Exception $e) { // Also tried JwtException
            Logger::RecordLog($e, 'jwtDecode_error_');
            $arr = new \stdClass();
        }
        return $arr;
    }


}
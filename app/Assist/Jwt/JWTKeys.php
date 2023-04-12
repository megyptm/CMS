<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2023-01-09
 * Time: 10:20 AM
 */

namespace App\Assist\Jwt;

abstract class JWTKeys
{
    protected string $ssl_secret = 'JWTSslSecret@@@';
    protected string $ssl_key = 'JwtSslKey$$$$';
    protected string $ssl_cipher_algo = 'AES-128-ECB';
}
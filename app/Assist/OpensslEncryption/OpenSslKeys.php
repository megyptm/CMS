<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2023-03-21
 * Time: 1:47 PM
 */

namespace App\Assist\OpensslEncryption;

use Maatify\OpenSSL\OpenSSL;

abstract class OpenSslKeys extends OpenSSL
{
    protected string $code;

    public function __construct()
    {
        $this->ssl_algo = 'AES-128-ECB';
    }
    protected function CronEmailSslKeys(): string
    {
        return 'EmailsCronJobSSLSecret###';
    }
    protected function AdminPasswordSslKeys(): string
    {
        return 'AdminPasswordSSLSecret###';
    }
    protected function AdminConfirmEmailSslKeys(): string
    {
        return 'AdminPasswordSSLSecret###';
    }
    protected function AdminAuthSslKeys(): string
    {
        return 'AdminPasswordSSLSecret###';
    }
    protected function AdminTokenSslKeys(): string
    {
        return 'AdminPasswordSSLSecret###';
    }
}
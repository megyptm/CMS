<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2023-02-27
 * Time: 4:25 PM
 */

namespace App\Assist\Encryptions;


use App\Assist\OpensslEncryption\OpenSslKeys;

class ConfirmEmailEncryption extends OpenSslKeys
{
    public function __construct()
    {
        parent::__construct();
        $this->ssl_secret = $this->AdminConfirmEmailSslKeys();
    }
}
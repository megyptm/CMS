<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2023-03-24
 * Time: 5:42 AM
 * https://www.Maatify.dev
 */

namespace App\DB\Assist\Portal\User;

use App\DB\Tables\Admin\AdminFailedLogin;
use Maatify\Json\Json;

class FieldLogins extends AdminFailedLogin
{
    private string $username;
    private string $ip;
    public function __construct()
    {
        parent::__construct();
        $this->username = $this->postValidator->Optional('username', 'username');
        $this->ip = $this->postValidator->Optional('ip', 'ip');
    }

    public function Get()
    {
        Json::Success($this->Log($this->username, $this->ip));
    }

    public function Remove()
    {
        $this->SuccessByAdmin($this->ip, $this->username);
        Json::Success($this->Log($this->username, $this->ip));
    }
}
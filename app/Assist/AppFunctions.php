<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2023-03-26
 * Time: 1:02 AM
 * https://www.Maatify.dev
 */

namespace App\Assist;

use Maatify\Functions\GeneralFunctions;

class AppFunctions
{

    public static function PortalUrl(): string
    {
        return GeneralFunctions::HostUrl() . 'portal/';
    }

    public static function SiteUrl(): string
    {
        return GeneralFunctions::HostUrl();
    }

    public static function HeaderMeta(string $title, string $description): void
    {
        echo '
        <meta name="description" content="' . $description . '">
        <meta property="og:title" content="' . $title . '" />
        <meta property="og:site_name" content="' . $title . '">
        <meta property="og:type" content="website" />
        <meta property="og:url" content="' . GeneralFunctions::CurrentUrl() . '">
        <meta property="og:image" content="' . site_url() . 'assets/img/logoo.png" />
        <meta property="og:description" content="' . $description . '">
        <title>' . $title . '</title> 
            ';
    }

}
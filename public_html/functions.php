<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2022-12-12
 * Time: 1:36 AM
 */

use App\Assist\AppFunctions;
use Maatify\Functions\GeneralFunctions;

//ob_start('ob_gzhandler');
//header("Cache-Control: public"); // HTTP/1.1
//header("Cache-Control: max-age=1209600"); // HTTP/1.1

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

require __DIR__ . '/../app/loader.php';

function site_url(): string
{
    return AppFunctions::SiteUrl();
}

function cdn_Key(): string
{
    return GeneralFunctions::CdnKeyWebsite();
}

function ClearSpaces(string $string): string
{
    return GeneralFunctions::ClearSpaces($string);
}

function gCaptchaV3SiteKey(): string
{
    return GeneralFunctions::GoogleCaptchaV3SiteKey();
}

function gCaptchaV2SiteKey(): string
{
    return GeneralFunctions::GoogleCaptchaV2SiteKey();
}

function header_meta(string $title, string $description): void
{
    AppFunctions::HeaderMeta($title, $description);
}

<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2023-01-09
 * Time: 2:02 PM
 */

namespace App\Assist\Files;

use Maatify\Uploader\UploadBase64;

class UploadFiles extends UploadBase64
{
    private string $upload_ct_doc_folder = __DIR__ . '/../../ct_docs';

    private static self $instance;
    public static function obj(): self
    {
        if(!isset(self::$instance))
        {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function UploadCustomerDoc(int $ct_id): string
    {
        $this->uploaded_for_id = $ct_id;
        $this->upload_folder = $this->upload_ct_doc_folder;
        return $this->Upload();
    }

}
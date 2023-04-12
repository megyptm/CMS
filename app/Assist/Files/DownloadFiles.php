<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2023-01-09
 * Time: 2:02 PM
 */

namespace App\Assist\Files;

use Maatify\Uploader\DownloadStreamFile;

class DownloadFiles extends DownloadStreamFile
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

    public function DownloadCustomerDoc(string $file): void
    {
        $this->file_path = $this->upload_ct_doc_folder . '/' . $file;
        $this->file_saved_name = 'customer-doc';
        $this->DownloadFile();
    }

}
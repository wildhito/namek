<?php

namespace AppBundle;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;

class FileUploader
{
    private $fs;

    public function __construct()
    {
        $this->fs = new FileSystem();
    }

    public function upload(UploadedFile $file, $targetDir, $targetName, $allowedMimetypes=array())
    {
        if (!in_array($file->getMimeType(), $allowedMimetypes)) {
            return false;
        }

        try {
            if (!$this->fs->exists($targetDir)) {
                $this->fs->mkdir($targetDir);
            }
            $file->move($targetDir, $targetName);
        } catch (Exception $e) {
            return false;
        }
        return true;
    }
}

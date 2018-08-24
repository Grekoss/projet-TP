<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploaderUser
{
    private $targetDirectory;
    private $currentFile;

    public function __construct($targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }

    public function setCurrentFile($file)
    {
        $this->currentFile = $file;
    }

    public function upload(? UploadedFile $file)
    {
        if ($file !== null) {
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move($this->getTargetDirectory(), $fileName);
        } else {
            $fileName = $this->currentFile;
        }

        return $fileName;
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }
}
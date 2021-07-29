<?php

namespace LaravelZip;

use ZipArchive;

class Zip
{
    /**
     * 'filename', 'localFilename', 'deleteOriginal' attributes are expected
     */
    private array $files = [];

    private string $filename;

    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    /**
     * Add a file to the list of files to zip.
     * 
     * @param $originalFilename
     * @param $localFilename The filename the file will assumes inside the archive
     * @param false $deleteOriginal
     */
    public function addFile($originalFilename, $localFilename, $deleteOriginal = false)
    {
        $this->files[] = [
            'filename' => $originalFilename,
            'localFilename' => $localFilename,
            'deleteOriginal' => $deleteOriginal
        ];
    }

    /**
     * Prepare the actual archive and then clear the files to remove.
     * 
     * @return string
     */
    public function generate()
    {
        // Add files to the archive
        $zip = new ZipArchive;
        if ($zip->open($this->filename, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
            foreach ($this->files as $file) {
                $zip->addFile($file['filename'], $file['localFilename']);
            }
        }
        $zip->close();

        // Delete files
        $this->clearFiles();

        return $this->filename;
    }

    /**
     * Clear the files (removing the from the filesystem) set as removable
     */
    public function clearFiles()
    {
        foreach ($this->files as $file) {
            $deleteOriginal = array_key_exists('deleteOriginal', $file) && $file['deleteOriginal'] ? true : false;

            if ($deleteOriginal) {
                unlink($file['filename']);
            }
        }
    }
}
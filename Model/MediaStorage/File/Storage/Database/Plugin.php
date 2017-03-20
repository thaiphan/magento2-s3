<?php
namespace Thai\S3\Model\MediaStorage\File\Storage\Database;

class Plugin
{
    private $helper;

    private $storageModel;

    public function __construct(
        \Thai\S3\Helper\Data $helper,
        \Thai\S3\Model\MediaStorage\File\Storage\S3 $storageModel
    ) {
        $this->helper = $helper;
        $this->storageModel = $storageModel;
    }

    public function aroundGetDirectoryFiles($subject, $proceed, $directory)
    {
        if ($this->helper->checkS3Usage()) {
            return $this->storageModel->getDirectoryFiles($directory);
        }
        return $proceed($directory);
    }
}

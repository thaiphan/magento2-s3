<?php
namespace Thai\S3\Model\MediaStorage\File\Storage\Directory\Database;

use Magento\MediaStorage\Model\File\Storage\Directory\Database;
use Thai\S3\Helper\Data as DataHelper;
use Thai\S3\Model\MediaStorage\File\Storage\S3 as S3Storage;

class Plugin
{
    /**
     * @var DataHelper
     */
    private $helper;

    /**
     * @var S3Storage
     */
    private $storageModel;

    /**
     * @param DataHelper $helper
     * @param S3Storage $storageModel
     */
    public function __construct(
        DataHelper $helper,
        S3Storage $storageModel
    ) {
        $this->helper = $helper;
        $this->storageModel = $storageModel;
    }

    public function aroundCreateRecursive($subject, $proceed, $path)
    {
        if ($this->helper->checkS3Usage()) {
            return $this;
        }

        return $proceed($path);
    }

    /**
     * @param Database $subject
     * @param \Closure $proceed
     * @param string $directory
     * @return array
     */
    public function aroundGetSubdirectories($subject, $proceed, $directory)
    {
        if ($this->helper->checkS3Usage()) {
            return $this->storageModel->getSubdirectories($directory);
        }

        return $proceed($directory);

    }

    /**
     * @param Database $subject
     * @param \Closure $proceed
     * @param string $path
     * @return S3Storage
     */
    public function aroundDeleteDirectory($subject, $proceed, $path)
    {
        if ($this->helper->checkS3Usage()) {
            return $this->storageModel->deleteDirectory($path);
        }

        return $proceed($path);
    }
}

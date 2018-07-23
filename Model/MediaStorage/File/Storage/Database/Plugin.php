<?php
namespace Thai\S3\Model\MediaStorage\File\Storage\Database;

use Magento\MediaStorage\Model\File\Storage\Database;
use Thai\S3\Helper\Data as DataHelper;
use Thai\S3\Model\MediaStorage\File\Storage\S3 as S3Storage;

/**
 * Plugin for Databse.
 *
 * @see Database
 */
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

    /**
     * @param Database $subject
     * @param \Closure $proceed
     * @param string $directory
     * @return array
     */
    public function aroundGetDirectoryFiles($subject, $proceed, $directory)
    {
        if ($this->helper->checkS3Usage()) {
            return $this->storageModel->getDirectoryFiles($directory);
        }

        return $proceed($directory);
    }
}

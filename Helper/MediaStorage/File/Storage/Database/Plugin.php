<?php
namespace Thai\S3\Helper\MediaStorage\File\Storage\Database;

use Magento\MediaStorage\Helper\File\Storage\Database;

class Plugin
{
    private $helper;

    private $s3StorageFactory;

    private $dbStorageFactory;

    private $storageModel = null;

    public function __construct(
        \Thai\S3\Helper\Data $helper,
        \Thai\S3\Model\MediaStorage\File\Storage\S3Factory $s3StorageFactory,
        \Magento\MediaStorage\Model\File\Storage\DatabaseFactory $dbStorageFactory
    ) {
        $this->helper = $helper;
        $this->s3StorageFactory = $s3StorageFactory;
        $this->dbStorageFactory = $dbStorageFactory;
    }

    /**
     * Check whether we are using either the database or S3 as our file storage
     * backend.
     *
     * @param Database $subject
     * @param bool $result
     * @return bool
     */
    public function afterCheckDbUsage(Database $subject, $result)
    {
        if (!$result) {
            $result = $this->helper->checkS3Usage();
        }
        return $result;
    }

    public function aroundGetStorageDatabaseModel(Database $subject, $proceed)
    {
        if (is_null($this->storageModel)) {
            if ($subject->checkDbUsage() && $this->helper->checkS3Usage()) {
                $this->storageModel = $this->s3StorageFactory->create();
            } else {
                $this->storageModel = $this->dbStorageFactory->create();
            }
        }
        return $this->storageModel;
    }

    public function aroundSaveFileToFilesystem(Database $subject, $proceed, $filename)
    {
        if ($subject->checkDbUsage() && $this->helper->checkS3Usage()) {
            $file = $subject->getStorageDatabaseModel()->loadByFilename($subject->getMediaRelativePath($filename));
            if (!$file->getId()) {
                return false;
            }

            return $subject->getStorageFileModel()->saveFile($file->getData(), true);
        }
        return $proceed($filename);
    }

    /**
     * The Magento_ImportExport module will try to run erroneous file paths,
     * e.g. pub/media/catalog/category/twswifty.jpg, through the parent function
     * of this plugin. The parent function can't handle this so it just returns
     * the original file path (when we really don't want the pub/media prefix at
     * all). This plugin will remove the pub/media prefix.
     *
     * @param Database $subject
     * @param string $result
     * @return string
     */
    public function afterGetMediaRelativePath(Database $subject, $result)
    {
        $newMediaRelativePath = $result;
        if ($this->helper->checkS3Usage()) {
            $prefixToRemove = 'pub/media/';
            if (substr($result, 0, strlen($prefixToRemove)) == $prefixToRemove) {
                $newMediaRelativePath = substr($result, strlen($prefixToRemove));
            }
        }
        return $newMediaRelativePath;
    }

    public function aroundDeleteFolder(Database $subject, $proceed, $folderName)
    {
        if ($this->helper->checkS3Usage()) {
            /** @var \Thai\S3\Model\MediaStorage\File\Storage\S3 $storageModel */
            $storageModel = $subject->getStorageDatabaseModel();
            $storageModel->deleteDirectory($folderName);
        } else {
            $proceed($folderName);
        }
    }

    /**
     * Removes any forward slashes from the start of the uploaded file name.
     * This addresses a bug where category pages were being saved with duplicate
     * slashes, e.g. catalog/category//tswifty_4.jpg.
     *
     * @param Database $subject
     * @param string $result
     * @return string
     */
    public function afterSaveUploadedFile(Database $subject, $result)
    {
        return ltrim($result, '/');
    }
}

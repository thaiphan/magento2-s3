<?php
namespace Thai\S3\Model\ResourceModel\MediaStorage\File\Storage;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\MediaStorage\Helper\File\Storage\Database;
use Magento\MediaStorage\Model\ResourceModel\File\Storage\File as StorageFile;
use Psr\Log\LoggerInterface;

/**
 * @inheritdoc
 */
class File extends StorageFile
{
    /**
     * @var Database
     */
    protected $fileStorageDb;

    /**
     * @param Filesystem $filesystem
     * @param LoggerInterface $log
     * @param Database $fileStorageDb
     */
    public function __construct(
        Filesystem $filesystem,
        LoggerInterface $log,
        Database $fileStorageDb
    ) {
        parent::__construct($filesystem, $log);

        $this->fileStorageDb = $fileStorageDb;
    }

    /**
     * Extend the original functionality of this method by also uploading the
     * requested file to S3.
     *
     * @param string $filePath
     * @param string $content
     * @param bool $overwrite
     * @return bool
     * @throws LocalizedException
     */
    public function saveFile($filePath, $content, $overwrite = false)
    {
        $result = parent::saveFile($filePath, $content, $overwrite);

        if ($result) {
            $this->fileStorageDb->saveFile($filePath);
        }

        return $result;
    }
}

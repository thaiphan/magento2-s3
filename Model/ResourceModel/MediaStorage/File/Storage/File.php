<?php
namespace Thai\S3\Model\ResourceModel\MediaStorage\File\Storage;

class File extends \Magento\MediaStorage\Model\ResourceModel\File\Storage\File
{
    protected $fileStorageDb;

    public function __construct(
        \Magento\Framework\Filesystem $filesystem,
        \Psr\Log\LoggerInterface $log,
        \Magento\MediaStorage\Helper\File\Storage\Database $fileStorageDb
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
     * @throws \Magento\Framework\Exception\LocalizedException
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

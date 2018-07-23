<?php
namespace Thai\S3\Model\Cms\Wysiwyg\Images\Storage;

use Magento\Cms\Model\Wysiwyg\Images\Storage;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\MediaStorage\Helper\File\Storage\Database;
use Thai\S3\Helper\Data;

/**
 * Plugin for Storage.
 *
 * @see Storage
 */
class Plugin
{
    /**
     * @var Data
     */
    private $helper;

    /**
     * @var Database
     */
    private $database;

    /**
     * @var Database
     */
    private $coreFileStorageDb;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    private $directory;

    /**
     * @var \Magento\MediaStorage\Model\File\Storage\Directory\DatabaseFactory
     */
    private $directoryDatabaseFactory;

    /**
     * @param Data $helper
     * @param Database $database
     * @param Database $coreFileStorageDb
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\MediaStorage\Model\File\Storage\Directory\DatabaseFactory $directoryDatabaseFactory
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        Data $helper,
        Database $database,
        \Magento\MediaStorage\Helper\File\Storage\Database $coreFileStorageDb,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\MediaStorage\Model\File\Storage\Directory\DatabaseFactory $directoryDatabaseFactory
    ) {
        $this->helper = $helper;
        $this->database = $database;
        $this->coreFileStorageDb = $coreFileStorageDb;
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->directoryDatabaseFactory = $directoryDatabaseFactory;
    }

    /**
     * This plugin addresses an issue where Magento doubles up on prepending
     * the Magento root path to your relative file path. You end up with
     * something silly like /var/www/pub/media/var/www/pub/media/wysiwyg/dog.jpg
     *
     * @param Storage $subject
     * @param string $path
     * @return array
     */
    public function beforeGetDirsCollection(Storage $subject, $path)
    {
        $this->createSubDirectories($path);

        return [$path];
    }

    /**
     * @param string $path
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    protected function createSubDirectories($path)
    {
        if ($this->coreFileStorageDb->checkDbUsage()) {
            $subDirectories = $this->directoryDatabaseFactory->create();
            $directories = $subDirectories->getSubdirectories($path);
            foreach ($directories as $directory) {
                $this->directory->create($directory['name']);
            }
        }
    }

    /**
     * @param Storage $subject
     * @param $result
     * @return mixed
     */
    public function afterResizeFile(Storage $subject, $result)
    {
        if ($this->helper->checkS3Usage()) {
            $thumbnailRelativePath = $this->database->getMediaRelativePath($result);
            $this->database->getStorageDatabaseModel()->saveFile($thumbnailRelativePath);
        }

        return $result;
    }

    /**
     * @param Storage $subject
     * @param string $result
     * @return string
     */
    public function afterGetThumbsPath(Storage $subject, $result)
    {
        return rtrim($result, '/');
    }
}

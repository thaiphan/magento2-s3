<?php
namespace Thai\S3\Model\MediaStorage\File\Storage\Synchronisation;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;
use Magento\MediaStorage\Model\File\Storage\Synchronization;
use Thai\S3\Model\MediaStorage\File\Storage\S3Factory;

/**
 * Plugin for Synchronization.
 *
 * @see Synchronization
 */
class Plugin
{
    /**
     * @var S3Factory
     */
    private $storageFactory;

    /**
     * @var Filesystem\Directory\WriteInterface
     */
    private $mediaDirectory;

    /**
     * @param S3Factory $storageFactory
     * @param Filesystem $filesystem
     * @throws FileSystemException
     */
    public function __construct(
        S3Factory $storageFactory,
        Filesystem $filesystem
    ) {
        $this->storageFactory = $storageFactory;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
    }

    /**
     * @param Synchronization $subject
     * @param string $relativeFileName
     * @return array
     */
    public function beforeSynchronize($subject, $relativeFileName)
    {
        $storage = $this->storageFactory->create();
        try {
            $storage->loadByFilename($relativeFileName);
        } catch (\Exception $e) {
        }

        if ($storage->getId()) {
            $file = $this->mediaDirectory->openFile($relativeFileName, 'w');
            try {
                $file->lock();
                $file->write($storage->getContent());
                $file->unlock();
                $file->close();
            } catch (FileSystemException $e) {
                $file->close();
            }
        }

        return [
            $relativeFileName,
        ];
    }
}

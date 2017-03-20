<?php
namespace Thai\S3\Model\MediaStorage\File\Storage\Synchronisation;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Exception\FileSystemException;

class Plugin
{
    private $storageFactory;

    private $mediaDirectory;

    public function __construct(
        \Thai\S3\Model\MediaStorage\File\Storage\S3Factory $storageFactory,
        Filesystem $filesystem
    ) {
        $this->storageFactory = $storageFactory;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
    }

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
            $relativeFileName
        ];
    }
}

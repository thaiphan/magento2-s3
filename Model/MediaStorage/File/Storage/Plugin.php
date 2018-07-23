<?php
namespace Thai\S3\Model\MediaStorage\File\Storage;

use Magento\MediaStorage\Helper\File\Storage as StorageHelper;
use Magento\MediaStorage\Model\File\Storage;

/**
 * Plugin for Storage.
 *
 * @see Storage
 */
class Plugin
{
    /**
     * @var StorageHelper
     */
    private $coreFileStorage;

    /**
     * @var S3Factory
     */
    private $s3Factory;

    /**
     * @param StorageHelper $coreFileStorage
     * @param S3Factory $s3Factory
     */
    public function __construct(
        StorageHelper $coreFileStorage,
        S3Factory $s3Factory
    ) {
        $this->coreFileStorage = $coreFileStorage;
        $this->s3Factory = $s3Factory;
    }

    /**
     * @param Storage $subject
     * @param \Closure $proceed
     * @param string $storage
     * @param array $params
     * @return bool
     */
    public function aroundGetStorageModel($subject, $proceed, $storage = null, array $params = [])
    {
        $storageModel = $proceed($storage, $params);
        if ($storageModel === false) {
            if (null === $storage) {
                $storage = $this->coreFileStorage->getCurrentStorageCode();
            }
            switch ($storage) {
                case \Thai\S3\Model\MediaStorage\File\Storage::STORAGE_MEDIA_S3:
                    $storageModel = $this->s3Factory->create();
                    break;
                default:
                    return false;
            }

            if (isset($params['init']) && $params['init']) {
                $storageModel->init();
            }
        }

        return $storageModel;
    }
}

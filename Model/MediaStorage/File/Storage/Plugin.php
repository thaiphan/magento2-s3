<?php
namespace Thai\S3\Model\MediaStorage\File\Storage;

class Plugin
{
    private $coreFileStorage;
    private $s3Factory;

    public function __construct(
        \Magento\MediaStorage\Helper\File\Storage $coreFileStorage,
        S3Factory $s3Factory
    ) {
        $this->coreFileStorage = $coreFileStorage;
        $this->s3Factory = $s3Factory;
    }

    public function aroundGetStorageModel($subject, $proceed, $storage = null, array $params = [])
    {
        $storageModel = $proceed($storage, $params);
        if ($storageModel === false) {
            if (is_null($storage)) {
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

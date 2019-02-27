<?php
namespace Thai\S3\Service\MediaStorage\ImageResize;

use Magento\Catalog\Model\Product\Media\ConfigInterface as MediaConfig;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\MediaStorage\Model\File\Storage\SynchronizationFactory;
use Magento\MediaStorage\Service\ImageResize;
use Thai\S3\Helper\Data;

class Plugin
{
    /**
     * @var Data
     */
    private $helper;

    /**
     * @var MediaConfig
     */
    private $imageConfig;

    /**
     * @var Filesystem
     */
    private $mediaDirectory;

    /**
     * @var SynchronizationFactory
     */
    private $syncFactory;

    public function __construct(
        Data $helper,
        SynchronizationFactory $syncFactory,
        MediaConfig $imageConfig,
        Filesystem $filesystem
    ) {
        $this->helper = $helper;
        $this->imageConfig = $imageConfig;
        $this->syncFactory = $syncFactory;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
    }

    public function beforeResizeFromImageName(ImageResize $subject, $originalImageName)
    {
        if ($this->helper->checkS3Usage()) {
            $relativeFileName = $this->imageConfig->getMediaPath($originalImageName);
            if (!$this->mediaDirectory->isFile($relativeFileName)) {
                /** @var \Magento\MediaStorage\Model\File\Storage\Synchronization $sync */
                $sync = $this->syncFactory->create(['directory' => $this->mediaDirectory]);
                $sync->synchronize($relativeFileName);
            }
        }

        return [
            $originalImageName,
        ];
    }
}
<?php
namespace Thai\S3\Helper\Swatches\Media;

use Magento\Catalog\Model\Product\Media\Config;
use Magento\MediaStorage\Helper\File\Storage\Database;
use Magento\Swatches\Helper\Media;

/**
 * Plugin for Media.
 *
 * @see Media
 */
class Plugin
{
    /**
     * @var Config
     */
    protected $mediaConfig;

    /**
     * Core file storage database
     *
     * @var Database
     */
    protected $fileStorageDb;

    /**
     * @var array
     */
    protected $swatchImageTypes = ['swatch_image', 'swatch_thumb'];

    /**
     * @param Config $mediaConfig
     * @param Database $fileStorageDb
     */
    public function __construct(
        Config $mediaConfig,
        Database $fileStorageDb
    ) {
        $this->mediaConfig = $mediaConfig;
        $this->fileStorageDb = $fileStorageDb;
    }

    /**
     * @param Media $subject
     * @param string $file
     * @return array
     */
    public function beforeMoveImageFromTmp(Media $subject, $file)
    {
        if ($this->fileStorageDb->checkDbUsage()) {
            if (strrpos($file, '.tmp') == strlen($file) - 4) {
                $updatedFile = substr($file, 0, strlen($file) - 4);
            }

            $destinationFile = $this->getUniqueFileName($updatedFile);

            // Magento saves uploaded swatches to the wrong place. Upload to the
            // correct location.
            $this->fileStorageDb->copyFile(
                $this->mediaConfig->getTmpMediaShortUrl($updatedFile),
                $subject->getAttributeSwatchPath($destinationFile)
            );

            // Database file storage doesn't have a forward slash in front of
            // files. We need to add manually.
            $file = '/' . ltrim($file, '/');
        }

        return [$file];
    }

    /**
     * @param Media $subject
     * @param callable $proceed
     * @param string $imageUrl
     * @return mixed
     */
    public function aroundGenerateSwatchVariations(Media $subject, callable $proceed, $imageUrl)
    {
        if ($this->fileStorageDb->checkDbUsage()) {
            // Magento prematurely deletes the uploaded thumbnails off the file
            // system before we can generate the swatch thumbnails. We need to
            // manually restore the file.
            $fileToRestore = $subject->getAttributeSwatchPath($imageUrl);

            $this->fileStorageDb->saveFileToFilesystem($fileToRestore);

            $result = $proceed($imageUrl);

            // Magento generates the swatch thumbnails but it doesn't bother to
            // upload to S3.
            foreach ($this->swatchImageTypes as $swatchType) {
                $imageConfig = $subject->getImageConfig();
                $fileName = $this->prepareFileName($imageUrl);
                $swatchPath = $subject->getSwatchCachePath($swatchType) . $subject->getFolderNameSize($swatchType,
                        $imageConfig) . $fileName['path'] . '/' . $fileName['name'];
                $this->fileStorageDb->saveFile($swatchPath);
            }

            return $result;
        }

        return $proceed($imageUrl);
    }

    /**
     * Image url /m/a/magento.png return ['name' => 'magento.png', 'path => '/m/a']
     *
     * @param string $imageUrl
     * @return array
     */
    protected function prepareFileName($imageUrl)
    {
        $fileArray = explode('/', $imageUrl);
        $fileName = array_pop($fileArray);
        $filePath = implode('/', $fileArray);

        return ['name' => $fileName, 'path' => $filePath];
    }

    /**
     * Check whether file to move exists. Getting unique name
     *
     * @param string $file
     * @return string
     */
    protected function getUniqueFileName($file)
    {
        return $this->fileStorageDb->getUniqueFilename(
            $this->mediaConfig->getBaseMediaUrlAddition(),
            $file
        );
    }
}

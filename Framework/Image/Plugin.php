<?php
namespace Thai\S3\Framework\Image;

use Magento\MediaStorage\Helper\File\Storage\Database;
use Magento\Theme\Model\Design\Backend\Favicon;
use Thai\S3\Helper\Data;

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
     * @param Data $helper
     * @param Database $database
     */
    public function __construct(
        Data $helper,
        Database $database
    )
    {
        $this->helper = $helper;
        $this->database = $database;
    }

    public function afterSave($subject, $result, $destination = null, $newFileName = null)
    {
        if ($this->helper->checkS3Usage()) {
            $relativeImgFile = $this->database->getMediaRelativePath($destination);
            $this->database->getStorageDatabaseModel()->saveFile($relativeImgFile);
        }

        return $result;
    }
}
<?php
namespace Thai\S3\Model\Theme\Design\Backend\Logo;

use Magento\MediaStorage\Helper\File\Storage\Database;
use Magento\Theme\Model\Design\Backend\Logo;
use Thai\S3\Helper\Data;

/**
 * Plugin for Logo.
 *
 * @see Logo
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

    /**
     * @param Logo $subject
     * @param Logo $result
     * @return Logo
     */
    public function afterBeforeSave(Logo $subject, Logo $result)
    {
        if ($this->helper->checkS3Usage()) {
            $imgFile = $subject::UPLOAD_DIR . '/' . $subject->getValue();
            $relativeImgFile = $this->database->getMediaRelativePath($imgFile);
            $this->database->getStorageDatabaseModel()->saveFile($relativeImgFile);
        }

        return $result;
    }
}

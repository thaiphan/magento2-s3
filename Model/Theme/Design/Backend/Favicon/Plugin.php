<?php
namespace Thai\S3\Model\Theme\Design\Backend\Favicon;

use Magento\MediaStorage\Helper\File\Storage\Database;
use Magento\Theme\Model\Design\Backend\Favicon;
use Thai\S3\Helper\Data;

/**
 * Plugin for Favicon.
 *
 * @see Favicon
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
     * @param Favicon $subject
     * @param Favicon $result
     * @return Favicon
     */
    public function afterBeforeSave(Favicon $subject, Favicon $result)
    {
        if ($this->helper->checkS3Usage()) {
            $imgFile = $subject::UPLOAD_DIR . '/' . $subject->getValue();
            $relativeImgFile = $this->database->getMediaRelativePath($imgFile);
            $this->database->getStorageDatabaseModel()->saveFile($relativeImgFile);
        }

        return $result;
    }
}

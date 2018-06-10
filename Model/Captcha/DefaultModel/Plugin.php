<?php
namespace Thai\S3\Model\Captcha\DefaultModel;

use Thai\S3\Helper\Data;
use Magento\Cms\Model\Wysiwyg\Images\Storage;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\MediaStorage\Helper\File\Storage\Database;

class Plugin
{
    private $helper;
    private $database;

    public function __construct(
        Data $helper,
        Database $database
    ) {
        $this->helper = $helper;
        $this->database = $database;
    }

    public function afterGenerate(\Magento\Captcha\Model\DefaultModel $subject, $result)
    {
        if ($this->helper->checkS3Usage()) {
            $imgFile = $subject->getImgDir() . $result . $subject->getSuffix();
            $relativeImgFile = $this->database->getMediaRelativePath($imgFile);
            $this->database->getStorageDatabaseModel()->saveFile($relativeImgFile);
        }

        return $result;
    }
}
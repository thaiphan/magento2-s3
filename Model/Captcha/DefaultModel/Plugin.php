<?php
namespace Thai\S3\Model\Captcha\DefaultModel;

use Magento\Captcha\Model\DefaultModel;
use Magento\MediaStorage\Helper\File\Storage\Database;
use Thai\S3\Helper\Data;

/**
 * Plugin for DefaultModel.
 *
 * @see DefaultModel
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
    ) {
        $this->helper = $helper;
        $this->database = $database;
    }

    /**
     * @param DefaultModel $subject
     * @param \Closure $result
     * @return mixed
     */
    public function afterGenerate(DefaultModel $subject, $result)
    {
        if ($this->helper->checkS3Usage()) {
            $imgFile = $subject->getImgDir() . $result . $subject->getSuffix();
            $relativeImgFile = $this->database->getMediaRelativePath($imgFile);
            $this->database->getStorageDatabaseModel()->saveFile($relativeImgFile);
        }

        return $result;
    }
}

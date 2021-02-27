<?php
namespace Thai\S3\Test\Model\MediaStorage\Config\Source\Storage\Media\Storage;

use Magento\MediaStorage\Model\Config\Source\Storage\Media\Storage;
use PHPUnit\Framework\TestCase;
use Thai\S3\Model\MediaStorage\Config\Source\Storage\Media\Storage\Plugin;

class PluginTest extends TestCase
{
    /**
     * @var Storage
     */
    protected $_storage;

    /**
     * @var Plugin
     */
    protected $_object;

    protected function setUp()
    {
        $this->_storage = $this->createMock(Storage::class);
        $this->_object = new Plugin();
    }

    public function testAmazonS3IsAddedToMediaStorageOptions()
    {
        $this->assertContains(
            [
                'value' => \Thai\S3\Model\MediaStorage\File\Storage::STORAGE_MEDIA_S3,
                'label' => __('Amazon S3'),
            ],
            $this->_object->afterToOptionArray($this->_storage, [])
        );
    }
}

<?php
namespace Thai\S3\Test\Model\Store\Store;

use Magento\Store\Model\Store;
use PHPUnit\Framework\TestCase;
use Thai\S3\Model\Store\Store\Plugin;

class PluginTest extends TestCase
{
    /**
     * @var Store
     */
    protected $_store;

    /**
     * @var Plugin
     */
    protected $_object;

    protected function setUp()
    {
        $this->_store = $this->createMock(Store::class);
        $this->_object = new Plugin();
    }

    public function testDoubleSlashIsReplacedWithSingleSlash()
    {
        $this->assertEquals($this->_object->afterGetBaseUrl($this->_store, "//get.php/"), "/get.php/");
    }
}

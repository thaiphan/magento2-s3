<?php
namespace Thai\S3\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;
use Thai\S3\Helper\S3 as S3Helper;

/**
 * Regions source.
 */
class Region implements ArrayInterface
{
    /**
     * @var S3Helper
     */
    private $helper;

    /**
     * @param S3Helper $helper
     */
    public function __construct(S3Helper $helper)
    {
        $this->helper = $helper;
    }

    /**
     * Return list of available Amazon S3 regions
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->helper->getRegions();
    }
}

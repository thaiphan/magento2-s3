<?php
namespace Thai\S3\Model\Config\Source;

class Region implements \Magento\Framework\Option\ArrayInterface
{
    private $helper;

    public function __construct(\Thai\S3\Helper\S3 $helper)
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

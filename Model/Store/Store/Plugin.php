<?php
namespace Thai\S3\Model\Store\Store;

class Plugin
{
    /**
     * This plugin fixes a bug where Magento incorrectly appends two forward
     * slashes to the media rewrite script. We remove one of those extra forward
     * slashes.
     *
     * @param \Magento\Store\Model\Store $subject
     * @param string $result
     * @return string
     */
    public function afterGetBaseUrl(\Magento\Store\Model\Store $subject, $result)
    {
        return str_replace('//get.php/', '/get.php/', $result);
    }
}

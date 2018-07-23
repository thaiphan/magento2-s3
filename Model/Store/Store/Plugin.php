<?php
namespace Thai\S3\Model\Store\Store;

use Magento\Store\Model\Store;

/**
 * Plugin fot Store.
 *
 * @see Store
 */
class Plugin
{
    /**
     * This plugin fixes a bug where Magento incorrectly appends two forward
     * slashes to the media rewrite script. We remove one of those extra forward
     * slashes.
     *
     * @param Store $subject
     * @param string $result
     * @return string
     */
    public function afterGetBaseUrl(Store $subject, $result)
    {
        return str_replace('//get.php/', '/get.php/', $result);
    }
}

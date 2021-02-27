<?php
namespace Thai\S3\Model\MediaStorage\Config\Source\Storage\Media\Storage;

use Magento\MediaStorage\Model\Config\Source\Storage\Media\Storage;

/**
 * Plugin for Storage.
 *
 * @see Storage
 */
class Plugin
{
    /**
     * This plugin adds "Amazon S3" to the list of available media storage
     * options (alongside "database" and "file system") in the system config.
     *
     * @param Storage $subject
     * @param array $result
     * @return array
     */
    public function afterToOptionArray($subject, $result)
    {
        $result[] = [
            'value' => \Thai\S3\Model\MediaStorage\File\Storage::STORAGE_MEDIA_S3,
            'label' => __('Amazon S3'),
        ];

        return $result;
    }
}

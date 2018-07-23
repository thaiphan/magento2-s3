<?php
namespace Thai\S3\Model\MediaStorage\Config\Source\Storage\Media\Storage;

class Plugin
{
    /**
     * @param \Closure $subject
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

<?php
namespace Thai\S3\Helper;

/**
 * Provides list of regions.
 */
class S3
{
    /**
     * Determines whether an S3 region code is valid.
     *
     * @param string $regionInQuestion
     * @return bool
     */
    public function isValidRegion($regionInQuestion)
    {
        foreach ($this->getRegions() as $currentRegion) {
            if ($currentRegion['value'] == $regionInQuestion) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array
     */
    public function getRegions()
    {
        return [
            [
                'value' => 'ap-northeast-1',
                'label' => 'Asia Pacific (Tokyo)',
            ],
            [
                'value' => 'ap-northeast-2',
                'label' => 'Asia Pacific (Seoul)',
            ],
            [
                'value' => 'ap-south-1',
                'label' => 'Asia Pacific (Mumbai)',
            ],
            [
                'value' => 'ap-southeast-1',
                'label' => 'Asia Pacific (Singapore)',
            ],
            [
                'value' => 'ap-southeast-2',
                'label' => 'Asia Pacific (Sydney)',
            ],
            [
                'value' => 'ca-central-1',
                'label' => 'Canada (Central)',
            ],
            [
                'value' => 'cn-north-1',
                'label' => 'China (Beijing)',
            ],
            [
                'value' => 'cn-northwest-1',
                'label' => 'China (Ningxia)',
            ],
            [
                'value' => 'eu-central-1',
                'label' => 'EU (Frankfurt)',
            ],
            [
                'value' => 'eu-west-1',
                'label' => 'EU (Ireland)',
            ],
            [
                'value' => 'eu-west-2',
                'label' => 'EU (London)',
            ],
            [
                'value' => 'eu-west-3',
                'label' => 'EU (Paris)',
            ],
            [
                'value' => 'sa-east-1',
                'label' => 'South America (Sao Paulo)',
            ],
            [
                'value' => 'us-east-1',
                'label' => 'US East (N. Virginia)',
            ],
            [
                'value' => 'us-east-2',
                'label' => 'US East (Ohio)',
            ],
            [
                'value' => 'us-west-1',
                'label' => 'US West (N. California)',
            ],
            [
                'value' => 'us-west-2',
                'label' => 'US West (Oregon)',
            ],
        ];
    }
}

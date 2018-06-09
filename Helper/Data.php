<?php
namespace Thai\S3\Helper;

use Thai\S3\Model\MediaStorage\File\Storage;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    private $useS3 = null;

    /**
     * Check whether we are allowed to use S3 as our file storage backend.
     *
     * @return bool
     */
    public function checkS3Usage()
    {
        if (is_null($this->useS3)) {
            $currentStorage = (int)$this->scopeConfig->getValue(Storage::XML_PATH_STORAGE_MEDIA);
            $this->useS3 = $currentStorage == Storage::STORAGE_MEDIA_S3;
        }
        return $this->useS3;
    }

    public function getAccessKey()
    {
        return $this->scopeConfig->getValue('thai_s3/general/access_key');
    }

    public function getSecretKey()
    {
        return $this->scopeConfig->getValue('thai_s3/general/secret_key');
    }

    public function getRegion()
    {
        return $this->scopeConfig->getValue('thai_s3/general/region');
    }

    public function getEndpointEnabled()
    {
        return $this->scopeConfig->getValue('thai_s3/custom_endpoint/enabled');
    }

    public function getEndpoint()
    {
        return $this->scopeConfig->getValue('thai_s3/custom_endpoint/endpoint');
    }

    public function getEndpointRegion()
    {
        return $this->scopeConfig->getValue('thai_s3/custom_endpoint/region');
    }

    public function getBucket()
    {
        return $this->scopeConfig->getValue('thai_s3/general/bucket');
    }

    public function getExpires()
    {
        return $this->scopeConfig->getValue('thai_s3/headers/expires');
    }

    public function getCacheControl()
    {
        return $this->scopeConfig->getValue('thai_s3/headers/cache_control');
    }

    public function getCustomHeaders()
    {
        return $this->scopeConfig->getValue('thai_s3/headers/custom_headers');
    }
}

<?php
namespace Thai\S3\Console\Command;

use Magento\Config\Model\Config\Factory;
use Magento\MediaStorage\Helper\File\Storage\Database;
use Magento\MediaStorage\Helper\File\StorageFactory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StorageExportCommand extends \Symfony\Component\Console\Command\Command
{
    private $configFactory;

    private $state;

    private $helper;

    private $client;

    private $coreFileStorage;

    private $coreFileStorageFactory;

    private $storageHelper;

    public function __construct(
        \Magento\Framework\App\State $state,
        Factory $configFactory,
        Database $storageHelper,
        StorageFactory $coreFileStorageFactory,
        \Thai\S3\Helper\Data $helper
    ) {
        $this->state = $state;
        $this->configFactory = $configFactory;
        $this->coreFileStorageFactory = $coreFileStorageFactory;
        $this->helper = $helper;
        $this->storageHelper = $storageHelper;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('s3:storage:export');
        $this->setDescription('Sync all of your media files over to S3.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->state->setAreaCode('adminhtml');
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            // intentionally left empty
        }

        $errors = $this->validate($input);
        if ($errors) {
            $output->writeln('<error>' . implode('</error>' . PHP_EOL .  '<error>', $errors) . '</error>');
            return;
        }

        $options = [
            'version' => 'latest',
            'region' => $this->helper->getRegion(),
            'credentials' => [
                'key' => $this->helper->getAccessKey(),
                'secret' => $this->helper->getSecretKey()
            ]
        ];

        if ($this->helper->getEndpointEnabled()) {
            if ($this->helper->getEndpoint()) {
                $options['endpoint'] = $this->helper->getEndpoint();
            }

            if ($this->helper->getEndpointRegion()) {
                $options['region'] = $this->helper->getEndpointRegion();
            }
        }

        try {
            $this->client = new \Aws\S3\S3Client($options);
        } catch (\Exception $e) {
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
            return;
        }

        if (!$this->client->doesBucketExist($this->helper->getBucket())) {
            $output->writeln('<error>The AWS credentials you provided did not work. Please review your details and try again. You can do so using our config script.</error>');
            return;
        }

        if ($this->getFileStorageHelper()->getCurrentStorageCode() == \Thai\S3\Model\MediaStorage\File\Storage::STORAGE_MEDIA_S3) {
            $output->writeln('<error>You are already using S3 as your media file storage backend!</error>');
            return;
        }

        $sourceModel = $this->getFileStorageHelper()->getStorageModel();
        /** @var \Thai\S3\Model\MediaStorage\File\Storage\S3 $destinationModel */
        $destinationModel = $this->getFileStorageHelper()->getStorageModel(\Thai\S3\Model\MediaStorage\File\Storage::STORAGE_MEDIA_S3);

        $offset = 0;
        while (($files = $sourceModel->exportFiles($offset, 1)) !== false) {
            foreach ($files as $file) {
                $object = ltrim($file['directory'] . '/' . $file['filename'], '/');

                $output->writeln(sprintf('Uploading %s to use S3.', $object));
            }
            $destinationModel->importFiles($files);
            $offset += count($files);
        }
    }

    public function validate(InputInterface $input)
    {
        $errors = [];

        if (is_null($this->helper->getAccessKey())) {
            $errors[] = 'You have not provided an AWS access key ID. You can do so using our config script.';
        }
        if (is_null($this->helper->getSecretKey())) {
            $errors[] = 'You have not provided an AWS secret access key. You can do so using our config script.';
        }
        if (is_null($this->helper->getBucket())) {
            $errors[] = 'You have not provided an S3 bucket. You can do so using our config script.';
        }
        if (is_null($this->helper->getRegion())) {
            $errors[] = 'You have not provided an S3 region. You can do so using our config script.';
        }

        return $errors;
    }

    /**
     * @return \Magento\MediaStorage\Helper\File\Storage
     */
    public function getFileStorageHelper()
    {
        if (is_null($this->coreFileStorage)) {
            $this->coreFileStorage = $this->coreFileStorageFactory->create();
        }
        return $this->coreFileStorage;
    }
}

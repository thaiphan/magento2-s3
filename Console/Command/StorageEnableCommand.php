<?php
namespace Thai\S3\Console\Command;

use Magento\Config\Model\Config\Factory;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\MediaStorage\Helper\File\Storage;
use Magento\MediaStorage\Helper\File\StorageFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Thai\S3\Helper\Data as DataHelper;

/**
 * @inheritdoc
 */
class StorageEnableCommand extends Command
{
    /**
     * @var Factory
     */
    private $configFactory;

    /**
     * @var State
     */
    private $state;

    /**
     * @var DataHelper
     */
    private $helper;

    /**
     * @var Storage
     */
    private $storage;

    /**
     * @param State $state
     * @param Factory $configFactory
     * @param StorageFactory $coreFileStorageFactory
     * @param DataHelper $helper
     */
    public function __construct(
        State $state,
        Factory $configFactory,
        StorageFactory $coreFileStorageFactory,
        DataHelper $helper
    ) {
        $this->state = $state;
        $this->configFactory = $configFactory;
        $this->helper = $helper;
        $this->storage = $coreFileStorageFactory->create();

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('s3:storage:enable');
        $this->setDescription('Enable use of S3 as your Magento 2 file storage backend.');
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        return $this->state->emulateAreaCode(Area::AREA_ADMINHTML, function () use ($output) {
            $errors = $this->validate();

            if ($errors) {
                $output->writeln('<error>' . implode('</error>' . PHP_EOL . '<error>', $errors) . '</error>');

                return 1;
            }

            try {
                $options = [
                    'version' => 'latest',
                    'region' => $this->helper->getRegion(),
                    'credentials' => [
                        'key' => $this->helper->getAccessKey(),
                        'secret' => $this->helper->getSecretKey(),
                    ],
                ];

                if ($this->helper->getEndpointEnabled()) {
                    if ($this->helper->getEndpoint()) {
                        $options['endpoint'] = $this->helper->getEndpoint();
                    }

                    if ($this->helper->getEndpointRegion()) {
                        $options['region'] = $this->helper->getEndpointRegion();
                    }
                }

                $client = new \Aws\S3\S3Client($options);
            } catch (\Exception $e) {
                $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));

                return 1;
            }

            if (!$client->doesBucketExist($this->helper->getBucket())) {
                $output->writeln('<error>The AWS credentials you provided did not work. Please review your details and try again. You can do so using our config script.</error>');

                return 1;
            }

            if ($this->storage->getCurrentStorageCode() === \Thai\S3\Model\MediaStorage\File\Storage::STORAGE_MEDIA_S3) {
                $output->writeln('<error>You are already using S3 as your media file storage backend!</error>');

                return 1;
            }

            $output->writeln('Updating configuration to use S3.');

            $config = $this->configFactory->create();
            $config->setDataByPath('system/media_storage_configuration/media_storage',
                \Thai\S3\Model\MediaStorage\File\Storage::STORAGE_MEDIA_S3
            );
            $config->save();
            $output->writeln(sprintf('<info>Magento now uses S3 for its file backend storage.</info>'));

            return 0;
        });
    }

    /**
     * @return array
     */
    public function validate()
    {
        $errors = [];

        if (null === $this->helper->getAccessKey()) {
            $errors[] = 'You have not provided an AWS access key ID. You can do so using our config script.';
        }

        if (null === $this->helper->getSecretKey()) {
            $errors[] = 'You have not provided an AWS secret access key. You can do so using our config script.';
        }

        if (null === $this->helper->getBucket()) {
            $errors[] = 'You have not provided an S3 bucket. You can do so using our config script.';
        }

        if (null === $this->helper->getRegion()) {
            $errors[] = 'You have not provided an S3 region. You can do so using our config script.';
        }

        return $errors;
    }
}

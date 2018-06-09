<?php
namespace Thai\S3\Console\Command;

use Magento\Config\Model\Config\Factory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StorageDisableCommand extends \Symfony\Component\Console\Command\Command
{
    private $configFactory;

    private $state;

    private $helper;

    private $client;

    private $coreFileStorage;

    private $storageHelper;

    public function __construct(
        \Magento\Framework\App\State $state,
        Factory $configFactory,
        \Magento\MediaStorage\Helper\File\Storage\Database $storageHelper,
        \Magento\MediaStorage\Helper\File\Storage $coreFileStorage,
        \Thai\S3\Helper\Data $helper
    ) {
        $this->state = $state;
        $this->configFactory = $configFactory;
        $this->coreFileStorage = $coreFileStorage;
        $this->helper = $helper;
        $this->storageHelper = $storageHelper;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('s3:storage:disable');
        $this->setDescription('Revert to using the local filesystem as your Magento 2 file storage backend.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->state->setAreaCode('adminhtml');
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            // intentionally left empty
        }

        $output->writeln('Updating configuration to use the local filesystem.');

        $config = $this->configFactory->create();
        $config->setDataByPath('system/media_storage_configuration/media_storage', \Magento\MediaStorage\Model\File\Storage::STORAGE_MEDIA_FILE_SYSTEM);
        $config->save();
        $output->writeln(sprintf('<info>Magento now uses the local filesystem for its file backend storage.</info>'));
    }
}

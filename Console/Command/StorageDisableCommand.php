<?php
namespace Thai\S3\Console\Command;

use Magento\Config\Model\Config\Factory;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Thai\S3\Model\MediaStorage\File\Storage;

/**
 * @inheritdoc
 */
class StorageDisableCommand extends \Symfony\Component\Console\Command\Command
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
     * @param State $state
     * @param Factory $configFactory
     */
    public function __construct(
        State $state,
        Factory $configFactory
    ) {
        $this->state = $state;
        $this->configFactory = $configFactory;

        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('s3:storage:disable');
        $this->setDescription('Revert to using the local filesystem as your Magento 2 file storage backend.');
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        return $this->state->emulateAreaCode(Area::AREA_ADMINHTML, function () use ($output) {
            $output->writeln('Updating configuration to use the local filesystem.');

            $config = $this->configFactory->create();
            $config->setDataByPath(
                'system/media_storage_configuration/media_storage',
                Storage::STORAGE_MEDIA_FILE_SYSTEM
            );
            $config->save();
            $output->writeln(sprintf('<info>Magento now uses the local filesystem for its file backend storage.</info>'));

            return 0;
        });
    }
}

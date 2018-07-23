<?php
namespace Thai\S3\Console\Command;

use Magento\Config\Model\Config\Factory;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @inheritdoc
 */
class CustomEndpointDisableCommand extends Command
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
        $this->setName('s3:custom-endpoint:disable');
        $this->setDescription('Revert to using Amazon S3 as the default endpoint.');
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        return $this->state->emulateAreaCode(Area::AREA_ADMINHTML, function () use ($output) {
            $output->writeln('Updating configuration to use Amazon S3 as the default endpoint.');

            $config = $this->configFactory->create();
            $config->setDataByPath('thai_s3/custom_endpoint/enabled', 0);
            $config->save();
            $output->writeln(sprintf('<info>Magento now uses Amazon S3 as the default endpoint.</info>'));

            return 0;
        });
    }
}

<?php
namespace Thai\S3\Console\Command;

use Magento\Config\Model\Config\Factory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CustomEndpointDisableCommand extends \Symfony\Component\Console\Command\Command
{
    private $configFactory;

    private $state;
    public function __construct(
        \Magento\Framework\App\State $state,
        Factory $configFactory
    ) {
        $this->state = $state;
        $this->configFactory = $configFactory;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('s3:custom-endpoint:disable');
        $this->setDescription('Revert to using Amazon S3 as the default endpoint.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->state->setAreaCode('adminhtml');
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            // intentionally left empty
        }

        $output->writeln('Updating configuration to use Amazon S3 as the default endpoint.');

        $config = $this->configFactory->create();
        $config->setDataByPath('thai_s3/custom_endpoint/enabled', 0);
        $config->save();
        $output->writeln(sprintf('<info>Magento now uses Amazon S3 as the default endpoint.</info>'));
    }
}

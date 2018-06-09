<?php
namespace Thai\S3\Console\Command;

use Magento\Config\Model\Config\Factory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CustomEndpointEnableCommand extends \Symfony\Component\Console\Command\Command
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
        $this->setName('s3:custom-endpoint:enable');
        $this->setDescription('Set a custom S3-compatible URL as the default endpoint.');
        $this->setDefinition($this->getOptionsList());
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->state->setAreaCode('adminhtml');
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            // intentionally left empty
        }

        $config = $this->configFactory->create();

        $targetEndpoint = $this->getTargetConfigSetting('endpoint', $config, $input);
        if (!$targetEndpoint) {
            $output->writeln('<error>Please provide an S3-compatible endpoint.</error>');
            return;
        }

        $targetRegion = $this->getTargetConfigSetting('region', $config, $input);
        if (!$targetRegion) {
            $output->writeln('<error>Please provide a region for the S3-compatible endpoint.</error>');
            return;
        }

        $output->writeln(sprintf('Updating configuration to use %s as the default endpoint.', $targetEndpoint));

        if ($targetEndpoint) {
            $config->setDataByPath('thai_s3/custom_endpoint/endpoint', $targetEndpoint);
            $config->save();
        }
        if ($targetRegion) {
            $config->setDataByPath('thai_s3/custom_endpoint/region', $targetRegion);
            $config->save();
        }
        $config->setDataByPath('thai_s3/custom_endpoint/enabled', 1);
        $config->save();
        $output->writeln(sprintf('<info>Magento now uses %s as the default endpoint.</info>', $targetEndpoint));
    }

    public function getOptionsList()
    {
        return [
            new InputOption('endpoint', null, InputOption::VALUE_OPTIONAL, 'an S3-compatible endpoint, e.g. https://nyc3.digitaloceanspaces.com'),
            new InputOption('region', null, InputOption::VALUE_OPTIONAL, 'a third-party region for the S3-compatible endpoint, e.g. nyc3'),
        ];
    }

    protected function getTargetConfigSetting($name, $config, InputInterface $input)
    {
        if ($input->getOption($name)) {
            return $input->getOption($name);
        }

        if ($config->getConfigDataValue(sprintf('thai_s3/custom_endpoint/%s', strtolower($name)))) {
            return $config->getConfigDataValue(sprintf('thai_s3/custom_endpoint/%s', strtolower($name)));
        }

        return false;
    }
}

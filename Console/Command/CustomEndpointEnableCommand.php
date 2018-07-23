<?php
namespace Thai\S3\Console\Command;

use Magento\Config\Model\Config;
use Magento\Config\Model\Config\Factory;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @inheritdoc
 */
class CustomEndpointEnableCommand extends \Symfony\Component\Console\Command\Command
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

    protected function configure()
    {
        $this->setName('s3:custom-endpoint:enable');
        $this->setDescription('Set a custom S3-compatible URL as the default endpoint.');
        $this->setDefinition($this->getOptionsList());
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        return $this->state->emulateAreaCode(Area::AREA_ADMINHTML, function () use ($input, $output) {
            $config = $this->configFactory->create();

            $targetEndpoint = $this->getTargetConfigSetting('endpoint', $config, $input);
            if (!$targetEndpoint) {
                $output->writeln('<error>Please provide an S3-compatible endpoint.</error>');

                return 1;
            }

            $targetRegion = $this->getTargetConfigSetting('region', $config, $input);
            if (!$targetRegion) {
                $output->writeln('<error>Please provide a region for the S3-compatible endpoint.</error>');

                return 1;
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

            return 0;
        });
    }

    /**
     * @return array
     */
    public function getOptionsList()
    {
        return [
            new InputOption('endpoint', null, InputOption::VALUE_OPTIONAL,
                'an S3-compatible endpoint, e.g. https://nyc3.digitaloceanspaces.com'),
            new InputOption('region', null, InputOption::VALUE_OPTIONAL,
                'a third-party region for the S3-compatible endpoint, e.g. nyc3'),
        ];
    }

    /**
     * @param string $name
     * @param Config $config
     * @param InputInterface $input
     * @return bool|\Magento\Framework\Simplexml\Element|mixed
     */
    protected function getTargetConfigSetting($name, Config $config, InputInterface $input)
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

<?php
namespace Thai\S3\Console\Command;

use Magento\Config\Model\Config\Factory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ConfigSetCommand extends \Symfony\Component\Console\Command\Command
{
    private $configFactory;

    private $state;

    private $helper;

    public function __construct(
        \Magento\Framework\App\State $state,
        Factory $configFactory,
        \Thai\S3\Helper\S3 $helper
    ) {
        $this->state = $state;
        $this->helper = $helper;
        $this->configFactory = $configFactory;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('s3:config:set');
        $this->setDescription('Allows you to set your S3 configuration via the CLI.');
        $this->setDefinition($this->getOptionsList());
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->state->setAreaCode('adminhtml');
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            // intentionally left empty
        }

        if (!$input->getOption('region') && !$input->getOption('bucket') && !$input->getOption('secret-key') && !$input->getOption('access-key-id')) {
            $output->writeln($this->getSynopsis());
            return;
        }

        $errors = $this->validate($input);
        if ($errors) {
            $output->writeln('<error>' . implode('</error>' . PHP_EOL .  '<error>', $errors) . '</error>');
            return;
        }

        $config = $this->configFactory->create();

        foreach ($this->getOptions() as $option => $pathValue) {
            if ( ! empty($input->getOption($option))) {
                $config->setDataByPath('thai_s3/general/'.$pathValue, $input->getOption($option));
                $config->save();
            }
        }

        $output->writeln('<info>You have successfully updated your S3 credentials.</info>');
    }

    public function getOptions()
    {
        return [
            'access-key-id' => 'access_key',
            'secret-key'    => 'secret_key',
            'bucket'        => 'bucket',
            'region'        => 'region',
        ];
    }

    public function getOptionsList()
    {
        return [
            new InputOption('access-key-id', null, InputOption::VALUE_OPTIONAL, 'a valid AWS access key ID'),
            new InputOption('secret-key', null, InputOption::VALUE_OPTIONAL, 'a valid AWS secret access key'),
            new InputOption('bucket', null, InputOption::VALUE_OPTIONAL, 'an S3 bucket name'),
            new InputOption('region', null, InputOption::VALUE_OPTIONAL, 'an S3 region, e.g. us-east-1'),
        ];
    }

    public function validate(InputInterface $input)
    {
        $errors = [];
        if ($input->getOption('region')) {
            if (!$this->helper->isValidRegion($input->getOption('region'))) {
                $errors[] = sprintf('The region "%s" is invalid.', $input->getOption('region'));
            }
        }
        return $errors;
    }
}

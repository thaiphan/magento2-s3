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
        if (!$input->getOption('region') && !$input->getOption('bucket') && !$input->getOption('secret-key') && !$input->getOption('access-key-id')) {
            $output->writeln($this->getSynopsis());
            return;
        }

        $errors = $this->validate($input);
        if ($errors) {
            $output->writeln('<error>' . implode('</error>' . PHP_EOL .  '<error>', $errors) . '</error>');
            return;
        }

        $this->state->setAreaCode('adminhtml');
        $config = $this->configFactory->create();

        if (!empty($input->getOption('access-key-id'))) {
            $config->setDataByPath('thai_s3/general/access_key', $input->getOption('access-key-id'));
            $config->save();
        }

        if (!empty($input->getOption('secret-key'))) {
            $config->setDataByPath('thai_s3/general/secret_key', $input->getOption('secret-key'));
            $config->save();
        }

        if (!empty($input->getOption('bucket'))) {
            $config->setDataByPath('thai_s3/general/bucket', $input->getOption('bucket'));
            $config->save();
        }

        if (!empty($input->getOption('region'))) {
            $config->setDataByPath('thai_s3/general/region', $input->getOption('region'));
            $config->save();
        }

        $output->writeln('<info>You have successfully updated your S3 credentials.</info>');
    }

    public function getOptionsList()
    {
        return [
            new InputOption('access-key-id', null, InputOption::VALUE_OPTIONAL, 'a valid AWS access key ID'),
            new InputOption('secret-key', null, InputOption::VALUE_OPTIONAL, 'a valid AWS secret access key'),
            new InputOption('bucket', null, InputOption::VALUE_OPTIONAL, 'an S3 bucket name'),
            new InputOption('region', null, InputOption::VALUE_OPTIONAL, 'an S3 region, e.g. us-east-1')
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

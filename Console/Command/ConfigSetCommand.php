<?php
namespace Thai\S3\Console\Command;

use Magento\Config\Model\Config\Factory;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Thai\S3\Helper\S3 as S3Helper;

/**
 * @inheritdoc
 */
class ConfigSetCommand extends Command
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
     * @var S3Helper
     */
    private $helper;

    /**
     * @param State $state
     * @param Factory $configFactory
     * @param S3Helper $helper
     */
    public function __construct(
        State $state,
        Factory $configFactory,
        S3Helper $helper
    ) {
        $this->state = $state;
        $this->helper = $helper;
        $this->configFactory = $configFactory;

        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('s3:config:set');
        $this->setDescription('Allows you to set your S3 configuration via the CLI.');
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
            if (!$input->getOption('region') && !$input->getOption('bucket') && !$input->getOption('secret-key') && !$input->getOption('access-key-id')) {
                $output->writeln($this->getSynopsis());

                return 1;
            }

            $errors = $this->validate($input);
            if ($errors) {
                $output->writeln('<error>' . implode('</error>' . PHP_EOL . '<error>', $errors) . '</error>');

                return 1;
            }

            $config = $this->configFactory->create();

            foreach ($this->getOptions() as $option => $pathValue) {
                if (!empty($input->getOption($option))) {
                    $config->setDataByPath('thai_s3/general/' . $pathValue, $input->getOption($option));
                    $config->save();
                }
            }

            $output->writeln('<info>You have successfully updated your S3 credentials.</info>');

            return 0;
        });
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return [
            'access-key-id' => 'access_key',
            'secret-key' => 'secret_key',
            'bucket' => 'bucket',
            'region' => 'region',
        ];
    }

    /**
     * @return array
     */
    public function getOptionsList()
    {
        return [
            new InputOption('access-key-id', null, InputOption::VALUE_OPTIONAL, 'a valid AWS access key ID'),
            new InputOption('secret-key', null, InputOption::VALUE_OPTIONAL, 'a valid AWS secret access key'),
            new InputOption('bucket', null, InputOption::VALUE_OPTIONAL, 'an S3 bucket name'),
            new InputOption('region', null, InputOption::VALUE_OPTIONAL, 'an S3 region, e.g. us-east-1'),
        ];
    }

    /**
     * @param InputInterface $input
     * @return array
     */
    public function validate(InputInterface $input)
    {
        $errors = [];

        if ($input->getOption('region') && !$this->helper->isValidRegion($input->getOption('region'))) {
            $errors[] = sprintf('The region "%s" is invalid.', $input->getOption('region'));
        }

        return $errors;
    }
}

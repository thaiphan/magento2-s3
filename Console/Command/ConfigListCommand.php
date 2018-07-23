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
class ConfigListCommand extends Command
{
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
        $this->setName('s3:config:list');
        $this->setDescription('Lists whatever credentials for S3 you have provided for Magento.');
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        return $this->state->emulateAreaCode(Area::AREA_ADMINHTML, function () use ($output) {
            $config = $this->configFactory->create();

            $output->writeln('Here are your AWS credentials.');
            $output->writeln('');
            $output->writeln(sprintf(
                'Access Key ID:     %s',
                $config->getConfigDataValue('thai_s3/general/access_key')
            ));
            $output->writeln(sprintf(
                'Secret Access Key: %s',
                $config->getConfigDataValue('thai_s3/general/secret_key')
            ));
            $output->writeln(sprintf(
                'Bucket:            %s',
                $config->getConfigDataValue('thai_s3/general/bucket')
            ));
            $output->writeln(sprintf(
                'Region:            %s',
                $config->getConfigDataValue('thai_s3/general/region')
            ));

            return 0;
        });
    }
}

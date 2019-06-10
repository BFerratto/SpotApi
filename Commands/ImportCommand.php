<?php
namespace SpotApi\Commands;

use Shopware\Commands\ShopwareCommand;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportCommand extends ShopwareCommand
{
    protected static $defaultName = 'spotapi:import';
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('spotapi:import')
            ->setDescription('Import data from spotify API.')
            ->addArgument(
                'apiKey',
                InputArgument::REQUIRED,
                'The api key.'
            )
            ->setHelp(<<<EOF
The <info>%command.name%</info> imports data from spotify API.
EOF
            )
        ;
    }
    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $apiKey = $input->getArgument('apiKey');
        $output->writeln('<info>'.sprintf("Got apiKey: %s.", $apiKey).'</info>');
    }
}
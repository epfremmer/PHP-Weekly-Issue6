<?php
/**
 * ResetCommand.php
 *
 * @package Command
 */
namespace Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;

/**
 * Class ResetCommand
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
class ResetCommand extends Command
{

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('game:reset')
             ->setDescription('Reset Hacking Mini Game')
        ;
    }

    /**
     * {@inheritdoc}
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = GameCommand::getLockFile();

        $output->writeLn('<info>Removing game lock file...</info>');

        if (!file_exists($file)) {
            $output->writeln('<error>No lock file found!!!</error>');
            die(1);
        }

        unlink($file);

        $output->writeLn('<info>Lock file removed.</info>');
    }

}
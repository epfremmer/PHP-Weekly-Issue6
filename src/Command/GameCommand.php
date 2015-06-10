<?php
/**
 * GameCommand.php
 *
 * @package Command
 */
namespace Command;

use Game\PasswordGame;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * Class GameCommand
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
class GameCommand extends Command
{

    // failed lock file
    const FAILED_LOCK_FILE = 'failed.lock';

    /**
     * Cli Input
     * @var InputInterface
     */
    protected $input;

    /**
     * Cli Output
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var PasswordGame
     */
    protected $game;

    /**
     * Constructor
     *
     * @param array $passwords
     * @param string $password
     */
    public function __construct(array $passwords, $password)
    {
        $this->game = new PasswordGame($passwords, $password);

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('game:password')
             ->setDescription('Computer Hacking Mini Game')
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
        $this->input  = $input;
        $this->output = $output;

        if (file_exists($this->getLockFile())) {
            $this->printLocked();
            die(1);
        }

        $this->printDescription();
        $this->printQuestion();
    }

    /**
     * Output the command description to provide usage
     * information at the beginning of the game
     *
     * @return self
     */
    protected function printDescription()
    {
        $this->output->writeln('<info>' . $this->getDescription() . '</info>');

        return $this;
    }

    /**
     * Print password choices
     *
     * @return self
     */
    protected function printPasswords()
    {
        $this->output->writeln('');

        foreach ($this->game->getPasswords() as $index => $password) {
            $this->output->writeln(sprintf('<comment>%s)</comment> %s', $index + 1, $password));
        }

        $this->output->writeln('');

        return $this;
    }

    /**
     * Handle the user's password guess question
     *
     * @return void
     */
    protected function printQuestion()
    {
        $dialog = $this->getHelper('question');

        $question = new Question(sprintf(
            '<question>Please choose a password</question> (attempts remaining: %s): ',
            PasswordGame::MAX_ATTEMPTS - $this->game->getAttempts()
        ));

        $this->printPasswords();

        $guess = $dialog->ask($this->input, $this->output, $question);

        $this->checkAnswer($guess);
    }

    /**
     * Print failure text when you loose the game
     *
     * @return void
     */
    protected function printFailure()
    {
        $this->output->writeln('<error>Too many failed attempts!!!</error>');
        $this->output->writeln('<error>The computer is now locked and the door you needed to open to complete the game closed for all time.</error>');
        $this->output->writeln('');
        $this->output->writeln('<info>Looks like you\'re gonna have to reload your saved game form like forever ago...</info>');
        $this->output->writeln('<info>Next time might I recommend restarting the game before this happens so you don\'t have to reload your game every damn time</info>');
    }

    /**
     * Print terminal locked message
     *
     * This occurs when you attempt to hack a terminal that you have
     * already locked due to too many failed password attempts
     *
     * @reutrn void
     */
    protected function printLocked()
    {
        $this->output->writeLn('<error>Terminal Already Locked!!!</error>');

        $this->printFailure();
    }

    /**
     * Validate the guess
     *
     * @param int $index
     * @return void
     */
    protected function checkAnswer($index)
    {
        if (!$guess = $this->game->getGuess($index)) {
            $this->output->writeln(sprintf(
                'Invalid password selected. Please choose a password choice 1-%s from the list',
                count($this->game->getPasswords())
            ));

            $this->printQuestion();
            return;
        }

        if ($guess === $this->game->getPassword()) {
            $this->handleCorrectGuess();
        } else {
            $this->handleIncorrectGuess($guess);
        }
    }

    /**
     * Print win output value
     *
     * @return void
     */
    protected function handleCorrectGuess()
    {
        $this->output->writeln('You winn!!!');
    }

    /**
     * Increment password attempts and print
     * guess output on an incorrect guess.
     *
     * @param string $guess
     */
    protected function handleIncorrectGuess($guess)
    {
        $this->game->incrementAttempts();

        $this->output->writeln(sprintf(
            '%s/5 correct',
            $this->game->getCorrectCount($guess)
        ));

        if ($this->game->getAttempts() >= PasswordGame::MAX_ATTEMPTS) {
            $this->printFailure();
            $this->writeLockFile();
            die(1);
        }

        $this->printQuestion();
        $this->beep();
    }

    /**
     * Trigger console beep
     *
     * @return void
     */
    protected function beep()
    {
        $this->output->write("\x07");
    }

    /**
     * Return game lock file path
     *
     * @return string
     */
    public static function getLockFile()
    {
        return sys_get_temp_dir() . DIRECTORY_SEPARATOR . self::FAILED_LOCK_FILE;
    }

    /**
     * Write failed attempt lock file
     *
     * This is used to prevent the user from trying to hack the
     * computer again.
     *
     * Once you have failed the attempt once you can NEVER try again.
     *
     * @return void
     */
    protected function writeLockFile()
    {
        $file = $this->getLockFile();

        file_put_contents($file, 'true');
    }

}
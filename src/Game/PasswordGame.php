<?php
/**
 * PasswordGame.php
 *
 * @package Game
 */
namespace Game;

/**
 * Class PasswordGame
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
class PasswordGame
{

    // max password attempts
    const MAX_ATTEMPTS = 4;

    /**
     * Game password choices
     * @var array|string[]
     */
    protected $passwords;

    /**
     * Correct Password
     * @var string
     */
    protected $password;

    /**
     * Password Attempts
     * @var int
     */
    protected $attempts = 0;

    /**
     * @param array $passwords
     * @param string $password
     */
    public function __construct(array $passwords, $password)
    {
        $this->passwords = $passwords;
        $this->password  = $password;
    }

    /**
     * Return password choice or null if invalid index provided
     *
     * @param int $index
     * @return null|string
     */
    public function getGuess($index)
    {
        if (!array_key_exists($index-1, $this->passwords)) {
            return null;
        }

        return $this->passwords[$index-1];
    }

    /**
     * Return the count of correct letters for
     * the password guess
     *
     * @param string $guess
     * @return int
     */
    public function getCorrectCount($guess)
    {
        $guess   = str_split($guess);
        $correct = 0;

        foreach (str_split($this->password) as $i => $letter) {
            if ($letter === $guess[$i]) $correct++;
        }

        return $correct;
    }

    /**
     * Increment the attempt counter
     *
     * @return self
     */
    public function incrementAttempts()
    {
        $this->attempts++;

        return $this;
    }

    /**
     * @return array|\string[]
     */
    public function getPasswords()
    {
        return $this->passwords;
    }

    /**
     * @param array|\string[] $passwords
     * @return PasswordGame
     */
    public function setPasswords($passwords)
    {
        $this->passwords = $passwords;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return PasswordGame
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return int
     */
    public function getAttempts()
    {
        return $this->attempts;
    }
}
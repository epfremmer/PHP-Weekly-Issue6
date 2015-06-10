<?php
/**
 * Created by IntelliJ IDEA.
 * User: epfremme
 * Date: 6/10/15
 * Time: 11:10 AM
 */

require __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Console\Application;
use Command\GameCommand;
use Command\ResetCommand;

$application = new Application('Game', '1.0');

$passwords = [
    "LOWER",
    "CREED",
    "JAMES",
    "CAGES",
    "CARES",
    "OFFER",
    "CAVES",
    "TIRED",
];

$application->add(new GameCommand($passwords, 'LOWER'));
$application->add(new ResetCommand());
$application->run();
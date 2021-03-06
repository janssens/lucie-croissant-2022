<?php
// php_src/Command/NationBuilderEventCommand.php
//namespace App\Command;

include_once(__DIR__.'/../../vendor/autoload.php');

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NationBuilderEventCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:nation-builder:event';

    protected function configure(): void
    {
        $this
            // If you don't like using the $defaultDescription static property,
            // you can also define the short description using this method:
            // ->setDescription('...')

            // the command help shown when running the command with the "--help" option
            ->setHelp('This command allows you to retrieve events from nationbuilder')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // ... put here the code to create the user

        // this method must return an integer number with the "exit status code"
        // of the command. You can also use these constants to make code more readable

        // return this if there was no problem running the command
        // (it's equivalent to returning int(0))
        return Command::SUCCESS;

        // or return this if some error happened during the execution
        // (it's equivalent to returning int(1))
        // return Command::FAILURE;

        // or return this to indicate incorrect command usage; e.g. invalid options
        // or missing arguments (it's equivalent to returning int(2))
        // return Command::INVALID
    }
}
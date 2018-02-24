<?php

namespace Jeeves\Commands;

use Jeeves\Jeeves;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class GenerateCommand extends Command
{
    /**
     * @var Jeeves
     */
    private $jeeves;

    public function __construct(Jeeves $jeeves, string $name = 'generate')
    {
        parent::__construct($name);

        $this->jeeves = $jeeves;
    }

    protected function configure()
    {
        $this->addOption('force', InputOption::VALUE_NONE, null, 'Force Jenkinsfile generation');
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        if ($this->jeeves->jenkinsFileExists() && !$input->getOption('force')) {
            $question = new ConfirmationQuestion(
                'This will override the current Jenkinsfile, are you sure? [y/N] ',
                false
            );

            if (!$helper->ask($input, $output, $question)) {
                $output->writeln('Exiting');
                return;
            }
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
    }
}

<?php

namespace Jeeves\Commands;

use Jeeves\Jeeves;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

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
        $this->addOption('build-dir', InputOption::VALUE_OPTIONAL, null, 'Build directory to use');
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        if ($this->jeeves->jenkinsFileExists() && !$input->getOption('force')) {
            if (!$helper->ask($input, $output, new ConfirmationQuestion(
                'This will override the current Jenkinsfile, are you sure? [y/N] ',
                false
            ))) {
                $output->writeln('Exiting');
                return;
            }
        }

        if (!$input->getOption('build-dir')) {
            $buildDirectory = $helper->ask($input, $output, new Question(
                'What build directory do want to use? [build] ',
                'build'
            ));

            $this->jeeves->setBuildDirectory($buildDirectory);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
    }
}

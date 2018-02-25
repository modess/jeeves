<?php

namespace Jeeves\Commands;

use Jeeves\Jeeves;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
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
        $this->addOption('force', null, InputOption::VALUE_NONE, 'Force Jenkinsfile generation');
        $this->addOption('build-dir', null, InputOption::VALUE_OPTIONAL, 'Build directory to use');
        $this->addOption('source-dir', null, InputOption::VALUE_OPTIONAL, 'Source directory');
        $this->addOption('slack-channel', null, InputOption::VALUE_OPTIONAL, 'Slack channel');
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

        $this->setValueFromInput(
            $input,
            $output,
            $helper,
            'build-dir',
            'What build directory do want to use?',
            'build',
            function ($value) {
                $this->jeeves->setBuildDirectory($value);
            }
        );

        $this->setValueFromInput(
            $input,
            $output,
            $helper,
            'source-dir',
            'Where is the source located in your application?',
            'src',
            function ($value) {
                $this->jeeves->setSourceDirectory($value);
            }
        );

        $this->setValueFromInput(
            $input,
            $output,
            $helper,
            'slack-channel',
            'Would you like to use Slack notifications? Enter channel name, or empty for disabling',
            '',
            function ($value) {
                $this->jeeves->setSlackChannel($value);
            }
        );
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param QuestionHelper  $questionHelper
     * @param string          $option
     * @param string          $question
     * @param string          $default
     * @param \Closure        $closure
     */
    public function setValueFromInput(
        InputInterface $input,
        OutputInterface $output,
        QuestionHelper $questionHelper,
        string $option,
        string $question,
        string $default,
        \Closure $closure
    ) {
        $option = $input->getOption($option);

        if (!$option) {
            $option = $questionHelper->ask($input, $output, new Question(
                $question . ' [' . $default . '] ',
                $default
            ));
        }

        $closure($option);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Generating Jenkinsfile');
        $this->jeeves->generateJenkinsFile();

        if ($this->jeeves->phpcsXmlExists()) {
            $output->writeln('The file phpcs.xml exists, skipping');
        } else {
            $output->writeln('Copying phpcs.xml');
            $this->jeeves->copyPhpcsXml();
        }

        if ($this->jeeves->phpmdXmlExists()) {
            $output->writeln('The file phpmd.xml exists, skipping');
        } else {
            $output->writeln('Copying phpmd.xml');
            $this->jeeves->copyPhpmdXml();
        }
    }
}

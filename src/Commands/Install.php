<?php

namespace RefinedDigital\Mailchimp\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Question\Question;
use Validator;
use Artisan;
use RuntimeException;

class Install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'refinedCMS:install-form-builder-mailchimp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Installs the form builder mailchimp module';

    protected string $key = '';
    protected string $listId = '';
    protected string $endpoint = '';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->askQuestions();
        $this->updateEnvFile();
        $this->publishConfig();
        $this->info('Mailchimp Form Builder has been successfully installed');
    }

    protected function askQuestions()
    {
        $helper = $this->getHelper('question');

        $question = new Question('Api Key?: ', false);
        $question->setValidator(function ($answer) {
            if(strlen($answer) < 1) {
                throw new RuntimeException('Api Key is required');
            }
            return $answer;
        });
        $question->setMaxAttempts(3);
        $this->key = $helper->ask($this->input, $this->output, $question);

        $question = new Question('List ID?: ', false);
        $question->setValidator(function ($answer) {
            if(strlen($answer) < 1) {
                throw new RuntimeException('List ID is required');
            }
            return $answer;
        });
        $question->setMaxAttempts(3);
        $this->listId = $helper->ask($this->input, $this->output, $question);

        $question = new Question('Endpoint URL?: ', false);
        $question->setValidator(function ($answer) {
            if(strlen($answer) < 1) {
                throw new RuntimeException('Endpoint URL is required');
            }
            return $answer;
        });
        $question->setMaxAttempts(3);
        $this->endpoint = $helper->ask($this->input, $this->output, $question);
    }

    protected function updateEnvFile()
    {
        $env = app()->environmentFilePath();
        $file = file_get_contents($env);

        // add in the cache settings
        $file .= "\n\nNEWSLETTER_API_KEY=".$this->key."
NEWSLETTER_ENDPOINT=".$this->endpoint."
NEWSLETTER_LIST_ID=".$this->listId;
        file_put_contents($env, $file);
    }

    protected function publishConfig()
    {
        \Artisan::call('vendor:publish', [
            '--tag' => 'newsletter-config'
        ]);

        sleep(2);

        $filePath = config_path('newsletter.php');
        $config = file_get_contents($filePath);
        $config = str_replace('MailcoachDriver::class', 'MailchimpDriver::class', $config);
        file_put_contents($filePath, $config);
    }
}

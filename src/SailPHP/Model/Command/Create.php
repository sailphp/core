<?php

namespace SailPHP\Model\Command;

use SailPHP\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Create extends Command
{
    protected $name = 'model:create';

    protected $description = "Create a new model file.";

    protected $help = "This will create a new model class.";

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $this->formatClassname($input->getArgument('name'));

        $created = $this->putModel($name);
        if($created) {
            $output->writeln('<info>Successfully created model.</info>');
        } else {
            $output->writeln('<error>Something went wrong.</error>');
        }

        $output->writeln(["", ""]);
    }

    protected function putModel($name)
    {   
        $file = "<?php \n
namespace App\Models;
        
use SailPHP\Model\Model;

class ".$name." extends Model 
{
    protected \$table = '".strtolower($name)."';
}
";
        $path = './app/Models/'.$name.'.php';

        if(file_exists($path)) {
            return false;
        }

        file_put_contents($path, $file);

        return true;

    }

    protected  function configure()
    {
        parent::configure();

        $this->addArgument('name', InputArgument::REQUIRED, 'The name for the model.');
    }

    private function formatClassname($name)
    {
        return ucfirst($name);
    }
}
<?php

namespace SailPHP\Controller\Command;

use SailPHP\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Create extends Command
{
    protected $name = 'controller:create';

    protected $description = "Create a new controller file.";

    protected $help = "This will create a new controller class.";

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $this->formatClassname($input->getArgument('name'));

        if(preg_match('/Controller$/', $name)) {
            $output->writeln('<error>Please do not append Controller.</error>');
            return;
        }

        $created = $this->putController($name);
        if($created) {
            $output->writeln('<info>Successfully created controller.</info>');
        } else {
            $output->writeln('<error>Controller already exists.</error>');
        }

        $output->writeln(["", ""]);
    }

    protected function putController($name)
    {   
        
        $name = $name.'Controller';

        $file = "<?php

namespace App\Controllers;

use SailPHP\Controller\Controller;
        
class ".$name." extends Controller
{

}
";
        $path = './app/Controllers/'.$name.'.php';

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
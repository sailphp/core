<?php

namespace SailPHP\Model\Command;

use SailPHP\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Create
 * @package SailPHP\Model\Command
 */
class Create extends Command
{
    /**
     * @var string
     */
    protected $name = 'model:create';

    /**
     * @var string
     */
    protected $description = "Create a new model file.";

    /**
     * @var string
     */
    protected $help = "This will create a new model class.";

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     */
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

    /**
     * @param $name
     * @return bool
     */
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

    /**
     *
     */
    protected  function configure()
    {
        parent::configure();

        $this->addArgument('name', InputArgument::REQUIRED, 'The name for the model.');
    }

    /**
     * @param $name
     * @return string
     */
    private function formatClassname($name)
    {
        return ucfirst($name);
    }
}

<?php
namespace ADR\TestBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class ExecCommand extends  ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('redis:test')
            ->setDescription('Test Massive insertion')
            ->addArgument('total')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $total = $input->getArgument('total');

        $test = $this->getContainer()->get('snc_redis.default_client');

        $initialTime = $this->microtime_float();
        for ($i=0; $i < $total; $i++) {
            //$pointer = $this-> microtime_float();
            $test->rpush('test',$i.'-bar');
            $value = $test->lindex('test',$i);
            //$endTime = $this-> microtime_float();
            /*if ($i%100 == 0) {
                echo '<br>It:'.$i.'--'.($endTime-$pointer);
            }*/
        }
        $ensdTime = $this->microtime_float();

        $text = "<br>total time:".($ensdTime-$initialTime);


        $output->writeln($text);
    }

    protected function microtime_float()
    {
        list($useg, $seg) = explode(" ", microtime());
        return ((float)$useg + (float)$seg);
    }
}
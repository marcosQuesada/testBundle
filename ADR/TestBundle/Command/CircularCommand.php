<?php
namespace ADR\TestBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class CircularCommand extends  ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('redis:circular')
            ->setDescription('Test circularPool')
            ->addArgument('action')
            ->addArgument(
                'total',
                InputArgument::OPTIONAL,
                'Queue Items'
        )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $expiracy = 60;
        $total = $input->getArgument('total');
        if( $action = $input->getArgument('action') == 'init') {
            $this->initPool($total);
        }else{
            $redis = $this->getContainer()->get('snc_redis.default_client');

            $initialTime = $this->microtime_float();
            $totalExecutions = 0;
            $initExec = time();
            for ($i=0; $i < $total; $i++) {
                $timestamp = $redis->rpoplpush('circularQueue.timestamp', 'circularQueue.timestamp');
                $redis->rpoplpush('circularQueue.payload', 'circularQueue.payload');

                if (($timestamp + $expiracy) < time()) {
                    //echo "\n caducado tm:".$timestamp."-time:".time()."\n";
                    $redis->lpop('circularQueue.timestamp');
                    $payload = $redis->lpop('circularQueue.payload');

                    var_dump(unserialize($payload));

                }
                $totalItems = $redis->llen('circularQueue.timestamp');
                if ($totalItems === 0)
                    break;


                if (time() > ($initExec + 1)) {
                    echo "totalExecutios per second:".$totalExecutions."\n";
                    echo $redis->llen('circularQueue.timestamp')."\n";
                    $totalExecutions = 0;
                    $initExec = time();
                }
                $totalExecutions++;
                /*if ($i%100 == 0) {
                    echo '<br>It:'.$i.'--'.($endTime-$pointer);
                }*/
            }
            $ensdTime = $this->microtime_float();

            $text = "<br>total time:".($ensdTime-$initialTime);


            $output->writeln($text);
        }
    }

    protected function initPool($total)
    {
        $redis = $this->getContainer()->get('snc_redis.default_client');
        for ($i=0; $i < $total; $i++) {
            //sleep(1);
            echo time()."\n";
            $redis->sAdd('circularQueue:'.$i, time());
            $redis->sAdd('circularQueue:'.$i ,serialize($this->getObject(time())));
            $redis->lPush('circularQueue.timestamp', time());
            $redis->lPush('circularQueue.payload', serialize($this->getObject(time())));
        }
    }

    protected function getObject($userId)
    {
        $obj = new \StdClass();
        $obj->userId = $userId;
        $obj->c = 100;
        $obj->d = 100;
        $obj->xp = 100;

        return $obj;
    }
    protected function microtime_float()
    {
        list($useg, $seg) = explode(" ", microtime());
        return ((float)$useg + (float)$seg);
    }
}
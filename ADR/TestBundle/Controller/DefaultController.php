<?php

namespace ADR\TestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{

    public function indexAction($name)
    {
        $test = $this->container->get('snc_redis.default_client');

        $initialTime = $this->microtime_float();
        for ($i=0; $i < 4000; $i++) {
            //$pointer = $this-> microtime_float();
            $test->rpush('test',$i.'-bar');
            $value = $test->lindex('test',$i);
            //$endTime = $this-> microtime_float();
            /*if ($i%100 == 0) {
                echo '<br>It:'.$i.'--'.($endTime-$pointer);
            }*/
        }
        $ensdTime = $this->microtime_float();

        echo "<br>total time:".($ensdTime-$initialTime);

        //echo $test->get('foo');

        return $this->render('ADRTestBundle:Default:index.html.twig', array('name' => $name));
    }

    function microtime_float()
    {
        list($useg, $seg) = explode(" ", microtime());
        return ((float)$useg + (float)$seg);
    }
}

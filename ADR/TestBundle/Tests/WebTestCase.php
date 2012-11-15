<?php

namespace ADR\TestBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Console\Command\Command;


abstract class WebTestCase extends BaseWebTestCase
{
    protected $environment = 'test';
    protected $container;
    protected $kernelDir;
    protected $newKernel;
    protected $application;
    protected $maxMemory = 5242880;

    public function setUp()
    {
        $options = array(
            'environment' => $this->environment
        );
        $this->newKernel = $this->createKernel($options);
        $this->newKernel->boot();
    }

    public function tearDown()
    {
        //shutdown kernel if any
        if(null !== $this->newKernel) {
            $this->newKernel->shutdown();
        }
        parent::tearDown();
    }

    public function getApplication()
    {
        if(is_null($this->application)) {
            $this->application = new Application($this->newKernel);
        }

        return $this->application;
    }

    protected function runCommand($name, array $params = array())
    {
        array_unshift($params, $name);

        $application = new Application($this->newKernel);
        $application->setAutoExit(false);

        $input = new ArrayInput($params);
        $input->setInteractive(false);

        $fp = fopen('php://temp/maxmemory:'.$this->maxMemory, 'r+');
        $output = new StreamOutput($fp);

        $application->run($input, $output);

        rewind($fp);
        return stream_get_contents($fp);
    }


    public function getContainer()
    {
        if(null === $this->container) {
            $this->container = $this->newKernel->getContainer();
        }

        return $this->container;
    }
}
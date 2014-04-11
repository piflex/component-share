<?php

namespace BWC\Share\Symfony\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;


abstract class DoctrineFixtureRunCommand extends ContainerAwareCommand
{
    protected function configure() {
        $this
            ->setName('bwc:doctrine:fixture:run')
            ->setDescription('Runs specified fixture')
            ->addArgument('fixtures', InputArgument::IS_ARRAY)
        ;
    }

    /**
     * @param InputInterface $input
     * @return string[]
     */
    private function getFixtures(InputInterface $input) {
        return $input->getArgument('fixtures');
    }


    protected function execute(InputInterface $input, OutputInterface $output) {
        $fixtures = $this->getFixtures($input);

        $loader = new Loader();
        foreach ($fixtures as $fixture) {
            $loader->addFixture(new $fixture());
        }

        $purger = new ORMPurger();
        /** @var $em \Doctrine\ORM\EntityManager */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $executor = new ORMExecutor($em, $purger);
        $executor->execute($loader->getFixtures());
    }


}
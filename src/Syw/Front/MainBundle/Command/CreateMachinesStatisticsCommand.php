<?php

namespace Syw\Front\MainBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use FOS\UserBundle\Model\User;
use Syw\Front\MainBundle\Entity\StatsMachines;

/**
 *
 */
class CreateMachinesStatisticsCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('syw:statistics:machines')
            ->setDescription('Creates the statistics for machines')
            ->setDefinition(array())
            ->setHelp(<<<EOT
The <info>syw:statistics:machines</info> command Creates the statistics for machines.

EOT
            );
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();

        $monthrange = $this->monthRange(new \DateTime());
        $dql   = "SELECT COUNT(a) AS num FROM SywFrontMainBundle:Machines a WHERE a.createdAt >= '".$monthrange[0]->format('Y-m-d H:i:s')."'";
        $num = $em->createQuery($dql)->getSingleScalarResult();

        $stat = $em->getRepository('SywFrontMainBundle:StatsMachines')->findOneBy(array("month" => $monthrange[0]));
        if (false === isset($stat) || false === is_object($stat) || null === $stat) {
            $stat = new StatsMachines();
            $stat->setMonth($monthrange[0]);
        }
        $stat->setNum($num);
        $em->persist($stat);
        $em->flush();
    }

    protected function monthRange($date)
    {
        //First day of month
        $date->modify('first day of this month');
        $firstday = $date->format('Y-m-d 00:00:00');
        //Last day of month
        $date->modify('last day of this month');
        $lastday = $date->format('Y-m-d 23:59:59');

        return array(new \DateTime($firstday), new \DateTime($lastday));
    }

    /**
     * @see Command
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {

    }
}

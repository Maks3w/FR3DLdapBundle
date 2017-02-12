<?php
/**
 * Created by Wassa. http://www.wassa.io
 * Date: 13/02/2017
 * Time: 01:14
 */

namespace FR3D\LdapBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class LdapSyncCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('fr3d-ldap:sync')
            ->setDescription('Sync user accounts from LDAP.')
            ->setHelp("This command allows you to sync user accounts from LDAP.");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getContainer()->get('fr3d_ldap.ldap_manager.default')->findAllUsers();
    }

}
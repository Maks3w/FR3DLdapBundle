<?php

namespace FR3D\LdapBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This class contains the configuration information for the bundle
 *
 * This information is solely responsible for how the different configuration
 * sections are normalized, and merged.
 *
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('fr3d_ldap');

        $rootNode
            ->children()
              ->arrayNode('client')
                ->children()
                  ->scalarNode('host')->isRequired()->cannotBeEmpty()->end()
                  ->scalarNode('port')->defaultValue(389)->end()
                  ->scalarNode('version')->defaultValue(3)->end()
                  ->scalarNode('useSsl')->defaultFalse()->end()
                  ->scalarNode('useStartTls')->defaultFalse()->end()
                  ->scalarNode('username')->end()
                  ->scalarNode('password')->end()
                  ->scalarNode('optReferrals')->end()
                ->end()
              ->end()
              ->arrayNode('user')
                ->children()
                  ->scalarNode('baseDn')->isRequired()->cannotBeEmpty()->end()
                  ->scalarNode('filter')->defaultValue('')->end()
                  ->arrayNode('attributes')
                    ->defaultValue(array(array('ldap_attr' => 'uid', 'user_method' => 'setUsername')))
                    ->prototype('array')
                      ->children()
                        ->scalarNode('ldap_attr')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('user_method')->isRequired()->cannotBeEmpty()->end()
                      ->end()
                    ->end()
                  ->end()
                ->end()
              ->end()
            ->end()
            ->validate()
              ->ifTrue(function($v){return $v['client']['useSsl'] && $v['client']['useStartTls'];})
              ->thenInvalid('The useSsl and useStartTls options are mutually exclusive.')
            ->end();

        $this->addServiceSection($rootNode);

        return $treeBuilder;
    }

    private function addServiceSection(ArrayNodeDefinition $node)
    {
        $node
            ->addDefaultsIfNotSet()
            ->children()
              ->arrayNode('service')
                ->addDefaultsIfNotSet()
                  ->children()
                    ->scalarNode('user_manager')->defaultValue('fos_user.user_manager')->end()
                    ->scalarNode('ldap_manager')->defaultValue('fr3d_ldap.ldap_manager.default')->end()
                  ->end()
                ->end()
              ->end()
            ->end();
    }
}

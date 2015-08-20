<?php

namespace FR3D\LdapBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This class contains the configuration information for the bundle.
 *
 * This information is solely responsible for how the different configuration
 * sections are normalized, and merged.
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('fr3d_ldap');

        $rootNode
            ->children()
                ->arrayNode('driver')
                    ->children()
                        ->scalarNode('host')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('port')->defaultValue(389)->end()
                        ->scalarNode('useStartTls')->defaultFalse()->end()
                        ->scalarNode('useSsl')->defaultFalse()->end()
                        ->scalarNode('username')->end()
                        ->scalarNode('password')->end()
                        ->scalarNode('bindRequiresDn')->defaultFalse()->end()
                        ->scalarNode('baseDn')->end()
                        ->scalarNode('accountCanonicalForm')->end()
                        ->scalarNode('accountDomainName')->end()
                        ->scalarNode('accountDomainNameShort')->end()
                        ->scalarNode('accountFilterFormat')->end()
                        ->scalarNode('allowEmptyPassword')->end()
                        ->scalarNode('optReferrals')->end()
                        ->scalarNode('tryUsernameSplit')->end()
                        ->scalarNode('networkTimeout')->end()
                    ->end()
                ->end()
                ->arrayNode('user')
                    ->children()
                        ->scalarNode('baseDn')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('filter')->defaultValue('')->end()
                        ->scalarNode('usernameAttribute')->defaultValue('uid')->end()
                        ->arrayNode('attributes')
                            ->defaultValue([
                                [
                                    'ldap_attr' => 'uid',
                                    'user_method' => 'setUsername',
                                ],
                            ])
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
                ->ifTrue(function ($v) {
                    return $v['driver']['useSsl'] && $v['driver']['useStartTls'];
                })
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
                            ->scalarNode('user_hydrator')->defaultValue('fr3d_ldap.user_hydrator.default')->end()
                            ->scalarNode('ldap_manager')->defaultValue('fr3d_ldap.ldap_manager.default')->end()
                            ->scalarNode('ldap_driver')->defaultValue('fr3d_ldap.ldap_driver.zend')->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }
}

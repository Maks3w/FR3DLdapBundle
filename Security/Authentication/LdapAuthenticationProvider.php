<?php

namespace FR3D\LdapBundle\Security\Authentication;

use FR3D\LdapBundle\Ldap\LdapManagerInterface;
use Symfony\Component\Security\Core\Authentication\Provider\UserAuthenticationProvider;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AuthenticationServiceException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class LdapAuthenticationProvider extends UserAuthenticationProvider
{
    /**
     * @var UserProviderInterface
     */
    private $userProvider;

    /**
     * @var LdapManagerInterface
     */
    private $ldapManager;

    /**
     * Constructor.
     *
     * @param UserCheckerInterface  $userChecker                An UserCheckerInterface interface
     * @param string                $providerKey                A provider key
     * @param UserProviderInterface $userProvider               An UserProviderInterface interface
     * @param LdapManagerInterface  $ldapManager                An LdapProviderInterface interface
     * @param bool                  $hideUserNotFoundExceptions Whether to hide user not found exception or not
     */
    public function __construct(UserCheckerInterface $userChecker, $providerKey, UserProviderInterface $userProvider, LdapManagerInterface $ldapManager, $hideUserNotFoundExceptions = true)
    {
        parent::__construct($userChecker, $providerKey, $hideUserNotFoundExceptions);

        $this->userProvider = $userProvider;
        $this->ldapManager = $ldapManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function retrieveUser($username, UsernamePasswordToken $token)
    {
        $user = $token->getUser();
        if ($user instanceof UserInterface) {
            return $user;
        }

        try {
            $user = $this->userProvider->loadUserByUsername($username);

            return $user;
        } catch (UsernameNotFoundException $notFound) {
            throw $notFound;
        } catch (\Exception $repositoryProblem) {
            $e = new AuthenticationServiceException($repositoryProblem->getMessage(), (int) $repositoryProblem->getCode(), $repositoryProblem);
            $e->setToken($token);

            throw $e;
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function checkAuthentication(UserInterface $user, UsernamePasswordToken $token)
    {
        $currentUser = $token->getUser();
        $presentedPassword = $token->getCredentials();
        if ($currentUser instanceof UserInterface) {
            if ('' === $presentedPassword) {
                throw new BadCredentialsException(
                    'The password in the token is empty. You may forgive turn off `erase_credentials` in your `security.yml`'
                );
            }

            if (!$this->ldapManager->bind($currentUser, $presentedPassword)) {
                throw new BadCredentialsException('The credentials were changed from another session.');
            }
        } else {
            if ('' === $presentedPassword) {
                throw new BadCredentialsException('The presented password cannot be empty.');
            }

            if (!$this->ldapManager->bind($user, $presentedPassword)) {
                throw new BadCredentialsException('The presented password is invalid.');
            }
        }
    }
}

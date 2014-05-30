<?php

namespace FR3D\LdapBundle\Security\Authentication;

use FR3D\LdapBundle\Ldap\LdapManagerInterface;
use FR3D\LdapBundle\Model\UserManagerInterface;
use FR3D\LdapBundle\Security\User\LdapUserProvider;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Security\Core\Authentication\Provider\UserAuthenticationProvider;
use Symfony\Component\Security\Core\User\ChainUserProvider;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\AuthenticationServiceException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

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
     * @var mixed
     */
    private $userManager;

    /**
     * @var bool
     */
    private $updateUser;

    /**
     * Constructor.
     *
     * @param UserCheckerInterface $userChecker An UserCheckerInterface interface
     * @param string $providerKey A provider key
     * @param UserProviderInterface $userProvider An UserProviderInterface interface
     * @param LdapManagerInterface $ldapManager An LdapProviderInterface interface
     * @param $userManager
     * @param Boolean $hideUserNotFoundExceptions Whether to hide user not found exception or not
     * @param Boolean $updateUser Whether to update the user on each log
     */
    public function __construct(
        UserCheckerInterface $userChecker,
        $providerKey,
        UserProviderInterface $userProvider,
        LdapManagerInterface $ldapManager,
        $userManager,
        $hideUserNotFoundExceptions = true,
        $updateUser = false
    ) {
        parent::__construct($userChecker, $providerKey, $hideUserNotFoundExceptions);

        $this->userProvider = $userProvider;
        $this->ldapManager = $ldapManager;
        $this->userManager = $userManager;
        $this->updateUser = $updateUser;
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

            if ($this->updateUser && $this->userProvider instanceof ChainUserProvider) {
                foreach ($this->userProvider->getProviders() as $provider) {
                    if ($provider instanceof LdapUserProvider) {
                        $ldapUser = $provider->loadUserByUsername($username);
                        $user->setRoles($ldapUser->getRoles());
                        $this->userManager->updateUser($user);
                    }
                }
            }

            return $user;
        } catch (UsernameNotFoundException $notFound) {
            throw $notFound;
        } catch (\Exception $repositoryProblem) {
            if (Kernel::MINOR_VERSION <= 1) {
                throw new AuthenticationServiceException($repositoryProblem->getMessage(), $token, (int)$repositoryProblem->getCode(), $repositoryProblem);
            } else {
                $e = new AuthenticationServiceException($repositoryProblem->getMessage(), (int)$repositoryProblem->getCode(), $repositoryProblem);
                $e->setToken($token);
                throw $e;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function checkAuthentication(UserInterface $user, UsernamePasswordToken $token)
    {
        $currentUser = $token->getUser();
        if ($currentUser instanceof UserInterface) {
            if (!$this->ldapManager->bind($currentUser, $currentUser->getPassword())) {
                throw new BadCredentialsException('The credentials were changed from another session.');
            }
        } else {
            if (!$presentedPassword = $token->getCredentials()) {
                throw new BadCredentialsException('The presented password cannot be empty.');
            }

            if (!$this->ldapManager->bind($user, $presentedPassword)) {
                throw new BadCredentialsException('The presented password is invalid.');
            }
        }
    }
}

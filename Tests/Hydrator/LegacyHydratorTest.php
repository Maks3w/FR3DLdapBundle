<?php

namespace FR3D\LdapBundle\Tests\Hydrator;

use FOS\UserBundle\Model\UserManagerInterface;
use FR3D\LdapBundle\Hydrator\LegacyHydrator;
use FR3D\LdapBundle\Tests\TestUser;
use PHPUnit\Framework\MockObject\MockObject;

class LegacyHydratorTest extends AbstractHydratorTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        /** @var UserManagerInterface|MockObject $userManager */
        $userManager = $this->createMock(UserManagerInterface::class);
        $userManager
            ->method('createUser')
            ->willReturn(new TestUser());

        $this->hydrator = new LegacyHydrator($userManager, $this->getDefaultUserConfig());
    }
}

<?php

namespace Dsnet\UserBundle\Security\Authentication\Provider;

use Doctrine\ORM\EntityManager;
use Dsnet\UserBundle\Entity\SystemUser;
use Dsnet\UserBundle\Security\Authentication\Token\OAuthUserToken;
use Dsnet\UserBundle\Services\PanelResourceOwner;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class OAuthProvider implements AuthenticationProviderInterface
{

    private $userProvider;
    private $resourceOwner;
    private $em;

    public function __construct(UserProviderInterface $userProvider, PanelResourceOwner $resourceOwner, EntityManager $em)
    {
        $this->userProvider = $userProvider;
        $this->resourceOwner = $resourceOwner;
        $this->em = $em;
    }

    /**
     * Attempts to authenticate a TokenInterface object.
     *
     * @param TokenInterface $token The TokenInterface instance to authenticate
     *
     * @return TokenInterface An authenticated TokenInterface instance, never null
     *
     * @throws AuthenticationException if the authentication fails
     */
    public function authenticate(TokenInterface $token)
    {
        $userInfo = $this->resourceOwner->getUserInfo($token->getAccessToken());
        try {
            $user = $this->userProvider->loadUserByUsername($userInfo['email']);
        } catch (UsernameNotFoundException $e) {
            $user = new SystemUser();
            $user->setEmail($userInfo['email']);
            $user->setName($userInfo['name']);
            $user->setSurname($userInfo['surname']);
            $user->setPanelUserId($userInfo['tenant_id']);
            $this->em->persist($user);
            $this->em->flush();
        }

        $user->setPanelAccessToken($token->getAccessToken());
        $token->setAuthenticated(true);
        $token->setUser($user);

        return $token;

    }

    /**
     * Checks whether this provider supports the given token.
     *
     * @param TokenInterface $token A TokenInterface instance
     *
     * @return bool true if the implementation supports the Token, false otherwise
     */
    public function supports(TokenInterface $token)
    {
        return $token instanceof OAuthUserToken;
    }
}

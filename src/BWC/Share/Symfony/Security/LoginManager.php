<?php

namespace BWC\Share\Symfony\Security;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\User\UserInterface;


class LoginManager
{
    /** @var \Symfony\Component\Security\Core\SecurityContextInterface */
    private $_securityContext;

    /** @var string */
    private $_providerKey;

    /** @var SessionInterface */
    private $_session;

    /** @var string */
    private $_sessionAuthKey;



    function __construct(
        SecurityContextInterface $securityContext,  // @security.context
        $providerKey,                               // main - firewall name
        SessionInterface $session,                  // @session
        $sessionAuthKey                             // _security_primary_auth|_security_secured_area  = '_security' + contextName
    ) {
        $this->_securityContext = $securityContext;
        $this->_providerKey = $providerKey;
        $this->_session = $session;
        $this->_sessionAuthKey = $sessionAuthKey;
    }



    function login($user) {
        if ($user instanceof UserInterface) {
            $token = new UsernamePasswordToken($user, null, $this->_providerKey, $user->getRoles());
        } else {
            $token = new AnonymousToken($this->_providerKey, $user ?: 'anon.');
        }
        $this->loginToken($token);
    }


    function loginToken(TokenInterface $token) {
        $this->_securityContext->setToken($token);
        $this->_session->set($this->_sessionAuthKey, serialize($token));
    }


    /**
     * @return null|TokenInterface
     */
    function getToken() {
        return $this->_securityContext->getToken();
    }

}
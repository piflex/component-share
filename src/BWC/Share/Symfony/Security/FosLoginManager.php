<?php

namespace BWC\Share\Symfony\Security;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;


class FosLoginManager extends LoginManager
{
    /** @var \BWC\Share\Symfony\Security\FosUserProvider */
    private $_fosUserProvider;


    function __construct(
        SecurityContextInterface $securityContext,  // @security.context
        $providerKey,                               // main - firewall name
        SessionInterface $session,                  // @session
        $sessionAuthKey,                             // _security_primary_auth|_security_secured_area  = '_security' + contextName
        FosUserProvider $fosUserProvider
    ) {
        parent::__construct($securityContext, $providerKey, $session, $sessionAuthKey);
        $this->_fosUserProvider = $fosUserProvider;
    }


    /**
     * @param string $un
     * @param string $pw
     * @return bool
     */
    function loginByUnPw($un, $pw) {
        $result = false;
        $user = $this->_fosUserProvider->getUserByUnPw($un, $pw);
        if ($user) {
            $this->login($user);
            $result = true;
        }
        return $result;
    }

    /**
     * @param string $email
     * @param string $pw
     * @return bool
     */
    function loginByEmailPw($email, $pw) {
        $result = false;
        $user = $this->_fosUserProvider->getUserByEmailPw($email, $pw);
        if ($user) {
            $this->login($user);
        }
        return $result;
    }


}
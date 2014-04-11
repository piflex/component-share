<?php

namespace BWC\Share\Symfony\Security;

use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class FosUserProvider
{
    /** @var \FOS\UserBundle\Model\UserManagerInterface */
    private $_fosUserManager;

    /** @var \Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface */
    private $_encoderFactory;



    function __construct(
        UserManagerInterface $fosUserManager, // @fos_user.user_manager
        EncoderFactoryInterface $encoderFactory // @security.encoder_factory
    ) {
        $this->_fosUserManager = $fosUserManager;
        $this->_encoderFactory = $encoderFactory;
    }


    /**
     * @param string $un
     * @param string $pw
     * @return \FOS\UserBundle\Model\UserInterface|null
     */
    function getUserByUnPw($un, $pw) {
        $result = null;
        if ($un && $pw) {
            $user = $this->_fosUserManager->findUserByUsername($un);
            if ($user && $this->isPasswordValid($user, $pw)) {
                $result = $user;
            }
        }
        return $result;
    }


    /**
     * @param string $email
     * @param string $pw
     * @return \FOS\UserBundle\Model\UserInterface|null
     */
    function getUserByEmailPw($email, $pw) {
        $result = null;
        if ($email && $pw) {
            $user = $this->_fosUserManager->findUserByEmail($email);
            if ($user && $this->isPasswordValid($user, $pw)) {
                $result = $user;
            }
        }
        return $result;
    }


    /**
     * @param UserInterface $user
     * @param string $pw
     * @return bool
     */
    private function isPasswordValid(UserInterface $user, $pw) {
        $pwdEncoded = $this->_encoderFactory->getEncoder($user)->encodePassword($pw, $user->getSalt());
        $result = $pwdEncoded == $user->getPassword();
        return $result;
    }

}
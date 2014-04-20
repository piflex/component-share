<?php

namespace BWC\Share\Symfony\Security\User;

use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class AccountPasswordEncoder implements AccountPasswordEncoderInterface
{
    /** @var \Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface  */
    private $encoderFactory;


    public function __construct(EncoderFactoryInterface $encoderFactory)
    {
        $this->encoderFactory = $encoderFactory;
    }

    /**
     * @param AdvancedUserAccountInterface $account
     * @return void
     */
    public function encodePassword(AdvancedUserAccountInterface $account)
    {
        if (null === $account->getPassword()) {
            $account->setPassword('');
        }
        if (0 !== strlen($password = $account->getPlainPassword())) {
            $encoder = $this->getEncoder($account);
            $account->setPassword($encoder->encodePassword($password, $account->getSalt()));
        }
        $account->eraseCredentials();
    }


    /**
     * @param AdvancedUserAccountInterface $account
     * @return \Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface
     */
    protected function getEncoder(AdvancedUserAccountInterface $account)
    {
        return $this->encoderFactory->getEncoder($account);
    }

} 
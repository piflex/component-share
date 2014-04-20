<?php

namespace BWC\Share\Symfony\Security\User;


interface AccountPasswordEncoderInterface
{
    /**
     * @param AdvancedUserAccountInterface $account
     * @return void
     */
    public function encodePassword(AdvancedUserAccountInterface $account);

} 
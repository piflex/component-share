<?php

namespace BWC\Share\Symfony\Security\User;

use Symfony\Component\Security\Core\User\AdvancedUserInterface;

interface AdvancedUserAccountInterface extends AdvancedUserInterface, \Serializable
{
    const ROLE_DEFAULT = 'ROLE_USER';
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';


    /**
     * @return mixed
     */
    public function getId();

    /**
     * @param string $name
     * @return AdvancedUserAccountInterface|$this
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $username
     * @return AdvancedUserAccountInterface
     */
    public function setUsername($username);

    /**
     * Gets email.
     *
     * @return string
     */
    public function getEmail();

    /**
     * Sets the email.
     *
     * @param string $email
     *
     * @return self
     */
    public function setEmail($email);

    /**
     * Gets the plain password.
     *
     * @return string
     */
    public function getPlainPassword();

    /**
     * Sets the plain password.
     *
     * @param string $password
     *
     * @return AdvancedUserAccountInterface|$this
     */
    public function setPlainPassword($password);

    /**
     * Sets the hashed password.
     *
     * @param string $password
     *
     * @return AdvancedUserAccountInterface|$this
     */
    public function setPassword($password);

    /**
     * Tells if the the given user has the super admin role.
     *
     * @return bool
     */
    public function isSuperAdmin();

    /**
     * @param boolean $boolean
     *
     * @return AdvancedUserAccountInterface|$this
     */
    public function setEnabled($boolean);

    /**
     * Sets the locking status of the user.
     *
     * @param boolean $boolean
     *
     * @return AdvancedUserAccountInterface|$this
     */
    public function setLocked($boolean);

    /**
     * Sets the super admin status
     *
     * @param boolean $boolean
     *
     * @return AdvancedUserAccountInterface|$this
     */
    public function setSuperAdmin($boolean);

    /**
     * Gets the confirmation token.
     *
     * @return string
     */
    public function getConfirmationToken();

    /**
     * Sets the confirmation token
     *
     * @param string $confirmationToken
     *
     * @return AdvancedUserAccountInterface|$this
     */
    public function setConfirmationToken($confirmationToken);

    /**
     * Sets the timestamp that the user requested a password reset.
     *
     * @param null|\DateTime $date
     *
     * @return AdvancedUserAccountInterface|$this
     */
    public function setPasswordRequestedAt(\DateTime $date = null);

    /**
     * Checks whether the password reset request has expired.
     *
     * @param integer $ttl Requests older than this many seconds will be considered expired
     *
     * @return boolean true if the user's password request is non expired, false otherwise
     */
    public function isPasswordRequestNonExpired($ttl);

    /**
     * Sets the last login time
     *
     * @param \DateTime $time
     *
     * @return AdvancedUserAccountInterface|$this
     */
    public function setLastLogin(\DateTime $time);

    /**
     * Never use this to check if this user has access to anything!
     *
     * Use the SecurityContext, or an implementation of AccessDecisionManager
     * instead, e.g.
     *
     *         $securityContext->isGranted('ROLE_USER');
     *
     * @param string $role
     *
     * @return bool
     */
    public function hasRole($role);

    /**
     * Sets the roles of the user.
     *
     * This overwrites any previous roles.
     *
     * @param array $roles
     *
     * @return AdvancedUserAccountInterface|$this
     */
    public function setRoles(array $roles);

    /**
     * Adds a role to the user.
     *
     * @param string $role
     *
     * @return AdvancedUserAccountInterface|$this
     */
    public function addRole($role);

    /**
     * Removes a role to the user.
     *
     * @param string $role
     *
     * @return AdvancedUserAccountInterface|$this
     */
    public function removeRole($role);

    /**
     * @return string
     */
    public function getLocale();

    /**
     * @param string $locale
     * @return AdvancedUserAccountInterface|$this
     */
    public function setLocale($locale);


} 
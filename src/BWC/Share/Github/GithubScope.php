<?php

namespace BWC\Share\Github;

final class GithubScope
{
    /**
     * Grants read/write access to profile info only. Note that this scope includes user:email and user:follow.
     */
    const USER = 'user';

    /**
     * Grants read access to a user’s email addresses.
     */
    const USER_EMAIL = 'user:email';

    /**
     * Grants access to follow or unfollow other users.
     */
    const USER_FOLLOW = 'user:follow';

    /**
     * Grants read/write access to code, commit statuses, and deployment statuses for public repositories and organizations.
     */
    const REPO_PUBLIC = 'public_repo';

    /**
     * Grants read/write access to code, commit statuses, and deployment statuses for public and private repositories and organizations.
     */
    const REPO = 'repo';

    /**
     * Grants read/write access to public and private repository commit statuses. This scope is only necessary to grant
     * other users or services access to private repository commit statuses without granting access to the code.
     */
    const REPO_STATUS = 'repo:status';

    /**
     * Grants access to delete adminable repositories.
     */
    const REPO_DELETE = 'delete_repo';

    /**
     * Grants read access to a user’s notifications. repo also provides this access.
     */
    const NOTIFICATIONS = 'notifications';

    /**
     * Grants write access to gists.
     */
    const GIST = 'gist';

    /**
     * Grants read and ping access to hooks in public or private repositories.
     */
    const REPO_HOOK_READ = 'read:repo_hook';

    /**
     * Grants read, write, and ping access to hooks in public or private repositories.
     */
    const REPO_HOOK_WRITE = 'write:repo_hook';

    /**
     * Grants read, write, ping, and delete access to hooks in public or private repositories.
     */
    const REPO_HOOK_ADMIN = 'admin:repo_hook';

    /**
     * Read-only access to organization, teams, and membership.
     */
    const ORG_READ = 'read:org';

    /**
     * Publicize and unpublicize organization membership.
     */
    const ORG_WRITE = 'write:org';

    /**
     * Fully manage organization, teams, and memberships.
     */
    const ORG_ADMIN = 'admin:org';

    /**
     * List and view details for public keys.
     */
    const PUBLIC_KEY_READ = 'read:public_key';

    /**
     * Create, list, and view details for public keys.
     */
    const PUBLIC_KEY_WRITE = 'write:public_key';

    /**
     * Fully manage public keys.
     */
    const PUBLIC_KEY_ADMIN = 'admin:public_key';



    private function __construct() { }
} 
<?php 

namespace App\Auth;

use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;

class LdapGuard implements Guard
{
    use GuardHelpers;

    protected $provider;
    protected $ldap;

    public function user()
    {
        return Auth::user();
    }

    public function __construct(UserProvider $provider, LdapConnection $ldap)
    {
        $this->provider = $provider;
        $this->ldap = $ldap;
    }

    public function attempt(array $credentials = [], $remember = false)
    {
        // Perform LDAP authentication using the provided credentials
        $username = $credentials['username'];
        $password = $credentials['password'];
        $dn = "cn=$username,dc=yourdomain,dc=com";
        try {
            $this->ldap->bind($dn, $password);
        } catch (Exception $e) {
            return false;
        }

        // Retrieve the user from the user provider or create a new user
        $user = $this->provider->retrieveByCredentials(['username' => $username]);
        if (!$user) {
            $user = $this->provider->create(['username' => $username]);
        }

        // Set the user as authenticated and return true
        $this->setUser($user);
        return true;
    }
}

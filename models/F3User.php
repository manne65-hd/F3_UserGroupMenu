<?php
namespace manne65hd;

class F3User extends \DB\Cortex {
    const AUTH_TYPE_LOCAL = 0;
    const AUTH_TYPE_LDAP = 1;

    protected $db = 'DB'; // F3 hive key of a valid DB object
    protected $table = 'users'; // the DB table to work on
    protected $ldapServer; // The LDAP-connection-object 

    public function login($username, $password) {
        $f3 = \Base::instance();

        if ($this->load(['username = ?', $username])) {
            // Username was found ...
            if ($this->auth_type === self::AUTH_TYPE_LOCAL) {
                // ... and it is a LOCAL user, so we can check against pw_hash
                return (password_verify($password, $this->pw_hash));

            } elseif ($this->auth_type === self::AUTH_TYPE_LDAP) {
                // ... but user is from LDAP, so let's check auth via LDAP
                self::connectLdapServer($f3->get('ldap.server_type'));
                $ldap_user_info = $this->ldapServer->ldapGetUserInfo($username);
                $f3->Dumper->collect('$ldap_user_info',$ldap_user_info);
                return $this->ldapServer->ldapAuth($ldap_user_info['user']['distinguishedname'], $password);
            }
        } else {
            // Username was NOT found ... check LDAP ??
            self::connectLdapServer($f3->get('ldap.server_type'));
            $ldap_user_info = $this->ldapServer->ldapGetUserInfo($username);
            $f3->Dumper->collect('$ldap_user_info',$ldap_user_info);
            return $this->ldapServer->ldapAuth($ldap_user_info['user']['distinguishedname'], $password);
        }
    }

    protected function connectLdapServer($server_type){
        switch ($server_type) {
            case LdapServer::TYPE_ACTIVE_DIRECTORY:
                $this->ldapServer = new LdapServerActiveDirectory();
                break;
            case LdapServer::TYPE_OPEN_LDAP:
                $this->ldapServer = new LdapServerOpenLDAP();
                break;
            case LdapServer::TYPE_FREE_IPA:
                $this->ldapServer = new LdapServerFreeIPA();
                break;
            case LdapServer::TYPE_OTHER:
                $this->ldapServer = new LdapServerOther();
                break;
        }
    }

}
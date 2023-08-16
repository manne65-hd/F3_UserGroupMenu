<?php
namespace manne65hd;

use LdapRecord\Container;
use LdapRecord\Connection;
use LdapRecord\Models\ActiveDirectory\User;
use LdapRecord\Models\ActiveDirectory\Group;

class F3User extends \DB\Cortex {
    const AUTH_TYPE_LOCAL = 0;
    const AUTH_TYPE_LDAP = 1;

    protected $db = 'DB'; // F3 hive key of a valid DB object
    protected $table = 'users'; // the DB table to work on
    protected $ldapServer; // The LDAP-connection-object 
    protected $ldapUser; // The LDAP-user-object
    protected $ldapGroup; // The LDAP-group-object

    public function login($username, $password) {
        $f3 = \Base::instance();

        if ($this->load(['username = ?', $username])) {
            // Username was found ...
            if ($this->auth_type === self::AUTH_TYPE_LOCAL) {
                // ... and it is a LOCAL user, so we can check against pw_hash
                return (password_verify($password, $this->pw_hash));

            } elseif ($this->auth_type === self::AUTH_TYPE_LDAP) {
                // ... but user is from LDAP, so let's check auth via LDAP
                self::ldapConnect();
                $ldap_user_info = self::ldapGetUserInfo($username);
                $f3->Dumper->collect('$ldap_user_info',$ldap_user_info);
                return self::ldapAuth($ldap_user_info['user']['distinguishedname'], $password);
            }
        } else {
            // Username was NOT found ... check LDAP ??
            self::ldapConnect();
            $ldap_user_info = self::ldapGetUserInfo($username);
            $f3->Dumper->collect('$ldap_user_info',$ldap_user_info);
            return self::ldapAuth($ldap_user_info['user']['distinguishedname'], $password);
        }
    }

    private function ldapConnect() {
        $f3 = \Base::instance();
        $this->ldapServer = new Connection([
            'hosts' => $f3->get('LDAP.HOSTS'),
            'base_dn' => $f3->get('LDAP.BASE_DN'),
            'username' => $f3->get('LDAP.QRY_USERNAME'),
            'password' => $f3->get('LDAP.QRY_PASSWORD'),
          ]);
    
        Container::addConnection($this->ldapServer);
        $this->ldapUser = new \LdapRecord\Models\ActiveDirectory\User();
        $this->ldapGroup =  new \LdapRecord\Models\ActiveDirectory\Group();
    
    }

    private function ldapAuth($distinguishedname, $password, $create_local = FALSE) {
        return $this->ldapServer->auth()->attempt($distinguishedname, $password);
    }

    private function ldapGetUserInfo($username) {
        $f3 = \Base::instance();

        //$ldap_query = $this->ldapServer->query();
        //$ldap_user = $ldap_query->findBy('samaccountname', $username);
        //$user = $this->ldapUser::find($ldap_user['distinguishedname'][0]);
        $user = $this->ldapUser::findByAnr($username);

        if ($user) {
            $user_info = array(
                'distinguishedname' => $user->getDn(),
                'guid' => $user->getConvertedGuid(),
                'username'  => $user->getFirstAttribute('samaccountname'),
                'firstname'  => $user->getFirstAttribute('givenname'),
                'lastname'  => $user->getFirstAttribute('sn'),
                'email'  => $user->getFirstAttribute('mail'),
            );
            $groups = $user->groups()->get();
            $usergroups = [];
            foreach ($groups as $group) {
                $usergroups[] = array(
                    'name' => $group->getName(),
                    'guid' => $group->getConvertedGuid(),
                );
            }

            return array(
                'user' => $user_info,
                'groups' => $usergroups,
            );
        } else {
            return false;
        }
    }


}
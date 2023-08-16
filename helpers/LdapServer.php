<?php
namespace manne65hd;

use LdapRecord\Container;
use LdapRecord\Connection;
use LdapRecord\Models\ActiveDirectory\User;
use LdapRecord\Models\ActiveDirectory\Group;

abstract class LdapServer {

public function __construct(){
    $f3 = \Base::instance();
    $ldap_server =  new Connection([
        'hosts' => $f3->get('ldap.hosts'),
        'base_dn' => $f3->get('ldap.users_base_dn'),
        'username' => $f3->get('ldap.qry_username'),
        'password' => $f3->get('ldap.qry_password'),
      ]);

    Container::addConnection($ldap_server);
    $f3->ldapUser = new \LdapRecord\Models\ActiveDirectory\User();
    $f3->ldapGroup =  new \LdapRecord\Models\ActiveDirectory\Group();

    return $ldap_server;
}

    public function ldapAuth($distinguishedname, $password) {
        return $this->ldapServer->auth()->attempt($distinguishedname, $password);
    }

    abstract public function ldapGetUserInfo($username);


}
<?php
namespace manne65hd;

use LdapRecord\Container;
use LdapRecord\Connection;
use LdapRecord\Models\ActiveDirectory\User;
use LdapRecord\Models\ActiveDirectory\Group;

abstract class LdapServer {
    const TYPE_ACTIVE_DIRECTORY = 'ActiveDirectory';
    const TYPE_OPEN_LDAP = 'OpenLDAP';
    const TYPE_FREE_IPA = 'FreeIPA';
    const TYPE_OTHER = 'Other';

    protected $ldap_server; // The LDAP-connection-object
    protected $ldap_user; // The LDAP-user-object
    protected $ldap_group; // The LDAP-group-object


public function __construct(){
    $f3 = \Base::instance();
    $this->ldap_server =  new Connection([
        'hosts' => $f3->get('ldap.hosts'),
        'base_dn' => $f3->get('ldap.users_base_dn'),
        'username' => $f3->get('ldap.qry_username'),
        'password' => $f3->get('ldap.qry_password'),
      ]);

    Container::addConnection($this->ldap_server);
    $this->ldap_user = new \LdapRecord\Models\ActiveDirectory\User();
    $this->ldap_group =  new \LdapRecord\Models\ActiveDirectory\Group();

    return $this->ldap_server;
}

    public function ldapAuth($distinguishedname, $password) {
        return $this->ldap_server->auth()->attempt($distinguishedname, $password);
    }

    abstract public function ldapGetUserInfo($username);


}
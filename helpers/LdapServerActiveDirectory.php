<?php
namespace manne65hd;

/*
use LdapRecord\Container;
use LdapRecord\Connection;
use LdapRecord\Models\ActiveDirectory\User;
use LdapRecord\Models\ActiveDirectory\Group;
*/

final class LdapServerActiveDirectory extends LdapServer {


    public function ldapGetUserInfo($username) {
        $f3 = \Base::instance();

        $user = $this->ldap_user::findByAnr($username);

        if ($user) {
            $user_info = array(
                'distinguishedname' => $user->getDn(),
                'guid' => $user->getConvertedGuid(),
                'username'  => $user->getFirstAttribute('samaccountname'),
                'firstname'  => $user->getFirstAttribute('givenname'),
                'lastname'  => $user->getFirstAttribute('sn'),
                'email'  => $user->getFirstAttribute('mail'),
            );
            $direct_ldap_groups = $user->groups()->whereContains('dn', 'ToDo')->get();
            foreach ($direct_ldap_groups as $group) {
                echo $group->getName() . '<br>';
            }

            $groups = $user->groups()->get();
            // Array to hold the relevant group-memberships of the user based on $f3->get('ldap.groups_base_dn')
            $user_groups = []; 
            // Array to hold *all* LDAP-group-memberships of the user
            $ldap_groups = []; 

            foreach ($groups as $group) {
                // check if current group is INSIDE the relevant groups
                if (str_contains(mb_strtolower($group->getDn()), mb_strtolower($f3->get('ldap.groups_base_dn')))) {
                    $user_groups[] = array(
                        'name'  => $group->getName(),
                        'dn'    => $group->getDn(),
                        'guid'  => $group->getConvertedGuid(),
                    );
                }
                $ldap_groups[] = array(
                    'name'  => $group->getName(),
                    'dn'    => $group->getDn(),
                    'guid'  => $group->getConvertedGuid(),
                );
            }

            return array(
                'user' => $user_info,
                'user_groups' => $user_groups,
                'ldap_groups' => $ldap_groups,
            );
        } else {
            return false;
        }
    }


}
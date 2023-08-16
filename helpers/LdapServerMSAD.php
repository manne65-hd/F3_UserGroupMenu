<?php
namespace manne65hd;

use LdapRecord\Container;
use LdapRecord\Connection;
use LdapRecord\Models\ActiveDirectory\User;
use LdapRecord\Models\ActiveDirectory\Group;

final class LdapServerMSAD extends LdapServer {


    public function ldapGetUserInfo($username) {
        $f3 = \Base::instance();

        $user = $f3->ldapUser::findByAnr($username);

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
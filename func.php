<?php

# Authorization check
$login = $_POST['login'].$domain;
$password = $_POST['password'];

function ldap_auth($auth_host,$auth_port,$auth_login,$auth_pass) {
    // Connect to AD Server
    $auth_ldap = ldap_connect($auth_host,$auth_port) or return 1;
    ldap_set_option($auth_ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_set_option($auth_ldap, LDAP_OPT_REFERRALS, 0);
    // Authorize user
	ldap_bind($auth_ldap,$auth_login,$auth_pass) or return 2;

	return $auth_ldap;
}

function ldap_group_check($bc_ldap,$bc_user,$bc_group,$bc_base) {
    $bc_group = $bc_group.','.$bc_base;
    // Building query
    $bc_query = '(&(memberof='.$bc_group.')(sAMAccountName='.$bc_user.'))';
    $bc_result = ldap_search($bc_ldap,$bc_base,$bc_query);
    // Count the results
    $bc_result_ent = ldap_get_entries($bc_ldap,$bc_result);
    // Authorized butlers go to main.php and other users warned
    if ($bc_result_ent['count'] != 0) {
        // Authorized
        $_SESSION['user_id'] = $login;
        return TRUE;
    }
    else {
        // Not authorized
        return FALSE;
    }
}

ldap_unbind($ldap);

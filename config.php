<?php
// Global configuration values for ldapasaurus

// title of the HTML pages
$title = "SugarCRM LDAP Admin";

// url for redirects to bounce to
$weburl = "http://ldap.sjc.sugarcrm.pvt/";

// app name for session
$appname = "ldapasaurus";

// ldap login info
$url = "ldaps://ldap.sjc.sugarcrm.pvt";
$admin = "cn=admin,dc=sugarcrm,dc=pvt";
$pass = "[REMOVED]";

$userdn = "ou=people,dc=sugarcrm,dc=pvt";
$groupdn = "ou=groups,dc=sugarcrm,dc=pvt";
$machinedn = "ou=systems,dc=sugarcrm,dc=pvt";

// admin user and group
$admingroup = "operations";
$adminuser = "admin";

// about string to show at the bottom of the page
$aboutstring = 'LDAPasaurus 0.1.1 - <a href="mailto:chris@sugarcrm.com">Chris Kelly</a>';

session_start();
if (!array_key_exists($appname, $_SESSION)) {
        $_SESSION[$appname] = array();
}

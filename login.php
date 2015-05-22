<?php
        include("config.php");
        session_regenerate_id();
        $ds = ldap_connect($url) or die(ldap_error($ds));
        ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3) or die(ldap_error($ds));
        if ($_POST['user'] != $adminuser && ldap_bind($ds,"uid=".$_POST['user'].",".$userdn,$_POST['password'])) {
                $_SESSION[$appname]['bound'] = 1;
                $_SESSION[$appname]['uid'] = $_POST['user'];

                $sr = ldap_search($ds, 'cn='.$admingroup.','.$groupdn,'(memberUid='.$_SESSION[$appname]['uid'].')') or die(ldap_error($ds));
                if (ldap_count_entries($ds, $sr) > 0) {
                        $_SESSION[$appname]['admin'] = 1;
                } else {
                        $_SESSION[$appname]['admin'] = 0;
                }
        } else {
                $_SESSION[$appname]['bound'] = 0;
                sleep(5);
        }
        header("Location: $weburl");


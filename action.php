<?php
	// This does all the doing that ldapasaurus does

	include("config.php");
	session_regenerate_id();
	if (array_key_exists('admin',$_SESSION[$appname]) && $_SESSION[$appname]['admin'] == 0) {

		$ds = ldap_connect($url) or die(ldap_error($ds));
		ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3) or die(ldap_error($ds));
		ldap_bind($ds, $admin, $pass) or die(ldap_error($ds));

		if ($_GET['a'] == 'changeMyPassword') {
			$newpassword = "{CRYPT}" . crypt($_POST['password']);
			ldap_mod_replace($ds,'uid='.$_SESSION[$appname]['uid'].','.$userdn, array('userPassword' => $newpassword)) 
				 or header("HTTP/1.0 404 Not Found");
		}	
	} else if (array_key_exists('admin',$_SESSION[$appname]) && $_SESSION[$appname]['admin'] == 1) {
		
		$ds = ldap_connect($url) or die(ldap_error($ds));
		ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3) or die(ldap_error($ds));
		ldap_bind($ds, $admin, $pass) or die(ldap_error($ds));

		$output = "";

		if ($_GET['a'] == 'getUsers') {
			$output = "<ul>";
			$sr = ldap_search($ds, $userdn, '(cn=*)') or die(ldap_error($ds));

			$entries = array();
			foreach (ldap_get_entries($ds, $sr) as $entry) {
				if (is_array($entry) && array_key_exists('uidnumber', $entry)) {
					$entries[$entry['uidnumber'][0].'-'.$entry['cn'][0]] = $entry;
				}
			}
			ksort($entries);
			foreach($entries as $entry) {
				if (!($entry['cn'][0] == '')) {
					if (array_key_exists('uidnumber', $entry)) {
						$output .= '<li>'.$entry['cn'][0].' ('.$entry['uid'][0].','.$entry['uidnumber'][0].') ';
					} else {
						$output .= '<li>'.$entry['cn'][0].' ('.$entry['uid'][0].')';
					}
					$output .= '<button onclick="a=confirm(\'really delete?\'); if (a) { YAHOO.util.Connect.asyncRequest(\'GET\', \'action.php?a=deleteUser&uid='.$entry['uid'][0].'\', gotDeleteUser);document.getElementById(\'userlist\').style.display = \'none\';};">delete</button>';
					$output .= '<button onclick="document.getElementById(\'pwchangeuid\').value=\''.$entry['uid'][0].'\'; changepassworddialog.show(); ">change password</button>';
					$output .= '</li>';
				}
			}
			$output .= "</ul>";
		
		} else if ($_GET['a'] == 'getGroups') {
			$output = "<ul>";
			$sr = ldap_search($ds, $groupdn, '(cn=*)') or die(ldap_error($ds));
			
			$entries = array();
			
			foreach(ldap_get_entries($ds, $sr) as $entry) {
				$entries[$entry['gidnumber'][0].'-'.$entry['cn'][0]] = $entry;
			}
			ksort($entries);
			foreach($entries as $entry) {
				if ($entry['cn'][0] != '') {
					$output .= '<li><a href="#" onclick="YAHOO.util.Connect.asyncRequest(\'GET\', \'action.php?a=getGroup&cn='. $entry['cn'][0] . '\', gotGroups);">' . $entry['cn'][0] . '</a> ('.$entry['gidnumber'][0].') ';
					$output .= '</li>';
				}
			}
			$output .= "</ul>";
		} else if ($_GET['a'] == 'getMachines') {
			$output = "<ul>";
			$sr = ldap_search($ds, $machinedn, '(cn=*)') or die(ldap_error($ds));
			foreach(ldap_get_entries($ds, $sr) as $entry) {
				if (!($entry['cn'][0] == '')) {
					$output .= '<li><a href="#" onclick="YAHOO.util.Connect.asyncRequest(\'GET\', \'action.php?a=getMachine&cn='.$entry['cn'][0].'\', gotMachines);">'.$entry['cn'][0] . '</a>';
					$output .= '</li>';
				}
			}	
			$output .= "</ul>";	

		} else if ($_GET['a'] == 'deleteUser') {
			if ($_GET['uid'] != $adminuser) {
				ldap_delete($ds, "uid=".$_GET['uid'].','.$userdn)
					or header("HTTP/1.0 404 Not Found");
			} else {
				header("HTTP/1.0 404 Not Found");
			}

		} else if ($_GET['a'] == 'deleteGroup') {
			if ($_GET['cn'] != $admingroup) {
				ldap_delete($ds, "cn=".$_GET['cn'].','.$groupdn)
					or header("HTTP/1.0 404 Not Found");
			} else {
				header("HTTP/1.0 404 Not Found");
			}

		} else if ($_GET['a'] == 'deleteMachine') {
			ldap_delete($ds, "cn=".$_GET['cn'].",ou=systems,dc=sugarcrm,dc=pvt") 
				or header("HTTP/1.0 404 Not Found");

		} else if ($_GET['a'] == 'addUser') {
			$person['cn'] = $_POST['first'].' '.$_POST['last'];
			$person['sn'] = $_POST['last'];
			$person['givenName'] = $_POST['first'];
			$person['uid'] = $_POST['uid'];
			$person['loginShell'] = '/bin/bash';
			$person['gecos'] = $_POST['first'].' '.$_POST['last'];
			$person['uidNumber'] = $_POST['uidnumber'];
			$person['gidNumber'] = $_POST['gidnumber'];
			$person['homeDirectory'] = '/home/'.$_POST['uid'];
			$person['objectClass'][0] = 'top';
			$person['objectClass'][1] = 'person';
			$person['objectClass'][2] = 'organizationalPerson';
			$person['objectClass'][3] = 'inetOrgPerson';
			$person['objectClass'][4] = 'posixAccount';
			$person['objectClass'][5] = 'shadowAccount';
			$person['objectClass'][6] = 'hostObject';
			$person['userPassword'] = "{CRYPT}" . crypt($_POST['password']);
			ldap_add($ds, 'uid='.$_POST['uid'].','.$userdn, $person)
				or header("HTTP/1.0 404 Not Found");

		} else if ($_GET['a'] == 'addGroup') {
			$group['cn'] = $_POST['name'];
			$group['description'] = $_POST['description'];
			$group['objectClass'][0] = 'posixGroup';
			$group['objectClass'][1] = 'top';
			$group['gidnumber'] = $_POST['gid'];
			ldap_add($ds, 'cn='.$_POST['name'].','.$groupdn, $group)
				or header("HTTP/1.0 404 Not Found");

		} else if ($_GET['a'] == 'addMachine') {
			$machine['cn'] = $_POST['name'];
			$machine['objectClass'] = 'top';
			$machine['objectClass'] = 'device';
			ldap_add($ds, 'cn='.$_POST['name'].','.$machinedn, $machine) 
				or header("HTTP/1.0 404 Not Found");

		} else if ($_GET['a'] == 'changePassword') {
			$newpassword = "{CRYPT}" . crypt($_POST['password']);
			ldap_mod_replace($ds,'uid='.$_POST['pwchangeuid'].','.$userdn, array('userPassword' => $newpassword)) 
				 or header("HTTP/1.0 404 Not Found");

		} else if ($_GET['a'] == 'getGroup') {
			$sr = ldap_search($ds, $groupdn, '(cn='.$_GET['cn'].')') or header("HTTP/1.0 404 Not Found");
			if (ldap_count_entries($ds, $sr) > 0) {
				$entries = ldap_get_entries($ds, $sr);
				$output .= "<h3>Group: ".$entries[0]['description'][0]." (".$entries[0]['cn'][0].",".$entries[0]['gidnumber'][0].")</h3>";
				$output .= '<button onclick="a=confirm(\'really delete?\'); if (a) { YAHOO.util.Connect.asyncRequest(\'GET\', \'action.php?a=deleteGroup&cn='.$entries[0]['cn'][0].'\', gotDeleteGroup);document.getElementById(\'grouplist\').style.display = \'none\';};">delete group</button>';
				$output .= '<button onclick="document.getElementById(\'addusertogroupcn\').value=\''.$entries[0]['cn'][0].'\'; addusertogroupdialog.show(); ">add user to group</button>';
				$output .= "<ul>";
				if (array_key_exists('memberuid',$entries[0])) {
					foreach($entries[0]['memberuid'] as $id => $uid) {
						if (!($id === 'count')) {
							$output .= "<li>$uid ";
							$output .= '<button onclick="a=confirm(\'really remove?\'); if (a) { YAHOO.util.Connect.asyncRequest(\'GET\', \'action.php?a=deleteGroupUser&cn='.$entries[0]['cn'][0].'&uid='.$uid.'\', gotDeleteGroupUser);document.getElementById(\'grouplist\').style.display = \'none\';};">remove from group</button>';
							$output .= "</li>";
						}
					}
				}
				$output .= "</ul>";
			}

		} else if ($_GET['a'] == 'addGroupUser') {
			$sr = ldap_search($ds, 'cn='.$_POST['addusertogroupcn'].','.$groupdn,'(cn=*)') or header("HTTP/1.0 404 Not Found");
			if (ldap_count_entries($ds, $sr) > 0) {
				$entries = ldap_get_entries($ds, $sr);
				print('<pre>');
				$uids[] = $_POST['uid'];
				if (array_key_exists('memberuid',$entries[0])) {
					foreach($entries[0]['memberuid'] as $id => $uid) {
                                                if (!($id === 'count')) {
							$uids[] = $uid;
						}
					}
				}
				ldap_mod_replace($ds,  'cn='.$_POST['addusertogroupcn'].','.$groupdn, array('memberUid' => $uids)) 
					or  header("HTTP/1.0 404 Not Found");	
			}
		} else if ($_GET['a'] == 'deleteGroupUser') {
			$sr = ldap_search($ds, 'cn='.$_GET['cn'].','.$groupdn,'(cn=*)') or header("HTTP/1.0 404 Not Found");
			if (ldap_count_entries($ds, $sr) > 0) {
				$entries = ldap_get_entries($ds, $sr);
				$uids = array();
				if (array_key_exists('memberuid',$entries[0])) {
					foreach($entries[0]['memberuid'] as $id => $uid) {
                                                if (!($id === 'count') && (!($uid === $_GET['uid']))) {
							$uids[] = $uid;
						}
					}
				}
				ldap_mod_replace($ds,  'cn='.$_GET['cn'].','.$groupdn, array('memberUid' => $uids)) 
					or  header("HTTP/1.0 404 Not Found");	
			}
		} else if ($_GET['a'] == 'getMachine') {
			$sr = ldap_search($ds, $machinedn, '(cn='.$_GET['cn'].')') or header("HTTP/1.0 404 Not Found");
			if (ldap_count_entries($ds, $sr) > 0) {
				$entries = ldap_get_entries($ds, $sr);
				$output .= "<h3>Machine: ".$entries[0]['cn'][0]."</h3>";
				$output .= '<button onclick="a=confirm(\'really delete?\'); if (a) { YAHOO.util.Connect.asyncRequest(\'GET\', \'action.php?a=deleteMachine&cn='.$entries[0]['cn'][0].'\', gotDeleteMachine);document.getElementById(\'machinelist\').style.display = \'none\';};">delete machine</button>';
				$output .= '<button onclick="document.getElementById(\'addusertomachinecn\').value=\''.$entries[0]['cn'][0].'\'; addusertomachinedialog.show(); ">add user to machine</button>';
				$output .= "<ul>";
				$sr2 = ldap_search($ds, $userdn, '(host='.$_GET['cn'].')') or header("HTTP/1.0 404 Not Found");
				if (ldap_count_entries($ds, $sr2) > 0) {
					foreach(ldap_get_entries($ds, $sr2) as $entry) {
						if (is_array($entry)) {
						$output .= "<li>".$entry['uid'][0].' ';
						$output .= '<button onclick="a=confirm(\'really remove?\'); if (a) { YAHOO.util.Connect.asyncRequest(\'GET\', \'action.php?a=deleteMachineUser&cn='.$entries[0]['cn'][0].'&uid='.$entry['uid'][0].'\', gotDeleteMachineUser);document.getElementById(\'machinelist\').style.display = \'none\';};">remove from machine</button>';
						$output .= '</li>';
						}
					}	
				}
				$output .= "</ul>";
			}

		} else if ($_GET['a'] == 'addMachineUser') {
			$sr = ldap_search($ds, 'uid='.$_POST['uid'].','.$userdn,'(cn=*)') or header("HTTP/1.0 404 Not Found");
			if (ldap_count_entries($ds, $sr) > 0) {
				$entries = ldap_get_entries($ds, $sr);
				$hosts[] = $_POST['addusertomachinecn'];
				if (array_key_exists('host',$entries[0])) {
					foreach($entries[0]['host'] as $id => $host) {
                                                if (!($id === 'count')) {
							$hosts[] = $host;
						}
					}
				}
				ldap_mod_replace($ds,  'uid='.$_POST['uid'].','.$userdn, array('host' => $hosts)) 
					or  header("HTTP/1.0 404 Not Found");	
			}

		} else if ($_GET['a'] == 'deleteMachineUser') {
			$sr = ldap_search($ds, 'uid='.$_GET['uid'].','.$userdn,'(cn=*)') or header("HTTP/1.0 404 Not Found");
			if (ldap_count_entries($ds, $sr) > 0) {
				$entries = ldap_get_entries($ds, $sr);
				$hosts = array();
				if (array_key_exists('host',$entries[0])) {
					foreach($entries[0]['host'] as $id => $host) {
                                                if (!($id === 'count') && (!($host === $_GET['cn']))) {
							$hosts[] = $host;
						}
					}
				}
				ldap_mod_replace($ds,  'uid='.$_GET['uid'].','.$userdn, array('host' => $hosts)) 
					or  header("HTTP/1.0 404 Not Found");	
			}
		}
		print $output;

	}
?>

<?php
	include("config.php");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <link rel="stylesheet" type="text/css" href="style.css" />
<title>LDAPasaurus: <?php print($title); ?></title>

<link rel="stylesheet" type="text/css" href="yui/build/tabview/assets/skins/sam/tabview.css" />
<link rel="stylesheet" type="text/css" href="yui/build/button/assets/skins/sam/button.css" />
<link rel="stylesheet" type="text/css" href="yui/build/container/assets/skins/sam/container.css">

<script type="text/javascript" src="yui/build/yahoo-dom-event/yahoo-dom-event.js"></script>
<script type="text/javascript" src="yui/build/event/event-min.js"></script>
<script type="text/javascript" src="yui/build/connection/connection-min.js"></script>
<script type="text/javascript" src="yui/build/dragdrop/dragdrop-min.js"></script>
<script type="text/javascript" src="yui/build/element/element-beta-min.js"></script>
<script type="text/javascript" src="yui/build/button/button-min.js"></script>
<script type="text/javascript" src="yui/build/container/container-min.js"></script>
<script type="text/javascript" src="yui/build/tabview/tabview-min.js"></script>

<style type="text/css">
</style>

<body class="yui-skin-sam">
<?php
	if (array_key_exists('bound',$_SESSION[$appname]) && $_SESSION[$appname]['bound']) {
?>
<div id="header">
        <img src="ldapasaurus_small.jpg" alt="" />
        <span id="title">
                <h1>LDAPasaurus: <?php print($title); ?></h1>
                <h3>Datacenter Authentication and Authorization (on <?php print($url); ?>)</h3>
        </span>
        <span id="menu">
		<a id="logoutbutton" href="logout.php">logout <?php print($_SESSION[$appname]['uid']); ?></a>
        </span>
</div>

<?
	}
	if (!(array_key_exists('bound',$_SESSION[$appname])) || !($_SESSION[$appname]['bound'])) {
?>
<div align="center">
<img style="margin: 50px;" src="ldapasaurus.jpg" />
<div id="logindialog">
	<div class="hd">Login</hd>
	<div class="bd">
		<form id="login" action="login.php" method="post" name="login"> 
			<label for="user">Username: </label><input type="text" name="user" /><br />
			<label for="password">Password: </label><input type="password" name="password" />
		</form>
	</div>
</div>
<script type="text/javascript">
var logindialog = new YAHOO.widget.Dialog("logindialog");
logindialog.cfg.queueProperty("buttons", [ { text: "Login", isDefault:true, handler:function() { this.submit(); }, isDefault:true} ]);
logindialog.cfg.queueProperty("fixedcenter", true);
logindialog.cfg.queueProperty("modal", true);
logindialog.cfg.queueProperty("visible", true);
logindialog.cfg.queueProperty("close", false);
logindialog.cfg.queueProperty("postmethod", "form");
logindialog.cfg.queueProperty("hideaftersubmit", false);
logindialog.render();
</script>
<?php
	} else  if (array_key_exists('admin',$_SESSION[$appname]) && $_SESSION[$appname]['admin'] == 1) {
?>
<div id="content">
<div id="tabs" class="yui-navset">
    <ul class="yui-nav">
	<li class="selected"><a href="#usertab"><em>Users</em></a></li>
	<li><a href="#grouptab"><em>Groups</em></a></li>
	<li><a href="#machinetab"><em>Machines</em></a></li>
    </ul>
    <div class="yui-content">
	<div id="usertab">
		<p><button id="adduserbutton">Add User</button><button id="refreshuserbutton">Refresh</button></p>
		<div id="userlist"></div>
	</div>
	<div id="grouptab">
		<p><button id="addgroupbutton">Add Group</button><button id="refreshgroupbutton">Refresh</button></p>
		<div id="grouplist"></div>
	</div>
	<div id="machinetab">
		<p><button id="addmachinebutton">Add Machine</button><button id="refreshmachinebutton">Refresh</button></p>
		<div id="machinelist"></div>
	</div>
    </div>
</div>
<div id="adduserdialog">
	<div class="hd">Add a user</div>
	<div class="bd">
		<form method="POST" action="action.php?a=addUser">
			<label for="uid">User ID:</label><input type="textbox" name="uid" /><br />
			<label for="uidnumber">UID num:</label><input type="textbox" name="uidnumber" /><br />
			<label for="gidnumber">GID num:</label><input type="textbox" name="gidnumber" /><br />
			<label for="first">First:</label><input type="textbox" name="first" /><br />
			<label for="last">Last:</label><input type="textbox" name="last" /><br />
			<label for="password">Passwd:</label><input type="password" name="password" />
		</form>
	</div>
</div>
<div id="addgroupdialog">
	<div class="hd">Add a group</div>
	<div class="bd">
		<form method="POST" action="action.php?a=addGroup">
			<label for="name">Name:</label><input type="textbox" name="name" /><br />
			<label for="description">Description:</label><input type="textbox" name="description" /><br />
			<label for="gid">GID:</label><input type="textbpx" name="gid" />
		</form>
	</div>
</div>
<div id="addmachinedialog">
	<div class="hd">Add a machine</div>
	<div class="bd">
		<form method="POST" action="action.php?a=addMachine">
			<label for="name">Hostname:</label><input type="textbox" name="name" /><br />
		</form>
	</div>
</div>
<div id="changepassworddialog">
	<div class="hd">Change Password</div>
	<div class="bd">
		<form method="POST" action="action.php?a=changePassword">
			<label for="pwchangeuid">UID:</label><input type="text" name="pwchangeuid" id="pwchangeuid"><br />
			<label for="password">Password:</label><input type="password" name="password" />
		</form>
	</div>
</div>
<div id="addusertogroupdialog">
	<div class="hd">Add user to group</div>
	<div class="bd">
		<form method="POST" action="action.php?a=addGroupUser">
			<label for="addusertogroupcn">Group:</label><input type="text" name="addusertogroupcn" id="addusertogroupcn"><br />
			<label for="uid">UID:</label><input type="text" name="uid" />
		</form>
	</div>
</div>
<div id="addusertomachinedialog">
	<div class="hd">Add user to machine</div>
	<div class="bd">
		<form method="POST" action="action.php?a=addMachineUser">
			<label for="addusertomachinecn">Group:</label><input type="text" name="addusertomachinecn" id="addusertomachinecn"><br />
			<label for="uid">UID:</label><input type="text" name="uid" />
		</form>
	</div>
</div>
</div>
<?php } else { ?>
<div id="content">
<button id="changepasswordbutton">Change Password</button>
<div id="changepassworddialog">
	<div class="hd">Change Password</div>
	<div class="bd">
		<form method="POST" action="action.php?a=changeMyPassword">
			<label for="password">Password:</label><input type="password" name="password" />
		</form>
	</div>
</div>
<script type="text/javascript">
var logoutButton = new YAHOO.widget.Button("logoutbutton");

var changepassworddialog = new YAHOO.widget.Dialog("changepassworddialog");
changepassworddialog.cfg.queueProperty("buttons", [ { text: "Submit", handler:function() { this.submit(); }, isDefault:true},
					{ text:"Cancel", handler:function() { this.cancel();} }]);
changepassworddialog.cfg.queueProperty("fixedcenter", true);
changepassworddialog.cfg.queueProperty("modal", true);
changepassworddialog.cfg.queueProperty("visible", false);
changepassworddialog.callback.success = function(o) { alert("password changed!");} ;
changepassworddialog.callback.failure = function(o) { alert("failed to change password!");};
changepassworddialog.render();

var changepasswordbutton = new YAHOO.widget.Button("changepasswordbutton");
YAHOO.util.Event.addListener("changepasswordbutton","click", changepassworddialog.show, changepassworddialog, true);
</script>
</div>
<?php 
	}
	if (array_key_exists('admin',$_SESSION[$appname]) && $_SESSION[$appname]['admin']) {
?>

<script type="text/javascript">

function refreshUsers(eventn) {
	document.getElementById('userlist').style.display='none';
	YAHOO.util.Connect.asyncRequest('GET','action.php?a=getUsers', gotUsers);
}
function refreshGroups(eventn) {
	document.getElementById('grouplist').style.display='none';
	YAHOO.util.Connect.asyncRequest('GET','action.php?a=getGroups', gotGroups);
}
function refreshMachines(eventn) {
	document.getElementById('machinelist').style.display='none';
	YAHOO.util.Connect.asyncRequest('GET','action.php?a=getMachines', gotMachines);
}

var handleDeleteSuccess = function(o) {
	if (o.argument.id == 'userlist') {
		refreshUsers();
		alert('User deleted!');
	} else if (o.argument.id == 'grouplist') {
		refreshGroups();
		alert('Group deleted!');
	} else if (o.argument.id == 'machinelist') {
		refreshMachines();
		alert('Machine deleted!');
	} else if (o.argument.id == 'groupuser') {
		refreshGroups();
		alert('User removed from group!');
	} else if (o.argument.id == 'machineuser') {
		refreshMachines();
		alert('User removed from machine!');
	}
}

var handleDeleteFailure = function(o) {
	document.getElementById(o.argument.id).style.display='block';
	alert('ERROR: entry not deleted!');
}

var handleGetSuccess = function(o) {
	if (o.responseText !== undefined) {
		var div = document.getElementById(o.argument.id);
		div.innerHTML = o.responseText;
		div.style.display='block';
	}
}
var handleGetFailure = function(o) {
	if (o.responseText !== undefined) {
		var div = document.getElementById(o.argument.id);
		div.innerHtml = "error retreiving data!";
		div.style.display='block';
	}
}

var gotUsers = { success: handleGetSuccess, failure: handleGetFailure, argument: {id: 'userlist'} };
var gotGroups = { success: handleGetSuccess, failure: handleGetFailure, argument: {id: 'grouplist'} };
var gotMachines = { success: handleGetSuccess, failuter: handleGetFailure, argument: {id: 'machinelist'} };

var gotDeleteUser = { success: handleDeleteSuccess, failure: handleDeleteFailure, argument: {id: 'userlist' } };
var gotDeleteGroup = { success: handleDeleteSuccess, failure: handleDeleteFailure, argument: {id: 'grouplist' } };
var gotDeleteMachine = { success: handleDeleteSuccess, failure: handleDeleteFailure, argument: {id: 'machinelist' } };
var gotDeleteGroupUser = { success: handleDeleteSuccess, failure: handleDeleteFailure, argument: {id: 'groupuser' } };
var gotDeleteMachineUser = { success: handleDeleteSuccess, failure: handleDeleteFailure, argument: {id: 'machineuser' } };

var adduserdialog = new YAHOO.widget.Dialog("adduserdialog");
adduserdialog.cfg.queueProperty("buttons", [ { text: "Submit", handler:function() { this.submit(); }, isDefault:true},
					{ text:"Cancel", handler:function() { this.cancel();} }]);
adduserdialog.cfg.queueProperty("fixedcenter", true);
adduserdialog.cfg.queueProperty("modal", true);
adduserdialog.cfg.queueProperty("visible", false);
adduserdialog.callback.success = function(o) { refreshUsers(); alert("added user!");} ;
adduserdialog.callback.failure = function(o) { alert("failed to add user!");};
adduserdialog.render();

var addgroupdialog = new YAHOO.widget.Dialog("addgroupdialog");
addgroupdialog.cfg.queueProperty("buttons", [ { text: "Submit", handler:function() { this.submit(); }, isDefault:true},
					{ text:"Cancel", handler:function() { this.cancel();} }]);
addgroupdialog.cfg.queueProperty("fixedcenter", true);
addgroupdialog.cfg.queueProperty("modal", true);
addgroupdialog.cfg.queueProperty("visible", false);
addgroupdialog.callback.success = function(o) { refreshGroups(); alert("added group!");} ;
addgroupdialog.callback.failure = function(o) { alert("failed to add group!");};
addgroupdialog.render();

var addmachinedialog = new YAHOO.widget.Dialog("addmachinedialog");
addmachinedialog.cfg.queueProperty("buttons", [ { text: "Submit", handler:function() { this.submit(); }, isDefault:true},
					{ text:"Cancel", handler:function() { this.cancel();} }]);
addmachinedialog.cfg.queueProperty("fixedcenter", true);
addmachinedialog.cfg.queueProperty("modal", true);
addmachinedialog.cfg.queueProperty("visible", false);
addmachinedialog.callback.success = function(o) { refreshMachines(); alert("added machine!");} ;
addmachinedialog.callback.failure = function(o) { alert("failed to add machine!");};
addmachinedialog.render();

var changepassworddialog = new YAHOO.widget.Dialog("changepassworddialog");
changepassworddialog.cfg.queueProperty("buttons", [ { text: "Submit", handler:function() { this.submit(); }, isDefault:true},
					{ text:"Cancel", handler:function() { this.cancel();} }]);
changepassworddialog.cfg.queueProperty("fixedcenter", true);
changepassworddialog.cfg.queueProperty("modal", true);
changepassworddialog.cfg.queueProperty("visible", false);
changepassworddialog.callback.success = function(o) { alert("password changed!");} ;
changepassworddialog.callback.failure = function(o) { alert("failed to change password!");};
changepassworddialog.render();

var addusertogroupdialog = new YAHOO.widget.Dialog("addusertogroupdialog");
addusertogroupdialog.cfg.queueProperty("buttons", [ { text: "Submit", handler:function() { this.submit(); }, isDefault:true},
					{ text:"Cancel", handler:function() { this.cancel();} }]);
addusertogroupdialog.cfg.queueProperty("fixedcenter", true);
addusertogroupdialog.cfg.queueProperty("modal", true);
addusertogroupdialog.cfg.queueProperty("visible", false);
addusertogroupdialog.callback.success = function(o) { refreshGroups(); alert("user added to group!");} ;
addusertogroupdialog.callback.failure = function(o) { alert("failed to add user to group!");};
addusertogroupdialog.render();

var addusertomachinedialog = new YAHOO.widget.Dialog("addusertomachinedialog");
addusertomachinedialog.cfg.queueProperty("buttons", [ { text: "Submit", handler:function() { this.submit(); }, isDefault:true},
					{ text:"Cancel", handler:function() { this.cancel();} }]);
addusertomachinedialog.cfg.queueProperty("fixedcenter", true);
addusertomachinedialog.cfg.queueProperty("modal", true);
addusertomachinedialog.cfg.queueProperty("visible", false);
addusertomachinedialog.callback.success = function(o) { refreshMachines(); alert("user added to machine!");} ;
addusertomachinedialog.callback.failure = function(o) { alert("failed to add user to machine!");};
addusertomachinedialog.render();

(function() {	
	var tabView = new YAHOO.widget.TabView('tabs');
	var logoutButton = new YAHOO.widget.Button("logoutbutton");
	var addUserButton = new YAHOO.widget.Button("adduserbutton");
	YAHOO.util.Event.addListener("adduserbutton","click", adduserdialog.show, adduserdialog, true);
	var refreshUserButton = new YAHOO.widget.Button("refreshuserbutton");
	refreshUserButton.on("click", refreshUsers);
	var addGroupButton = new YAHOO.widget.Button("addgroupbutton");
	YAHOO.util.Event.addListener("addgroupbutton","click", addgroupdialog.show, addgroupdialog, true);
	var refreshGroupButton = new YAHOO.widget.Button("refreshgroupbutton");
	refreshGroupButton.on("click", refreshGroups);
	var addMachineButton = new YAHOO.widget.Button("addmachinebutton");
	YAHOO.util.Event.addListener("addmachinebutton","click", addmachinedialog.show, addmachinedialog, true);
	var refreshMachineButton = new YAHOO.widget.Button("refreshmachinebutton");
	refreshMachineButton.on("click", refreshMachines); 

	YAHOO.util.Connect.asyncRequest('GET','action.php?a=getUsers', gotUsers);
	YAHOO.util.Connect.asyncRequest('GET','action.php?a=getGroups', gotGroups);
	YAHOO.util.Connect.asyncRequest('GET','action.php?a=getMachines', gotMachines);

})();
</script>
<?php } ?>
<?php if (array_key_exists('bound',$_SESSION[$appname]) && $_SESSION[$appname]['bound']) { ?>
<div id="footer">
        <img src="itondemand_logo.gif" alt="" />
        <span>
                <h4>ldapasaurus - build 2009.07.07:12:23 EDT - <a href="mailto:chris@sugarcrm.com">chris@sugarcrm.com</a></h4>
        </span>
</div>
<?php } ?>
</body>
</html>

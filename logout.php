<?php
        include("config.php");
        session_regenerate_id();
        $_SESSION[$appname] = array();
        header("Location:".$weburl);

<?php

// To try a pre-auth and purchase on $_REQUEST, use
// http://example.com/?gffd_action=fd_request
// and sent $_POST data
// 
if(
	isset($_REQUEST)
	&& isset($_REQUEST['gffd_action'])
	&& $_REQUEST['gffd_']=='fd_request'
){ 
	fd_request();
}

?>
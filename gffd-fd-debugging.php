<?php

// If you would like to see debug information, 
// you may call this script with &debug=1
// 
if(
	isset($_REQUEST['gffd_debug'])
	&& isset($result['print_r']) 
){ 
	include "gffd-debug.html.php";
}

?>
<?php
 /*
          ThWClone - PHP/MySQL Bulletin Board System
        ==============================================
          (c) 2003 by
           Mario Pischel         <mario@aqzone.de>

          download the latest version:
          https://developer.berlios.de/projects/thwc/

          This  program is  free  software;  you can
          redistribute it and/or modify it under the
          terms of the GNU General Public License as
          published by the Free Software Foundation;
          either  version 2 of  the License,  or (at
          your option) any later version.

        ==============================================
 */
 include ( 'inc/header.inc.php' );
 
 $session_cat = 'c'.$catid; // openclose Variable
 if( isset( $_SESSION[$session_cat] ) )
 {
     if( $_SESSION[$session_cat] == 0 )
	     $open = 1;
	 else
	     $open = 0;
	 $_SESSION[$session_cat] = $open;
 }
 // Weiterleitung ------------------
 message_redirect('Auswahl wird bearbeitet, bitte warten ...', 'index.php');
?>
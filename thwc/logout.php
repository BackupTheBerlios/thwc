<?php
 /* $Id: logout.php,v 1.1 2003/06/13 22:15:00 master_mario Exp $ */
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
 
 db_query("UPDATE ".$pref."user SET
     user_session=''
 WHERE user_id='".U_ID."'");
 
 session_unset();
 @session_destroy();
 
 message_redirect('Du hast Dich erfolgreich ausgeloggt, bitte warten ...', 'index.php');
?>
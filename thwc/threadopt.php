<?php
 /* $Id: threadopt.php,v 1.1 2003/06/13 21:37:57 master_mario Exp $ */
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
 if( $action == '' )
 {
     $TBoard = Get_Template( 'templates/'.$style['styletemplate'].'/board.html' );
     message ( 'Bitte w&auml;le eine Funktion aus.', 'Fehler', 0 );
 }
 // dellink -------------------------------------------  
 elseif( $action == 'dellink' )
 {
     $TBoard = Get_Template( 'templates/'.$style['styletemplate'].'/board.html' );
     $r_thread = db_query("SELECT
	     board_id,
		 link
	 FROM ".$pref."thread WHERE thread_id='$threadid' AND link='$boardid'");
	 if( db_rows( $r_thread ) != 1 )
	     message ( 'Sorry! Fehlerhafter Link.', 'Fehler', 0 );
	 $P = boardPermissions ( U_GROUPIDS, $boardid );
	 if( $P[25] != 1 )
	     message ( 'Sorry! Du bist nicht berechtigt diesen Link zu l&ouml;schen.', 'Fehler', 0 );
	 else
	 {
	     db_query("UPDATE ".$pref."thread SET
		     link='0'
		 WHERE thread_id='$threadid'");
	 
         $basename = basename($HTTP_SERVER_VARS["SCRIPT_NAME"]);
         db_query( "INSERT INTO ".$pref."modlog SET
             logtime='$board_time',
             loguser='".U_NAME."',
             logip='".getenv('REMOTE_ADDR')."',
             logfile='$basename',
             action='".addslashes($action)."'");
         message_redirect('Linkgel&ouml;scht, bitte warten ...', 'board.php?boardid='.$boardid );
	 } 
 }   
?>
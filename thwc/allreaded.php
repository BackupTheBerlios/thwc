<?php
 /* $Id: allreaded.php,v 1.1 2003/06/13 21:38:13 master_mario Exp $ */
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
 // alle Boards als gelesen makieren
 if( !isset( $boardid ) )
 {
     if( U_ID != 0 )
	 {
         $r_board = db_query("SELECT
             board_id
         FROM ".$pref."board WHERE category!='0' AND disabled!='1'");
         if( db_rows( $r_board ) > 0 )
         {
             while( $a_board = db_result( $r_board ) )
             {
                 $session_var_name = 'b'.$a_board['board_id'];
                 $r_post_id = db_query("SELECT
                     MAX(post_id)
                 FROM ".$pref."post WHERE board_id='$a_board[board_id]'");
                 if( db_rows( $r_post_id ) == 0 )
                     $_SESSION[$session_var_name] = 0;
                 else
                 {
                     $a_post_id = db_result( $r_post_id );
					 list(, $poid ) = each( $a_post_id );
                     $_SESSION[$session_var_name] = $poid;
                 }
			 }
         }
     }
     message_redirect('Alle Foren wurden als gelesen makiert, bitte warten ...', 'index.php' );
 }
 // einzelnes Board als gelesen makieren
 else
 {
     if( U_ID != 0 )
	 {
         $session_var_name = 'b'.$boardid;
         $r_post_id = db_query("SELECT
             MAX(post_id)
         FROM ".$pref."post WHERE board_id='$boardid'");
         if( db_rows( $r_post_id ) == 0 )
             $_SESSION[$session_var_name] = 0;
         else
         {
             $a_post_id = db_result( $r_post_id );
			 list(, $poid ) = each( $a_post_id );
             $_SESSION[$session_var_name] = $poid;
		 }
     }    
     message_redirect('Board wurde als gelesen makiert, bitte warten ...', 'board.php?boardid='.$boardid );
 }
?>
<?php
 /* $Id: login.php,v 1.1 2003/06/12 13:59:19 master_mario Exp $ */
 include ( 'inc/header.inc.php' );
 
 $r_login = db_query("SELECT
     user_id,
	 user_pw,
	 user_lastacttime
 FROM ".$pref."user WHERE user_name='".addslashes($login['name'])."'");
 if( db_rows( $r_login ) == 1 )
 {
     $a_login = db_result( $r_login );
	 if( md5(addslashes($login['pw'])) == $a_login['user_pw'] )
	 {
	     // login --------------------------
	     db_query("UPDATE ".$pref."user SET
		     user_session='".$sid."',
			 user_oldsavet='".$a_login['user_lastacttime']."'
		 WHERE user_id='".$a_login['user_id']."'");
		 // gast löschen -------------------
         db_query("DELETE FROM ".$pref."guest WHERE session_id='$sid'");
         db_query("OPTIMIZE TABLE ".$pref."guest");
		 // new Posts ----------------------
         $r_boards = db_query("SELECT
             board_id
         FROM ".$pref."boards WHERE category!='0' AND disabled!='0'");
		 if( db_rows( $r_boards > 0 ) )
		 {
              while( $a_boards = db_result( $r_boards ) )
              {
                  $session_var = 'b'.$a_boards['board_id'];
                  $r_post_id = db_query("SELECT
                       MIN(post_id),
                       COUNT(post_id)
                  FROM ".$pref."post WHERE board_id='$a_boards[board_id]' AND post_time>'$a_user[user_lastacttime]'");
                  $a_post_id = db_result( $r_post_id );
                  if( $a_post_id[1] == 0 )
                  {
                       $r_max_post = db_query("SELECT
                            MAX(post_id)
                       FROM ".$pref."post WHERE board_id='$a_boards[board_id]'");
                       $a_max_post = db_result( $r_max_post );
                       $_SESSION[$session_var] = $a_max_post[0];
                  }
                  else
                  {
                      $_SESSION[$session_var] = $a_post_id[0]-1;
                  }
              } // while
		 } // if
		 
		 // Weiterleitung ------------------
		 message_redirect('Sie wurden erfolgreich eingeloggt, bitte warten ...', 'index.php');
	 }
	 else
	 {
     	 $TBoard = Get_Template( 'templates/'.$style['styletemplate'].'/board.html' );
     	 message ( 'Das Passwort ist falsch.', 'Fehler', 0 );
	 }
 }
 else
 {
     $TBoard = Get_Template( 'templates/'.$style['styletemplate'].'/board.html' );
     message ( 'Es ist kein User mit diesem Namen registriert.', 'Fehler', 0 );
 }
?>
<?php
 /* $Id: login.php,v 1.3 2003/06/16 18:08:20 master_mario Exp $ */
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
		 setNewposts( $a_login['user_lastacttime'] );
          // Weiterleitung ------------------
          message_redirect('Du hast Dich erfolgreich eingeloggt, bitte warten ...', $loginscript );
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
<?php
 /* $Id: calevent.php,v 1.1 2003/06/26 13:46:47 master_mario Exp $ */
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
 include( 'inc/tagbar.inc.php' );
 // nav_path ------------------------------------------
 $data['nav_path'] .= '&nbsp;&gt;&gt;&nbsp;<a href="calendar.php" class="bg">Kalender</a>';
 $data['nav_path'] .= '&nbsp;&gt;&gt;&nbsp;Neuer Eintrag';
 // global Templates ----------------------------------
 $TBoard = Get_Template( 'templates/'.$style['styletemplate'].'/board.html' );
 $TNewevent = Get_Template( 'templates/'.$style['styletemplate'].'/new_event.html' );
 // Permissions
 if( ( U_ID > 0 && P_CEVENT == 0 ) || ( U_ID < 1 && ( P_CEVENT == 0 || $config['guest_calendar'] == 0 ) ) )
     message( 'Du bist nicht berechtigt Kalendereintr&auml;ge zu machen', 'Rechte', 0 );
 
 if( !isset( $send ) )
 {
     $data['max_len'] = $config['max_event_len'];
	 $data['bcode'] = '&nbsp;';
	 if( $config['eventcode'] == 1 )
	     $data['bcode'] = $tagbar;
		 
     if( !isset( $back ) )
	 {
	     if( !isset( $m ) )
		     $m = date( "m", $board_time );
		 if( !isset( $y ) )
		     $y = date( "Y", $board_time );
		 $data['eday'] = '';
		 $data['emonth'] = $m;
		 $data['eyear'] = $y;
		 $data['ehours'] = '';
		 $data['emin'] = '';
		 $data['etopic'] = '';
		 $data['etext'] = '';
	 }
	 else
	 {
		 $data['eday'] = $event['day'];
		 $data['emonth'] = $m;
		 $data['eyear'] = $y;
		 $data['ehours'] = $event['hours'];
		 $data['emin'] = $event['min'];
		 $data['etopic'] = $event['topic'];
		 $data['etext'] = $event['text'];
		 if( $event['report'] == 1 )
		     $data['ereport'] = 'checked';
	 }
 }
 else
 {
     // usereingaben testen ------------------
	 $err_mess = '';
	 if( checkdate($event['month'],$event['day'],$event['year']) == FALSE )
	     $err_mess = 'Du hast ein ung&uuml;ltiges Datum angegeben.';
	 $string = $event['topic'];
     $legalchars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789 [|](){}.-_äöüÄÖÜß";
     for( $i = 0; $i < strlen($string); $i++ )
     {
         if( !strstr($legalchars, $string[$i]) )
         {
		     $err_mess .= ( $err_mess == '' ? '' : '<br />' ).'Der gew&auml;lte Name enth&auml;lt nicht erlaubte Zeichen. ( '.$string[$i].' )';
         }
     }
	 if( strlen( $string ) < 3 )
	     $err_mess .= ( $err_mess == '' ? '' : '<br />' ).'Das Topic ist zu kurz, es mu&szlig; mindestens 3 Zeichen lang sein.';
	 if( strlen( $string ) > 50 )
	     $err_mess .= ( $err_mess == '' ? '' : '<br />' ).'Das Topic ist zu lang, es darf maximal 50 Zeichen lang sein.';
	 if( strlen( $text ) < 3 )
	     $err_mess .= ( $err_mess == '' ? '' : '<br />' ).'Der Text ist zu kurz, er mu&szlig; mindestens 3 Zeichen lang sein.';
	 if( strlen( $text ) > $config['max_event_len'] )
	     $err_mess .= ( $err_mess == '' ? '' : '<br />' ).'Der Text ist zu lang, es darf maximal '.$config['max_event_len'].' Zeichen lang sein.';
     if( $event['hours'] < 0 || $event['hours'] > 23 )		
	     $err_mess .= ( $err_mess == '' ? '' : '<br />' ).'Die Angegebene Uhrzeit ist nicht korrekt (Stunden).';
     if( $event['min'] < 0 || $event['min'] > 59 )		
	     $err_mess .= ( $err_mess == '' ? '' : '<br />' ).'Die Angegebene Uhrzeit ist nicht korrekt (Minuten).';
     if( $err_mess != '' )
	 {
	     $mess = '<form action="calevent.php" name="sendback" method="post">
		  '.$err_mess.'
		  <input type="hidden" name="back" value="1" />
		  <input type="hidden" name="event[day]" value="'.$event['day'].'" />
		  <input type="hidden" name="m" value="'.$event['month'].'" />
		  <input type="hidden" name="y" value="'.$event['year'].'" />
		  <input type="hidden" name="event[hours]" value="'.$event['hours'].'" />
		  <input type="hidden" name="event[min]" value="'.$event['min'].'" />
		  <input type="hidden" name="event[topic]" value="'.$event['topic'].'" />
		  <input type="hidden" name="event[text]" value="'.$text.'" />
		  <input type="hidden" name="event[report]" value="'.( isset( $event['report'] ) ? 1 : 0 ).'" />
		  </form>';
		 message( $mess, 'Folgende Fehler sind aufgetreten', 1 );
	 }
	 else
	 {
	     db_query("INSERT INTO ".$pref."calendar SET
		     caltime='".mktime( $event['hours'],$event['min'],0,$event['month'],$event['day'],$event['year'] )."',
			 caltopic='".addslashes($event['topic'])."',
			 calautor='".U_NAME."',
			 caltext='".addslashes($text)."',
			 showasevent='".( isset( $event['report'] ) ? 1 : 0 )."',
			 aktiv='1'");
		 message_redirect('Dein Kalendereintrag wurde aufgenommen, bitte warten ...', 'calendar.php?m='.intval($event['month']).'&y='.$event['year'] );
	 }
 }
 
 $data['boardtable'] = Template( $TNewevent );
 echo Output( Template ( $TBoard ) );
?>
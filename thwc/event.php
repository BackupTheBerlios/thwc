<?php
 /* $Id: event.php,v 1.1 2003/06/26 13:46:47 master_mario Exp $ */
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
 include ( 'inc/bcode.inc.php' );
 // global Template
 $TEvent = Get_Template( 'templates/'.$style['styletemplate'].'/event.html' );
 
 $r_event = db_query("SELECT
     caltime,
	 caltopic,
	 calautor,
	 caltext
 FROM ".$pref."calendar WHERE calid='$event'");
 if( db_rows( $r_event ) == 1 )
 {
     $event = db_result( $r_event );
	 
	 $data['datum'] = date( "d.m.Y (H:i\U\h\\r)", $event['caltime'] );
	 $data['event'] = parse_code($event['caltopic'], 1, 0, 0, $config['eventcode']);
	 $data['text'] = parse_code($event['caltext'], 1, 0, $config['eventcode'], $config['eventcode']);
	 $data['autor'] = '<a href="s_profile.php?username='.$event['calautor'].'" target="_blank">'.$event['calautor'].'</a>';
 }
 else
 {
     echo 'Fehler! Kein Event mit dieser ID gefunden.';
 }

 echo Output( Template ( $TEvent ) );
?>
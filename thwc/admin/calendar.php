<?php
/* $Id: calendar.php,v 1.1 2003/06/12 13:59:32 master_mario Exp $ */
 include( 'adhead.inc.php' );
 $data['work'] = '<b>Kalender</b><br /><br />';
 // FUNKTION
 function eventForm( $event, $action )
 {
     if( $action == 'addEvent' )
     {
         $event['aktiv'] = 1;
         $event['showasevent'] = 1;
     }

     $back = '<form action="calendar.php" method="post">
      <table cellpadding="4" cellspacing="0" border="0">
       <tr>
        <td style="width:200px"><b>Datum:</b></td>
        <td>&nbsp;</td>
        <td>
         <input type="text" name="event[day]" size="2" maxlength="2" value="'.( isset($event['day']) ? $event['day'] : 'TT' ).'" id="border-tab" />
         <input type="text" name="event[month]" size="2" maxlength="2" value="'.( isset($event['month']) ? $event['month'] : 'MM' ).'" id="border-tab" />
         <input type="text" name="event[year]" size="4" maxlength="4" value="'.( isset($event['year']) ? $event['year'] : 'JJJJ' ).'" id="border-tab" />
        </td>
       </tr>
       <tr>
        <td style="width:200px"><b>Uhrzeit:</b><br /><font size="1">Mu&szlig; nicht gesetzt werden<br />Format 24h</font></td>
        <td>&nbsp;</td>
        <td style="vertical-align:top">
         <input type="text" name="event[hours]" size="2" maxlength="2" value="'.( isset($event['hours']) ? $event['hours'] : 'hh' ).'" id="border-tab" />
         <input type="text" name="event[min]" size="2" maxlength="2" value="'.( isset($event['min']) ? $event['min'] : 'mm' ).'" id="border-tab" />
        </td>
       </tr>
       <tr>
        <td><b>Topic:</b></td>
        <td>&nbsp;</td>
        <td><input type="text" name="event[topic]" size="50" maxlength="50" value="'.( isset($event['caltopic']) ? $event['caltopic'] : '' ).'" id="border-tab" /></td>
       </tr>
       <tr>
        <td style="vertical-align:top"><b>Text:</b></td>
        <td>&nbsp;</td>
        <td><textarea name="event[text]" cols="50" rows="5" id="border-tab">'.( isset($event['caltext']) ? $event['caltext'] : '' ).'</textarea></td>
       </tr>
       <tr>
        <td><b>Aktiv?:</b></td>
        <td>&nbsp;</td>
        <td>
         <input type="radio" name="event[aktiv]" value="1"'.( $event['aktiv'] == 1 ? ' checked' : '' ).' />Ja&nbsp;
         <input type="radio" name="event[aktiv]" value="0"'.( $event['aktiv'] == 1 ? '' : ' checked' ).' />Nein
        </td>
       </tr>
       <tr>
        <td><b>Event?:</b><br /><font size="1">Wenn Ja wird der Eintrag<br />als Event auf Index gemeldet.</font></td>
        <td>&nbsp;</td>
        <td style="vertical-align:top">
         <input type="radio" name="event[event]" value="1"'.( $event['showasevent'] == 1 ? ' checked' : '' ).' />Ja&nbsp;
         <input type="radio" name="event[event]" value="0"'.( $event['showasevent'] == 1 ? '' : ' checked' ).' />Nein
        </td>
       </tr>
       <tr>
        <td colspan="3" style="text-align:center">
         <input type="hidden" name="action" value="'.$action.'" />
         <input type="hidden" name="event[calid]" value="'.( isset($event['calid']) ? $event['calid'] : '' ).'" />
         <input type="submit" value=" Senden " id="border-tab" />
        </td>
       </tr>
      </table>
     </form>';
     return $back;
 }
 // CODE
 if( $config['calendar'] == 0 )
 {
     $data['work'] .= '...ist nicht aktiviert. Aktivieren kannst Du ihn <a href="basics.php">hier</a>.';
 }
 else
 {
     // addEvent ----------------------------------------------------------
     if( $action == 'addEvent' )
     {
         $err = 0;
         if( $event['day'] < 1 || $event['day'] > 31 )
             $err = 1;
         if( $event['month'] < 1 || $event['month'] > 12 )
             $err = 1;
         if( $event['year'] < 1970 || $event['year'] > 2037 )
             $err = 1;
         if( $event['hours'] < 0 || $event['hours'] > 24 )
             $err = 1;
         if( $event['min'] < 0 || $event['min'] > 59 )
             $err = 1;
         $err_message = '';
         if( $err == 1 )
             $err_message .= 'Das Datum und/oder Uhrzeit ist nicht korrekt.';
         if( $event['topic'] == '' )
             $err_message .= '&nbsp;Du hast kein Topic angegeben.';
         if( $err_message != '' )
             $data['work'] .= $err_message;
         else
         {
             $time = mktime($event['hours'],$event['min'],0,$event['month'],$event['day'],$event['year']);
             db_query("INSERT INTO ".$pref."calendar SET
                 caltime='$time',
                 caltopic='".encode(addslashes($event['topic']))."',
                 calautor='$admin[user_name]',
                 caltext='".encode(addslashes($event['text']))."',
                 showasevent='$event[event]',
                 aktiv='$event[aktiv]'");
             $data['work'] .= '<font color="[color_err]">Calenderevent hinzugef&uuml;gt<br /></font>';
             $action = '';
         }
     }
     // deleEvent ---------------------------------------------------------
     if( $action == 'deleEvent' )
     {
         db_query("DELETE FROM ".$pref."calendar WHERE calid='$calid'");
         db_query("OPTIMIZE TABLE ".$pref."calendar");
         $data['work'] .= '<font color="[color_err]">Calenderevent gel&ouml;scht<br /></font>';
         $action = '';
     }
     // editEvent -------------------------------------------------------
     if( $action == 'editEvent' )
     {
         $r_event = db_query("SELECT
             *
         FROM ".$pref."calendar WHERE calid='$calid'");
         $event = db_result( $r_event );
         $event['day'] = date( "d", $event['caltime'] );
         $event['month'] = date( "m", $event['caltime'] );
         $event['year'] = date( "Y", $event['caltime'] );
         $event['hours'] = date( "H", $event['caltime'] );
         $event['min'] = date( "i", $event['caltime'] );

         $data['work'] .= '<b>Kalenderevent editieren</b><br />';
         $data['work'] .= eventForm( $event, 'updateEvent' );
     }
     // updateEvent ----------------------------------------------------------
     if( $action == 'updateEvent' )
     {
         $err = 0;
         if( $event['day'] < 1 || $event['day'] > 31 )
             $err = 1;
         if( $event['month'] < 1 || $event['month'] > 12 )
             $err = 1;
         if( $event['year'] < 1970 || $event['year'] > 2037 )
             $err = 1;
         if( $event['hours'] < 0 || $event['hours'] > 24 )
             $err = 1;
         if( $event['min'] < 0 || $event['min'] > 59 )
             $err = 1;
         $err_message = '';
         if( $err == 1 )
             $err_message .= 'Das Datum und/oder Uhrzeit ist nicht korrekt.';
         if( $event['topic'] == '' )
             $err_message .= '&nbsp;Du hast kein Topic angegeben.';
         if( $err_message != '' )
             $data['work'] .= $err_message;
         else
         {
             $time = mktime($event['hours'],$event['min'],0,$event['month'],$event['day'],$event['year']);
             db_query("UPDATE ".$pref."calendar SET
                 caltime='$time',
                 caltopic='".encode(addslashes($event['topic']))."',
                 calautor='$admin[user_name]',
                 caltext='".encode(addslashes($event['text']))."',
                 showasevent='$event[event]',
                 aktiv='$event[aktiv]'
             WHERE calid='$event[calid]'");
             $data['work'] .= '<font color="[color_err]">Calenderevent editiert<br /></font>';
             $action = '';
         }
     }
     // list events -------------------------------------------------------
     if( $action == '' )
     {
         $r_events = db_query("SELECT
             calid,
             caltime,
             caltopic,
             calautor
         FROM ".$pref."calendar ORDER BY caltime ASC");

         $data['work'] .= '<table width="100%" cellpadding="4" cellspacing="0" border="0">
          <tr>
           <td><i>Event</i></td>
           <td>&nbsp;</td>
           <td>&nbsp;</td>
           <td>&nbsp;</td>
           <td><i>Von</i></td>
           <td>&nbsp;</td>
           <td><i>Optionen</i></td>
          </tr>
         ';
         if( db_rows( $r_events ) > 0 )
         {
             $i=0;
             while( $a_events = db_result( $r_events ) )
             {
                 $data['work'] .= '<tr bgcolor="'.( $i % 2 == 0 ? '#DADADA' : '' ).'">
                  <td>'.date( "d.m.Y H:i\U\h\\r", $a_events['caltime'] ).( $a_events['caltime'] < $board_time ? ' <font color="[color_err]" size="1">(alt)</font>' : '' ).'</td>
                  <td>&nbsp;</td>
                  <td>'.$a_events['caltopic'].'</td>
                  <td>&nbsp;</td>
                  <td>'.$a_events['calautor'].'</td>
                  <td>&nbsp;</td>
                  <td>
                   <a href="calendar.php?action=editEvent&calid='.$a_events['calid'].'">Editieren</a> |
                   <a href="calendar.php?action=deleEvent&calid='.$a_events['calid'].'">L&ouml;schen</a>
                  </td>
                 </tr>';
                 $i++;
             }
         }
         $data['work'] .= '</table><hr>';
         // new Event ---------------------------------------------------------------
         $data['work'] .= '<b>Neues Kalenderevent</b><br />';
         $data['work'] .= eventForm( array(), 'addEvent' );
     }
 }
 $data['javascript'] = '';
 echo Template( Get_Template( 'templates/admin.html' ) );
 tb_footer();
?>
<?php
 /* $Id: calendar.php,v 1.1 2003/06/26 13:46:47 master_mario Exp $ */
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
 // nav_path ------------------------------------------
 $data['nav_path'] .= '&nbsp;&gt;&gt;&nbsp;Kalender';
 // global Templates ----------------------------------
 $TBoard = Get_Template( 'templates/'.$style['styletemplate'].'/board.html' );
 $TCal = Get_Template( 'templates/'.$style['styletemplate'].'/calendar.html' );
 // Permissions ---------------------------------------
 if( U_ID < 1 && $config['guest_calenda'] == 0 )
     message( 'Der Kalender wurde vom Administrator f&uuml;r G&auml;ste deaktiviert.', 'Rechte', 0 );
 if( $config['calendar'] == 0 )
     message( 'Der Kalender wurde vom Administrator deaktiviert.', 'Kalender', 0 );
 // ----------------------------------------------------
 $month_array = array(
     1 => 'Jannuar',
	 2 => 'Februar',
	 3 => 'M&auml;rz',
	 4 => 'April',
	 5 => 'Mai',
	 6 => 'Juni',
	 7 => 'Juli',
	 8 => 'August',
	 9 => 'September',
	 10 => 'Oktober',
	 11 => 'November',
	 12 => 'Dezember');
 // -----------------------------------------------------
 $datum = date( "d.m.Y", $board_time );
 $data['datum'] = $datum;
 $data['month'] = date( "m", $board_time );
 $data['year'] = date( "Y", $board_time );
 
 if( !isset( $m ) )
     $m = date( "n", $board_time );
 if( !isset( $y ) )
     $y = date( "Y", $board_time );
 $data['thisyear'] = $y;
 $data['monthyear'] = $month_array[$m].' '.$y;
 
 $data['lastmonth'] = $m-1;
 if( $data['lastmonth'] < 1 )
     $data['lastmonth'] == 12;
	 
 $data['nextmonth'] = $m+1;
 if( $data['nextmonth'] > 12 )
     $data['nextmonth'] == 1;
 
 // -------- Month list ----------------------------------
 $data['monate'] = '<select name="m" size="1" id="border-tab">';
 foreach( $month_array as $key=>$monat )
 {
     $data['monate'] .= '<option value="'.$key.'" '.( $key == $m ? 'selected' : '' ).'>'.$monat.'</option>';
 }
 $data['monate'] .= '</select>';
 // -------- Year list ----------------------------------
 $data['jahre'] = '<select name="y" size="1" id="border-tab">';
 for( $x = 1975; $x < 2026; $x++ )
 {
     $data['jahre'] .= '<option value="'.$x.'" '.( $x == $y ? 'selected' : '' ).'>'.$x.'</option>';
 }
 $data['jahre'] .= '</select>';
 // -- first day of month -------------
 $first = date( 'w', mktime(0,0,0,$m,1,$y));
 $last = 28;
 while (checkdate($m,$last,$y))
 {
   $last++;
 }
 $last--;
 // -------------- calendar ---------------
 $data['calendar'] = '';
 $lastrow = 0;
 $cells = 0;
 $days = 0;
 while( $lastrow == 0 )
 {
      $data['calendar'] .= '<tr>';
	  for( $x = 0; $x < 7; $x++ )
	  {
	      $cells++;
	      $data['calendar'] .= '<td class="cellb" height="70" style="vertical-align:top">';
		  if( $cells > $first && $days < $last )
		  {
		      $days++;
			  $data['calendar'] .= '<b>'.$days.'</b>';
			  // time index -------------------------
			  $start = mktime(23,59,59,$m,$days-1,$y);
			  $end   = mktime(0,0,0,$m,$days+1,$y);
			  // event ------------------------------
			  $r_event = db_query("SELECT 
			      calid,
				  caltime,
				  caltopic
			  FROM ".$pref."calendar WHERE caltime>'$start' AND caltime<'$end' AND aktiv='1'");
			  if( db_rows( $r_event ) > 0 )
			  {
			      while( $event = db_result( $r_event ) )
				  {
				      $data['calendar'] .= "<br />[smallfont][ <a href=# onclick=\"window.open('event.php?event=$event[calid]','show_event','width=400,height=500,scrollbars=yes,menubar=no,toolbar=no,statusbar=no')\">".$event['caltopic']."</a> ][smallfontend]";
				  }
			  }
			  // user -------------------------------
			  if( U_ID > 0 || ( U_ID < 1 && $config['guest_memberlist'] == 1 ) )
			  $r_user = db_query("SELECT 
			      user_name,
				  user_bday,
				  user_bday_year
			  FROM ".$pref."user WHERE user_bday='".( $days < 10 ? '0'.$days : $days ).'-'.( $m < 10 ? '0'.$m : $m )."'");
			  if( db_rows( $r_user ) > 0 )
			  {
			      while( $user = db_result( $r_user ) )
				  {
                 	  $dat = explode( '-', $user['user_bday'] ); 
            	      $bday = mktime(0,0,0,$dat[1],$dat[0],$user['user_bday_year']);  
                 	  $t = mktime(0,0,0,$m,$days,$y) - $bday;
	                  $t = @bcdiv( $t, 31547600, 0 );
				      $data['calendar'] .= '<br />[smallfont]<a href="s_profile.php?username='.$user['user_name'].'" target="_blank">'.$user['user_name'].'</a> ('.$t.')[smallfontend]';
				  }
			  }
		  }
	      $data['calendar'] .= '</td>';
		  if( $days >= $last )
		      $lastrow = 1;
	  }
      $data['calendar'] .= '</tr>';
 } 
 $data['boardtable'] = Template( $TCal );
 echo Output( Template ( $TBoard ) );
?>
<?php
 /* $Id: s_profile.php,v 1.3 2003/07/01 16:33:49 master_mario Exp $ */
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
 // global Templates
 $TBoard = Get_Template( 'templates/'.$style['styletemplate'].'/board.html' );
 $TProfile = Get_Template( 'templates/'.$style['styletemplate'].'/s_profile.html' ); 
 // Function color
 function color( $i )
 {
     if( $i % 2 == 0 )
	     $back = '[CellA]';
	 else
	     $back = '[CellB]';
	 return $back;
 }
 // nav_path---------
 $data['nav_path'] .= '&nbsp;&gt;&gt;&nbsp;Userprofil';
 // nur für registrierte Mitglieder ---
 if( U_ID < 1 )
     message( 'Sorry! Profile k&ouml;nnen nur von registrierten Mitgliedern ge&ouml;ffnet werden.', 'Rechte', 0 );
 // daten lesen ----------
 $r_user = db_query("SELECT
     user_name,
	 user_mail,
	 user_nomail,
	 user_title,
	 user_isadmin,
	 user_ismod,
	 user_lastpos,
	 user_lastacttime,
	 post_count,
	 user_lasttopic,
	 user_lastpostt,
	 user_avatar,
	 user_join,
	 signatur,
	 user_ishidden,
	 user_icq,
	 user_hp,
	 user_ort,
	 user_aim,
	 user_msn,
	 user_interests,
	 user_lastpostid,
	 user_bday,
	 user_bday_year,
	 user_job
 FROM ".$pref."user WHERE user_name='$username'");
 $profile = db_result( $r_user ); 
 // daten auswerten
 $i=0;
 $table = '';
 $line = '<tr bgcolor="[bgcolor]">
  <td style="width:150px; vertical-align:top"><b>&nbsp;[name]</b></td>
  <td>&nbsp;[value]</td>
 </tr>';
 // Name
 $row = str_replace( '[bgcolor]', color($i), $line );
 $row = str_replace( '[name]', 'Name', $row );
 $row = str_replace( '[value]', $profile['user_name'].'<br />&nbsp;[smallfont]'.$profile['user_title'].'[smallfontend]', $row );
 $table .= $row;
 $i++;
 // Beiträge
 $row = str_replace( '[bgcolor]', color($i), $line );
 $row = str_replace( '[name]', 'Beitr&auml;ge', $row );
 $row = str_replace( '[value]', $profile['post_count'], $row );
 $table .= $row;
 $i++;
 // E-Mail
 $row = str_replace( '[bgcolor]', color($i), $line );
 $row = str_replace( '[name]', 'E-Mail', $row );
 if( $profile['user_nomail'] == 1 && U_ISADMIN == 0 && U_ISMOD == 0 )
     $row = str_replace( '[value]', '- Versteckt', $row );
 else
     $row = str_replace( '[value]', '<a href="mailto:'.$profile['user_mail'].'">'.$profile['user_mail'].'</a>', $row );
 $table .= $row;
 $i++;
 // Registrierung
 $row = str_replace( '[bgcolor]', color($i), $line );
 $row = str_replace( '[name]', 'Registrierdatum', $row );
 $row = str_replace( '[value]', datum($profile['user_join']), $row );
 $table .= $row;
 $i++;
 // last Post time
 if( $profile['user_lastpostt'] != 0 )
 {
     $row = str_replace( '[bgcolor]', color($i), $line );
     $row = str_replace( '[name]', 'Letzter Beitrag [smallfont](Zeit)[smallfontend]', $row );
     $row = str_replace( '[value]', datum($profile['user_lastpostt']), $row );
     $table .= $row;
     $i++;
 }
 // last Post
 if( $profile['user_lastpostid'] != 0 )
 {
     $row = str_replace( '[bgcolor]', color($i), $line );
     $row = str_replace( '[name]', 'Letzter Beitrag', $row );
     $row = str_replace( '[value]', '<a href="findpost.php?postid='.$profile['user_lastpostid'].'">'.$profile['user_lasttopic'].'</a>', $row );
     $table .= $row;
     $i++;
 }
 // optionen
 $row = str_replace( '[bgcolor]', color($i), $line );
 $row = str_replace( '[name]', 'Optionen', $row );
 $value = '';
 if( $config['pm'] == 1 )
     $value .= '<a href="pm.php?action=new&username='.$profile['user_name'].'">'.$profile['user_name'].' eine Private Nachricht schicken</a>';
 $value .= ( $value != '' ? '<br />' : '' ).'&nbsp;<a href="search.php?lookfor=autor&word='.$profile['user_name'].'&search=1">Nach weiteren Posts von mario suchen</a>';
 $row = str_replace( '[value]', $value, $row );
 $table .= $row;
 $i++;
 // Userposition
 if( $profile['user_ishidden'] == 0 || ( $profile['user_ishidden'] == 1 && P_CANSEEINVIS == 1 ) )
 {
     $pos = new Position( $pref );
     $row = str_replace( '[bgcolor]', color($i), $line );
     $row = str_replace( '[name]', 'Letzte Action', $row );
     $row = str_replace( '[value]', $pos->check_position( $profile['user_lastpos'] ), $row );
     $table .= $row;
     $i++;
 }
 // Rang
 $row = str_replace( '[bgcolor]', color($i), $line );
 $row = str_replace( '[name]', 'Rang', $row );
 if( $config['ranks'] == 0 )
     $row = str_replace( '[value]', '- Vom Administrator deaktiviert', $row );
 else
 {
     $r_rank = db_query("SELECT
	     *
	 From ".$pref."ranks ORDER BY post_counts ASC");
	 if( db_result( $r_rank ) == 0 )
	     $row = str_replace( '[value]', '- Keine R&auml;nge definiert', $row );
	 else
	 {
	     $rank = array();
	     while( $a_rank = db_result( $r_rank ) )
		 {
		     $rank[] = array( 'count' => $a_rank['post_counts'],
                 'title' => $a_rank['ranktitle'],
				 'image' => $a_rank['rankimage'] );
		 }
    	 mysql_free_result( $r_rank );
    	 unset( $a_rank );
		 foreach( $rank as $val )
		 {
			if( $profile['post_count'] > $val['count'] )
		    {
			    $user_rank = $val['title'];
				break;
            }
		 } 
		 if( $val['image'] != '' )
		     $user_rank .= '<br/ >&nbsp;<img src="'.$val['image'].'" border="0" />';
	     $row = str_replace( '[value]', $user_rank, $row );
	 }
 }
 $table .= $row;
 $i++;
 // avatar
 if( $profile['user_avatar'] != '' && $profile['user_avatar'] != 'nicht erlaubt' )
 {
     $row = str_replace( '[bgcolor]', color($i), $line );
     $row = str_replace( '[name]', 'Avatar', $row );
     $row = str_replace( '[value]', '<img src="'.$profile['user_avatar'].'" border="0" />', $row );
     $table .= $row;
     $i++;
 }
 // hp
 if( $profile['user_hp'] != '' && $profile['user_hp'] != 'http://' )
 {
     $row = str_replace( '[bgcolor]', color($i), $line );
     $row = str_replace( '[name]', 'Homepage', $row );
     $row = str_replace( '[value]', '<a href="'.$profile['user_hp'].'">'.$profile['user_hp'].'</a>', $row );
     $table .= $row;
     $i++;
 }
 // Wohnort
 if( $profile['user_ort'] != '' )
 {
     $row = str_replace( '[bgcolor]', color($i), $line );
     $row = str_replace( '[name]', 'Wohnort', $row );
     $row = str_replace( '[value]', $profile['user_ort'], $row );
     $table .= $row;
     $i++;
 }
 // icq
 if( $profile['user_icq'] != 0 )
 {
     $row = str_replace( '[bgcolor]', color($i), $line );
     $row = str_replace( '[name]', '#ICQ', $row );
     $row = str_replace( '[value]', intval($profile['user_icq']), $row );
     $table .= $row;
     $i++;
 }
 // aim
 if( $profile['user_aim'] != '' )
 {
     $row = str_replace( '[bgcolor]', color($i), $line );
     $row = str_replace( '[name]', 'AIM Name', $row );
     $row = str_replace( '[value]', $profile['user_aim'], $row );
     $table .= $row;
     $i++;
 }
 // msn
 if( $profile['user_msn'] != '' )
 {
     $row = str_replace( '[bgcolor]', color($i), $line );
     $row = str_replace( '[name]', 'MSN Name', $row );
     $row = str_replace( '[value]', $profile['user_msn'], $row );
     $table .= $row;
     $i++;
 }
 // alter
 if( $profile['user_bday'] != '00-00' && $profile['user_bday_year'] != '0000' )
 {
     $row = str_replace( '[bgcolor]', color($i), $line );
     $row = str_replace( '[name]', 'Alter', $row );
	 $dat = explode( '-', $profile['user_bday'] ); 
	 $bday = mktime(0,0,0,$dat[1],$dat[0],$profile['user_bday_year']);
	 $t = $board_time - $bday;
	 $t = @bcdiv( $t, 31547600, 0 );
     $row = str_replace( '[value]', $t, $row );
     $table .= $row;
     $i++;
 }
 // beruf
 if( $profile['user_job'] != '' )
 {
     $row = str_replace( '[bgcolor]', color($i), $line );
     $row = str_replace( '[name]', 'Beruf', $row );
     $row = str_replace( '[value]', $profile['user_job'], $row );
     $table .= $row;
     $i++;
 }
 // interessen
 if( $profile['user_interests'] != '' )
 {
     $row = str_replace( '[bgcolor]', color($i), $line );
     $row = str_replace( '[name]', 'Interessen', $row );
	 $string = str_replace("\n", '<br>&nbsp;', $profile['user_interests']);
     $row = str_replace( '[value]', $string, $row );
     $table .= $row;
     $i++;
 }
 // signatur
 if( $profile['signatur'] != '' )
 {
     $row = str_replace( '[bgcolor]', color($i), $line );
     $row = str_replace( '[name]', 'Signatur', $row );
	 $string = str_replace("\n", '<br>&nbsp;', $profile['signatur']);
     $row = str_replace( '[value]', $string, $row );
     $table .= $row;
     $i++;
 }
 

 
 
 $TProfile = str_replace( '[table]', $table, $TProfile );
 $data['boardtable'] = Template( $TProfile );
 echo Output( Template ( $TBoard ) );
?>
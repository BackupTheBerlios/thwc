<?php
 /* $Id: pm.php,v 1.1 2003/06/24 17:10:25 master_mario Exp $ */
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
 include ( 'inc/tagbar.inc.php' );
 include ( 'inc/bcode.inc.php' );
 
 $TBoard = Get_Template( 'templates/'.$style['styletemplate'].'/board.html' );
 $data['nav_path'] .= '&nbsp;&gt;&gt;&nbsp;<a href="pm.php" class="bg">Privatnachrichten</a>';

 if( U_ID == 0 )
     message( 'Das ist eine Mitgliederfunktion. <a href="register.php">Hier</a> kannst Du Dich registrieren.', 'Rechte', 0 );
 if( $config['pm'] == 0 )
     message( 'Private Messages sind vom Administrator deaktiviert.', 'Rechte', 0 );
 // autodelete
 if( $config['max_pm_life'] > 0 )
 {
     db_query("DELETE FROM ".$pref."pm WHERE pm_empf='".U_NAME."' AND pm_saved='0' AND pm_time<'".($board_time-(86400*$config['max_pm_life']))."'");
	 db_query("OPTIMIZE TABLE ".$pref."pm");
 }
 // action == '' --- Übersicht -------------
 if( !isset( $action ) )
 {
     $data['nav_path'] .= '&nbsp;&gt;&gt;&nbsp;&Uuml;bersicht';
     $TPm = Get_Template( 'templates/'.$style['styletemplate'].'/mcenter.html' );  
	 $data['in_rows'] = '';
	 // Posteingang
	 $data['in_count'] = 0;
     $data['full_count'] = $config['max_pm_count'];
	 $r_in = db_query("SELECT
	     pm_id,
		 pm_autor,
		 pm_empf,
		 pm_time,
		 pm_topic,
		 pm_gelesen,
		 pm_antwort,
		 pm_saved
	 FROM ".$pref."pm WHERE pm_empf='".U_NAME."' ORDER BY pm_time DESC");
	 if( db_rows( $r_in ) < 1 )
	 {
	     $data['in_rows'] = '<tr><td class="cellb" colspan="7"><center>[smallfont]Keine Nachrichten im Posteingang.[smallfontend]</center></td></tr>';
		 $data['proz'] = 0;
	 }
	 else
	 {
	     $TMinrow = Get_Template( 'templates/'.$style['styletemplate'].'/minrow.html' );  
		 $i=0;
		 while( $in = db_result( $r_in ) )
		 {
		     $icon = 'fullalpha_new';
			 if( $in['pm_gelesen'] == 1 )
			     $icon = 'fullalpha';
			 $icon = '<img src="templates/'.$style['styletemplate'].'/images/icon/'.$icon.'.gif" border="0" />';
			 $back = 'pm_back';
			 $back_alt = '';
			 if( $in['pm_antwort'] == 1 )
			 {
			     $back = 'pm_back_ok';
				 $back_alt = '(ist bereits beantwortet)';
			 }
			 $saved = '<a href="pm.php?action=save&pmid='.$in['pm_id'].'"><img src="templates/'.$style['styletemplate'].'/images/saved_no.gif" border="0" width="15" height="15" alt="Nachricht ungeschützt" /></a>';
			 if( $in['pm_saved'] == 1 )
			     $saved = '<a href="pm.php?action=save&pmid='.$in['pm_id'].'"><img src="templates/'.$style['styletemplate'].'/images/saved.gif" border="0" width="15" height="15" alt="Nachricht vor automatischem Löschen gesichert" /></a>';
		     $row = $TMinrow;
		     $row = str_replace( '[icon]', $icon, $row );
		     $row = str_replace( '[cellcol]', ( $i % 2 == 0 ? '[CellA]' : '[CellB]' ), $row );
		     $row = str_replace( '[topic]', $in['pm_topic'], $row );
		     $row = str_replace( '[autor]', $in['pm_autor'], $row );
		     $row = str_replace( '[time]', datum( $in['pm_time'] ), $row );
		     $row = str_replace( '[back]', $back, $row );
		     $row = str_replace( '[back_alt]', $back_alt, $row );
		     $row = str_replace( '[saved]', $saved, $row );
		     $row = str_replace( '[pmid]', $in['pm_id'], $row );
		     $data['in_rows'] .= $row;
			 $i++;
		 }
		 $data['in_count'] = $i;
		 $data['proz'] = proz( $config['max_pm_count'], $i );
	 }  
	 $data['out_rows'] = '';
	 // postausgang	 
	 $data['out_count'] = 0;
	 $r_out = db_query("SELECT
	     pm_id,
		 pm_autor,
		 pm_empf,
		 pm_time,
		 pm_topic,
		 pm_gelesen,
		 pm_antwort,
		 pm_saved
	 FROM ".$pref."pm WHERE pm_autor='".U_NAME."' AND pm_outbox='1' ORDER BY pm_time DESC");
	 if( db_rows( $r_out ) < 1 )
	     $data['out_rows'] = '<tr><td class="cellb" colspan="7"><center>[smallfont]Keine Nachrichten im Postausgang.[smallfontend]</center></td></tr>';
	 else
	 {
	     $TMoutrow = Get_Template( 'templates/'.$style['styletemplate'].'/moutrow.html' );  
		 $i=0;
		 while( $out = db_result( $r_out ) )
		 {
		     $icon = '<img src="templates/'.$style['styletemplate'].'/images/space.gif" border="0" />';
			 if( $out['pm_gelesen'] == 1 )
			     $icon = '<img src="templates/'.$style['styletemplate'].'/images/read.gif" border="0" />';			 $back = 'pm_back';
			 $back = 'pm_back';
			 $back_alt = '';
			 if( $out['pm_antwort'] == 1 )
			 {
			     $back = 'pm_back_ok';
				 $back_alt = '(ist bereits beantwortet)';
			 }
			 
		     $row = $TMoutrow;
		     $row = str_replace( '[icon]', $icon, $row );
		     $row = str_replace( '[cellcol]', ( $i % 2 == 0 ? '[CellA]' : '[CellB]' ), $row );
		     $row = str_replace( '[topic]', $out['pm_topic'], $row );
		     $row = str_replace( '[empf]', $out['pm_empf'], $row );
		     $row = str_replace( '[time]', datum( $out['pm_time'] ), $row );
		     $row = str_replace( '[back]', $back, $row );
		     $row = str_replace( '[back_alt]', $back_alt, $row );
		     $row = str_replace( '[pmid]', $out['pm_id'], $row );
		     $data['out_rows'] .= $row;
			 $i++;		 
		 }
		 $data['out_count'] = $i;
	 }
 }
 // new ----------------------
 elseif( $action == 'new' )
 {
     $data['nav_path'] .= '&nbsp;&gt;&gt;&nbsp;Neue Nachricht';
	 if( !isset( $send ) )
	 {
	     $TPm = Get_Template( 'templates/'.$style['styletemplate'].'/pm.html' );
	     if( !isset($username) )
	         $username = '';
		 if( !isset( $back ) )
		 {
			 $mail = '';
			 if( $config['mail_func'] == 1 )
			     $mail = '<input type="radio" name="art" value="1" />&nbsp;E-Mail';
			 $topic = '';
			 $text = '';
			 $copy = '';
			 if( isset( $more ) )
			 {
			     $r_pm = db_query("SELECT
				     pm_autor,
					 pm_empf,
					 pm_topic,
					 pm_text
				 FROM ".$pref."pm WHERE pm_id='$pmid'");
	             if( db_rows( $r_pm ) != 1 )
	                 message( 'Sorry! Es gibt keine PM mit dieser ID.', 'Fehler', 0 );
	             $pm = db_result( $r_pm );
	             if( $pm['pm_autor'] != U_NAME && $pm['pm_empf'] != U_NAME )
	                 message( 'Du bist nicht berechtigt diese Nachricht zu verwenden.', 'Rechte', 0 );
				 switch( $more )
				 {
				     case 'reply':
					 {
					     $username = $pm['pm_autor'];
						 if( $username == U_NAME )
						     $username = $pm['pm_empf'];
						 $topic = $pm['pm_topic'];
						 $toptest = substr( $topic, 0, 2 );
						 if( $toptest != 'Re:' )
						     $topic = 'Re: '.$topic;
						 break;
					 }
					 case 'zit':
					 {
						 $topic = $pm['pm_topic'];
						 $toptest = substr( $topic, 0, 2 );
						 if( $toptest != 'Fw:' )
						     $topic = 'Fw: '.$topic;
						 $text = '[quote][i]Zitat von '.$pm['pm_autor'].'[/i]
'.$pm['pm_text'].'[/quote]';
						 break;					     
					 }
					 case 'quote':
					 {
					     $username = $pm['pm_autor'];
						 if( $username == U_NAME )
						     $username = $pm['pm_empf'];
						 $topic = $pm['pm_topic'];
						 $toptest = substr( $topic, 0, 2 );
						 if( $toptest != 'Re:' )
						     $topic = 'Re: '.$topic;
						 $text = '[quote][i]Zitat von '.$pm['pm_autor'].'[/i]
'.$pm['pm_text'].'[/quote]';
						 break;		
					 }
					 $username = '';
			         $topic = '';
			         $text = '';					 
				 }
			 }
		     $pm = 'checked';
		 }
		 else
		 {
		     $pm = '';
		     if( $art == 0 )
			     $pm = 'checked';
			 $mail_yes = '';
			 if( $art == 1 )
			     $mail_yes = 'checked';
			 $mail = '';
			 if( $config['mail_func'] == 1 )
			     $mail = '<input type="radio" name="art" value="1" '.$mail_yes.' />&nbsp;E-Mail';
			 if( $copy == 1 )
			     $copy = 'checked';
			 else
			     $copy = '';
		 }
	     $TPm = str_replace( '[username]', $username, $TPm );
	     $TPm = str_replace( '[pm]', $pm, $TPm );
	     $TPm = str_replace( '[mail]', $mail, $TPm );
	     $TPm = str_replace( '[topic]', $topic, $TPm );
	     $TPm = str_replace( '[tagbar]', $tagbar, $TPm );
	     $TPm = str_replace( '[text]', $text, $TPm );
	     $TPm = str_replace( '[copy]', $copy, $TPm );
	 }
	 else
	 {
	     // usereingaben checken ---------------------------
		 $err_mess = '';
		 $r_user = db_query("SELECT 
		     user_id,
		     user_name,
			 user_mail,
			 pm_count,
			 groupids
		 FROM ".$pref."user WHERE user_name='".addslashes($username)."'");
		 if( db_rows( $r_user ) != 1 )
		 {
		     $err_mess .= 'Es ist kein User mit diesem Namen registriert.';
		 }
		 else
		 {
		     $user = db_result( $r_user );
			 if( $user['user_id'] == U_ID )
			     $err_mess .= 'Du kannst keine Nachricht an Dich selbst senden.';
		     if( $art == 1 && ( $user['user_mail'] == '' || $config['mail_func'] == 0 ) )
			 {
			     $err_mess .= ( $err_mess == '' ? '' : '<br />' ).'Ein Versand per E-Mail ist leider nicht m&ouml;glich.';
			 }
			 $err_mess .= ( $err_mess == '' ? '' : '<br />' ).check_string( $topic, 1 );
			 if( strlen( $text ) < 3 )
			     $err_mess .= ( $err_mess == '' ? '' : '<br />' ).'Der Text ist zu kurz.';
			 if( strlen( $text ) > $config['max_pm_len'] )
			     $err_mess .= ( $err_mess == '' ? '' : '<br />' ).'Der Text ist zu lang.';
			 if( $user['pm_count'] == $config['max_pm_count'] )
			 {
			     $P = globalPermissions ( $user['groupids'] );
				 if( $P[19] == 0 )
				 {
			         $err_mess .= ( $err_mess == '' ? '' : '<br />' ).'Die PMbox des Empf&auml;ngers ist leider voll.';
					 db_query("UPDATE ".$pref."user SET
					     pm_overflow='1'
					 WHERE user_id='$user[user_id]'");
				 }
			 }
		 }
		 if( $err_mess != '' )
		 {
		     // back -----------------
			 $mess = '<form action="pm.php" method="post" name="sendback">
			  '.$err_mess.'
			  <input type="hidden" name="action" value="new" />
			  <input type="hidden" name="back" value="1" />
			  <input type="hidden" name="username" value="'.$username.'" />
			  <input type="hidden" name="art" value="'.$art.'" />
			  <input type="hidden" name="topic" value="'.$topic.'" />
			  <input type="hidden" name="text" value="'.$text.'" />
			  <input type="hidden" name="copy" value="'.( isset( $copy ) ? 1 : 0 ).'" />
			 </form>';
			 message( $mess, 'Volgende Fehler sind aufgetreten', 1 );
		 }
		 else
		 {
		     // senden ----------------
			 $more = '';
			 if( isset( $copy ) )
			     $more = ", pm_outbox='1'";
			 db_query("INSERT INTO ".$pref."pm SET
			     pm_autor='".U_NAME."',
				 pm_empf='".$user['user_name']."',
				 pm_time='$board_time',
				 pm_topic='".addslashes($topic)."',
				 pm_text='".addslashes($text)."'
				 ".$more." ");
			 db_query("UPDATE ".$pref."user SET
			     pm_new='1',
				 pm_count='".($user['pm_count']+1)."'
			 WHERE user_id='$user[user_id]'");
             message_redirect('Die Nachricht wurde versand, bitte warten ...', 'pm.php' );
		 }
	 }
 }
 // pm lesen --------------------
 elseif( $action == 'read' )
 {
     $data['nav_path'] .= '&nbsp;&gt;&gt;&nbsp;&Privatnachricht lesen';
	 $r_pm = db_query("SELECT
	     pm_id,
		 pm_autor,
		 pm_text,
		 pm_empf,
		 pm_time,
		 pm_topic,
		 pm_gelesen,
		 pm_antwort,
		 pm_saved
	 FROM ".$pref."pm WHERE pm_id='$pmid'");
	 if( db_rows( $r_pm ) != 1 )
	     message( 'Sorry! Es gibt keine PM mit dieser ID.', 'Fehler', 0 );
	 $pm = db_result( $r_pm );
	 if( $pm['pm_autor'] != U_NAME && $pm['pm_empf'] != U_NAME )
	     message( 'Du bist nicht berechtigt diese Nachricht zu lesen.', 'Rechte', 0 );
	 
	 db_query("UPDATE ".$pref."pm SET
	     pm_gelesen='1'
	 WHERE pm_id='$pmid'");
	 
	 $TPm = Get_Template( 'templates/'.$style['styletemplate'].'/pm_read.html' );
	 
	 $TPm = str_replace( '[pmid]', $pm['pm_id'], $TPm );
	 $TPm = str_replace( '[autor]', '<a href="s_profile.php?username='.$pm['pm_autor'].'">'.$pm['pm_autor'].'</a>', $TPm );
	 $TPm = str_replace( '[empf]', '<a href="s_profile.php?username='.$pm['pm_empf'].'">'.$pm['pm_empf'].'</a>', $TPm );
	 $TPm = str_replace( '[datum]', datum( $pm['pm_time'] ), $TPm );
	 $TPm = str_replace( '[topic]', $pm['pm_topic'], $TPm );
	 $TPm = str_replace( '[text]', parse_code($pm['pm_text'], 1, 1, 1, $config['smilies'] ), $TPm );
	 	 
	 // user update
	 $r_pm = db_query("SELECT
	     COUNT(pm_id)
	 FROM ".$pref."pm WHERE pm_empf='".U_NAME."' AND pm_gelesen='0'");
	 $pm = db_result( $r_pm );
	 list(, $new ) = each( $pm );
	 db_query("UPDATE ".$pref."user SET
		 pm_new='".( $new == 0 ? 0 : 1 )."'
	 WHERE user_id='".U_ID."'");	 
 }
 // delete
 elseif( $action == 'delete' )
 {
     if( isset( $all ) )
	 {
         $r_pm = db_query("SELECT
	         pm_id
	     FROM ".$pref."pm WHERE pm_autor='".U_NAME."' OR pm_empf='".U_NAME."'");
	     $pmdel = array();
	     if( db_rows( $r_pm ) > 0 )
	     {
	         while( $pm = db_result( $r_pm ) )
		     {
		         $pmdel[] = $pm['pm_id'];
		     }
	     }
	 }
     if( !isset( $pmdel ) )
	     message( 'Du hast keine zu l&ouml;schende PM ausgw&auml;hlt.', 'Fehler', 0 );
	 $no_delete = 0;
	 if( !is_array( $pmdel ) )
	 {
	     $pm = $pmdel;
	     $pmdel = array();
		 $pmdel[] = $pm;
	 }
	 foreach( $pmdel as $value )
	 {
	     $r_pm = db_query("SELECT 
			 pm_autor,
			 pm_empf,
			 pm_saved
		 FROM ".$pref."pm WHERE pm_id='$value'");
		 if( db_rows( $r_pm ) != 1 )
		     continue;
		 $pm = db_result( $r_pm );
		 if( $pm['pm_autor'] == U_NAME )
		 {
		     db_query("UPDATE ".$pref."pm SET
			     pm_outbox='0'
			 WHERE pm_id='$value'");
		 }
		 if( $pm['pm_empf'] == U_NAME )
		 {
		     if( $pm['pm_saved'] == 1 )
			 {
			     $no_delete = 1;
				 continue;
			 }
			 else
			 {
			     db_query("DELETE FROM ".$pref."pm WHERE pm_id='$value'");
			 }
		 }
	 }
	 db_query("OPTIMIZE TABLE ".$pref."pm");
	 
	 // user update
	 $r_user = db_query("SELECT
	     pm_count
	 FROM ".$pref."user WHERE user_id='".U_ID."'");
	 $user = db_result( $r_user );
	 $r_pm = db_query("SELECT
	     COUNT(pm_id)
	 FROM ".$pref."pm WHERE pm_empf='".U_NAME."'");
	 $pm = db_result( $r_pm );
	 list(, $pm_count ) = each( $pm );
	 $r_pm = db_query("SELECT
	     COUNT(pm_id)
	 FROM ".$pref."pm WHERE pm_empf='".U_NAME."' AND pm_gelesen='0'");
	 $pm = db_result( $r_pm );
	 list(, $pm_noread ) = each( $pm );
	 
	 db_query("UPDATE ".$pref."user SET
	     pm_count='$pm_count',
		 pm_overflow='".( $pm_count <= $config['max_pm_count'] ? 0 : 1 )."',
		 pm_new='".( $pm_noread == 0 ? 0 : 1 )."'
	 WHERE user_id='".U_ID."'");
	 
	 if( $no_delete == 1 )
	     message( '<form action="pm.php" method="post" name="weiter">
		  Mindestens eine der gew&auml;lten PM wurde durch Dich gesch&uuml;tzt.</form>', 'Gesch&uuml;tzte PM gefunden', 2 );
     message_redirect('PM gel&ouml;scht, bitte warten ...', 'pm.php' );
 }
 // save
 elseif( $action == 'save' )
 {
     $r_pm = db_query("SELECT
	     pm_saved
	 FROM ".$pref."pm WHERE pm_empf='".U_NAME."' AND pm_id='$pmid'");
	 if( db_rows( $r_pm ) == 1 )
	 {
	     $pm = db_result( $r_pm );
	     $save = 0;
	     if( $pm['pm_saved'] == 0 )
		     $save = 1;
		 $r_pm = db_query("SELECT
		     COUNT(pm_id)
		 FROM ".$pref."pm WHERE pm_empf='".U_NAME."' AND pm_saved='1'");
		 $pm = db_result( $r_pm );
		 list(, $saved_pms ) = each( $pm );
		 if( $saved_pms >= $config['max_pm_saved'] && $save == 1 )
		     message( 'Du kannst maximal '.$config['max_pm_saved'].' PMs sch&uuml;tzen.', 'Maximum erreicht', 0 );
		 db_query("UPDATE ".$pref."pm SET
		     pm_saved='$save'
		 WHERE pm_id='$pmid'");
	 }
	 message_redirect('PM-Sicherung bearbeitet, bitte warten ...', 'pm.php' );
 }
 // other action ----------------
 else
     message( 'Bitte Eine Funktion w&auml;hlen.', 'Fehler', 0 );
 

 $data['boardtable'] = Template( $TPm );
 echo Output( Template ( $TBoard ) );
?>
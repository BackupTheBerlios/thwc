<?php
 /* $Id: report.php,v 1.1 2003/06/20 20:41:52 master_mario Exp $ */
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
 
 $TBoard = Get_Template( 'templates/'.$style['styletemplate'].'/board.html' );
 $TReport = Get_Template( 'templates/'.$style['styletemplate'].'/report.html' );
 
 $data['nav_path'] = board_nav( $boardid, $threadid, $data['nav_path'] ).'&nbsp;&gt;&gt;&nbsp;Melden';
 
 if( $config['report'] == 0 )
     message( 'Meldungen sind vom Administrator deaktiviert.', 'Rechte', 0 );
 if( U_ID == 0 )
     message( 'Meldungen k&ouml;nnen nur von registrierten Usern gemacht werden.', 'Rechte', 0 );
 $r_report = db_query("SELECT
     report_id
 FROM ".$pref."report WHERE user_id='".U_ID."' AND post_id='$postid'");
 if( db_rows( $r_report ) > 0 )
     message( 'Du hast dieses Posting bereits gemeldet.', 'Fehler', 0 );
 
 $form = 'form';
 $textarea = 'text';
 $laenge = $config['report_max_len'];
 if( $laenge > 1500 )
     $laenge = 1500;
 $data['javascript'] = "
 function textlen ()
 {
     textarea = window.document.forms['$form'].elements['$textarea'];
     alert(textarea.value.length + ' Zeichen (Maximal erlaubte L‰nge $laenge Zeichen)');
 }";
 
 if( !isset( $send ) )
 {
     $r_post = db_query("SELECT
	     post_text,
		 bcode,
		 post_smilies
	 FROM ".$pref."post WHERE post_id='$postid'");
	 $post = db_result( $r_post );
	 $data['post'] =  parse_code(stripslashes($post['post_text']), 1, 1, $post['bcode'], $post['post_smilies'] );
	 $data['inputs'] = '<input type="hidden" name="boardid" value="'.$boardid.'" />
	     <input type="hidden" name="threadid" value="'.$threadid.'" />
	     <input type="hidden" name="postid" value="'.$postid.'" />
	     <input type="hidden" name="page" value="'.$page.'" />';
     if( !isset( $back ) )
	 {
	     $data['text'] = '';
	 }
	 else
	 {
	     $data['text'] = $text;
	 }
	 $TReport = Template( $TReport );
 }
 else
 {
     // checken der Usereingaben -----------------------------------------------
	 $err_mess = '';
	 $text = trim( $text );
     $legalchars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789 [|](){}.-_‰ˆ¸ƒ÷‹ﬂ,";
     for( $i = 0; $i < strlen($text); $i++ )
     {
         if( !strstr($legalchars, $text[$i]) )
         {
		     $err_mess .= ( $err_mess == '' ? '' : '<br />' ).'Der Text enth&auml;lt nicht erlaubte Zeichen. ( '.$text[$i].' )';
         }
     }
	 if( strlen( $text ) > $config['report_max_len'] )
		 $err_mess .= ( $err_mess == '' ? '' : '<br />' ).'Der Text ist zu lang.';
	 if( strlen( $text ) < $config['report_min_len'] )
		 $err_mess .= ( $err_mess == '' ? '' : '<br />' ).'Der Text ist zu kurz.';
     if( $err_mess != '' )
     {
         $mess = '<form action="report.php" method="post" name="sendback">
	      '.$err_mess.'
	     <input type="hidden" name="boardid" value="'.$boardid.'" />
	     <input type="hidden" name="threadid" value="'.$threadid.'" />
	     <input type="hidden" name="postid" value="'.$postid.'" />
	     <input type="hidden" name="page" value="'.$page.'" />
	     <input type="hidden" name="back" value="1" />
	     <input type="hidden" name="text" value="'.$text.'" />
   		 </form>';
         message ( $mess, 'Fehler', 1 );
     } 
	 else
	 {
	     db_query("INSERT INTO ".$pref."report SET
		     report_time='$board_time',
			 user_name='".U_NAME."',
			 user_id='".U_ID."',
			 user_ip='".getenv('REMOTE_ADDR')."',
			 report='".addslashes($text)."',
			 post_id='$postid'");
		 message_redirect('Die Meldung ist erfolgt, bitte warten ...', 'showtopic.php?boardid='.$boardid.'&threadid='.$threadid.'&page='.$page.'#p'.$postid );
	 }
 }
 
 $data['boardtable'] = $TReport;
 echo Output( Template ( $TBoard ) );
?>
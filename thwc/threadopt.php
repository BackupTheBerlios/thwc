<?php
 /* $Id: threadopt.php,v 1.3 2003/06/20 10:38:32 master_mario Exp $ */
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
 
 $TBoard = Get_Template( 'templates/'.$style['styletemplate'].'/board.html' );
 
 if( !isset($action) )
     message ( 'Bitte w&auml;le eine Funktion aus.', 'Fehler', 0 );
 if( U_ID == 0 )
     message ( 'Sorry! Das sind keine G&auml;stefunktionen.', 'Fehler', 0 );
 // read threaddata
 if( $action != '' )
 {
     $where = "board_id='".$boardid."'";
	 if( $action == 'dellink' )
	     $where = "link='".$boardid."'";
     $r_thread = db_query("SELECT
	     *
	 FROM ".$pref."thread WHERE thread_id='$threadid' AND ".$where." ");
	 if( db_rows( $r_thread ) != 1 )
	     message ( 'Sorry! Fehlerhafter Link.', 'Fehler', 0 );
	 $thread = db_result( $r_thread );
 } 
 // dellink -------------------------------------------  
 if( $action == 'dellink' )
 {
	 if( P_OMOVE != 1 )
	     message ( 'Sorry! Du bist nicht berechtigt diesen Link zu l&ouml;schen.', 'Fehler', 0 );
	 else
	 {
	     db_query("UPDATE ".$pref."thread SET
		     link='0'
		 WHERE thread_id='$threadid'");
		 $source = 'board.php?boardid='.$boardid;
		 $action .= ' (t'.$threadid.')'; 
	 } 
 }   
 // thread öffnen/schliessen -----------------------------------
 elseif( $action == 'openclose' )
 {
	 $own_thread = 0;
	 if( U_ID == $thread['autor_id'] )
	     $own_thread = 1;
	 if( ( $own_thread == 1 && P_CLOSE == 1 ) || ( $own_thread == 0 && P_OCLOSE == 1 ) )
	 {
	     if( $thread['thread_closed'] == 0 )
		     $close = 1;
		 else
		     $close = 0;
		 db_query("UPDATE ".$pref."thread SET
		     thread_closed='$close'
		 WHERE thread_id='$threadid'");
		 $source = 'board.php?boardid='.$boardid;
		 $action .= ' (t'.$threadid.')'; 
	 }
	 else
	     message ( 'Sorry! Du bist nicht berechtigt diesen Link zu l&ouml;schen.', 'Fehler', 0 );
 }
 // delete Thread -------------------------
 elseif( $action == 'delete' )
 {
	 $own_thread = 0;
	 if( U_ID == $thread['autor_id'] )
	 {    $own_thread = 1;    }
	 if( $thread['deleted'] == 1 )
	 {
	     message ( 'Sorry! Dieser Thread ist bereits als gel&ouml;scht makiert.<br />
		           Echtes l&ouml;schen ist nur im Mod,- oder Admincenter durch Administratoren m&ouml;glich.', 'Fehler', 0 );
	 }
	 
	 if( ( $own_thread == 1 && P_DELTHREAD == 1 ) || ( $own_thread == 0 && P_ODELTHREAD ) )
	 {
	     // Thread löschen
		 $post_count = $thread['replies']+$thread['replies_del']+1;
		 $thread_count = 1;
		 // posts als gelöscht makieren
		 db_query("UPDATE ".$pref."post SET
		     deleted='1'
		 WHERE thread_id='$threadid'");
		 // boarddaten lesen
		 $r_board = db_query("SELECT
		     posts,
			 posts_del,
		     threads,
			 threads_del,
			 last_post_id
		 FROM ".$pref."board WHERE board_id='$boardid'");
		 $board = db_result( $r_board );
		 // boarddaten anpassen und schreiben	
		 $posts = $board['posts']-($thread['replies']+1);
		 $posts_del = $board['posts_del']+($thread['replies']+1);
		 $threads = $board['threads']-1;
		 $threads_del = $board['threads_del']+1;
		 	 
		 $more = '';
		 if( $board['last_post_id'] == $thread['last_post_id'] )
		 $more = ", 
		  last_act_time='',
		  last_post_id='',
		  last_thread_id='',
		  last_act_user='',
		  last_act_uid='',
		  last_act_thread=''";
		 db_query("UPDATE ".$pref."board SET
		     posts='".$posts."',
			 threads='".$threads."',
			 posts_del='".$posts_del."',
			 threads_del='".$threads_del."'
			 ".$more."
		 WHERE board_id='$boardid'");	
		 // statsdaten
		 $r_stats = db_query("SELECT
		     posts,
			 posts_del,
			 threads,
			 threads_del
		 FROM ".$pref."stats");
		 $stats = db_result( $r_stats );
		 
		 $posts = $stats['posts']-($thread['replies']+1);
		 $posts_del = $stats['posts_del']+($thread['replies']+1);
		 $threads = $stats['threads']-1;
		 $threads_del = $stats['threads_del']+1;
		 
		 db_query("UPDATE ".$pref."stats SET
		     posts='".$posts."',
			 threads='".$threads."',
			 posts_del='".$posts_del."',
			 threads_del='".$threads_del."'");
		 // thread update
		 db_query("UPDATE ".$pref."thread SET
		     deleted='1',
			 replies='0',
			 replies_del='$thread[replies]'
		 WHERE thread_id='$threadid'");	 
		 
		 $source = 'board.php?boardid='.$boardid;
		 $action .= ' (t'.$threadid.')'; 
	 }
	 else
	     message ( 'Sorry! Du bist nicht berechtigt diesen Thread zu l&ouml;schen.', 'Fehler', 0 );
 }
 // move --------------------------------
 elseif( $action == 'move' )
 {
     if( $thread['deleted'] == 1 )
	     message ( 'Gel&ouml;schte Threads k&ouml;nnen nicht verschoben werden.', 'Rechte', 0 );
     if( P_OMOVE == 1 )
	 {
	     if( !isset( $send ) )
		 {
		     $boardbox = '<select name="new_board" size="1" id="border-tab">';
		     $r_category = db_query("SELECT 
			     category_id,
				 category_name
			 FROM ".$pref."category WHERE category_id!='0' ORDER BY category_order");
			 while( $category = db_result( $r_category ) )
			 {
			     $boardbox .= '<option value="0">'.$category['category_name'].'</option>'; 
			     $r_board = db_query("SELECT
				     board_id,
					 board_name
				 FROM ".$pref."board WHERE board_id!='$boardid' && category='".$category['category_id']."'");
				 if( db_rows( $r_board ) > 0 )
				 {
				     while( $board = db_result( $r_board ) )
					 {
					     $boardbox .= '<option value="'.$board['board_id'].'">--&nbsp;'.$board['board_name'].'</option>';
					 }
				 }
			 }
			 $boardbox .= '</select>';
			 $data['boardtable'] = '<table width="100%" cellpadding="3" cellspacing="0" border="0" style="border-collapse:collapse">
			  <tr>
			   <td class="header">
			    &nbsp;
			   </td>
			  </tr>
			  <tr>
			   <td class="cellb" style="text-align:center">
			    <form action="threadopt.php" method="post">
				<input type="hidden" name="boardid" value="'.$boardid.'">
				<input type="hidden" name="threadid" value="'.$threadid.'">
				<input type="hidden" name="action" value="move">
				<input type="hidden" name="send" value="1"><br />
				Neues Forum:&nbsp;'.$boardbox.'<br /><br />
				<input type="checkbox" name="link" value="1" />&nbsp;Link in jetzigem Forum hinterlassen.<br /><br />
			   </td>
			  </tr>
			  <tr>
			   <td class="header" style="text-align:center">
			    <input type="submit" value=" Weiter &gt;&gt; " id="border-tab" />
			   </td>
			  </tr>
			  </form>
			 </table>'; 
             $data['nav_path'] .= '&nbsp;&gt;&gt;&nbsp;'.$thread['thread_topic'].'&nbsp;&gt;&gt;&nbsp;Verschieben';
             echo Output( Template ( $TBoard ) );
			 exit;
		 }
		 else
		 {
		     $r_board = db_query("SELECT
			     category
			 FROM ".$pref."board WHERE board_id='$new_board'");
			 if( db_rows( $r_board ) != 1 )
			     message( 'Dieses gew&auml;hlte Board existiert nicht!', 'Fehler', 0 );
			 else
			 {
			 // zugriff auf Adminboards verhindern
			     $board = db_result( $r_board );
				 if( $board['category'] == 0 )
			         message( 'Dieses gew&auml;hlte Board existiert nicht!', 'Fehler', 0 );
			 } 
			 
			 // threaddaten ändern
			 $more = '';
			 if( isset( $link ) )
			     $more = ", link='".$boardid."'";
			 db_query("UPDATE ".$pref."thread SET
			     board_id='$new_board'
				 ".$more."
			 WHERE thread_id='$threadid'");
			 // postdaten ändern
			 db_query("UPDATE ".$pref."post SET
			     board_id='$new_board'
			 WHERE thread_id='$threadid'");
		     // boarddaten lesen
		     $r_board = db_query("SELECT
		         posts,
			     threads,
			     posts_del,
			     last_post_id
		     FROM ".$pref."board WHERE board_id='$new_board'");
		     $board = db_result( $r_board );
		     // boarddaten anpassen und schreiben
		     $post_count = $thread['replies']+1;
			 $del_count = $thread['replies_del'];
		     $posts = $board['posts']+$post_count;
		     $threads = $board['threads']+1;
		     $posts_del = $board['posts_del']+$del_count;
		     $more = '';
		     if( $board['last_post_id'] < $thread['last_post_id'] && $thread['deleted'] == 0 )
		     $more = ", 
		      last_act_time='".$thread['last_act_time']."',
		      last_post_id='".$thread['last_post_id']."',
		      last_thread_id='".$thread['thread_id']."',
		      last_act_user='".$thread['last_act_user']."',
		      last_act_uid='',
		      last_act_thread='".$thread['thread_topic']."'";
		     db_query("UPDATE ".$pref."board SET
		         posts='$posts',
			     threads='$threads',
			     posts_del='$posts_del'
			     ".$more."
		     WHERE board_id='$new_board'");
			 // auch beim alten Board
		     // boarddaten lesen
		     $r_board = db_query("SELECT
		         posts,
			     threads,
			     posts_del,
			     last_post_id
		     FROM ".$pref."board WHERE board_id='$boardid'");
		     $board = db_result( $r_board );
		     // boarddaten anpassen und schreiben
		     $post_count = $thread['replies']+1;
			 $del_count = $thread['replies_del'];
		     $posts = $board['posts']-$post_count;
		     $threads = $board['threads']-1;
		     $posts_del = $board['posts_del']-$del_count;
		     $more = '';
		     if( $board['last_post_id'] == $thread['last_post_id'] )
		     $more = ", 
		      last_act_time='',
		      last_post_id='',
		      last_thread_id='',
		      last_act_user='',
		      last_act_uid='',
		      last_act_thread=''";
		     db_query("UPDATE ".$pref."board SET
		         posts='$posts',
			     threads='$threads',
			     posts_del='$posts_del'
			     ".$more."
		     WHERE board_id='$boardid'");		 
		     $source = 'board.php?boardid='.$new_board;
		     $action .= ' (t'.$threadid.')'; 
		 }
	 }
	 else
	     message ( 'Sorry! Du bist nicht berechtigt diesen Thread zu verschieben.', 'Fehler', 0 );
 }
 // sticky ------------------------------
 elseif( $action == 'sticky' )
 {
     if( !P_TOP )
	     message( 'Du bist nicht berechtigt Threads *fest* zu machen.', 'Rechte', 0 );
	 if( $thread['sticky'] == 0 )
	     $sticky = 1;
	 else
	     $sticky = 0;
	
	 db_query("UPDATE ".$pref."thread SET
	     sticky='$sticky'
	 WHERE thread_id='$threadid'"); 
	 
     $source = 'board.php?boardid='.$boardid;
	 $action .= ' (t'.$threadid.')'; 
 }
 // no action ---------------------------
 else
     message ( 'Bitte w&auml;le eine Funktion aus.', 'Fehler', 0 );
 if( $action != '' && U_ID != 0 )
 {
     $basename = basename($HTTP_SERVER_VARS["SCRIPT_NAME"]);
     db_query( "INSERT INTO ".$pref."modlog SET
         logtime='$board_time',
         loguser='".U_NAME."',
         logip='".getenv('REMOTE_ADDR')."',
         logfile='$basename',
         action='".addslashes($action)."'");
     message_redirect('Funktion ausgef&uuml;hrt, bitte warten ...', $source );
 }
?>
<?php
 /* $Id: index.php,v 1.1 2003/06/12 13:59:19 master_mario Exp $ */
 include ( 'inc/header.inc.php' );
 
 $TCatrow = Get_Template( 'templates/'.$style['styletemplate'].'/catrow.html' );
 $TBoardrow = Get_Template( 'templates/'.$style['styletemplate'].'/index_b_row.html' );
 $TIndex = Get_Template( 'templates/'.$style['styletemplate'].'/boardtable.html' ); 
 $TBoard = Get_Template( 'templates/'.$style['styletemplate'].'/board.html' );

 $r_category = db_query("SELECT
     category_id,
	 category_name,
	 category_is_open
 FROM ".$pref."category ORDER BY category_order");
 if( db_rows( $r_category ) < 1 )
     $data['boards'] = '<tr><td class="cella" colspan="5"><center>Keine Kategorien angelegt.</center></td></tr>';
 else
 {
     $boards = '';
     while( $a_category = db_result( $r_category ) )
	 {
	     $board_count = 0; 
         $r_boards = db_query("SELECT
	         board_id,
		     board_name,
			 board_under,
			 last_post_id,
			 threads,
			 posts
	     FROM ".$pref."board WHERE disabled='0' AND category='".$a_category['category_id']."' ORDER BY board_order ASC");
		 if( db_rows( $r_boards ) > 0 )
		 {
		     $category = '';
		     while( $board = db_result( $r_boards ) )
			 {
			     $board_count++;
				 
				 $session_var = 'b'.$board['board_id'];
				 $new_topic = 0;
				 $gif = '';
				 if( U_ID != 0 )
				 {
				     if( $board['last_post_id'] > $_SESSION[$session_var] && $_SESSION[$session_var] != 0 )
				     {    $gif = '_new'; $new_topic = 1;    } 
				 }
				 $boardname = '<a href="board.php?boardid='.$board['board_id'].'"><b>'.$board['board_name'].'</b></a>';
				 $boardname .= '<br />[smallfont]'.$board['board_under'].'[smallfontend]';
				 
				 $row = $TBoardrow;
				 $row = str_replace( '[board_gif]', '<img src="templates/'.$style['styletemplate'].'/images/board'.$gif.'.gif" border="0">', $row );
				 $row = str_replace( '[forum_name]', $boardname, $row );
				 $row = str_replace( '[threads]', $board['threads'], $row );
				 $row = str_replace( '[posts]', $board['posts'], $row );
				 $category .= $row; 
			 }
		 }
		 $catblock = '';
		 if( $board_count > 0 )
		 {
	         $catname = '<a href="category.php?catid='.$a_category['category_id'].'"><b>'.$a_category['category_name'].'</b></a>';	
			 $catname .= '&nbsp;<font size="1">[<a href="open_cat.php?catid='.$a_category['category_id'].'&open=">open/close</a>]</font>'; 
		     $catimage = '';
			 if( $new_topic == 1 ) $catimage = '_new';
		 
		     $row = $TCatrow;
			 $row = str_replace( '[category_name]', $catname, $row );
			 $row = str_replace( '[catimage]', '<img src="templates/'.$style['styletemplate'].'/images/board'.$catimage.'.gif" border="0" width="20" height="8">', $row );
			 $catblock = $row;
			 if( $a_category['category_is_open'] == 1 )
			 {
			     $catblock .= $category;
			 }			
			 $boards .= $catblock;
		 }
	 }
	 
 }
 
 $data['boards'] = $boards;
 $data['user'] = U_NAME;
 $data['nav_path'] .= '&nbsp;&gt;&gt;&nbsp;Foren&uuml;bersicht';
 $data['time'] = 'Serverzeit: '.date( "d.M.Y\, H:i \U\h\\r", $board_time );
 $data['boardtable'] = Template( $TIndex );
 echo Output( Template ( $TBoard ) );
?>
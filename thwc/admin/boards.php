<?php
 /* $Id: boards.php,v 1.1 2003/06/12 13:59:28 master_mario Exp $ */
 include( 'adhead.inc.php' );
 if( isset( $_POST['action'] ) ) $action=$_POST['action'];
 elseif( isset( $_GET['action'] ) )  $action=$_GET['action'];
 else $action  = '';
 $data['work'] = '';
 /*FUNCTIONEN*/
 function BoardForm($board, $action)
 {
     global $pref, $config;

     if( !isset( $board['board_name'] ) )
         $board['board_name'] = '';
     if( !isset( $board['board_under'] ) )
         $board['board_under'] = '';
     if( !isset( $board['category'] ) )
         $board['category'] = '';
     if( !isset( $board['styleid'] ) )
         $board['styleid'] = '';
     if( !isset( $board['disabled'] ) )
         $board['disabled'] = 1;
     if( !isset( $board['board_id'] ) )
         $board['board_id'] = '';

     $back = '<form action="boards.php" method="post">';
     $back .= '
      <table width="100%" cellpadding="3" cellspacing="0" border="0">
       <tr>
        <td id="blank" style="width:300px">Forumname</td>
        <td id="blank">
         <input type="text" name="board_name" maxlength="'.$config['max_boardname'].'" id="border-tab" value="'.htmlspecialchars($board['board_name']).'" />
        </td>
       </tr>
       <tr>
        <td id="blank">Beschreibung<br />
        <font size="1">Wird unter dem Forumnamen angezeigt.</font>
        </td>
        <td id="blank">
         <input type="text" name="under" maxlength="255" id="border-tab" value="'.htmlspecialchars($board['board_under']).'" />
        </td>
       </tr>
       <tr>
        <td id="blank">Category<br /><font size="1"><b>Achtung!!</b> (Administrationsboards) sind nicht &ouml;ffendlich!<br />Sie sind f&uuml;r den Gebrauch
        im Admin,- und Modcenter vorgesehen.<br /><b>&Ouml;ffendliche und Administrationsboards können<br />untereinander <u>nicht</u> umgewandelt werden.</b></font></td>
        <td id="blank">';
     if( $board['category'] == '' )
         $back .= listbox( "category", "category_id", "category_name", $pref."category", $board['category'], '(Administrationsboard)', '0');
     else
     {
         if( $board['category'] == 0 )
             $back .= '<b>Administrationsboard</b>';
         else
             $back .= listbox( "category", "category_id", "category_name", $pref."category", $board['category'] );
     }
     $back .= '</td>
       </tr>
       <tr>
        <td id="blank">Style</td>
        <td id="blank">
         '.listbox( "styleid", "styleid", "stylename", $pref."style", $board['styleid'], '( Defaultstyle )', '0').'
        </td>
       </tr>
       <tr>
        <td id="blank"><b>Status</b><br />
         <font size="1">Hier kannst Du dieses Forum<br />
         vor&uuml;bergehend abschalten.</font>
        </td>
        <td id="blank">
         <SELECT id="tab" name="disabled">
          <option value="1"'.( $board['disabled'] == 1 ? " selected" : "" ).'>aktiviere Board</option>
          <option value="0"'.( $board['disabled'] == 0 ? " selected" : "" ).'>deaktiviere Board</option>
         </SELECT>
        </td>
       </tr>
       <tr>
        <td id="blank">&nbsp;</td>
        <td id="blank">
         <input type="hidden" name="action" value="'.$action.'" />
         <input type="hidden" name="update" value="1" />
         <input type="hidden" name="boardid" value="'.$board['board_id'].'" />
         <input type="submit" name="send" value="  Senden  " id="border-tab" />
        </td>
       </tr>
     ';
     $back .= '</table>
      <br><br><br>
      Note: Den Defaultstyle kannst Du <a href="style.php?action=ListStyles">hier</a> definieren.
      </form>';
     return $back;
 }
 /* CODE */
 // list boards ---------------------------------------------------------------------
 if( $action == '' )
 {
     $data['work'] = '<b>Board,- und Kategorieorder anpassen</b><br>';
     $data['work'] .= '<form action="boards.php" method="post">
      <table cellpadding="3" cellspacing="0" border="0">
       <tr>
        <td colspan="2" id="blank"><i>Anzeige</i></td>
        <td colspan="2" id="blank" style="text-align:center"> <i>Optionen </i></td>
        <td id="blank">&nbsp;</td>
        <td id="blank">&nbsp;</td>
       </tr>';
     $r_category = db_query("SELECT
         category_id,
         category_name,
         category_order
     FROM ".$pref."category ORDER BY category_order ASC");
     while( $category = db_result( $r_category ) )
     {
         $data['work'] .= '
          <tr>
           <td id="blank">
            <input type="text" name="catorder['.$category['category_id'].']" size="3" maxlength="3" value="'.$category['category_order'].'" id="border-tab" />
           </td>
           <td id="blank">&nbsp;</td>
           <td id="blank" colspan="2">
            <a href="boards.php?action=RenameCategory&c_id='.$category['category_id'].'">edit</a>&nbsp;|
            <a href="boards.php?action=delcat&c_id='.$category['category_id'].'">delete</a>
           </td>
           <td id="blank">&nbsp;</td>
           <td id="blank"><b>'.$category['category_name'].'</b></td>
          </tr>
         ';
         $r_board = db_query("SELECT
             board_id,
             board_name,
             board_under,
             board_order
         FROM ".$pref."board WHERE category='$category[category_id]' ORDER BY board_order ASC");
         while( $board = db_result( $r_board ) )
         {
             $data['work'] .= '
              <tr>
               <td id="blank">&nbsp;</td>
               <td id="blank">
                <input type="text" name="boardorder['.$board['board_id'].']" size="3" maxlength="3" value="'.$board['board_order'].'" id="border-tab" />
               </td>
               <td colspan="2" id="blank">
                <a href="boards.php?action=edit&id='.$board['board_id'].'">edit</a>
                | <a href="boards.php?action=delete&forumid='.$board['board_id'].'">delete</a>
                | <a href="groups.php?action=grouppermtable&boardid='.$board['board_id'].'">Rechte</a></td>
               <td id="blank">&nbsp;</td>
               <td id="blank">'.$board['board_name'].'<br />
               <font size="1">'.$board['board_under'].'</font></td>
              </tr>
             ';
         }
     }
     $data['work'] .= '
       <tr>
        <td colspan="6" id="blank"><b>Administrationsboards</b><br /><font size="1"><b>Bitte lesen!</b> Diese Boards
        sind f&uuml;r den Gebrauch im Admin,- und Modcenter vorgesehen<br />und unterliegen nicht dem &ouml;ffendlichen
        Rechtesysthem. F&uuml;r diese Boards gibt es fest<br /> definierte Rechte. Bitte in der <a href="doc.php">Dokumentation</a>
        nachlesen.</font></td>
       </tr>';
     $r_board = db_query("SELECT
         board_id,
         board_name,
         board_under,
         board_order
     FROM ".$pref."board WHERE category='0' ORDER BY board_order ASC");
     if( db_rows( $r_board ) > 0 )
     {
     while( $board = db_result( $r_board ) )
     {
         $data['work'] .= '
          <tr>
           <td id="blank">&nbsp;</td>
           <td id="blank">
            <input type="text" name="boardorder['.$board['board_id'].']" size="3" maxlength="3" value="'.$board['board_order'].'" id="border-tab" />
           </td>
           <td colspan="2" id="blank">
            <a href="boards.php?action=edit&id='.$board['board_id'].'">edit</a>
            | <a href="boards.php?action=delete&forumid='.$board['board_id'].'">delete</a></td>
           <td id="blank">&nbsp;</td>
           <td id="blank">'.$board['board_name'].'<br />
           <font size="1">'.$board['board_under'].'</font></td>
          </tr>
         ';
     }
     }

     $data['work'] .= '
      </table>
      <br />
      <input type="hidden" name="action" value="updateorder" />
      <input type="submit" value="Boardorder anpassen" id="border-tab" />
     </form>';
 }
 // updateorder
 elseif( $action=="updateorder" )
 {
     foreach( $_POST['boardorder'] as $boardid=>$boardorder )
     {
         db_query("UPDATE ".$pref."board SET
             board_order='$boardorder'
         WHERE board_id='$boardid'");
     }
     foreach( $_POST['catorder'] as $categoryid=>$categoryorder )
     {
         db_query("UPDATE ".$pref."category SET
             category_order='$categoryorder'
         WHERE category_id='$categoryid'");
     }
     $data['work'] = 'Board,- und Kategorieorder angepasst!';
 }
 //edit Board
 elseif( $action == "edit" )
 {
     if( isset( $_POST['send'] ) )
     {
         if( $_POST['board_name'] == '' )
             $data['work'] = 'Bitte gib einen Namen f&uuml;r das Board an.';
         else
         {
             $r_board = db_query("SELECT
                 category,
                 board_order
             FROM ".$pref."board WHERE board_id='$_POST[boardid]'");
             $oldboard = db_result( $r_board );
             if( $oldboard['category'] != $_POST['category'] )
             {
                 $result = db_query( "SELECT
                     max(board_order)
                 FROM ".$pref."board WHERE category='$_POST[category]'" );
                 list($maxorder) = mysql_fetch_row($result);
                 $maxorder++;
             }
             else
             {
                 $maxorder = $oldboard['board_order'];
             }
             db_query("UPDATE ".$pref."board SET
                 board_name='". addslashes($_POST['board_name']) . "',
                 board_under='" . addslashes($_POST['under']) . "',
                 category='$_POST[category]',
                 board_order='$maxorder',
                 styleid='$_POST[styleid]',
                 disabled = '$_POST[disabled]'
             WHERE board_id='$_POST[boardid]'");
             $data['work'] = "Board wurde upgedated!";
         }
     }
     else
     {
         $r_board = db_query("SELECT
             board_id,
             board_name,
             board_under,
             category,
             styleid,
             disabled
         FROM ".$pref."board WHERE board_id='$_GET[id]'");
         $board = db_result( $r_board );
         $data['work'] = '<b>Board editieren</b><br><br>';
         $data['work'] .= BoardForm($board, 'edit');
     }
 }
 // delete
 elseif( $action == "delete" )
 {
     if( isset( $_GET['confirm'] ) )
     {
         // delete the board
         db_query("DELETE FROM ".$pref."board WHERE board_id='$_GET[forumid]'");
         // delete messages
         $result=db_query("SELECT
             thread_id
         FROM ".$pref."thread WHERE board_id='$_GET[forumid]'");
         while( $topic= db_result( $result ) )
         {
             db_query("DELETE FROM ".$pref."post WHERE thread_id='$topic[thread_id]'");
         }
         // delete topics
         db_query("DELETE FROM ".$pref."thread WHERE board_id='$_GET[forumid]'");
         // delete permission
         db_query("DELETE FROM ".$pref."groupboard WHERE boardid='$_GET[forumid]'");
         db_query("OPTIMIZE TABLE ".$pref."board");
         db_query("OPTIMIZE TABLE ".$pref."post");
         db_query("OPTIMIZE TABLE ".$pref."thread");
         db_query("OPTIMIZE TABLE ".$pref."groupboard");
         $data['work'] = 'Board wurde erfolgreich gel&ouml;scht.';
     }
     else
     {
         $data['work'] = '<font color=red><b>ACHTUNG: Du bist dabei ein Board zu l&ouml;schen!</b></font><br><br>';
         $data['work'] .= 'Klick <a href="boards.php?action=delete&forumid='.$_GET['forumid'].'&confirm=1">hier</a> um weiter zu machen';
     }
 }
 // add board
 elseif( $action == 'newboard' )
 {
     if( isset( $_POST['send'] ) )
     {
         if( $_POST['board_name'] == '' )
             $data['work'] = 'Bitte gib einen Namen f&uuml;r das Board an.';
         else
         {
             $result = db_query( "SELECT
                 max(board_order)
             FROM ".$pref."board WHERE category='".$_POST['category']."'" );
             list($maxorder) = mysql_fetch_row($result);
             $maxorder++;

             db_query("INSERT INTO ".$pref."board SET
                 board_name='".addslashes($_POST['board_name'])."',
                 board_under='".addslashes($_POST['under'])."',
                 category='".$_POST['category']."',
                 board_order='$maxorder',
                 styleid='".$_POST['styleid']."',
                 disabled='".$_POST['disabled']."'");
             $data['work'] = 'Forum wurde angelegt, bitte &uuml;berpr&uuml;ffe die Rechte.';
         }
     }
     else
     {
         $data['work'] = '<b>Board hinzuf&uuml;gen</b><br><br>';
         $data['work'] .= BoardForm(array(), 'newboard');
     }
 }
 // new cat
 elseif( $action == 'newcat' )
 {
     if( isset( $_POST['send'] ) )
     {
         if( $_POST['name'] == '' )
             $data['work'] = 'Bitte gib einen Namen f&uuml;r die Kategorie an.';
         else
         {
             $result = db_query( "SELECT
                 max(category_order)
             FROM ".$pref."category" );
             list($maxorder) = mysql_fetch_row($result);
             $maxorder++;

             if( !isset( $_POST['open'] ) )
                 $_POST['open'] = 0;
             db_query("INSERT INTO ".$pref."category SET
                 category_name='".addslashes($_POST['name'])."',
                 category_order='$maxorder',
                 category_is_open='$_POST[open]'");
             $data['work'] = 'Kategorie wurde angelegt.';
         }
     }
     else
     {
         $data['work'] = '<b>Kategorie hinzuf&uuml;gen</b><br><br>';
         $data['work'] .= '
          <form action="boards.php" method="post">
           Kategoriename:&nbsp;&nbsp;
           <input type="text" name="name" maxlength="'.$config['max_catname'].'" id="border-tab" />
           <br /><br />
           <input type="checkbox" name="open" value="1" checked />&nbsp;&nbsp;Kategorie offen&nbsp;
           <font size="1">(Standarteinstellung bei Boardaufruff)</font>
           <br /><br />
           <input type="hidden" name="action" value="'.$action.'" />
           <input type="submit" name="send" value="Kategorie anlegen" id="border-tab" />
          </form>
         ';
     }
 }
 // delcat
 elseif( $action == "delcat" )
 {
     $r_board = db_query("SELECT
         count(board_id) AS boardcount
     FROM ".$pref."board WHERE category='$_GET[c_id]'");
     $board = db_result( $r_board );
     if( $board['boardcount'] > 0 )
     {
         $data['work'] = 'Fehler: Es gibt noch Boards in dieser Kategorie!';
     }
     else
     {
         db_query("DELETE FROM ".$pref."category WHERE category_id='$_GET[c_id]'");
         db_query("OPTIMIZE TABLE ".$pref."category");
         $data['work'] = 'Die Kategorie wurde erfolgreich gel&ouml;scht.';
     }
 }
 // RenameCategory
 elseif( $action == "RenameCategory" )
 {
     if( isset( $_POST['send'] ) )
     {
         if( $_POST['name'] == '' )
             $data['work'] = 'Bitte gib einen Namen f&uuml;r die Kategorie an.';
         else
         {
             if( !isset( $_POST['open'] ) )
                 $_POST['open'] = 0;
             db_query("UPDATE ".$pref."category SET
                 category_name='".addslashes($_POST['name'])."',
                 category_is_open='$_POST[open]'
             WHERE category_id='$_POST[categoryid]'");
             $data['work'] = 'Kategorie wurde geupdated.';
         }
     }
     else
     {
         $data['work'] = '<b>Kategorie update</b><br><br>';
         $r_category = db_query("SELECT
             category_id,
             category_name,
             category_is_open
         FROM ".$pref."category WHERE category_id='$_GET[c_id]'");
         $category = db_result( $r_category );
         $data['work'] .= '
          <form action="boards.php" method="post">
           Kategoriename:&nbsp;&nbsp;
           <input type="text" name="name" maxlength="'.$config['max_catname'].'" value="'.$category['category_name'].'" id="border-tab" />
           <br /><br />
           <input type="checkbox" name="open" value="1"'.($category['category_is_open'] == 1 ? ' checked' : '' ).' />&nbsp;&nbsp;Kategorie offen&nbsp;
           <font size="1">(Standarteinstellung bei Boardaufruff)</font>
           <br /><br />
           <input type="hidden" name="action" value="'.$action.'" />
           <input type="hidden" name="categoryid" value="'.$category['category_id'].'" />
           <input type="submit" name="send" value="Kategorie updaten" id="border-tab" />
          </form>
         ';
     }
 }





 $data['javascript'] = '';
 echo Template( Get_Template( 'templates/admin.html' ) );
 tb_footer();
?>
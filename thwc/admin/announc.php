<?php
/* $Id: announc.php,v 1.1 2003/06/12 13:59:32 master_mario Exp $ */
 include( 'adhead.inc.php' );

 $data['work'] = '<b>Ank&uuml;ndigungen</b><br /><br />';
 // FUNKTION
 function annoncForm( $announc, $action, $boards )
 {
     global $pref;
     $boards[] = 'a';

     $back = '<form action="announc.php" method="post">
      <table cellpadding="4" cellspacing="0" border="0">
       <tr>
        <td style="width:200px"><b>Bezeichener</b><br /><font size="1">(Wenn Du das Feld leer l&auml;sst<br />ist der Bezeichner <b>Ank&uuml;ndigung</b>)</font></td>
        <td>&nbsp;</td>
        <td style="vertical-align:top">
         <input type="text" maxlength="50" name="announc[word]" value="'.( isset($announc['newsword']) ? decode(addslashes($announc['newsword'])) : '' ).'" id="border-tab" />
        </td>
       </tr>
       <tr>
        <td><b>Topic</b></td>
        <td>&nbsp;</td>
        <td><input type="text" size="50" maxlength="255" name="announc[topic]" value="'.( isset($announc['newstopic']) ? decode(addslashes($announc['newstopic'])) : '' ).'" id="border-tab" /></td>
       </tr>
       <tr>
        <td style="vertical-align:top"><b>Text</b></td>
        <td>&nbsp;</td>
        <td><textarea cols="50" rows="6" name="announc[text]" id="border-tab">'.( isset($announc['newstext']) ? decode(addslashes($announc['newstext'])) : '' ).'</textarea></td>
       </tr>
       <tr>
        <td><b>Index</b></td>
        <td>&nbsp;</td>
        <td>
         <input type="checkbox" name="announc[index]" value="1"'.( in_array( '0', $boards) === FALSE ? '' : ' checked' ).' />
         <font size="1">Auch auf der Indexseite anzeigen.</font>
        </td>
       </tr>
       <tr>
        <td style="vertical-align:top"><b>Boards</b></td>
        <td>&nbsp;</td>
        <td>';
     $back .= '<select name="boardids[]" size="5" id="border-tab" multiple>';
     $r_boards = db_query("SELECT
         board_id,
         board_name
     FROM ".$pref."board");
     if( db_rows( $r_boards ) == 0 )
         $back .= 'Noch keine Boards angelegt.';
     else
     {
         while( $a_boards = db_result( $r_boards ) )
         {
             $back .= '<option value="'.$a_boards['board_id'].'"'.( in_array( $a_boards['board_id'], $boards) === FALSE ? '' : ' selected' ).'>'.$a_boards['board_name'].'</option>';
         }
     }
     $back .= '</select>
        </td>
       </tr>
      </table>
      <br />
      <center>
       <input type="hidden" name="action" value="'.$action.'" />
       <input type="hidden" name="announc[id]" value="'.( isset($announc['newsid']) ? $announc['newsid'] : '' ).'" />
       <input type="submit" value=" Senden " id="border-tab" />
      </center>
     </form>';
     return $back;
 }
 // CODE
 if( $config['announc'] == 0 )
 {
     $data['work'] .= '...sind nicht aktiviert. Aktivieren kannst Du sie <a href="basics.php">hier</a>.';
 }
 else
 {
     // addAnnounc -----------------------------------------------------------
     if( $action == 'addAnnounc' )
     {
         if( !$announc['topic'] || !$announc['text'] )
             $data['work'] = 'Du mu&szlig;t wenistens Topic und Text angeben.';
         else
         {
             $boards = array();
             if( isset( $announc['index'] ) )
                 $boards[] = '0';
             while( list(, $id) = @each($boardids) )
             {
                 $boards[] = $id;
             }
             $boards = implode( ',', $boards );

             db_query("INSERT INTO ".$pref."news SET
                 newstopic='".encode(addslashes($announc['topic']))."',
                 newstext='".encode(addslashes($announc['text']))."',
                 newstime='$board_time',
                 boards='$boards',
                 newsword='".encode(addslashes($announc['word']))."'");
             $data['work'] .= 'Ank&uuml;ndigung hinzugef&uuml;gt<br />';
             $action = '';
         }
     }
     // editAnnounc ----------------------------------------------------------
     if( $action == 'editAnnounc' )
     {
         $r_announc = db_query("SELECT
             *
         FROM ".$pref."news WHERE newsid='$_GET[newsid]'");
         $announc = db_result( $r_announc );
         $boards = explode( ',', $announc['boards'] );
         $data['work'] = '<b>Ank&uuml;ndigung editieren</b><br />';
         $data['work'] .= annoncForm( $announc, 'updateAnnounc', $boards );
     }
     // updateAnnounc -----------------------------------------------------------
     if( $action == 'updateAnnounc' )
     {
         $announc = $_POST['announc'];
         if( isset($_POST['boardids']) ) $boardids = $_POST['boardids'];
         if( !$announc['topic'] || !$announc['text'] )
             $data['work'] = 'Du mu&szlig;t wenistens Topic und Text angeben.';
         else
         {
             $boards = array();
             if( isset( $announc['index'] ) )
                 $boards[] = '0';
             while( list(, $id) = @each($boardids) )
             {
                 $boards[] = $id;
             }
             $boards = implode( ',', $boards );

             db_query("UPDATE ".$pref."news SET
                 newstopic='".encode(addslashes($announc['topic']))."',
                 newstext='".encode(addslashes($announc['text']))."',
                 newstime='$board_time',
                 boards='$boards',
                 newsword='".encode(addslashes($announc['word']))."' WHERE newsid='$announc[id]'");
             $data['work'] .= 'Ank&uuml;ndigung editiert<br />';
             $action = '';
         }
     }
     // deleAnnounc ----------------------------------------------------------
     if( $action == 'deleAnnounc' )
     {
         db_query("DELETE FROM ".$pref."news WHERE newsid='$_GET[newsid]'");
         db_query("OPTIMIZE TABLE ".$pref."news");
         $data['work'] .= 'Ank&uuml;ndigung gel&ouml;scht<br />';
         $action = '';
     }
     // list announc ---------------------------------------------------------
     if( $action == '' )
     {
         $data['work'] .= '
          <table width="100%" cellpadding="4" cellspacing="0" border="0">
           <tr>
            <td><i>Erstellt</i></td>
            <td>&nbsp;</td>
            <td><i>Topic</i></td>
            <td>&nbsp;</td>
            <td><i>Optionen</i></td>
           </tr>';
         $r_announc = db_query("SELECT
             newsid,
             newstopic,
             newstime
         FROM ".$pref."news ORDER BY newstime ASC");
         if( db_rows( $r_announc ) > 0 )
         {
             $i=0;
             while( $a_announc = db_result( $r_announc ) )
             {
                 $data['work'] .= '<tr bgcolor="'.( $i % 2 == 0 ? '#DADADA' : '' ).'">
                   <td>'.date( "d.m.Y H:i\U\h\\r", $a_announc['newstime'] ).'</td>
                   <td>&nbsp;</td>
                   <td>'.$a_announc['newstopic'].'</td>
                   <td>&nbsp;</td>
                   <td>
                    <a href="announc.php?action=editAnnounc&newsid='.$a_announc['newsid'].'">Editieren</a> |
                    <a href="announc.php?action=deleAnnounc&newsid='.$a_announc['newsid'].'">L&ouml;schen</a>
                   </td>
                  </tr>';
                 $i++;
             }
         }
         $data['work'] .= '</table><hr>';
         // newAnnounc -----------------------------------------------------------
         $data['work'] .= '<b>Neue Ank&uuml;ndigung erstellen</b><br />';
         $data['work'] .= annoncForm( array(), 'addAnnounc', array() );
     }
 }

 $data['javascript'] = '';
 echo Template( Get_Template( 'templates/admin.html' ) );
 tb_footer();
?>
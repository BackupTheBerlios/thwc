<?php
/* $Id: qlink.php,v 1.1 2003/06/12 13:59:31 master_mario Exp $ */
 include( 'adhead.inc.php' );

 function qlinkForm ( $qlink, $action )
 {
     $back = '<form action="qlink.php" method="post">
      <table cellpadding="4" cellspacing="0" border="0">';
     if( isset( $qlink['linkid'] ) )
     {
       $back .= '<tr>
        <td style="width:200px"><b>ID:</b></td>
        <td>&nbsp;</td>
        <td>'.$qlink['linkid'].'<input type="hidden" name="qlink[id]" value="'.$qlink['linkid'].'" /></td>
       </tr>';
     }
     $back .= '<tr>
        <td style="width:200px"><b>URL/link:</b></td>
        <td>&nbsp;</td>
        <td><input type="text" size="50" maxlength="128" name="qlink[link]" value="'.( isset( $qlink['linklink'] ) ? $qlink['linklink'] : '' ).'" id="border-tab" /></td>
       </tr>
       <tr>
        <td style="width:200px"><b>Linkname:</b></td>
        <td>&nbsp;</td>
        <td><input type="text" size="50" maxlength="128" name="qlink[name]" value="'.( isset( $qlink['linkname'] ) ? $qlink['linkname'] : '' ).'" id="border-tab" /></td>
       </tr>
       <tr>
        <td style="width:200px; vertical-align:top">
         <b>Alt:</b><br /><font size="1">(Wird beim &Uuml;berfahren mit der Maus angezeigt.)</font>
        </td>
        <td>&nbsp;</td>
        <td><textarea name="qlink[alt]" cols="50" rows="5" id="border-tab">'.( isset( $qlink['linkalt'] ) ? $qlink['linkalt'] : '' ).'</textarea></td>
       </tr>
       <tr>
        <td style="width:200px"><b>Counter:</b></td>
        <td>&nbsp;</td>
        <td><input type="text" size="6" maxlength="10" name="qlink[count]" value="'.( isset( $qlink['linkcount'] ) ? $qlink['linkcount'] : '' ).'" id="border-tab" /></td>
       </tr>
       <tr>
        <td style="width:200px"><b>Linkstatus:</b></td>
        <td>&nbsp;</td>
        <td>';
     if( isset( $qlink['linkstatus'] ) )
         $select = $qlink['linkstatus'];
     else
         $select = 0;
     $back .= '<select name="qlink[status]" size="1" id="border-tab">
          <option value="0"'.( $select == 0 ? ' selected' : '' ).'>aktiviert</option>
          <option value="1"'.( $select == 1 ? ' selected' : '' ).'>deaktiviert</option>
         </select>
        </td>
       </tr>
       <tr>
        <td style="width:200px"><b>Linkverhalten:</b></td>
        <td>&nbsp;</td>
        <td>';
     if( isset( $qlink['linkart'] ) )
         $select = $qlink['linkart'];
     else
         $select = 0;
     $back .= '<select name="qlink[art]" size="1" id="border-tab">
          <option value="0"'.( $select == 0 ? ' selected' : '' ).'>Im selben Fester &ouml;ffnen</option>
          <option value="1"'.( $select == 1 ? ' selected' : '' ).'>In neuem Fenster &ouml;ffnen</option>
         </select>
        </td>
       </tr>
       <tr>
        <td colspan=3" style="text-align:center">
         <input type="submit" value=" Senden " id="border-tab"/>
         <input type="hidden" name="action" value="'.$action.'" />
        </td>
       </tr></table></form>';

     return $back;
 }
 //CODE
 if( $config['qlinks'] == 0 )
 {
     $data['work'] = '<b>Quicklinks</b><br /><br />
      ...sind nicht aktiviert. Aktivieren kannst Du sie <a href="basics.php">hier</a>.';
 }
 else
 {
     $data['work'] = '<b>Quicklinks</b><br /><br />';
     // delLink ----------------------------------------------
     if( $action == 'delLink' )
     {
         db_query("DELETE FROM ".$pref."qlink WHERE linkid='$_GET[linkid]'");
         db_query("OPTIMIZE TABLE ".$pref."qlink");

         $data['work'] .= 'Link gel&ouml;scht.';
         $action = '';
     }
     // editLink ----------------------------------------------
     if( $action == 'editLink' )
     {
         $r_qlink = db_query("SELECT
             *
         FROM ".$pref."qlink WHERE linkid='$_GET[linkid]'");
         $qlink = db_result( $r_qlink );
         $data['work'] .= '<hr><b>Quicklink editieren</b><br />';
         $data['work'] .= qlinkForm( $qlink, 'updateQlink' );
     }
     // updateQlink ----------------------------------------------
     if( $action == 'updateQlink' )
     {
         $qlink = $_POST['qlink'];
         if( !$qlink['link'] || !$qlink['name'] )
             $data['work'] .= 'Du mu&szlig;t wenigstens URL und Name angeben.';
         else
         {
             if( substr($qlink['link'], 0, 7) != "http://" )
                 $qlink['link'] = "http://" . $qlink['link'];
             db_query("UPDATE ".$pref."qlink SET
                 linkname='".addslashes($qlink['name'])."',
                 linklink='".addslashes($qlink['link'])."',
                 linkcount='".intval($qlink['count'])."',
                 linkart='".intval($qlink['art'])."',
                 linkalt='".addslashes($qlink['alt'])."',
                 linkstatus='$qlink[status]'
             WHERE linkid='$qlink[id]'");
             $data['work'] .= 'Link editiert.';
             $action = '';
         }
     }
     // addQlink ----------------------------------------------
     if( $action == 'addQlink' )
     {
         $qlink = $_POST['qlink'];
         if( !$qlink['link'] || !$qlink['name'] )
             $data['work'] .= 'Du mu&szlig;t wenigstens URL und Name angeben.';
         else
         {
             if( substr($qlink['link'], 0, 7) != "http://" )
                 $qlink['link'] = "http://" . $qlink['link'];
             db_query("INSERT INTO ".$pref."qlink SET
                 linkname='".addslashes($qlink['name'])."',
                 linklink='".addslashes($qlink['link'])."',
                 linkcount='".intval($qlink['count'])."',
                 linkart='".intval($qlink['art'])."',
                 linkalt='".addslashes($qlink['alt'])."',
                 linkstatus='$qlink[status]'");
             $data['work'] .= 'Link gespeichert.';
             $action = '';
         }
     }
     // list qlinks -------------------------------------------
     if( $action == '' )
     {
         $r_qlink = db_query("SELECT
             *
         FROM ".$pref."qlink");
         if( db_rows( $r_qlink ) == 0 )
             $data['work'] .= 'Keine quicklinks gespeichert.';
         else
         {
             $data['work'] .= '<table width="100%" cellpadding="4" cellspacing="0" border="0">
             <tr>
              <td><i>Link</i></td>
              <td>&nbsp;</td>
              <td><i>Name</i></td>
              <td>&nbsp;</td>
              <td><i>Optionen</i></td>
             </tr>';
             $i = 0;
             while( $a_qlink = db_result( $r_qlink ) )
             {
                 $data['work'] .= '<tr bgcolor="'.( $i % 2 == 0 ? '#DADADA' : '' ).'">
                  <td><a href="'.$a_qlink['linklink'].'">'.$a_qlink['linklink'].'</a></td>
                  <td>&nbsp;</td>
                  <td>'.$a_qlink['linkname'].'</td>
                  <td>&nbsp;</td>
                  <td>
                   <a href="qlink.php?action=editLink&linkid='.$a_qlink['linkid'].'">Editieren</a> |
                   <a href="qlink.php?action=delLink&linkid='.$a_qlink['linkid'].'">L&ouml;schen</a>
                  </td>
                 </tr>';
                 $i++;
             }
             $data['work'] .= '</table>';
         }
         // (addlink)
         $data['work'] .= '<hr><b>Quicklink anlegen</b><br />';
         $data['work'] .= qlinkForm( array(), 'addQlink' );
     }
 }

 $data['javascript'] = '';
 echo Template( Get_Template( 'templates/admin.html' ) );
 tb_footer();
?>
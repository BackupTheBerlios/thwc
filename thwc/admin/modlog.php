<?php
 /* $Id: modlog.php,v 1.1 2003/06/12 13:59:34 master_mario Exp $ */
 include( 'adhead.inc.php' );

 $data['work'] = '<b>Moderatorenlogs</b><br />';



 // deleteModlog ---------------------------------------------------------------------------
 if( $action == 'deleteModlog' )
 {
     if( $admin['is_uradmin'] != 1 )
         $data['work'] = 'Dazu hast Du kein Recht, das darf nur der UrAdmin.';
     else
     {
         $delete = $_POST['delete'];
         foreach( $delete as $key=>$value )
         {
             db_query("DELETE FROM ".$pref."modlog WHERE logtime='$key'");
         }
         db_query("OPTIMIZE TABLE ".$pref."modlog");
     }
     $action = '';
 }

 // show Modlogs -------------------------------------------------------------------------------
 $data['work'] .= '<center>';
 if( $admin['is_uradmin'] == 1 )
 {
     $data['work'] .= '<form action="modlog.php" method="post">';
 }
 $data['work'] .= '<table cellpadding="2" cellspacing="0" border="0">
   <tr>
    <td><i>Zeit</i></td>
    <td><i>User</i></td>
    <td><i>IP</i></td>
    <td><i>File</i></td>
    <td><i>Action</i></td>';
 if( $admin['is_uradmin'] == 1 )
 {
     $data['work'] .= '<td>&nbsp;</td><td>&nbsp;</td>';
 }
 $data['work'] .= '</tr>';
 $r_log = db_query("SELECT
     *
 FROM ".$pref."modlog ORDER BY logtime DESC");
 $i=0;
 while( $log = db_result( $r_log ) )
 {
     $data['work'] .= '
      <tr bgcolor="'.( $i % 2 == 0 ? '#D5D5D5' : '' ).'">
       <td id="blank">'.date( "d.m.Y H:i:s", $log['logtime'] ).'&nbsp;&nbsp;&nbsp;</td>
       <td id="blank">'.$log['loguser'].'&nbsp;&nbsp;&nbsp;</td>
       <td id="blank">'.$log['logip'].'&nbsp;&nbsp;&nbsp;</td>
       <td id="blank">'.$log['logfile'].'&nbsp;&nbsp;&nbsp;</td>
       <td id="blank">'.$log['action'].'</td>';
     if( $admin['is_uradmin'] == 1 )
     {
         $data['work'] .= '<td>&nbsp;</td><td><input type="checkbox" name="delete['.$log['logtime'].']" value="1"'.( isset($_GET['selectAll']) ? ' checked' : '' ).'/></td>';
     }
     $data['work'] .= '</tr>';
     $i++;
 }

 $data['work'] .= '</table>';
 if( $admin['is_uradmin'] == 1 )
 {
     $data['work'] .= '<input type="hidden" name="action" value="deleteModlog" /><br />
      <input type="submit" value=" makierte l&ouml;schen " id="border-tab" /></form>
      <a href="modlog.php?selectAll=1">Alle makieren</a>';
 }
 $data['work'] .= '</center>';









 echo Template( Get_Template( 'templates/admin.html' ) );
 tb_footer();
?>
<?php
 /* $Id: basics.php,v 1.1 2003/06/12 13:59:25 master_mario Exp $ */
 include( 'adhead.inc.php' );

 if( isset( $_POST['action'] ) ) $action=$_POST['action'];
 if( isset( $_GET['action'] ) )  $action=$_GET['action'];

 if( $action == 'WriteSettings' )
 {
        //var_dump($Xconfig);
        while( list($key, $value) = each($_POST['Xconfig']) )
        {
                db_query("UPDATE $pref"."registry SET keyvalue='".addslashes($value)."' WHERE keyname='".$key."'");
        }
        $data['work'] = '<b>Basics gespeichert</b>';
 }
 else
 {
     $data['work'] = '<b>Forum Settings</b><br><br>';
     $data['work'] .= '
      <form action="basics.php" method="post">
       <table width="100%" border="0" cellspacing="3" cellpadding="4">';

     $a_registry = array();
     $r_basics = db_query("SELECT
         keyname,
         keyvalue,
         keydescription,
         keydetails,
         keygroup,
         keyorder,
         keytype
     FROM ".$pref."registry ORDER BY keyorder ASC");
     while( $registry = db_result( $r_basics ) )
     {
         $a_registry[$registry['keygroup']][] = $registry;
     }
     mysql_free_result( $r_basics );
     unset( $registry );
     // groups
     $i = 0;
     $r_registrygroup = db_query("SELECT
         keygroupname,
         keygroupid,
         keygrouporder
     FROM ".$pref."registrygroup ORDER BY keygrouporder ASC");
     //print(mysql_num_rows($r_registrygroup));
     while( $registrygroup = mysql_fetch_array($r_registrygroup) )
     {
         $data['work'] .= '
          <tr>
           <td colspan="2" bgcolor="#999999">
            <font size="2" color="white"><b>'.$registrygroup['keygroupname'].'</b></font>
           </td>
          </tr>';
          while( list(, $registry) = @each($a_registry[$registrygroup['keygroupid']]) )
          {
              if( $registry['keygroup'] == 0 ) // 0 -> hide
                  continue;
              $data['work'] .= '
               <tr>
                <td'.($i % 2 == 0 ? ' class="cella"' : ' class="cellb"').' valign="top" width="50%"><b>'.$registry['keydescription'].'</b>';
              if( $registry['keydetails'] )
              {
                  $data['work'] .= '<font size="1"><br>'.$registry['keydetails'].'</font>';
              }
              $data['work'] .= '</td>';
              $data['work'] .= '
               <td'.($i % 2 == 0 ? ' class="cella"' : ' class="cellb"').' valign="top" width="50%">';
              switch( $registry['keytype'] )
              {
                  case 'boolean':
                      $data['work'] .= '
                       <input type="radio" name="Xconfig['.$registry['keyname'].']" value="1"' . ( $registry['keyvalue'] ? " checked" : "" ) . ' id="tab">&nbsp;Yes
                       <input type="radio" name="Xconfig['.$registry['keyname'].']" value="0"' . ( !$registry['keyvalue'] ? " checked" : "" ) . ' id="tab">&nbsp;No';
                      break;
                  case 'integer':
                      $data['work'] .= '<input class="tbinput" type="text" size="6" name="Xconfig['.$registry['keyname'].']" value="'.intval($registry['keyvalue']).'" id="border-tab">';
                      break;
                  case 'array':
                      $data['work'] .= '<textarea class="tbinput" cols="60" rows="8" name="Xconfig['.$registry['keyname'].']">'.htmlspecialchars($registry['keyvalue']).'</textarea>';
                      break;
                  case 'string':
                      $data['work'] .= '<input type="text" class="tbinput" name="Xconfig['.$registry['keyname'].']" value="'.htmlspecialchars($registry['keyvalue']).'" id="border-tab">';
                      break;
              }
              $data['work'] .= '
                </td>
               </tr>';
              $i++;
          }
     }
    $data['work'] .= '</table>
    <br>
    <center>
    <input type="hidden" name="action" value="WriteSettings">
    <input type="submit" name="update_settings" value="Save settings" id="border-tab">
    </center>
    </form>';
 }
 $data['javascript'] = '';
 echo Template( Get_Template( 'templates/admin.html' ) );
 tb_footer();
?>
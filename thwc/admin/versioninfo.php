<?php
/* $Id: versioninfo.php,v 1.2 2003/06/12 14:20:31 master_mario Exp $ */
 include( 'adhead.inc.php' );

 $data['work'] = '<b>Generic information</b><br>';
 $data['work'] .= '
 ThWClone-version: <font color="darkblue">'.$config['version'].'</font><br>
 PHP-version: <font color="darkblue">'.phpversion().'</font><br>
 MySQL-version: <font color="darkblue">'.mysql_get_server_info().'</font><br>';
 $ver = @file('/proc/version');
 if( $ver )
 {
     $data['work'] .= 'OS-version: <font color="darkblue">'.$ver[0].'</font><br>';
 }
 $data['work'] .= '<br>';
 $a_dir = array('../', '../inc/', './', '../mod/');
 $a_dev = array(
     'master_mario'  => 'Mario Pischel');

 $data['work'] .= '<b>ThWboard file versions</b><br>';
 $data['work'] .= '<table width="100%" border="0" cellspacing="0" cellpadding="3">';
 $data['work'] .= '  <tr>
  <td id="blank"><i>Dateiname</i></td>
  <td id="blank"><i>Version</i></td>
  <td id="blank"><i>letzte &Auml;nderung</i></td>
  <td id="blank"><i>Autor</i></td>
 </tr>';

 while( list(, $dir) = each($a_dir) )
 {
     $dp = opendir($dir);
     $a_file = array();
     while( $file = readdir($dp) )
     {
         if( substr($file, -4) == '.php' )
             $a_file[] = $file;
     }
     closedir($dp);
     sort($a_file);
     $data['work'] .= '
      <tr>
       <td colspan="4" id="blank"><br><font color="darkblue">Directory '.$dir.'</font></td>
      </tr>';
     $i = 0;
     while( list(, $file) = each($a_file) )
     {



         $fp = fopen($dir.$file, 'r');
         $datas = fread($fp, 128);
         fclose($fp);
         //^.[$()|*+?{\
         if( ereg('/\* \$'.'Id'.': ([^[:space:]]*) ([^[:space:]]*) ([^[:space:]]*) ([^[:space:]]*) ([^[:space:]]*) ([^[:space:]]*) \$ \*/', $datas, $regs) )
         {
             $data['work'] .= '  <tr bgcolor="'.( $i % 2 == 0 ? '#E5E5E5' : '#F2F2F2').'">
              <td id="blank"><font size="1">'.$file.'</font></td>
              <td id="blank"><font size="1">'.$regs[2].'</font></td>
              <td id="blank"><font size="1">'.$regs[3].' '.$regs[4].'</font></td>
              <td id="blank"><font size="1">'.$a_dev[$regs[5]].'</font></td>
             </tr>';
             $i++;
         }



     }
 }
 $data['work'] .= '</table>';
 $data['javascript'] = '';
 echo Template( Get_Template( 'templates/admin.html' ) );
 tb_footer();
?>
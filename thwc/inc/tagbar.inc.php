<?php
/* $Id: tagbar.inc.php,v 1.1 2003/06/12 13:59:24 master_mario Exp $ */
$textarea = 'text';
$form = 'form';
$laenge = $config['max_post_len'];

$data['javascript'] = "
var lasttag = '';

function textlen ()
{
 textarea = window.document.forms['$form'].elements['$textarea'];
 alert(textarea.value.length + ' Zeichen (Maximal erlaubte L‰nge $laenge Zeichen)');
}

function inserttag(tag)
{
    textarea = window.document.forms['$form'].elements['$textarea'];
    if( tag == 'img' )
    {
        imageurl = prompt( 'Geben sie bitte die URL des Bildes ein.' );
        textarea.value += '[img]' + imageurl + '[/img]';
    }
    else if( tag == 'url' )
    {
        urlurl = prompt( 'Geben sie bitte die URL des Links ein.' );
        urlname = prompt( 'Geben sie bitte den Namen des Links ein.' );
        textarea.value += '[url=\"' + urlurl +'\"]' + urlname + '[/url]';
    }
    else if( tag == 'mail' )
    {
        mail = prompt( 'Geben sie bitte die Mailadresse ein.' );
        mailname = prompt( 'Geben sie bitte den Namen der Adresse ein.' );
        textarea.value += '[mail=\"' + mail +'\"]' + mailname + '[/mail]';
    }
    else
    {
        textarea.value += '[' + tag + ']';
        lasttag = tag;
    }
    textarea.focus();
}

function seticon(icon)
{
        textarea = window.document.forms['$form'].elements['$textarea'];
        textarea.value += ' ' + icon + ' ';
        textarea.focus();
}

function insertcolor(color)
{
    textarea = window.document.forms['$form'].elements['$textarea'];
    colored = prompt( 'Geben sie den gef‰rbten Text ein.' );
    textarea.value += '[color=\"' + color + '\"]' + colored + '[/color]';
    textarea.focus();
}

function closelasttag()
{
    if( lasttag != '' )
    {
        textarea = window.document.forms['$form'].elements['$textarea'];
        textarea.value += '[/' + lasttag + ']';
        lasttag = '';
    }
    textarea.focus();
}

function closealltags()
{
    textarea = window.document.forms['$form'].elements['$textarea'];
    opentags = textarea.value.match(/\\[\w+\\]|\\[-\\]/g).reverse();
    closedtags = textarea.value.match(/\\[\\/\w+\\]|\\[\\/-\\]/g);
    if( opentags )
    {
        for (i=0; i<opentags.length; i++)
        {
            if( closedtags )
            {
                for (j=0; j<closedtags.length; j++)
                {
                    if (opentags[i] == closedtags[j].replace(/\\//, ''))
                    {
                        opentags[i] = '';
                        closedtags[j] = '';
                    }
                }
            }
            if (opentags[i] != '')
            {
                textarea.value += opentags[i].replace(/\\[/, '[/');
            }
        }
    }
    textarea.focus();
}
";

$tagbar = '
<table border="0" cellspacing="2" cellpadding="2">
 <tr>
  <td>
      <input name="button" type="button" id="border-tab" value=" B " onClick="inserttag(\'b\')">
      <input name="button" type="button" id="border-tab" value=" I " onClick="inserttag(\'i\')">
      <input name="button" type="button" id="border-tab" value=" U " onClick="inserttag(\'u\')">
      <input name="button" type="button" id="border-tab" value=" - " onClick="inserttag(\'-\')">
      <select name="select" id="border-tab" onChange="insertcolor(this.options[this.selectedIndex].value)">
        <option value="0">Schriftfarbe</option>
        <option value="skyblue" style="color:skyblue">sky blue</option>
        <option value="royalblue" style="color:royalblue">royal blue</option>
        <option value="blue" style="color:blue">blue</option>
        <option value="darkblue" style="color:darkblue">dark-blue</option>
        <option value="orange" style="color:orange">orange</option>
        <option value="orangered" style="color:orangered">orange-red</option>
        <option value="crimson" style="color:crimson">crimson</option>
        <option value="red" style="color:red">red</option>
        <option value="firebrick" style="color:firebrick">firebrick</option>
        <option value="darkred" style="color:darkred">dark red</option>
        <option value="green" style="color:green">green</option>
        <option value="limegreen" style="color:limegreen">limegreen</option>
        <option value="seagreen" style="color:seagreen">sea-green</option>
        <option value="deeppink" style="color:deeppink">deeppink</option>
        <option value="tomato" style="color:tomato">tomato</option>
        <option value="coral" style="color:coral">coral</option>
        <option value="purple" style="color:purple">purple</option>
        <option value="indigo" style="color:indigo">indigo</option>
        <option value="burlywood" style="color:burlywood">burlywood</option>
        <option value="sandybrown" style="color:sandybrown">sandy brown</option>
        <option value="sienna" style="color:sienna">sienna</option>
        <option value="chocolate" style="color:chocolate">chocolate</option>
        <option value="teal" style="color:teal">teal</option>
        <option value="silver" style="color:silver">silver</option>
      </select>
    </td>
    <td style="padding-left:10px">
      <input name="button" type="button" style="color:red; font-weight:bold" id="border-tab" value=" x " onClick="closealltags()"> '.$style['smallfont'].'Alle Tags schlieﬂen'.$style['smallfontend'].'
    </td>
    <td style="padding-left:10px">
      <button name="button" type="button" id="border-tab" onClick="seticon(\':)\')"><img src="templates/images/icon/smile_new.gif" width="15" height="15" border="0" alt=""></button>&nbsp;
      <button name="button" type="button" id="border-tab" onClick="seticon(\'\;)\')"><img src="templates/images/icon/wink_new.gif" width="15" height="15" border="0" alt=""></button>&nbsp;
      <button name="button" type="button" id="border-tab" onClick="seticon(\':D\')"><img src="templates/images/icon/biggrin_new.gif" width="15" height="15" border="0" alt=""></button>&nbsp;
      <button name="button" type="button" id="border-tab" onClick="seticon(\'=)\')"><img src="templates/images/icon/gumble_new.gif" width="15" height="15" border="0" alt=""></button>&nbsp;
      <button name="button" type="button" id="border-tab" onClick="seticon(\':rolleyes:\')"><img src="templates/images/icon/rolleyes_new.gif" width="15" height="15" border="0" alt=""></button>
    </td>
  </tr>
  <tr>
    <td>
      <input name="button" type="button" id="border-tab" value="http://" onClick="inserttag(\'url\')">
      <input name="button" type="button" id="border-tab" value=" @ " onClick="inserttag(\'mail\')">
      <input name="button" type="button" id="border-tab" value="IMG" onClick="inserttag(\'img\')">
      <input name="button" type="button" id="border-tab" value="Quote" onClick="inserttag(\'quote\')">
    </td>
    <td style="padding-left:10px">
      <input name="button" type="button" style="color:red; font-weight:bold" id="border-tab" value=" x " onClick="closelasttag()"> '.$style['smallfont'].'Aktuelles Tag schlieﬂen'.$style['smallfontend'].'
    </td>
    <td style="padding-left:10px">
      <button name="button" type="button" id="border-tab" onClick="seticon(\':oah:\')"><img src="templates/images/icon/oah_new.gif" width="15" height="15" border="0" alt=""></button>&nbsp;
      <button name="button" type="button" id="border-tab" onClick="seticon(\':?\')"><img src="templates/images/icon/question_new.gif" width="15" height="15" border="0" alt=""></button>&nbsp;
      <button name="button" type="button" id="border-tab" onClick="seticon(\':|\')"><img src="templates/images/icon/strange_new.gif" width="15" height="15" border="0" alt=""></button>&nbsp;
      <button name="button" type="button" id="border-tab" onClick="seticon(\':(\')"><img src="templates/images/icon/frown_new.gif" width="15" height="15" border="0" alt=""></button>&nbsp;
      <button name="button" type="button" id="border-tab" onClick="seticon(\'>:(\')"><img src="templates/images/icon/angry_new.gif" width="15" height="15" border="0" alt=""></button>&nbsp;
      <button name="button" type="button" id="border-tab" onClick="seticon(\':pref:\')"><img src="templates/images/icon/prefect_new.gif" width="15" height="15" border="0" alt=""></button>
    </td>
  </tr>
</table>';
?>
<?php
/* $Id: bcode.inc.php,v 1.1 2003/06/12 13:59:22 master_mario Exp $ */    
/*
          ThWboard - PHP/MySQL Bulletin Board System
        ==============================================
            (c) 2000, 2001 by
               Paul Baecher         <paul@thewall.de>
               Felix Gonschorek   <funner@thewall.de>

          download the latest version:
            http://www.thwboard.de

          This  program is  free  software;  you can
          redistribute it and/or modify it under the
          terms of the GNU General Public License as
          published by the Free Software Foundation;
          either  version 2 of  the License,  or (at
          your option) any later version.

        ==============================================

*/

define('SEARCH', 0);
define('REPLACE', 1);

$a_thwbcode = array();
$a_smilies = array();

function get_smilies()
{
        $a_smilies = array(
                ':)'         => 'smile',
                '&gt;:(' => 'angry',
                ':('        => 'frown',
                ':D'        => 'biggrin',
                ';)'        => 'wink',
                ':?'        => 'question',
                ':|'        => 'strange',
                ':pref:'    => 'prefect',
                '=)'        => 'gumble',
                ':oah:'        => 'oah',
                ':rolleyes:' => 'rolleyes'
        );

        define( 'THWB_SMILIES', true );

        return $a_smilies;
}


function get_thwb_tags()
{
        global $style;

        //ttt: please keep an eye on the order the tags appear;
        //           e.g. noparse /must/ come after [php] and [code]

        // [php]
        $a_thwbcode[SEARCH][] = '/\[php\](.*)\[\/php\]/Uesi';
        $a_thwbcode[REPLACE][] = 'format_phpsource(\'\1\')';
        // [code]
        $a_thwbcode[SEARCH][] = '/\[code\](.*)\[\/code\]/Uesi';
        $a_thwbcode[REPLACE][] = 'format_source(\'\1\')';
        // [noparse] - these are extracted before further parsing
        $a_thwbcode[SEARCH][] = '/\[noparse\](.*)\[\/noparse\]/Uesi';
        $a_thwbcode[REPLACE][] = 'noparse(\'\1\')';
        // http://www.thwboard.de
        $a_thwbcode[SEARCH][] = "/(^|[ \n])([a-zA-Z]+):\/\/([^ ,\"\n]+)/";
        $a_thwbcode[REPLACE][] = '\1<a href="\2://\3" target="_blank">\2://\3</a>';
        // www.thwboard.de
        $a_thwbcode[SEARCH][] = "/(^|[ \n])www\.([^ ,\"\n]+)/i";
        $a_thwbcode[REPLACE][] = '\1<a href="http://www.\2" target="_blank">www.\2</a>';
        // [mail]
        $a_thwbcode[SEARCH][] = '/\[mail\]([._0-9a-zA-Z-]+)@([._0-9a-zA-Z-]+)\[\/mail\]/Ui';
        $a_thwbcode[REPLACE][] = '<a href="mailto:\1@\2">\1@\2</a>';
        // [mail=""]
        $a_thwbcode[SEARCH][] = '/\[mail="([._0-9a-zA-Z-]+)@([._0-9a-zA-Z-]+)"\](.*)\[\/mail\]/Ui';
        $a_thwbcode[REPLACE][] = '<a href="mailto:\1@\2">\3</a>';
        // [color]
        $a_thwbcode[SEARCH][] = '/\[color="([a-zA-Z0-9# ]+)"\](.*)\[\/color\]/Uis';
        $a_thwbcode[REPLACE][] = '<font color="\1">\2</font>';
        // [b]
        $a_thwbcode[SEARCH][] = '/\[b\](.*)\[\/b\]/Uis';
        $a_thwbcode[REPLACE][] = '<b>\1</b>';
        // [i]
        $a_thwbcode[SEARCH][] = '/\[i\](.*)\[\/i\]/Uis';
        $a_thwbcode[REPLACE][] = '<i>\1</i>';
        // [u]
        $a_thwbcode[SEARCH][] = '/\[u\](.*)\[\/u\]/Uis';
        $a_thwbcode[REPLACE][] = '<u>\1</u>';
        // [-]
        $a_thwbcode[SEARCH][] = '/\[-\](.*)\[\/-\]/Uis';
        $a_thwbcode[REPLACE][] = '<s>\1</s>';
        // [quote]
        $a_thwbcode[SEARCH][] = '/\[quote\]/Ui';
        $a_thwbcode[REPLACE][] = '<br><table border="0" align="center" width="95%" cellpadding="3" cellspacing="1" bgcolor="' . $style['border_col'] . '"><tr><td>' . $style['smallfont'] . '<b>Zitat:</b>' . $style['smallfontend'] . '</td></tr><tr><td bgcolor="' . $style['CellA'] . '"><font size="2">' . '\1';
        // [/quote]
        $a_thwbcode[SEARCH][] = '/\[\/quote\]/Ui';
        $a_thwbcode[REPLACE][] = '</font></td></tr></table><br>';
        // [url]www.thwboard.de[/url]
        $a_thwbcode[SEARCH][] = "/\[url\]www\.([^ ,\"\n]+)\[\/url\]/Ui";
        $a_thwbcode[REPLACE][] = '<a href="http://www.\1" target="_blank">www.\1</a>';
        // [url]http://www.thwboard.de[/url]
        $a_thwbcode[SEARCH][] = "/\[url\]([a-zA-Z]+):\/\/([^ ,\"\n]+)\[\/url\]/Ui";
        $a_thwbcode[REPLACE][] = '<a href="\1://\2" target="_blank">\1://\2</a>';
        // [url=""]
        $a_thwbcode[SEARCH][] = "/\[url=\"([a-zA-Z]+):\/\/([^ ,\"\n]+)\"\](.*)\[\/url\]/Usi";
        $a_thwbcode[REPLACE][] = '<a href="\1://\2" target="_blank">\3</a>';

        define( 'THWB_TAGS', true );

        return $a_thwbcode;
}


class CStack {
        var $tos;
        var $stack;

        function CStack()
        {
                $this->stack = array();
                $this->tos = 0;
                $this->stack[$this->tos] = 0; // simplifies things a bit =)
        }
        function push( $var ) {        $this->stack[++$this->tos] = $var; }
        function peek()        { return $this->stack[$this->tos]; }
        function pop()
        {
                if( $this->tos )
                        return $this->stack[$this->tos--];
                else
                        return 0;
        }

        //ttt: now supports regexps
        function search( $var, $preg=0 )
        {
                for( $i = $this->tos; $i > 0; $i-- )
                {
                        if( $preg )
                        {
                                if( preg_match( $this->stack[$i], $var ) )
                                        return 1;
                        }
                        else
                        {
                                //echo "Stack:". $this->tos." $var\n";
                                if( !strcmp($this->stack[$i], $var) )
                                        return 1;
                        }
                }
                return 0;
        }
}


function close_tags( &$tags, &$s_pos, &$string, $curtag='' )
{
        if( strlen($curtag) > 0 )
        {
                if( !$tags->search($curtag) )
                {
                        //echo "removing $curtag";
                        // no corresponding start tag, remove this tag
                        $string = substr($string, 0, $s_pos - 1) . substr($string, $s_pos + strlen($curtag)+2);
                        $s_pos -= strlen($curtag)+3; //HACK: keep string positioning consistent
                        return;
                }
        }
        else
        {
                $curtag = "###"; // just a dummy
        }

        $oldtag = $tags->peek();
        while( $oldtag && ($oldtag != $curtag)  )
        {
                //echo "inserting /$curtag";
                // missing end tag, just insert one
                $string = substr($string, 0, $s_pos-1) .'[/'. $tags->pop() .']'. substr($string, $s_pos-1);
                $s_pos += strlen($oldtag) + 3; // skip over [/$oldtag]
                $oldtag = $tags->peek();

                //echo "\n\n ---- $string ---- \n\n";
        }
        $tags->pop();
}


function fixup_quotes( $string )
{
        // skip the whole thing when there are no quotes
        if( !(strchr($string,'[quote]') || strchr($string,'[/quote]')) )
                return $string;

        $tmp = $string;
        $s_pos = 0; // $s_pos is position in $string
        $t_pos = 0; // $t_pos is position in $tmp
        $tags = new CStack();

        while( is_integer($t_pos = strpos($tmp, '[')) )
        {
                $s_pos += $t_pos+1;
                $tmp = substr( $tmp, $t_pos+1 );

                $endpos = strpos( $tmp, ']' );
                if( is_integer($endpos) ) {
                        $curtag = substr( $tmp, 0, $endpos );
                        //$curtag = substr( $tmp, strpos($tmp, '/'), $endpos );
                        //echo "$curtag\n";
                        switch( $curtag )
                        {
                                case 'quote':
                                        $tags->push($curtag);
                                        //echo "PStack:". $tags->tos."\n";
                                        break;
                                case '/quote':
                                        //echo "Stack:". $tags->tos."\n";
                                        close_tags( $tags, $s_pos, $string, substr($curtag, 1) );
                                        break;
                                default:
                                        //ttt: don't be fooled by [[quote] stuff
                                        $endpos = -1;
                                        break;
                        }
                        $s_pos += $endpos+1;
                        $tmp = substr( $string, $s_pos );
                }
        }

        // if there are still some endtags missing, add them at the end
        $s_pos = strlen( $string ) +1; // normally this should be -1, but close_tags moves back 2 chars
        close_tags( $tags, $s_pos, $string );

        return $string;
}


function noparse($string, $insert = 0)
{
        static        $rpc, $a_replace;

        if( !$insert )
        {
                $string = str_replace('\"', '"', $string);
                $a_replace[++$rpc] = $string;
                return "<noparse $rpc>";
        }
        else
        {
                return $a_replace[$insert];
        }
}


function format_source($string)
{
        global $style;

        $string = str_replace('\"', '"', $string);
        $string = str_replace('  ', '&nbsp;&nbsp;', $string);
        $string = str_replace("\n", '<br>', $string);

        return '<br><table border="0" align="center" width="95%" cellpadding="3" cellspacing="1" bgcolor="' . $style['border_col'] . '"><tr><td><font size="1"><b>Quellcode:</b></font></td></tr><tr><td bgcolor="' . $style['CellA'] . '"><code>[noparse]' . $string . '[/noparse]</code></td></tr></table><br>';
}


function format_phpsource($string)
{
        global $style;

        $string = trim($string);
        $string = str_replace('\"', '"', $string);

        if( (float)(phpversion()) >= 4.2 )
        {
                $string = str_replace("<br>", "\n", $string);
                $string = str_replace('&lt;', '<', $string);
                $string = str_replace('&gt;', '>', $string);

                //ttt: automatically insert < ?php if necessary
                if( !preg_match( '/^\<\?(php)?/s', $string ) )
                        $string = "<?php\n". $string;

                if( !preg_match( '/\?\>$/s', $string ) )
                        $string .= "\n?>";

                $string = highlight_string($string, TRUE);
        }

        return '<br><table border="0" align="center" width="95%" cellpadding="3" cellspacing="1" bgcolor="' . $style['border_col'] . '"><tr><td><font size="1"><b>PHP-Quellcode:</b></font></td></tr><tr><td bgcolor="' . $style['CellA'] . '"><code>[noparse]' . $string . '[/noparse]</code></td></tr></table><br>';
}


function preparse_code($string)
{
        $string = trim($string);
        $string = str_replace("\r", '', $string);
        $string = str_replace(chr(160), '', $string);

        $string = str_replace("con\\con", '', $string);
        $string = str_replace("con/con", '', $string);

        // [code] tags.
        $string = str_replace("\t", '    ', $string);

        $string = ereg_replace("\[code\]([ \n]*)", '[code]', $string);
        $string = ereg_replace("\[/code\]([ \n]*)", '[/code]', $string);
        $string = ereg_replace("\[php\]([ \n]*)", '[php]', $string);
        $string = ereg_replace("\[/php\]([ \n]*)", '[/php]', $string);
        $string = ereg_replace("\[quote\]([ \n]*)", '[quote]', $string);
        $string = ereg_replace("\[/quote\]([ \n]*)", '[/quote]', $string);

        return $string;
}


function parse_code($string, $do_br = 0, $do_img = 0, $do_code = 0, $do_smilies = 0)
{
        global $config, $style;
        static $smilies_fixed = 0;

        // HTML-Security
        $string = str_replace('&', '&amp;', $string);
        $string = str_replace('<', '&lt;', $string);
        $string = str_replace('>', '&gt;', $string);

        if( $do_code )
        {
                global $a_thwbcode;

                if( !defined('THWB_TAGS') )
                        $a_thwbcode = get_thwb_tags();

                //echo "<pre style=\"font-size:8pt\">";
                // nested [quote] fixup
                $string = fixup_quotes($string);
                //echo "$string</pre>";
                $string = preg_replace($a_thwbcode[SEARCH], $a_thwbcode[REPLACE], $string );
        }

        if( $do_img && ($config['imageslevel'] != 2) )
                $string = preg_replace('/\[img\]([a-zA-Z]+):\/\/([^ ,\"\n]+)\[\/img\]/Usi', '<img src="\1://\2.\3" alt="" border="0">', $string);

        if( $do_smilies && $config['smilies'] )
        {
                global $a_smilies;

                if( !defined('THWB_SMILIES') )
                        $a_smilies = get_smilies();

                if( !$smilies_fixed )
                {
                        reset($a_smilies);
                        $url_prepend = '<img src="templates/images/icon/';
                        while( current( $a_smilies ) )
                        {
                                $a_smilies[key($a_smilies)] = $url_prepend.current($a_smilies).'_new.gif" width="15" height="15" border="0">';
                                next( $a_smilies );
                        }
                        $smilies_fixed = 1;
                }

                $string = strtr( $string, $a_smilies );
        }

        // reinsert extracted parts
        $string = preg_replace( '/\<noparse ([0-9]+)\>/e', 'noparse("",\1)', $string );

        if( $do_br )
                $string = str_replace("\n", '<br>', $string);
        else
                $string = str_replace("\n", '', $string);

        return $string;
}
?>
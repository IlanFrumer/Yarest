<?php

namespace Yarest\Parse;

/**
 * Yarest DocComment class.
 *
 * @package Yarest
 * @author Ilan Frumer <ilanfrumer@gmail.com>
 */

use \dflydev\markdown\MarkdownExtraParser as Markdown;
use \Yarest\Helpers\Collection as Collection;

class DocComment extends \Yarest\ReadOnlyArray
{
    public function __construct($comment)
    {
        $this->values['var']    = array();
        $this->values['return'] = array();
        $this->values['group']  = array();

        $lines = $this->commentSplit($comment);

        $this->parse($lines);

    }

    private function commentSplit($comment)
    {
        $pattern = '/(^\/\*\*)|(^\s*\**[ \/]?)|\s(?=@)|\s\*\//m';
        $striped_comment = preg_replace($pattern, '', $comment);
        $lines = preg_split("/(\r?\n)/", $striped_comment);
        $lines = array_map('trim', $lines);
        return $lines;
    }

    private function parseVar($values)
    {
        

        $var = array();

        $var['expression'] = null;

        $values = preg_replace_callback('/"([^"]*)"/', function ($a) use (&$var) {
            $var['expression'] = isset($a[1]) ? $a[1] : null;
        }, $values);


        $var['default'] = null;

        $values = preg_replace_callback('/\[([\w]*)\]/i', function ($a) use (&$var) {
            $var['default'] = isset($a[1]) ? $a[1] : null;
        }, $values);
        
        $values = trim($values);
        
        list($var['name'],$var['desc']) = Collection::splitBySpaces($values, 2);

        return $var;
    }

    private function parse(array $lines)
    {

        $short = array();
        $long  = array();

        $at_sign = false;
        $break_line = false;

        $group = null;

        foreach ($lines as $line) {

            if (strlen($line) == 0) {
                $group = null;
                if (!empty($short)) {
                    $break_line = true;
                }

            } elseif ($line[0] === "@") {

                $at_sign = true;

                if (preg_match('/^@([a-z_]+)\s+(.+)/i', $line, $matches)) {

                    $param  = $matches[1];
                    $values = $matches[2];

                    if ($param == 'group') {

                        $group = $values;

                    } else {

                        if ($param == 'var') {

                            $values = $this->parseVar($values);

                        } elseif ($param == 'return') {
                            
                            $values = preg_split('/\s+/', $values, 3);
                            $map = array('name','type','desc');
                            $values = Collection::mapAssoc($map, $values);
                        }

                        if (is_null($group)) {
                            $this->values[$param][] = $values;
                        } else {
                            $this->values['group'][$group][$param][] = $values;
                        }
                    }
                }
                
            } elseif ($at_sign == false) {
                
                if ($break_line) {
                    $long[]  = $line;
                } else {
                    $short[] = $line;
                }
            }
        }

        $markdownParser = new Markdown();

        $this->values['short'] = implode(" ", $short);
        $this->values['long'] = $markdownParser->transformMarkdown(implode("\r\n", $long));
    }
}

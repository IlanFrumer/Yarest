<?php

namespace Yarest\Parse;

/**
 * Yarest DocComment class.
 *
 * @package Yarest
 * @author Ilan Frumer <ilanfrumer@gmail.com>
 */

class DocComment extends \Yarest\ReadOnlyArray
{
    public function __construct($comment)
    {
        $this['var']    = array();
        $this['return'] = array();

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

    private function parseVar(array $values)
    {
        
        $var = array();

        $var['expression'] = null;

        $values = preg_replace_callback('/"([^"]*)"/', function ($a) use (&$v) {
            $var['expression'] = isset($a[1]) ? $a[1] : null;
        }, $values);

        $var['default'] = null;

        $values = preg_replace_callback('/\[([a-z0-9]+)\]/i', function ($a) use (&$v) {
            $var['default'] = isset($a[1]) ? $a[1] : null;
        }, $values);
        
        list($var['name'],$var['desc']) = Helpers\Collection::splitBySpaces($values, 2);

        return $var;
    }

    private function parse(array $lines)
    {

        $short = array();
        $long  = array();

        $mode = 0; // pre 0 | short 1 | long 2 | params 3

        foreach ($this->lines as $line) {

            if (strlen($line) == 0) {
                
                if ($mode == 1) {
                    $mode++;
                }

            } elseif (preg_match('/^@([a-z_])+\s(.+)/i', $line, $matches)) {

                $param  = $matches[1];
                $values = $matches[2];

                if ($param == 'var') {

                    $values = $this->parseVar($values);

                } elseif ($param == 'return') {
                    
                    $values = preg_split('/\s+/', $values, 3);
                    $map = array('name','type','desc');
                    $values = Helpers\Collection::mapAssoc($map, $values);

                }
                
                $this[$param][] = $values;
                
            } elseif ($mode < 2) {
                $mode = 1;
                $short[] = $line;
            } elseif ($mode == 2) {
                $long[]  = $line;
            }
        }

        $markdownParser = new \dflydev\markdown\MarkdownExtraParser();

        $this['short'] = implode(" ", $short);
        $this['long'] = $markdownParser->transformMarkdown(implode("\r\n", $long));
    }
}

<?php

namespace Yarest;

/**
 * Yarest helpers class.
 *
 * @package Yarest
 * @author Ilan Frumer <ilanfrumer@gmail.com>
 */
class Helpers
{

    /**
     * [parseComment description]
     * @param  [type] $comment
     * @return [type]
     */
    public static function parseComment($comment)
    {
        $object = array();

        $pattern = '/(^\/\*\*)|(^\s*\**[ \/]?)|\s(?=@)|\s\*\//m';
        $comment = preg_replace($pattern, '', $comment);
        $comments = preg_split("/(\r?\n)/", $comment);
        $comments = array_filter($comments);

        foreach ($comments as $comment) {
            
            $comment = preg_split('/\s+/', $comment);
            $param   = array_shift($comment);
            $object[$param][] = $comment;

        }

        return $object;
    }
}

<?php
/*
Написать функцию, которая на входе принимает массив из открывающихся или закрывающихся тегов  (например “<a>”, “</td>” ),  
а возвращает результат проверки корректности:  т.е. является ли принятая функцией последовательность тегов структурой корректного HTML документа. 
Например, последовательность “<a>”, “<div>”, “</div>”, “</a>”, “<span>”, “</span>” - корректная структура, а последовательность “<a>”, “<div>”, ”</a>” - некорректная структура.
Необходимо использовать нативный php без использования библиотек DOMDocument, Simplexml и тд. 
*/

function try_get_tag($tag, $is_open_tag = true)
{
    $close_char = $is_open_tag ? '' : '\/';

    $matches = [];
    preg_match("/<{$close_char}\s*(\w+).*?>/", $tag, $matches);

    return !empty($matches[1]) ? $matches[1] : false;
}

function check_html_struct($html_tags = [])
{

    if (empty($html_tags) || substr(trim($html_tags[0]), 0, 2) == "</") {
        return false;
    }

    $html_tags = array_filter($html_tags, function ($tag) {
        return substr($tag, -2) != "/>" || !in_array(
            try_get_tag($tag),
            [
                '<area>', '<base>', '<br>', '<col>',
                '<command>', '<embed>', '<hr>', '<img>',
                '<input>', '<keygen>', '<link>', '<meta>',
                '<param>', '<source>', '<track>', '<wbr>'
            ]
        );
    });

    if (empty($html_tags)) {
        return true;
    }

    if (count($html_tags) % 2 > 0) {
        return false;
    }

    $tags = [];

    foreach ($html_tags as $tag) {
        $tag = trim($tag);
        $open_tag = try_get_tag($tag, true);

        if ($open_tag !== false) {
            $tags[] = $open_tag;
            continue;
        }

        $close_tag = try_get_tag($tag, false);

        if ($close_tag !== false) {
            $last_tag = array_pop($tags);
            $result = $last_tag == $close_tag ? true : false;
        }

        if (!$result) {
            break;
        }
    }

    return $result;
}

$result = check_html_struct(["<hr>", "<div>", "</div>", "<br>", "<span>", "</span>"]);

echo $result ? "VALID" : "INVALID";

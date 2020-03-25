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

/**
 * Проверяем, является ли тег одиночным
 * 
 * @param string $tag тег
 * @param bool $validate_parenthesis флаг, который указывает, нужно ли проверять открывающие\закрывающие скобки
 * 
 */
function is_single_tag($tag, $validate_parenthesis = true)
{
    // Получаем тип тега без скобок
    $tag_type = try_get_tag($tag);

    if (!$validate_parenthesis) {
        $tag_type = try_get_tag($tag, false);
    }

    // Проверка скобок
    $parenthesis_check = $validate_parenthesis ? substr($tag, 0, 2) != "</" && substr($tag, 0, 1) == "<" && (substr($tag, -2) == "/>" || substr($tag, -1) == ">") : true;
    // Проверка типа
    $type_check = in_array(
        $tag_type,
        [
            'area', 'base', 'br', 'col', 'command',
            'embed', 'hr', 'img', 'input', 'keygen',
            'link', 'meta', 'param', 'source', 'track', 'wbr'
        ]
    );

    return $parenthesis_check && $type_check;
}

function check_html_struct($html_tags = [])
{
    // Если последовательность пуста или начинается с закрывающегося тега, то она не валидна
    if (empty($html_tags) || substr(trim($html_tags[0]), 0, 2) == "</") {
        return false;
    }

    $html_tags = array_filter($html_tags, function ($tag) {
        return !is_single_tag($tag);
    });

    // Убрали все одиночные теги. Если массив после этого пуст => полседовательность валидна
    if (empty($html_tags)) {
        return true;
    }

    // Т.к. остались только парные теги, их кол-во должно быть чётным
    if (count($html_tags) % 2 > 0) {
        return false;
    }

    $tags = [];

    foreach ($html_tags as $tag) {
        $tag = trim($tag);

        // Проверяем является ли тег плохим
        // Если полсе фильтрации последовательности остались одинарные
        // теги, то это значит что последовательность не валидна
        // Если есть парный тег записаный как одинарный (<div/>) последовательность также не валидна
        if (is_single_tag($tag, false) || substr($tag, -2) == "/>") {
            $result = false;
            break;
        }


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

    return $result && count($tags) == 0;
}

$result = check_html_struct(["<div>", "</div>", "<br>", "<span>", "<a/>", "</span>", "</hr/>"]);

echo $result ? "VALID" : "INVALID";

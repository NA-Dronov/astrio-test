<?php
/*
1. Дан массив "категории". Каждая категория имеет следующие параметры:
"id" — уникальный числовой идентификатор категорий
"title" — название категории
"children" - дочерние категории (массив из категорий)

Вложенность категории неограниченна (дочерние категории могу иметь свои вложенные категории и т.д.)

Пример массива :

$categories = array(
	array(
   	"id" => 1,
   	"title" =>  "Обувь",
   	'children' => array(
       	array(
           	'id' => 2,
           	'title' => 'Ботинки',
           	'children' => array(
               	array('id' => 3, 'title' => 'Кожа'),
               	array('id' => 4, 'title' => 'Текстиль'),
           	),
       	),
       	array('id' => 5, 'title' => 'Кроссовки',),
   	)
	),
	array(
   	"id" => 6,
   	"title" =>  "Спорт",
   	'children' => array(
       	array(
           	'id' => 7,
           	'title' => 'Мячи'
       	)
   	)
	),
);

Необходимо написать функцию searchCategory($categories, $id), которая по идентификатору категории возвращает название категории.
*/

$categories = array(
    array(
        "id" => 1,
        "title" =>  "Обувь",
        'children' => array(
            array(
                'id' => 2,
                'title' => 'Ботинки',
                'children' => array(
                    array('id' => 3, 'title' => 'Кожа'),
                    array('id' => 4, 'title' => 'Текстиль'),
                ),
            ),
            array('id' => 5, 'title' => 'Кроссовки',),
        )
    ),
    array(
        "id" => 6,
        "title" =>  "Спорт",
        'children' => array(
            array(
                'id' => 7,
                'title' => 'Мячи'
            )
        )
    ),
);

function searchCategory($categories, $id)
{
    if (!is_array($categories) || empty($categories) || intval($id) < 1) {
        return null;
    }

    $result = array_filter($categories, function ($category) use ($id) {
        return $category['id'] == $id;
    });

    if (!empty($result)) {
        return array_shift($result)['title'] ?? null;
    }

    $categories_with_children = function ($category) {
        return !empty($category['children']) &&
            is_array($category['children']);
    };

    foreach (array_filter($categories, $categories_with_children) as $child) {
        $result = searchCategory($child['children'], $id);

        if (isset($result)) {
            break;
        }
    }

    return is_array($result) ? null : $result;
}

$result = searchCategory($categories, 4);

echo $result;

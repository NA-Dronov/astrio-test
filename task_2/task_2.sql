/*
Выводит название отделов, в которых имеется 5 и более сотрудников
*/
SELECT
    `name`
FROM
    `department` AS `d`
INNER JOIN `worker` AS `w`
ON
    `d`.`id` = `w`.`department_id`
GROUP BY
    `d`.`id`
HAVING
    COUNT(`w`.`id`) > 5;
/*
Выводит 2 столбца, в первом выводится название отдела, во втором id всех сотрудников данного отдела, перечисленные через запятую
*/
SELECT
    `d`.`name`,
    GROUP_CONCAT(`w`.`id` SEPARATOR ',') AS `worker_ids`
FROM
    `department` AS `d`
LEFT JOIN `worker` AS `w`
ON
    `d`.`id` = `w`.`department_id`
GROUP BY
    `d`.`id`;
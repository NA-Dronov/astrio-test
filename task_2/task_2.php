<?php

require __DIR__ . '/vendor/autoload.php';

$config = require_once 'config.php';

function db_query($pdo, $statement)
{
    $statement = $pdo->prepare($statement);
    $result = $statement->execute();
    return $result;
}

function db_get_seed_statement($pdo, $table)
{
    $columns_statement = $pdo->prepare("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = :table");
    $columns_statement->bindValue(':table', $table, PDO::PARAM_STR);
    $columns_statement->execute();
    $columns = [];
    while ($row = $columns_statement->fetch(PDO::FETCH_ASSOC)) {
        if ($row['COLUMN_NAME'] == 'id') {
            continue;
        }
        $columns[] = $row['COLUMN_NAME'];
    }
    $columns_string = implode(', ', $columns);

    $placeholders = array_map(function ($el) {
        return ":{$el}";
    }, $columns);
    $placeholders_string = implode(", ", $placeholders);

    $statement = $pdo->prepare("INSERT INTO {$table} ({$columns_string}) VALUES ({$placeholders_string})");

    return $statement;
}

try {
    $pdo = new PDO("mysql:host={$config['hostname']};dbname={$config['dbname']}", $config['login'], $config['password']);

    if (!empty($config['refresh'])) {
        $faker = Faker\Factory::create();
        // Department
        db_query($pdo, "TRUNCATE TABLE `department`");

        $department_statement = db_get_seed_statement($pdo, 'department');

        $created_departments = [];
        for ($i = 0; $i < $config['departments_count']; $i++) {
            $department_statement->bindValue(':name', $faker->company, PDO::PARAM_STR);
            $department_statement->execute();
            $created_departments[] = $pdo->lastInsertId();
        }

        // Users
        db_query($pdo, "TRUNCATE TABLE `worker`");
        $user_statement = db_get_seed_statement($pdo, 'worker');

        $created_users = [];
        for ($i = 0; $i < $config['users_count']; $i++) {
            $user_statement->bindValue(':firstname', $faker->firstName, PDO::PARAM_STR);
            $user_statement->bindValue(':lastname', $faker->lastName, PDO::PARAM_STR);
            $user_statement->bindValue(':middlename', $faker->lastName, PDO::PARAM_STR);
            $user_statement->bindValue(':department_id', $created_departments[array_rand($created_departments, 1)], PDO::PARAM_INT);
            $user_statement->execute();
            $created_users[] = $pdo->lastInsertId();
        }
    }

    $five_or_more_workers_statement = "SELECT `name` FROM `department` as `d` INNER JOIN `worker` as `w` ON `d`.`id` = `w`.`department_id` GROUP BY `d`.`id` HAVING COUNT(`w`.`id`) > 5";
    $five_or_more_workers = $pdo->prepare($five_or_more_workers_statement);
    $five_or_more_workers->execute();

    echo "<b>{$five_or_more_workers_statement}</b><br>";
    echo '<pre>';
    print_r($five_or_more_workers->fetchAll(PDO::FETCH_ASSOC));
    echo '</pre>';

    $department_and_workers_statement = "SELECT `d`.`name`, GROUP_CONCAT(`w`.`id` SEPARATOR ',') as `worker_ids` FROM `department` as `d` LEFT JOIN `worker` as `w` ON `d`.`id` = `w`.`department_id` GROUP BY `d`.`id`";
    $department_and_workers = $pdo->prepare($department_and_workers_statement);
    $department_and_workers->execute();

    echo "<b>{$department_and_workers_statement}</b><br>";
    echo '<pre>';
    print_r($department_and_workers->fetchAll(PDO::FETCH_ASSOC));
    echo '</pre>';
} catch (Throwable $ex) {
    echo 'ERROR: ' . $ex->getMessage();
} finally {
    $pdo = null;
    die();
}

<?php // Получение данных по наличию варнингов
include ('blocks/variables.php'); // Подключаю переменные
$mysql_warning_info = new mysqli('localhost', $db_login, $db_pass, $db_name); // Подключаюсь к БД
$result_mysql_warning_info = $mysql_warning_info->query('SELECT COUNT(warning) FROM jira_issue WHERE warning=1 LIMIT 1'); // Проверяю количество задач с варнингом
$result_mysql_warning_info = $result_mysql_warning_info->fetch_assoc(); // Сохраняем результат
if (isset($result_mysql_warning_info)) {
    echo ($result_mysql_warning_info['COUNT(warning)']); // Выводим количество, если нашлось
//    $result_mysql_warning_info_names = $mysql_warning_info->query('SELECT user FROM jira_issue WHERE warning=1'); // Запрашиваю имена авторов запросов
//    for ($warning_info_names = array(); $row = $result_mysql_warning_info_names->fetch_assoc(); $warning_info_names[] = $row) ; //Сохраняю результат в массив
//    foreach ($warning_info_names as $value) {
//        echo "'".$value['user']."' ";
//    }
}
else {
    echo 0; // Иначе выводим 0
}
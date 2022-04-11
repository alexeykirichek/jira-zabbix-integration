<?php // Переменные
// База данных
$db_login = 'jira_critical_issues'; // Логин для подключения к БД
$db_pass = ''; // Пароль для подключения к БД
$db_name = 'jira_critical_issues'; // Название базы данных

// Jira Service Desk
$sd_login = ''; // Логин для авторизации в Jira
$sd_token = ''; // Токен для авторизации в Jira
$companyName = ''; // Название компании для подстановки в ссылку (https://'.$companyName.'.atlassian.net)

// Пути для логирования полученных данных
$jira_log_path = '/var/www/jira_critical_issues/log'; // От Jira
// При изменении путей их нужно поменять вручную в файле rotation_logs.php, который разположен в папке с логами

// Установка рабочего времени сотрудников
$work_time_begin = 9; // С
$work_time_end = 18; // До
?>

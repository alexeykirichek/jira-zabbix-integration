<?php // Скрипт для обработки данных от Jira
include ('blocks/variables.php'); // Переменные
include ('blocks/functions.php'); // Функции
$body = file_get_contents('php://input'); //Получаем в $body json строку от Джиры
save_jira_log($body);// Сохраняем полученные данные в лог
$arr = json_decode($body, true); //Разбираем json запрос на массив
if (isset($arr['webhookEvent'])) { // Проверяем есть ли какое-то обновление
    $data['webhookEvent']=$arr['webhookEvent']; // Сохраняем название обновления в основной массив
    $data['timestamp']=$arr['timestamp']; // Сохраняем время обновления в основной массив
    $data['id_issue']=$arr['issue']['id']; // Сохраняем идентификатор задачи, по которой было получено обновление, в основной массив
    $data['issueKey']=$arr['issue']['key']; // Сохраняем ключ задачи, по которой было получено обновление, в основной массив
    if ($data['webhookEvent']=='jira:issue_created') { // Если поступила информация о том, что была создана новая задача
        $data['user']=$arr['user']['displayName']; // Сохранить имя пользователя создателя задачи
        $data['user_id']=$arr['user']['accountId']; // Сохранить идентификатор создателя задачи
        $data['user_email']=take_email_author_issue($data['user_id']); // Получить e-mail автора запроса
        $data['user_email_domen']=take_domen_email_author_issue($data['user_email']); // Получить домен e-mail адреса автора запроса
        $non_work_time=check_non_work_time(); // Проверить не спят ли сотрудники
        $critical_users=check_critical_users($data['user_email_domen']); // Проверить от критического ли пользователя запрос
        $data['warning']=$non_work_time*$critical_users; // Проверить правдивы ли две предыдущих проверки
        if ($data['warning']==true) {
            save_issue_data($data); // Сохраняю данные в БД
        }
        exit('Ok'); // Завершаю работу, сообщив джире, что данные были корректно получены (сохранил инфу по созданной задаче)
    }
    if ($data['webhookEvent']=='jira:issue_updated') { // Если поступила информация о том, что был обновлен запрос
        $data['user_update']=$arr['user']['displayName']; // Сохраняем имя того, кто обновил запрос, в основной массив
        $data['id_changelog']=$arr['changelog']['id']; // Сохраняем идентификатор обновления в основной массив
        $issue_exist=check_issue_exist($data['issueKey']); // Проверяю если ли в базе такой запрос
        if ($issue_exist==true) {
            save_jira_input_updates($data); // Записываю данные по обновлению в БД
            foreach ($arr['changelog']['items'] as $key => $value) { // Перебираю все действия, полученные в рамках поступившего обновления
                // Сохраняем данные обновления в основной массив
                $data['field_item'] = $value['field'];
                $data['fieldtype_item'] = $value['fieldtype'];
                $data['fieldId_item'] = $value['fieldId'];
                $data['from_item'] = $value['from'];
                $data['fromString'] = $value['fromString'];
                $data['to_item'] = $value['to'];
                $data['toString'] = $value['toString'];
                save_input_changelog_items($data); // Записываю данные по полученному обновлению в БД
                if ($data['fieldId_item'] == 'status') { // Если были произведены изменения текущего статуса задачи
                    disable_warning($data['issueKey']); // Отключаю warning по задаче при необходимости
                }
            }
        }
        exit('Ok'); // Завершаю работу, сообщив джире, что данные были корректно получены (сохранил данные по обновлению запроса)
    }
    exit('Ok'); // Выходим, это не создание задачи и не обновление статуса задачи (нечего делать)
}
else (exit('Отсутствует webhookEvent')); // Завершаю работу, сообщив джире, что данные не были получены (даже webhookEvent не увидел)
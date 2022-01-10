<?php // Функции
function save_jira_log($body) { // Логирование полученных от Джиры данных
    $data_jira_log = new DateTime(); // Сохраняю текущую дату
    $data_jira_log=$data_jira_log->format('d_m_Y'); // Изменяю ее формат
    $file = $jira_log_path.'/jira_log_'.$data_jira_log.'.txt'; // Создаю путь к файлу с логом
    $current = file_get_contents($file); // Открываю файл для получения существующего содержимого или создаю файл, если его нет
    $current .= $body."\n"."---------------------"."\n"; // Пишу в строку полученные данные с разделителем
    file_put_contents($file, $current); // Пишу строку в файл
}
function save_issue_data($data) { // Сохранение данных по созданному в Джире запросу
    include ('variables.php'); // Переменные
    $issueKeynum = substr($data['issueKey'], 5);
    $mysql_save_issue_data = new mysqli('localhost', $db_login, $db_pass, $db_name); // Подключаюсь к БД
    $mysql_save_issue_data->query('INSERT INTO `jira_issue` (`jira_issue_id`, `webhookEvent`, `id_issue`, 
    `issueKey`, `issueKeynum`, `user`, `timestamp`, `update_time`, `warning`) VALUES (NULL, "'.$data['webhookEvent'].'", 
    "'.$data['id_issue'].'", "'.$data['issueKey'].'", "'.$issueKeynum.'", "'.$data['user'].'", "'.$data['timestamp'].'", 
    "'.$data['timestamp'].'", "'.$data['warning'].'");'); // Записываю данные в БД
}
function check_non_work_time() {// Проверяет нерабочее время и возвращяет true, если сейчас никто не работает
    $day_of_the_week = new DateTime(); // Сохраняю текущую дату
    $day_of_the_week=$day_of_the_week->format('l'); // Привожу ее в формат дня недели
    $hour_of_the_day=new DateTime(); // Сохраняю текущую дату
    $hour_of_the_day=$hour_of_the_day->format('H'); // Привожу ее в формат текущего часа
    $hour_of_the_day=(int)$hour_of_the_day; // Преобразую в число
    $date_of_the_day = new DateTime(); // Сохраняю текущую дату
    $date_of_the_day=$date_of_the_day->format('d.m'); // Привожу ее в формат текущей минуты
    if ($day_of_the_week=='Saturday'||$day_of_the_week=='Sunday') {
        return true;
    }
    elseif ($date_of_the_day=='01.01'||$date_of_the_day=='02.01'||$date_of_the_day=='03.01'||$date_of_the_day=='04.01'||
        $date_of_the_day=='05.01'||$date_of_the_day=='06.01'||$date_of_the_day=='07.01'||$date_of_the_day=='08.01'||
        $date_of_the_day=='23.02'||$date_of_the_day=='08.03'||$date_of_the_day=='01.05'||$date_of_the_day=='09.05'||
        $date_of_the_day=='12.06'||$date_of_the_day=='04.11') {
        return true;
    }
    elseif ($hour_of_the_day<9 || $hour_of_the_day>=18) {
        return true;
    }
    else {
        return 0;
    }
}
function check_critical_users($username) { // Проверяет есть ли пользователь в списке приоритетных клиентов
    include ('jira_critical_users.php'); // Подключаем список приоритетных клиентов
    $result_check_jira_critical_users=0; // Устанавливаем изначальное значение для результата работы функции. Он останется неизменным, если ни одно условие не сработает.
    if(!empty($jira_critical_users)) { // Проверяем есть ли в массиве данные
        foreach ($jira_critical_users as $jira_critical_user) { // Перебираем массивjira_issue
            if ($jira_critical_user==$username) { // Проверяем совпадает ли пользователь, создавший задачу, с приоритетным
                $result_check_jira_critical_users=true; // Если совпало, помечаем
                break; // И выходим и цикла перебора массива
            }
        }
    }
    return $result_check_jira_critical_users; // Возвращаем результат
}
function check_issue_exist($issueKey) { // Проверяю есть ли такая задача в БД
    include ('variables.php'); // Подключаю переменные
    $issueKeynum = substr($issueKey, 5);
    $mysql_check_issue_exist = new mysqli('localhost', $db_login, $db_pass, $db_name); // Подключаюсь к БД
    $result_check_issue_exist = $mysql_check_issue_exist->query('SELECT COUNT(*) FROM `jira_issue` WHERE jira_issue.issueKeynum="'.$issueKeynum.'"'); // Проверяю наличие такого запроса
    $issue_exist = $result_check_issue_exist->fetch_assoc(); // Сохраняем результат
    if ($issue_exist['COUNT(*)']!='0') { // Если нашлась задача
        return true; // Возвращаем результат
    }
    else {
        return 0; // Возвращаем результат
    }
}
function save_jira_input_updates($data) { // Сохраняем данные по полученному обновлению запроса
    include ('variables.php'); // Подключаю переменные
    $mysql_jira_input_updates = new mysqli('localhost', $db_login, $db_pass, $db_name); // Подключаюсь к БД
    $mysql_jira_input_updates->query('INSERT INTO `jira_updates` (`jira_update_id`, `id_issue`, 
    `timestamp`, `webhookEvent`, `user_update`, `id_changelog`, `issueKey`) VALUES (NULL, "'.$data['id_issue'].'", 
    "'.$data['timestamp'].'", "'.$data['webhookEvent'].'", "'.$data['user_update'].'", "'.$data['id_changelog'].'", 
    "'.$data['issueKey'].'");'); // Записываю данные по полученному обновлению в БД
    $mysql_jira_input_updates->query('UPDATE `jira_issue` SET `update_time` = "'.$data['timestamp'].'" WHERE `jira_issue`.`id_issue` = "'.$data['id_issue'].'";'); // Обновляю время последнего изменения задачи в таблице с задачами Jira
}
function save_input_changelog_items($data) { // Записываю данные по полученным изменениям в обновлении от Джиры
    include ('variables.php'); // Подключаю переменные
    $mysql_input_changelog_items = new mysqli('localhost', $db_login, $db_pass, $db_name); // Подключаюсь к БД
    $mysql_input_changelog_items->query('INSERT INTO `jira_input_changelog_items` (`id_input_changelog_item`, 
    `id_changelog`, `field_item`, `fieldtype_item`, `fieldId_item`, `from_item`, `to_item`, `fromString`, `toString`) 
    VALUES (NULL, "'.$data['id_changelog'].'", "'.$data['field_item'].'", "'.$data['fieldtype_item'].'", 
    "'.$data['fieldId_item'].'", "'.$data['from_item'].'", "'.$data['to_item'].'", "'.$data['fromString'].'", 
    "'.$data['toString'].'");'); // Записываю данные в БД
}
function disable_warning($issueKey) { // Отлючение варнинга по задаче
    include ('variables.php'); // Подключаю переменные
    $issueKeynum = substr($issueKey, 5);
    $mysql_disable_warning = new mysqli('localhost', $db_login, $db_pass, $db_name); // Подключаюсь к БД
    $result_disable_warning = $mysql_disable_warning->query('SELECT warning FROM `jira_issue` WHERE issueKeynum="' . $issueKeynum . '" LIMIT 1'); // Проверяю наличие варнинга по ключу запроса
    $status_warning = $result_disable_warning->fetch_assoc(); // Сохраняем результат
    if($status_warning['warning']==1) { // Если варнинг есть
        $mysql_disable_warning->query('UPDATE `jira_issue` SET `warning` = "0" WHERE `jira_issue`.`issueKeynum` = "' . $issueKeynum . '";'); // Сохраняю в БД
    }
}
function take_email_author_issue($user_id) {
    include ('variables.php'); // Подключаю переменные
    $auth_token=base64_encode($sd_login.':'.$sd_token); // Подготовка строки для авторизации в Jira
    // Подготовка и отправка запроса в Jira
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://prontosms.atlassian.net/rest/api/2/user?accountId='.$user_id,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Basic '.$auth_token
        ),
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    $author=json_decode($response, true); // Возврат ответа от Jira
    return $author['emailAddress'];
}
function take_domen_email_author_issue($user_email) {
    $pos_dog = strpos($user_email, '@');
    $domen_email = substr($user_email, $pos_dog+1);
    return $domen_email;
}
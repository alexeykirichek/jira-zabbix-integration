# Интеграция Jira и Zabbix

**The English version of the README is available below**

Данный проект представляет собой ПО, позволяющее контролировать наличие запросов от определенного списка пользователей. 

**Дисклеймер**: код проекта максимально простой. Проект не позиционируется как серьезная разработка. Я понимаю, что многое можно сделать правильнее, лучше, логичнее и т.д. Делалось на этапе обучения и с минимальными временными затратами.

## Инструкция по инсталляции:

Установка и настройка практически идентична проекту https://github.com/alexeykirichek/jira-telegram-integration, за исключением настройки Телеграм бота (в данном случае этого не требуется).

Подключить к системе Zabbix можно разными способами. Самый простой - путем вызова через браузер файла fFh48DOoCZ.php, он возвращает 1/0. Данное значение можно сохранить используя zabbix и в нем же настроить уведомление о наличии пометок. Однако, как показала практика, этот способ не надежный да и в принципе не оптимальный - периодически веб сервер не отвечает. Более разумным вариантом будет установить на сервер Zabbix agent и сделать UserParameter с подключеним к базе данных и выполнением sql запроса, подсчитывающего пометки (его можно посмотреть в файле fFh48DOoCZ.php), либо с вызовом этого же файла через консоль.

После выполнения всех настроек, система должна работать. Если у Вас возникли проблемы, свяжитесь со мной через e-mail alexkirichek@yandex.ru, а лучше опишите проблему во вкладке Issues. Я помогу Вам.

# Integration of Jira and Zabbix

** Русская версия README доступна выше**

This project is a software that allows you to monitor the availability of requests from a specific list of users. 

**Disclaimer**: The project code is as simple as possible. The project is not positioned as a serious development. I understand that much can be done more correctly, better, more logically, etc. It was done at the training stage and with minimal time costs.

## Installation Instructions:

Installation and configuration is almost identical to the project https://github.com/alexeykirichek/jira-telegram-integration , with the exception of setting up a Telegram bot (in this case, this is not required).

"Zabbix". The easiest way is by calling a file through the browser fFh48DOoCZ.php , it returns 1/0. "zabbix". However, as practice has shown, this method is not reliable and, in principle, not optimal - periodically the web server does not respond. A more reasonable option would be to install an agent on the Zabbix server and make a UserParameter with a connection to the database and executing SQL for a query counting the marks (you can see it in the file fFh48DOoCZ.php ), or by calling the same file via the console.

After making all the settings, the system should work. If you have any problems, contact me via email alexkirichek@yandex.ru , or better describe the problem in the Problems tab. I'll help you.

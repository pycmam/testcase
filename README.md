
# Пояснительная записка в тестовому заданию

## Примененные инструменты

* Symfony 4
* PostgreSQL
* RabbitMQ
* Vagrant

## Как развернуть

Нужен установленный Vagrant и VirtualBox, в корне проекта выполнить

```
vagrunt up && vagrant ssh
```

Vagrant скачает образ debian/jessie64 и развернет в виртуалке необходимое окружение.

Создать БД, мигрировать и загрузить тестовые данные:

```
cd ~/app
./console doctrine:database:create
./console doctrine:migrations:migrate -n
./console doctrine:fixtures:load -n
```

Запустить воркеры очереди (можно любое количество)

```
php console rabbitmq:consumer balance &
php console rabbitmq:consumer balance &
php console rabbitmq:consumer balance &
```

Команды для тестирования работы воркеров:

```
# показать текущее состояние аккаунта
# app:balance:show <account_id>

php console app:balance:show 1

# зачислить средства
# app:balance:add <account_id> <amount> --lock=<locked>

php console app:balance:add 1 555

# зачислить и поставить в блокировку (модерацию)
php console app:balance:add 1 1000 --lock=1

# списать/вывести средства
# app:balance:sub <account_id> <amount> --lock=<locked>

php console app:balance:sub 1 100

# перевод средств между аккаунтами
# app:balance:sub <source_id> <destination_id> <amount> --lock=<locked>

php console app:balance:transfer 1 2 100

# с блокировкой
php console app:balance:transfer 2 1 200 --lock=1

# подтвердить заблокированную опервацию
# app:lock:approve <lock_id>

php console app:lock:approve 1

# отменить заблокированную операцию
# app:lock:remove <lock_id>

php console app:lock:remove 2

```

Если блокировка была подтверждена, то ее нелья повторно подтвердить или удалить.

При выполнении каждой операции участвующие в ней аккаунты блокируются.
Блокировка аккаунта осуществляется подстановкой PID текущего воркера в поле account.busy_by_pid.
Если поле уже содержит PID другого воркера, то операция возвращается обратно в очередь и живет там,
пока воркер не снимет блокировку.

Таблика аккаунтов не содержит значения текущего и заблокированного баланса,
они вычисляются суммой поля amount по таблицам operation и lock.

На реальной базе, чтобы каждый раз не делать аггрегацию по таблицам, я бы
добавил триггеры на события добавления/изменения данных в таблицах operation и lock,
которые обновляли бы поле поле баланса в таблице account.


## Структура БД

### Entity\Account

Аккаунты пользователей (account)

* id (int, sequence)
* username (string 255)
* busy_by_pid (int, nullable)

### Entity\Operation

Операции с балансом (operation)

* id (int, sequence)
* account_id (int)
* amount (int)
* created (timestamp)

### Entity\Lock

Блокировки - операции на подтверджении (lock)

* id (int, sequence)
* source_id (int, nullable)
* destination_id (int, nullable)
* amount (int, unsigned)
* created (timestamp)
* approved (timestamp, nullable)






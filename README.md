
# Пояснительная записка в тестовому заданию

## Примененные инструменты

* Symfony 4
* PostgreSQL
* RabbitMQ
* Vagrant для dev-окружения

## Как развернуть

Нужен установленный Vagrant и VirtualBox, в корне проекта выполнить

```
vagrunt up
```

Запустить воркер очереди (можно любое количество)

```
./console rabbitmq:consumer balance&
```

Команды для тестирования работы воркеров:

```
# зачислить средства
app:balance:add <account_id> <amount>

# списать средства
app:balance:sub <account_id> <amount>

# перевод средств
app:balance:sub <source_id> <destination_id> <amount>

```

Для выполнения операции с блокировкой нужно добавить параметр --lock=1 к любой операции
Для

```
# поставить зачисление на блокировку
app:balance:add <account_id> <amount> --lock=1

# списать средства
app:balance:sub <account_id> <amount>

# перевод средств
app:balance:sub <source_id> <destination_id> <amount>

```

## Структура базы данных

### Accoun Entity

Аккаунты пользователей (account)

* id (int, sequence)
* username (string 255)
* busy_by_pid (int, nullable)

### Operation Entity

Операции с балансом (operation)

* id (int, sequence)
* account_id (int)
* amount (int)
* created (timestamp)

### Lock Entity

Блокировки - операции на подтверджении (lock)

* id (int, sequence)
* source_id (int, nullable)
* destination_id (int, nullable)
* amount (int, unsigned)
* created (timestamp)
* approved (timestamp, nullable)




# Docker Repository

## Разворачивание на локальной машине

Инициализация
> make init

- frontend: http://localhost:8080/
- backend: http://localhost:8081/

## Разворачивание на продакшен сервере

### Настаиваем сервер

1. Копируем provision/hosts.yml.dist в hosts.yml и задаём в нём свои настройки для соедининя с сервером по ssh
2. Из папки provision запускаем "make site" и ждём завершения настройки
3. Из папки provision запускаем "make authorize" для создания на сервере пользователя deploy, под которым будет
   происходить загрузка и запуск докера на сервере
4. Из папки provision запускаем "make docker-login" для авторизации сервера в docker registry

### Подготовка докер образа

1. Задаём переменные окружения REGISTRY и IMAGE_TAG и запускаем make build
2. Загружаем образы в удалённый репозиторий make push, пеередав переменные окружения REGISTRY и IMAGE_TAG
3. Загружаем исполняемые файлы и обновляем production сервер. Задаём REGISTRY, IMAGE_TAG, HOST, PORT, BUILD_NUMBER и
   запускаем make deploy

- frontend: https://demo-auction.savdevelop.ru
- backend: https://api.demo-auction.savdevelop.ru  
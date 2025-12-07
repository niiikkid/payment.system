# Техническое задание на добавление локализаций

## Статус по языкам
- [x] en — базовый
- [x] ru — базовый
- [x] uk — добавлен
- [ ] zh — требуется добавить
- [x] Казахстан
- [x] Узбекистан
- [ ] Кыргызстан
- [ ] Беларусь
- [ ] Молдова
- [ ] Азербайджан
- [ ] Турция
- [ ] ОАЭ
- [ ] Вьетнам
- [ ] Индонезия
- [ ] Таиланд
- [ ] Южная Корея
- [ ] Индия
- [ ] Пакистан
- [ ] Аргентина
- [ ] Мексика
- [ ] Польша
- [ ] Deutsch
- [ ] Français
- [ ] Español
- [ ] Português
- [ ] Italiano
- [ ] 日本語
- [ ] 한국어
- [ ] Türkçe
- [ ] العربية

## Общий регламент для каждого нового языка
- Создать `lang/{locale}/auth.php`, `messages.php`, `frontend.php`, `pagination.php`, `passwords.php`, `validation.php` по структуре `en/ru`.
- Создать корневой `lang/{locale}.json` с теми же ключами, что в `en/ru`.
- Перевести тексты, сохраняя плейсхолдеры (`:attribute`, `{id}`, `{count}` и т.п.).
- В `routes/web.php` добавить `{locale}` в список `$available` маршрута `lang/{locale}`, сохранять язык в сессию, вызывать `app()->setLocale`, возвращать `back()`.
- В `resources/js/components/ui/LanguageSwitcher.vue` добавить опцию `{ code, label, flag }` для нового языка.
- Проверить переключение: 404 отсутствует, строки подхватываются на основных страницах (логин, дашборд, платежка и т.д.).

## Предлагаемые коды локалей (уточнить перед реализацией)
- zh — китайский (уточнить вариант: zh-CN/zh-HK/zh-TW)
- kk — Казахстан
- uz — Узбекистан
- ky — Кыргызстан
- be — Беларусь
- ro — Молдова (уточнить приоритет: ro или md)
- az — Азербайджан
- tr — Турция / Türkçe
- ar — العربية / ОАЭ
- vi — Вьетнам
- id — Индонезия
- th — Таиланд
- ko — 한국어 / Южная Корея
- hi — Индия (уточнить язык: hi, en-IN или другой)
- ur — Пакистан (уточнить: ur или en-PK)
- es — Español (Мексика/Аргентина уточнить: es-MX, es-AR)
- pt — Português (уточнить вариант: pt-PT или pt-BR)
- de — Deutsch
- fr — Français
- it — Italiano
- ja — 日本語
- pl — Польша

## Чек-листы по языкам
### Беларусь
- [ ] Переводы: `lang/{locale}` + `lang/{locale}.json`, структура `en/ru`, перевести, плейсхолдеры сохранить.
- [ ] Маршрут: добавить `{locale}` в `$available` в `routes/web.php`.
- [ ] Фронтенд: опция в `LanguageSwitcher.vue` (`code`, label, flag).
- [ ] Проверка: переключение без 404, строки отображаются.

### Молдова
- [ ] Переводы: `lang/{locale}` + `lang/{locale}.json`, структура `en/ru`, перевести, плейсхолдеры сохранить.
- [ ] Маршрут: добавить `{locale}` в `$available` в `routes/web.php`.
- [ ] Фронтенд: опция в `LanguageSwitcher.vue` (`code`, label, flag).
- [ ] Проверка: переключение без 404, строки отображаются.

### Азербайджан
- [ ] Переводы: `lang/{locale}` + `lang/{locale}.json`, структура `en/ru`, перевести, плейсхолдеры сохранить.
- [ ] Маршрут: добавить `{locale}` в `$available` в `routes/web.php`.
- [ ] Фронтенд: опция в `LanguageSwitcher.vue` (`code`, label, flag).
- [ ] Проверка: переключение без 404, строки отображаются.

### Турция / Türkçe
- [ ] Переводы: `lang/{locale}` + `lang/{locale}.json`, структура `en/ru`, перевести, плейсхолдеры сохранить.
- [ ] Маршрут: добавить `{locale}` в `$available` в `routes/web.php`.
- [ ] Фронтенд: опция в `LanguageSwitcher.vue` (`code`, label, flag).
- [ ] Проверка: переключение без 404, строки отображаются.

### ОАЭ / العربية
- [ ] Переводы: `lang/{locale}` + `lang/{locale}.json`, структура `en/ru`, перевести, плейсхолдеры сохранить.
- [ ] Маршрут: добавить `{locale}` в `$available` в `routes/web.php`.
- [ ] Фронтенд: опция в `LanguageSwitcher.vue` (`code`, label, flag).
- [ ] Проверка: переключение без 404, строки отображаются.

### Вьетнам
- [ ] Переводы: `lang/{locale}` + `lang/{locale}.json`, структура `en/ru`, перевести, плейсхолдеры сохранить.
- [ ] Маршрут: добавить `{locale}` в `$available` в `routes/web.php`.
- [ ] Фронтенд: опция в `LanguageSwitcher.vue` (`code`, label, flag).
- [ ] Проверка: переключение без 404, строки отображаются.

### Индонезия
- [ ] Переводы: `lang/{locale}` + `lang/{locale}.json`, структура `en/ru`, перевести, плейсхолдеры сохранить.
- [ ] Маршрут: добавить `{locale}` в `$available` в `routes/web.php`.
- [ ] Фронтенд: опция в `LanguageSwitcher.vue` (`code`, label, flag).
- [ ] Проверка: переключение без 404, строки отображаются.

### Таиланд
- [ ] Переводы: `lang/{locale}` + `lang/{locale}.json`, структура `en/ru`, перевести, плейсхолдеры сохранить.
- [ ] Маршрут: добавить `{locale}` в `$available` в `routes/web.php`.
- [ ] Фронтенд: опция в `LanguageSwitcher.vue` (`code`, label, flag).
- [ ] Проверка: переключение без 404, строки отображаются.

### Южная Корея / 한국어
- [ ] Переводы: `lang/{locale}` + `lang/{locale}.json`, структура `en/ru`, перевести, плейсхолдеры сохранить.
- [ ] Маршрут: добавить `{locale}` в `$available` в `routes/web.php`.
- [ ] Фронтенд: опция в `LanguageSwitcher.vue` (`code`, label, flag).
- [ ] Проверка: переключение без 404, строки отображаются.

### Индия
- [ ] Переводы: `lang/{locale}` + `lang/{locale}.json`, структура `en/ru`, перевести, плейсхолдеры сохранить.
- [ ] Маршрут: добавить `{locale}` в `$available` в `routes/web.php`.
- [ ] Фронтенд: опция в `LanguageSwitcher.vue` (`code`, label, flag).
- [ ] Проверка: переключение без 404, строки отображаются.

### Пакистан
- [ ] Переводы: `lang/{locale}` + `lang/{locale}.json`, структура `en/ru`, перевести, плейсхолдеры сохранить.
- [ ] Маршрут: добавить `{locale}` в `$available` в `routes/web.php`.
- [ ] Фронтенд: опция в `LanguageSwitcher.vue` (`code`, label, flag).
- [ ] Проверка: переключение без 404, строки отображаются.

### Аргентина
- [ ] Переводы: `lang/{locale}` + `lang/{locale}.json`, структура `en/ru`, перевести, плейсхолдеры сохранить.
- [ ] Маршрут: добавить `{locale}` в `$available` в `routes/web.php`.
- [ ] Фронтенд: опция в `LanguageSwitcher.vue` (`code`, label, flag).
- [ ] Проверка: переключение без 404, строки отображаются.

### Мексика
- [ ] Переводы: `lang/{locale}` + `lang/{locale}.json`, структура `en/ru`, перевести, плейсхолдеры сохранить.
- [ ] Маршрут: добавить `{locale}` в `$available` в `routes/web.php`.
- [ ] Фронтенд: опция в `LanguageSwitcher.vue` (`code`, label, flag).
- [ ] Проверка: переключение без 404, строки отображаются.

### Польша
- [ ] Переводы: `lang/{locale}` + `lang/{locale}.json`, структура `en/ru`, перевести, плейсхолдеры сохранить.
- [ ] Маршрут: добавить `{locale}` в `$available` в `routes/web.php`.
- [ ] Фронтенд: опция в `LanguageSwitcher.vue` (`code`, label, flag).
- [ ] Проверка: переключение без 404, строки отображаются.

### Deutsch
- [ ] Переводы: `lang/{locale}` + `lang/{locale}.json`, структура `en/ru`, перевести, плейсхолдеры сохранить.
- [ ] Маршрут: добавить `{locale}` в `$available` в `routes/web.php`.
- [ ] Фронтенд: опция в `LanguageSwitcher.vue` (`code`, label, flag).
- [ ] Проверка: переключение без 404, строки отображаются.

### Français
- [ ] Переводы: `lang/{locale}` + `lang/{locale}.json`, структура `en/ru`, перевести, плейсхолдеры сохранить.
- [ ] Маршрут: добавить `{locale}` в `$available` в `routes/web.php`.
- [ ] Фронтенд: опция в `LanguageSwitcher.vue` (`code`, label, flag).
- [ ] Проверка: переключение без 404, строки отображаются.

### Español
- [ ] Переводы: `lang/{locale}` + `lang/{locale}.json`, структура `en/ru`, перевести, плейсхолдеры сохранить.
- [ ] Маршрут: добавить `{locale}` в `$available` в `routes/web.php`.
- [ ] Фронтенд: опция в `LanguageSwitcher.vue` (`code`, label, flag).
- [ ] Проверка: переключение без 404, строки отображаются.

### Português
- [ ] Переводы: `lang/{locale}` + `lang/{locale}.json`, структура `en/ru`, перевести, плейсхолдеры сохранить.
- [ ] Маршрут: добавить `{locale}` в `$available` в `routes/web.php`.
- [ ] Фронтенд: опция в `LanguageSwitcher.vue` (`code`, label, flag).
- [ ] Проверка: переключение без 404, строки отображаются.

### Italiano
- [ ] Переводы: `lang/{locale}` + `lang/{locale}.json`, структура `en/ru`, перевести, плейсхолдеры сохранить.
- [ ] Маршрут: добавить `{locale}` в `$available` в `routes/web.php`.
- [ ] Фронтенд: опция в `LanguageSwitcher.vue` (`code`, label, flag).
- [ ] Проверка: переключение без 404, строки отображаются.

### 日本語
- [ ] Переводы: `lang/{locale}` + `lang/{locale}.json`, структура `en/ru`, перевести, плейсхолдеры сохранить.
- [ ] Маршрут: добавить `{locale}` в `$available` в `routes/web.php`.
- [ ] Фронтенд: опция в `LanguageSwitcher.vue` (`code`, label, flag).
- [ ] Проверка: переключение без 404, строки отображаются.

### 한국어
- [ ] Переводы: `lang/{locale}` + `lang/{locale}.json`, структура `en/ru`, перевести, плейсхолдеры сохранить.
- [ ] Маршрут: добавить `{locale}` в `$available` в `routes/web.php`.
- [ ] Фронтенд: опция в `LanguageSwitcher.vue` (`code`, label, flag).
- [ ] Проверка: переключение без 404, строки отображаются.

### Türkçe
- [ ] Переводы: `lang/{locale}` + `lang/{locale}.json`, структура `en/ru`, перевести, плейсхолдеры сохранить.
- [ ] Маршрут: добавить `{locale}` в `$available` в `routes/web.php`.
- [ ] Фронтенд: опция в `LanguageSwitcher.vue` (`code`, label, flag).
- [ ] Проверка: переключение без 404, строки отображаются.

### العربية
- [ ] Переводы: `lang/{locale}` + `lang/{locale}.json`, структура `en/ru`, перевести, плейсхолдеры сохранить.
- [ ] Маршрут: добавить `{locale}` в `$available` в `routes/web.php`.
- [ ] Фронтенд: опция в `LanguageSwitcher.vue` (`code`, label, flag).
- [ ] Проверка: переключение без 404, строки отображаются.


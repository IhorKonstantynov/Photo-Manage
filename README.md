<h1 align="center"><a href="http://46.175.146.14" target="_blank">ProPhotosAI</a></h1>

## About Project

ProPhotosAI is ai photo generator using OpenAPI. Programming Stack is Laravel 10 and Vue3 inertia.

## Project Running.

```bash
git clone https://github.com/Jcsarokin/prophotos.git
```
Go to the project folder.
```javascript
composer install // install vendor
php artisan migrate // Generate database
```

```
npm install
npm run build
```
Setting evironment.
```
cp .env.example .env
```
Edit .env file and run laravel project.
```
php artisan key:generate
php artisan serve
```

Send Promo scheduer setting.
```
0 0 * * * cd /home/app.prophoto.ai && php artisan schedule:run >> /home/app.prophoto.ai/schedule.log 2>&1
```
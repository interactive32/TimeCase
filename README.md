![TimeCase](https://interactive32.com/resources/timecase2.png)
![TimeCase](https://interactive32.com/resources/timecase3.png)
![TimeCase](https://interactive32.com/resources/timecase4.png)

# TimeCase
TimeCase helps you keep track of your time.


# Usage with Docker and docker-compose (recommended)
Clone with git:
```
git clone git@github.com:interactive32/TimeCase.git && cd TimeCase
```

Start with Docker:
```
docker compose up -d
```

Fix permissions & prepare database
```
docker exec -i timecase-backend chown -R www-data:www-data /var/www/html
docker exec -i timecase-db mysql -uroot -pmypass -e 'create database timecase_database'
docker exec -i timecase-db mysql -uroot -pmypass timecase_database < timecase/database/database.sql
```

Usage:
```
http://localhost:9000
```

Default username login:
```
admin/admin123
```
After login, you should add your first Customer and a Project so you can start tracking your time.


# Server installation (alternative to docker)

Follow these steps to install and configure TimeCase:

- Make sure your have minimum PHP version 5.2 + Apache2 with mod_rewrite on
- Copy all files to your server (via FTP or similar)
- Import database.sql from database folder into your mysql server
- Update _machine_config.php to reflect your database settings
- Default administrator username/password is admin/admin123



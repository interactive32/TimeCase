## WARNING: THIS PROJECT IS NO LONGER MAINTAINED!

![TimeCase](https://interactive32.com/resources/timecase.png)

# TimeCase
TimeCase helps you keep track of your time.


# Usage with Docker and docker-compose
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

# CONTENTS:

1. Before you begin
2. Short description
3. Documentation
4. Quick Start


## Before you begin:

Our Warranties, Liability and Disclaimers:

NEITHER INTERACTIVE32 NOR ITS SUPPLIERS OR DISTRIBUTORS MAKE ANY SPECIFIC PROMISES ABOUT THIS SOFTWARE. FOR EXAMPLE, WE DON'T MAKE ANY COMMITMENTS ABOUT DATA AND CONTENT WITHIN THE SOFTWARE, THE SPECIFIC FUNCTION OF THE SERVICES, OR THEIR RELIABILITY, AVAILABILITY, OR ABILITY TO MEET YOUR NEEDS. WE PROVIDE THE SOFTWARE "AS IS".

INTERACTIVE32 WILL NOT BE RESPONSIBLE FOR LOST PROFITS, REVENUES, OR DATA, LOSS OF GOODWILL OR BUSINESS REPUTATION, FINANCIAL LOSSES OR INDIRECT, SPECIAL, CONSEQUENTIAL, OR EXEMPLARY DAMAGES IN ALL CASES.

## Short description

TimeCase helps you keep track of your time. It is a powerful yet easy to use web application for everyone who wants to see how much time is spent on certain tasks and projects.

Version: TimeCase v2.0

For more info please visit https://interactive32.com

Copyright interactive32.com. All rights reserved.


## Documentation

Documentation is located under documentation folder. Navigate to index.html file with your browser.


## Quick Start

Follow these steps to install and configure TimeCase:

- Make sure your have minimum PHP version 5.2 + Apache2 with mod_rewrite on
- Copy all files to your server (via FTP or similar)
- Import database.sql from database folder into your mysql server
- Update _machine_config.php to reflect your database settings
- Default administrator username/password is admin/admin123



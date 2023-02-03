# TimeCase
TimeCase is a free, open-source, self-hosted, time tracking web application.

- Keep track of your time with accuracy and precision. Use the same web app on your desktop, tablet or phone.
- Allow your customers to login and analyze time spent on their projects and view reports.
- You can assign different roles to different users and pick team managers.
- View and analyze reports in real-time with flexible filters. Export data as csv file or display them as printable html.

# Screenshots
![TimeCase](https://interactive32.com/resources/timecase2.png)
![TimeCase](https://interactive32.com/resources/timecase3.png)
![TimeCase](https://interactive32.com/resources/timecase4.png)

# Usage with Docker (recommended method)
Clone with git:
```
git clone git@github.com:interactive32/TimeCase.git && cd TimeCase
```

Start with Docker:
```
docker compose up -d
```

Create and populate the database
```
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


# Server installation (alternative to docker)

Follow these steps to install and configure TimeCase:

- Make sure your have minimum PHP version 5.2 + Apache2 with mod_rewrite on
- Copy all files to your server (via FTP or similar)
- Import database.sql from database folder into your mysql server
- Update _machine_config.php to reflect your database settings
- Default administrator username/password is admin/admin123


# Using TimeCase

Some options will not be available for all access levels - for more information about access levels please read the next chapter.

## Time tracking
Track your time either by manually selecting time span or by auto-tracking. Auto-tracking is done vie the start/stop button. When start button is pressed time will start to flow and after you click on stop timer button dialog will appear and you can save this entry. On this screen you can also select default project and default work type so you don't have to select this every time. By clicking on existing time entry you can edit this record. 

## Statuses
All customers and projects records have a status field. There are three default statuses - important, active and closed. All records will be sorted by status so important will go first. If record has closed status, then this record will not show on drop-down menus except on reports. This gives you ability to archive old customer/project records and make drop-down selections smaller and faster. You can add additional statuses but you cannot change basic three types.  

## Account settings
All users except customers have ability to change their personal account settings like full name, email, password and details.

# AccessLevels
TimeCase has five different access levels:
 

### Administrator
Administrator can perform all actions: track and manage tracked time, manage customers, projects, users, work types, statuses and see reports.

### Manager
Manager can perform all actions as administrator except manage users, work types and statuses. This access level can also track time on behalf of other user and edit all tracking records.

### User
User can track time (auto-track or choose specific time span), see reports and manage account. User can only track, edit and see own tracking records.
 
### Basicuser
Basic user can track time in auto-tracking mode only. 
Basic user can start and stop timer but cannot choose specific time span.
This access level cannot see reports.
 
### Customer
Customers who are allowed to login can access reports based on assigned projects. 
They can only see their own projects and tracking times assigned to them.
 
# Configuration
Database server settings, timezone configuration:
```
_machine_config.php
```
Application-wide configuration settings:

```
_app_config.php
```


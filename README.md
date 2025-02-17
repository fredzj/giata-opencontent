﻿# giata-opencontent

## Prerequisites
- PHP
- MySQL or MariaDB

## Setup Instructions

### 1. Save Database Credentials
Create a file `/config/db.ini` and enter your database credentials in the following format:
```ini
hostname=your_hostname
databasename=your_databasename
username=your_username
password=your_password
```

### 2. Create Database Tables
Import all SQL files from the database directory into your database:
```sh
mysql -u your_username -p your_databasename < /path/to/database/file.sql
```

### 3. Transfer Files
Transfer all files to your server.  

### 4. Import Giata Data
Schedule `importGiataDefinitions.php` in order to import definitions from Giata DRIVE and save it into your database.
Schedule `importGiataOpenContent.php` in order to import all accommodations from Giata DRIVE and save it into your database.

### 5. View Giata Dashboard
Open `giata.php` in your browser to view the Giata dashboard.

# giata-opencontent
 
## Save Database Credentials
1. Enter your hostname, databasename, username, and password in file /config/db.ini.

## Create Database Tables
2. Import all /database/*.sql files into your database.

## Transfer Files
3. Transfer all files to your server.  

## Download Giata Data
4. Schedule downloadGiataDefinitions.php in order to import Giata Definitions and save it into your database.
5. Schedule downloadGiataDrive.php in order to import Giata Open Content for EuroParcs accommodations and save it into your database.
6. Schedule downloadGiataOpenContent.php in order to import Giata Open Content for all accommodations and save it into your database.

## View Giata Dashboard
7. Open giata.php in your browser.

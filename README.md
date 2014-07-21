php-mysql-database-migrator
===========================

This script and model track a database with a timestamp version number, automatically executing new alter files it finds. This reduces database schema migrations among a development team to simply creating a new alter file, triggering execution locally, and committing it. 

This implementation assumes MySQL and ActiveRecord, however it could be easily modified to work with another database or another ORM.

## Files

### services/Migration.php

This is the body and logic of the migrator itself. The code reads/creates the "migration_history" database table, determines which files-on-disk are candidates to be executed (by the timestamp embedded in the filename), and executes them one-at-a-time, recording the execution and versioning in "migration_history".

If an execution fails, it is not recorded and the overall run stops. 

### model/MigrationHistory.php

This is the ActiveRecord model file for the "migration_history" database table. Although the table itself is created if DNE on the first run of the migrator, it's representative model is not. This is it. :)

### env.php

Environment-specific values.. namely the database connection parameters. 

This file also includes the $last_migration variable, which may or may not be appropriate here.

### migrationExec.php

This is the code that will be included inline with your program execution. It does the database connection, includes the Migration.php service, and actually runs the migrator.

Note that this assumes the existence of ActiveRecord at php-activerecord/ActiveRecord.php - although I am not including ActiveRecord in this repo.

### migrations/

Oh, there it is! This is the folder that stores the .sql alter files. 

## Installation

Place services/ and model/ together near/in the root of your application folder. If you are using ActiveRecord, you already have a folder for your database models and MigrationHistory.php should simply go in there. If you use other homegrown scripts, Migration.php can go with those.

Include env.php or incorporate its contents into your code. If you don't have environment-specific code, that's fine - just include it. You'll want to modify the database connection parameter values.

Include migrationExec.php or incorporate its contents into your code. 

## Workflow

When you need to modify the database schema:

1. Create a new file in migrations/ as YYYYMMDDHHMM_some_descriptive_name.sql. The only operationally important part of the name is the YYYYMMDDHHMM_. The rest is for you.
2. Fill in your new migration file with appropriate, database modifying SQL.
2. Run your code, or refresh the page that runs your code.
3. Enjoy this next moment, as you realize you're done making a database schema change that will propagate across your team and be deployed into staging or production automatically.
4. Ahh.. isn't this nice?
5. Oh, right, back to work.
6. Commit your migration file(s) alongside the code files which rely on it. 





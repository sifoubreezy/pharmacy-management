#  How to setup
##  Developpement

1. First make sure that you have php and composer installed in your system to check use `php -v` and `composer --version`  
if not make sure to install them and add them to your path variables.

2. Open your terminal and run this command  `composer run setup-dev`  
this command will make sure to insatll dependecies and create the database and the env file and run the migration for you.

3. In your terminal run this command `php artisan serve`.

4. Finally open your browser and navigate to `localhost:8000` and way to go.
   
##   Production

1. First make sure that you have php and composer installed in your system to check use `php -v` and `composer --version`  
if not make sure to install them and add them to your path variables.

2. Edit `.env.example` according to your environment.

3. Open your terminal and run this command  `composer run setup-prod`  
this command will make sure to install dependecies and create the database and the env file and run the migration for you.

4. Finally deploy the app to a web server and way to go.

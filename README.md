##  bKash Payment Tokenized Integration
Hello,
This is Unofficial guide about integrating bKash payment gateway in your application.
If you already have had enough with the documentation and API references provided officially by bKash, then this documentation is just for you.

# Getting started

## Installation Laravel Banckend

Clone the repository

Switch to the repo folder

    cd bkash-tokenized-integration-laravel

Install all the dependencies using composer

    composer update

Copy the example env file and make the required configuration changes in the .env file

    cp .env.example .env

Generate a new application key

    php artisan key:generate


Run the database migrations (**Set the database connection in .env before migrating**)
    php artisan migrate
   
Start the local development server

    php artisan serve

You can now access the server at http://localhost:8000

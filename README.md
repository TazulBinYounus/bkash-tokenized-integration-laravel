##  bKash Payment Tokenized Integration
Hello,
This is an Unofficial guide about integrating the bKash payment gateway into your application. If you already have had enough of the documentation and API references provided officially by bKash, then this documentation is just for you.

![agreement_list](https://user-images.githubusercontent.com/44948856/211843828-3c66bb4f-a7f8-49c6-a606-b3a336590867.PNG)





# Getting started

## Installation Laravel Project

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

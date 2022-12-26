# Crypto Portal - crypto trading platform
## Info
Cryptocurrency platform where you can :
* Create your personal account.
* Buy/Sell/Short cryptocurrency.
## GIFs
### Login and registration
![loginRegister](https://user-images.githubusercontent.com/106473441/209436081-80b5e33f-daf7-4630-bdef-086dfee00cbd.gif)
### Buy Sell Transfer cryptocurrencies
![buySellTransfer](https://user-images.githubusercontent.com/106473441/209436097-6e6e4be5-9b3a-4d9d-8d13-99d3bc3faa03.gif)
### Short cryptocurrencies
![short](https://user-images.githubusercontent.com/106473441/209436103-199bf7f3-aab4-462a-b0cb-67e6894fc314.gif)
## This project requires the following software
* PHP 7.4.33
* MySQL 8.0.31
* Composer 2.4.4
## Setup the project
1. Clone this repository - git clone https://github.com/IljaMartinovs/CryptoPortalPHP
2. Install all necessary packages with command - composer install
3. Rename ".env.example" to ".env" and inster DB username in USER , DB password in PASSWORD and APIKEY from https://coinmarketcap.com/api/
4. Import database file "crypto-api.sql" from dabatase directory :
run command - ```mysql -u username -p crypto-api < crypto-api.sql```(replace username with your actual username that you use to connect to the databse)
## Run the project
To run the project go to the public directory and there use a command  
```php -S localhost:8000```

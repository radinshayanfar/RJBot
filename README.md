
RJBot
=====
A simple Telegram bot to extract media links from Radio Javan share link. This bot was developed for educational purposes. 

Installation
--------------
 1. Create a new bot via [@BotFather](https://t.me/BotFather) and grab your token (keep it secure).
 2. Make a copy of the repository in your server.
  ```bash
  $ git clone --depth=1 https://github.com/radinshayanfar/RJBot.git
  ```
 3. Install `curl` according to your PHP version.
  ```bash
  $ sudo apt install php8.0-curl
  ```
 4. Create a MySQL database.
 5. Rename `.env.example` file to `.env` and edit it's content according to your configuration.
  ```bash
  $ mv .env.example .env
  $ vi .env
  ```
 6. Run install script to finish installation.
  ```bash
  $ php install/install.php
  ```

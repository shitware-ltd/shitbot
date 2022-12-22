# Shitware Ltd Discord Bot

### This bot is hand crafted using PHP. It is *fast enough*. You should expect nothing less.

## Setting Up:

- Create your [Discord Bot](https://discord.com/developers/applications). 
- Clone this repo. 
- Rename `.env.example` to `.env`
- Run `composer install`
- Supply your `BOT_TOKEN` and all other credentials in the `.env`
  - To use `!weather`, you must get an API key from [Weather API](https://www.weatherapi.com)
  - To use `!yt`, you must get an API key from [Google Developers Console](https://console.developers.google.com)
  - To use `!hype`, you must get an API key from [Hype Quotes](https://github.com/jorqensen/hypequotes)

## Running:

```bash
php shitbot.php
```

- To run on production, you should setup a supervisor/service. Example:

```
[Unit]
Description=Shitware Bot
After=network.target auditd.service

[Service]
User=www-data
Group=www-data
ExecStart=/usr/bin/php /var/www/shitware/shitbot/shitbot.php
Restart=always

[Install]
WantedBy=multi-user.target
```
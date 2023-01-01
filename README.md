# Shitware Ltd Discord Bot

### This bot is hand crafted using PHP. It is *fast enough*. You should expect nothing less.

## Notes:
- `PHP >= 8.1` is required.
- All http calls are handled through coroutines, thus non-blocking. This **php** bot can indeed handle many user commands at the same time.
- The use of most emojis for reactions inside the [Emoji](src/Support/Emoji.php) are from a private server, and will not work for you. You must swap out all emojis yourself.

## Setting Up:

- Create your [Discord Bot](https://discord.com/developers/applications).
- Add your discord bot to your server.
  - You **must** use the `bot` and `applications.commands` scopes when generating your bots oauth URL.
  - Permissions wise, [517544070208](https://discordapi.com/permissions.html#517544070208) is all this bot should need.
- Clone this repo. 
- Rename `.env.example` to `.env`
- Run `composer install`
- Supply your `BOT_TOKEN` and all other credentials in the `.env`
  - To use `!ask` or `!art`, you must get an API key from [OpenAI](https://beta.openai.com/account/api-keys)
  - To use `!weather`, you must get an API key from [Weather API](https://www.weatherapi.com)
  - To use `!yt`, you must get an API key from [Google Developers Console](https://console.developers.google.com)
  - To use `!hype`, you must get an API key from [Hype Quotes](https://github.com/jorqensen/hypequotes)
  - To use `!ip`, you must get an API key from [IP API](https://ip-api.com)

## Prefix Commands:

- `!help`: Send a help message listing every prefix command.
- `!art {prompt}`: Uses OpenAI's **[DALLE-2]** to generate an image based on the prompt.
- `!ask {prompt}`: Uses OpenAI's **[GPT-3 text-davinci-003]** to return completion(s).
- `!chuck`: Chuck Norris jokes.
- `!daddy`: Dad jokes.
- `!hype`: Lara-cord best quotes of all time.
- `!image`: Gets a random image from unsplash.
- `!insult {@mention(s)}`: Will give you a mean insult. If you @mention other users, it will tag and insult them instead.
- `!ip {ip}`: Obtain details about the supplied IP address.
- `!joke`: Basic setup and punchline jokes.
- `!rps {rock|paper|scissors}`: Play the most basic game on earth.
- `!weather {location}`: Get the current weather for the given location.
- `!wiki {search}`: Get the top 5 results from Wikipedia from your search.
- `!yomomma`: YoMomma jokes.
- `!yt {search}`: Search and obtain the top video result from YouTube.

## Application Commands:

- `/ping`: Pings the bot to see if it is online and working.

## Running:

***On your first run, you should use the install flag so the application command can be installed.***
```bash
php shitbot.php --install
```

***To run normally, use:***
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
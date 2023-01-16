# Shitware Ltd Discord Bot

### This bot is hand crafted using PHP. It is *fast enough*. You should expect nothing less.

## Notes:
- `PHP >= 8.1` is required.
- All http calls are handled through coroutines, thus non-blocking. This **php** bot can indeed handle many user commands at the same time.
- The use emojis for reactions inside the [Emoji](src/Support/Emoji.php) support class are from a private server, and will not work for you. You must swap the emojis out yourself.

## Setting Up:

- Create your [Discord Bot](https://discord.com/developers/applications).
- Add your discord bot to your server.
  - You **must** use the `bot` and `applications.commands` scopes when generating your bots oauth URL.
  - Permissions wise, [517544070208](https://discordapi.com/permissions.html#517544070208) is all this bot should need.
- Clone this repo. 
- Run `composer install`
- Rename `.env.example` to `.env`

## Configure .env

- `BOT_TOKEN`: Your discord bot token you received from [Discord](https://discord.com/developers/applications).
- `BOT_ACTIVITY_STATUS`: Bots presence status. Use: `online|idle|dnd|invisible`.
- `BOT_ACTIVITY_TYPE`: Bots activity. Leave empty to skip. Requires `BOT_ACTIVITY_NAME`. Available flags are:
    - `0` - Playing
    - `1` - Streaming
    - `2` - Listening
    - `3` - Watching
    - `4` - Custom
    - `5` - Competing
- `BOT_ACTIVITY_URL`: Only used for streaming.
- `BOT_ACTIVITY_NAME`: Describe the bots activity.
- `OWNER_IDS`: Supply your discord ID (separate multiple IDs with commas) to get elevated powers.
- `OPENAI_TOKEN`: To use `!ask` or `!art`, you must get an API key from [OpenAI](https://beta.openai.com/account/api-keys).
- `WEATHER_TOKEN`: To use `!weather`, you must get an API key from [Weather API](https://www.weatherapi.com).
- `YOUTUBE_TOKEN`: To use `!yt`, you must get an API key from [Google Developers Console](https://console.developers.google.com).
- `IP_TOKEN`: To use `!ip`, you must get an API key from [IP API](https://ip-api.com).
- `HYPE_TOKEN`: To use `!hype`, you must get an API key from [Hype Quotes](https://github.com/jorqensen/hypequotes).

## Prefix Commands:

- `!help`: Send a help message listing every prefix command.
- `!art {prompt}`: Uses OpenAI's **[DALLE-2]** to generate an image based on the prompt.
- `!ask {prompt}`: Uses OpenAI's **[GPT-3 text-davinci-003]** to return completion(s).
- `!balance`: See how much money from API usage you have spent.
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
- `!!!! {rest|wakeup|status|terminate}`: Admin commands.

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
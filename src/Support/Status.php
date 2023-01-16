<?php

namespace ShitwareLtd\Shitbot\Support;

use Discord\Parts\User\Activity;
use ShitwareLtd\Shitbot\Shitbot;
use Throwable;

class Status
{
    /**
     * @return void
     */
    public static function setDefault(): void
    {
        try {
            Shitbot::discord()->updatePresence(
                activity: self::activity(
                    type: Shitbot::config('BOT_ACTIVITY_TYPE'),
                    name: Shitbot::config('BOT_ACTIVITY_NAME')
                ),
                status: Shitbot::config('BOT_ACTIVITY_STATUS')
            );
        } catch (Throwable) {
            //Not important.
        }
    }

    /**
     * @param  string  $status
     * @param  int|null  $type
     * @param  string|null  $name
     * @return void
     */
    public static function set(string $status, ?int $type, ?string $name): void
    {
        try {
            Shitbot::discord()->updatePresence(
                activity: self::activity(
                    type: $type,
                    name: $name
                ),
                status: $status
            );
        } catch (Throwable) {
            //Not important.
        }
    }

    /**
     * @param  int|null  $type
     * @param  string|null  $name
     * @return Activity|null
     */
    private static function activity(?int $type, ?string $name): ?Activity
    {
        if (! $type || ! $name) {
            return null;
        }

        return new Activity(
            discord: Shitbot::discord(),
            attributes: [
                'type' => $type,
                'name' => $name,
                'details' => $name,
                'url' => Shitbot::config('BOT_ACTIVITY_URL') ?: null,
            ]
        );
    }
}

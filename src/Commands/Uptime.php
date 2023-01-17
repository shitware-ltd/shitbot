<?php

namespace ShitwareLtd\Shitbot\Commands;

use Carbon\CarbonInterface;
use Discord\Parts\Channel\Message;
use Illuminate\Support\Carbon;
use ShitwareLtd\Shitbot\Support\Helpers;

class Uptime extends Command
{
    /**
     * @return string
     */
    public function trigger(): string
    {
        return '!uptime';
    }

    /**
     * @return int
     */
    public function cooldown(): int
    {
        return 15000;
    }

    /**
     * @param  Message  $message
     * @param  array  $args
     * @return void
     */
    public function handle(Message $message, array $args): void
    {
        if ($this->skip($message)) {
            return;
        }

        $message->reply($this->message());

        $this->hitCooldown($message);
    }

    /**
     * @return string
     */
    private function message(): string
    {
        $currentMemory = Helpers::bytesToHuman(memory_get_usage());
        $peakMemory = Helpers::bytesToHuman(memory_get_peak_usage());
        $startTime = Carbon::parse(SHITBOT_START);
        $uptime = Carbon::now()->diffForHumans(
            other: $startTime,
            syntax: [
                'join' => ', ',
                'parts' => 5,
                'syntax' => CarbonInterface::DIFF_ABSOLUTE,
            ]
        );

        return <<<EOT
        > 🔌 Online since: **{$startTime->format('D, M j, Y g:i:s A')}**
        > ⌚ Total uptime: **$uptime**.
        > 💾 Current memory usage: **$currentMemory**.
        > 🧠 Peak memory usage: **$peakMemory**.
        EOT;
    }
}

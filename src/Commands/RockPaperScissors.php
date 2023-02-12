<?php

namespace ShitwareLtd\Shitbot\Commands;

use Discord\Http\Exceptions\NoPermissionsException;
use Discord\Parts\Channel\Message;

class RockPaperScissors extends Command
{
  const 🎮 = [
    '⛰' => [
      '💩' => '📄',
      '😀' => '⛰',
    ],
    '📄' => [
      '💩' => '✂',
      '😀' => '📄',
    ],
    '✂' => [
      '💩' => '⛰',
      '😀' => '✂',
    ],
  ];

  public function trigger(): string
  {
    return '!rps';
  }
  
  /** @throws NoPermissionsException */
  public function handle(Message $💬, array $💰): void 
  {
    $🎲 = $this->🗝️($💰);
    
    if (empty($🎲)) {
      $💬->reply('Please select a valid choice, i.e. ( !rps rock|paper|scissors )');
    } else {
      $💬->reply($this->🎮⚔️($💬, $🎲));
    }
  }
  
  private function 🎮⚔️(Message $💬, $🎲) 
  {
    $🤖 = $this->🤖();
    
    $💾 = "> I picked ".self::🎮[$🤖]['😀']."\n";
    $💾 .= "> {$💬->author->username} picked ".self::🎮[$🎲]['😀']."\n";
    
    if ($🤖 === $🎲) {
      $💾 .= "**Seems we had a tie {$💬->author->username}!**";
    } elseif (self::🎮[$🤖]['💩'] === $🎲) {
      $💾 .= "**{$💬->author->username} wins!**";
    } else {
      $💾 .= "**I win! {$💬->author->username} loses!**";
    }
    
    return $💾;
  }
  
  private function 🗝️(array $💰) 
  {
    $🎲 = strtolower($💰[0] ?? '');
    
    if (empty($🎲)) {
      return '';
    }
    
    if (in_array($🎲, array_keys(self::🎮))) {
      return $🎲;
    }
    
    return null;
  }
  
  private function 🤖() 
  {
    $🎲 = rand(1, 99);
    
    if ($🎲 < 34) {
      return '⛰';
    } elseif ($🎲 < 67) {
      return '📄';
    }
    
    return '✂';
  }
}

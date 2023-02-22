<?php

namespace ShitwareLtd\Shitbot\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class Emoji
{
    /**
     * @var array
     */
    private const EMOJIS = [
        'cool' => [
            ':BeanLike:955772154367078410',
            ':DanSureCan:1054559650030309408',
            ':FeelsJorqensenMan:948839460173414420',
            ':FeelsTippinMan:945779696870785044',
            ':StanManCan:986743587041599568',
            'a:mochoman:908433686523940884',
            'a:cooldoge:903865400914239508',
            ':OkFam:1034988675240566854',
            'a:coolblink:903877070336167956',
            'a:potatocheer:915906109917757471',
            'a:MarioLuigiDancing:930208778354319370',
            ':FeelsDanishMan:1059588246146916432',
            'a:Snek:1065134818452586576',
            ':FeelsPinkyParty:1065810135366246481',
            ':GoodShit:1063591083264716820',
            'a:RustParty:1063881476594208799',
            '😎',
            '🆒',
        ],
        'funny' => [
            ':KekwCamera:1031729132607909939',
            'a:MeLike:1029201658061803560',
            'a:kekamid:903876917575442432',
            'a:potatospin:913606622310445088',
            'a:drilldo:903834334044229693',
            ':KekwCry:1055646524840886292',
            'a:KekwRave:1055646475918516324',
            'a:lasercat:821888531060228138',
            '😂',
            '🤣',
            '😁',
        ],
        'think' => [
            'a:BlobEat:903866622345900052',
            ':ChiefThonk:1029200771096510534',
            'a:NeonThink:930095718771863552',
            ':ThinkGator:933287413449650206',
            ':watts:903836408639262790',
            ':SuperCereal:985316620928942231',
            ':virus:821890127252946964',
            'a:nervouscursor:903876962328670239',
            ':ShitForBrains:1077003694312263770',
            '🤔',
            '💭',
        ],
        'rage' => [
            'a:eyesshaking:930095703097745408',
            'a:GullScream:1008189021505212416',
            ':bsod:903869566533394462',
            'a:getsomehelp:903876935707410505',
            'a:smh:903877109825540186',
            'a:codeHard:888730726822981652',
            ':BeanThis:953772597110276147',
            'a:madbean:897099906584567818',
            ':beaned:867568151252041758',
            'a:yeangry:903877015604703232',
            'a:pain:903876735056085023',
            'a:codeRee:888733167530434580',
            'a:Kaboom:903867872344957008',
            'a:duckno:903876825925681153',
            'a:kermitgun:930095685158719509',
            'a:nonono:903831572065697875',
            ':DirtyPHP:1070511281880174703',
            ':GoodShit:1063591083264716820',
            'a:PetTheKosta:1075932133866082405',
            ':ShitForBrains:1077003694312263770',
            'a:pepe_facepalm:903877035280183306',
            '🖕',
            '💢',
            '😡',
            '😠',
        ],
        'drugs' => [
            'a:acidcat:903740834065829898',
            'a:cheers:903865719400321024',
            'a:catjamhigh:903877090074574918',
            'a:spliffroy:924213702633340928',
            'a:weedman:924213665077526578',
            ':FeelsArkoMan:1063543706596151316',
            ':kermitcoke:924214021199114261',
            ':pepesmoke:924213678901981244',
            ':smoking_goose:924213690327248897',
            ':weed:924213140152021003',
            '🍷',
            '🍺',
            '🍻',
            '🤮',
            '🤢',
        ],
        'trongate' => [
            ':trongate:1030233313266389062',
            ':trongate1:1064753779917979750',
            ':trongate2:1064753997640110141',
        ],
    ];

    /**
     * @param  string|null  $flavor
     * @return string
     */
    public static function get(?string $flavor = null): string
    {
        return Collection::make(
            static::EMOJIS[$flavor] ?? Arr::flatten(static::EMOJIS)
        )->random();
    }
}
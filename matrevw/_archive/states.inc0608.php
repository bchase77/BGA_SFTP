<?php
/**
 *------
 * BGA framework: Gregory Isabelli & Emmanuel Colin & BoardGameArena
 * MatRevW implementation :© Mike & Jack McKeever and Bryan Chase bryanchase@yahoo.com
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * states.inc.php
 *
 * MatRevW game states description
 *
 */


define('ST_PLAYER_CHOOSE_WRESTLER', 20);
define('ST_NEXT_PLAYER', 21);
define('ST_NEXT_PHASE', 22);
define('ST_END_GAME', 99);

$machinestates = [

    // The initial state. Please do not modify.

    1 => array(
        "name" => "gameSetup",
        "description" => "",
        "type" => "manager",
        "action" => "stGameSetup",
        "transitions" => ["next" => 2 ]
    ),

    // Note: ID=2 => your first state

    2 => [
        "name" => "deckSetup",
//        "description" => clienttranslate('${actplayer} must play a card or pass'),
   		"description" => clienttranslate('Deck Setup [ST2]'),
//        "descriptionmyturn" => clienttranslate('${you} must play a card or pass'),
        "descriptionmyturn" => clienttranslate('Deck Setup My Turn [ST2]'),
   		"type" => "game",
//		"action" => "stDeckSetup",
   		"transitions" => [ "" => ST_PLAYER_CHOOSE_WRESTLER ]
    ],
	
    ST_PLAYER_CHOOSE_WRESTLER => [
        'name' => 'playerChooseWrestler',
        'description' => clienttranslate('${actplayer} must choose a wrestler'),
        'descriptionmyturn' => clienttranslate('${you} must choose a wrestler'),
        'type' => 'multipleactiveplayer',
        'possibleactions' => ['chooseWrestler'],
        'transitions' => ['next' => ST_NEXT_PLAYER, 'done' => ST_NEXT_PHASE],  // customize
    ],	
	
	ST_NEXT_PLAYER => [
		'name' => 'nextPlayer',
		'description' => '',
		'type' => 'game',
		'action' => 'stNextPlayer',
		'transitions' => ['chooseNext' => ST_PLAYER_CHOOSE_WRESTLER, 'end' => ST_END_GAME],
	],
	
	
	
    // Final state.
    // Please do not modify (and do not overload action/args methods).
    99 => [
        "name" => "gameEnd",
        "description" => clienttranslate("End of game"),
        "type" => "manager",
        "action" => "stGameEnd",
        "args" => "argGameEnd"
    ],

];


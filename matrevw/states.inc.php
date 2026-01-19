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

/*
   Game state machine is a tool used to facilitate game developpement by doing common stuff that can be set up
   in a very easy way from this configuration file.

   Please check the BGA Studio presentation about game state to understand this, and associated documentation.

   Summary:

   States types:
   _ activeplayer: in this type of state, we expect some action from the active player.
   _ multipleactiveplayer: in this type of state, we expect some action from multiple players (the active players)
   _ game: this is an intermediary state where we don't expect any actions from players. Your game logic must decide what is the next game state.
   _ manager: special type for initial and final state

   Arguments of game states:
   _ name: the name of the GameState, in order you can recognize it on your own code.
   _ description: the description of the current game state is always displayed in the action status bar on
                  the top of the game. Most of the time this is useless for game state with "game" type.
   _ descriptionmyturn: the description of the current game state when it's your turn.
   _ type: defines the type of game states (activeplayer / multipleactiveplayer / game / manager)
   _ action: name of the method to call when this game state become the current game state. Usually, the
             action method is prefixed by "st" (ex: "stMyGameStateName").
   _ possibleactions: array that specify possible player actions on this step. It allows you to use "checkAction"
                      method on both client side (Javacript: this.checkAction) and server side (PHP: $this->checkAction).
   _ transitions: the transitions are the possible paths to go from a game state to another. You must name
                  transitions in order to use transition names in "nextState" PHP method, and use IDs to
                  specify the next game state for each transition.
   _ args: name of the method to call to retrieve arguments for this gamestate. Arguments are sent to the
           client side to be used on "onEnteringState" or to set arguments in the gamestate description.
   _ updateGameProgression: when specified, the game progression is updated (=> call to your getGameProgression
                            method).
*/

//    !! It is not a good idea to modify this file when a game is running !!

define('ST_PLAYER_CHOOSE_WRESTLER', 20);
define('ST_NEXT_PLAYER', 21);
define('ST_NEXT_PHASE', 22);

$machinestates = [

    // The initial state. Please do not modify.

    1 => array(
        "name" => "gameSetup",
        "description" => "",
        "type" => "manager",
        "action" => "stGameSetup",
        "transitions" => ["" => 2 ]
    ),

	// States:
	// 
	//  Player Setup:
	//  	Choose wrestlers and Special Moves
	//  Game Setup:
	//		Add Special Move cards to Basic Moves cards to make the deck
	//         Basic Move cards don't have stars. Special Move cards have stars.
	//		Set period and rounds
	//		Take stats from chosen cards to set Base gameboard settings
	//		Player with higher conditioning goes first
	//  ActivePlayer chooses Offense or Defense
	//     Take stats from chosen cards to adjust Temporary gameboard settings
	//	Both players choose a MOVE card simultaneously
	//  Reveal ActivePlayer card
	//      Game adjusts conditioning based on cards chosen
	//  ActivePlayer roll 1 die, either red or blue
	//		Pay TOKENS to reroll if desired
	//		Resolve STAR card outcome
	//		Apply effects from MOVE card + Wrestler trademark
	//		Collect or pay TOKENS as needed
	//      If ActivePlayer plays a scoring card & has higher stats then do Scramble
	//  Scramble:
	//      Depends on the rules on the scramble card
	//
	//      For example (5/25 solo):
	//        Roll D12.
	//		  Guess higher/lower until incorrect.
	//        If 3 or fewer, lose.
	//        If 4 or 5, win.
	//        If 6, burst.
	//      
	//      For example (7/25 ):
	//	 	  Both players choose a number 1-20.
	//        One player rolls D20.
	//		  Player who is closer wins (tie goes to defender).
	//		  Play 7 rounds.
	//        If 3 or fewer, lose.
	//        If 4, win.
	//        If 5,6,7 or guess a roll exactly, burst.
	//	
	//	    After Scramble:
	//        If ActivePlayer won (this is Pin Round 1):
	//			Score the points from the card.
	//			Start on TOP.
	//			Other player's turn is skipped.
	//		  If ActivePlayer got burst and it's Pin Round 2:
	//			Score 2 points.
	//			If successful go to Pin Round 3.
	//			If unsuccessful, the round is over.
	//			Draw another Scramble card.
	//		  If ActivePlayer got burst and it's Pin Round 3:
	//			Draw another Scramble card.
	//			If unsuccessful, the round is over.
	//			If successful then it's a pin and the game is over.
	//
	//	If there was a score and it was the ActivePlayer's STAR card then the other player doesn't get to play
	//	    <do the other steps above>
	//
	//	After all scramble games, go to next round or next period
	//	
    // Note: ID=2 => your first state

    2 => [
        "name" => "deckSetup",
//        "description" => clienttranslate('${actplayer} must play a card or pass'),
   		"description" => clienttranslate('Deck Setup [ST2]'),
//        "descriptionmyturn" => clienttranslate('${you} must play a card or pass'),
        "descriptionmyturn" => clienttranslate('Deck Setup My Turn [ST2]'),
   		"type" => "game",
//		"action" => "stDeckSetup",
   		"transitions" => [ "" => 20 ]
    ],
	
    ST_PLAYER_CHOOSE_WRESTLER => [
        'name' => 'playerChooseWrestler',
        'description' => clienttranslate('${actplayer} must choose a wrestler'),
        'descriptionmyturn' => clienttranslate('${you} must choose a wrestler'),
        'type' => 'activeplayer',
        'possibleactions' => ['chooseWrestler'],
        'transitions' => ['next' => ST_NEXT_PLAYER, 'done' => ST_NEXT_PHASE],  // customize
    ],	
	
	ST_NEXT_PLAYER = [
		'name' => 'nextPlayer',
		'description' => '',
		'type' => 'game',
		'action' => 'stNextPlayer',
		'transitions' => ['chooseNext' => ST_PLAYER_CHOOSE_WRESTLER, 'end' => ST_END_GAME],
	];	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
/*	
    20 => [
		"name" => "chooseWrestler",
		"description"       => clienttranslate('${actplayer} must choose a Wrestler card [ST20]'),
		"descriptionmyturn" => clienttranslate('${you} must choose a Wrestler card [ST20]'),
		"action" => "stMakeEveryoneActive",
		"type" => "multipleactiveplayer",
   		"transitions" => [ "roundsetup" => 40 ]
		// "possibleactions" => [
            // these actions are called from the front with bgaPerformAction, and matched to the function on the game.php file
            // "actPlayCard", 
            // "actPass",
        // ],
    ],


	40 => [
		"name" => "roundSetup",
		"description" => clienttranslate('[ST40] roundSetup'),
		"type" => "game",
		"action" => "stRoundSetup",
        "transitions" => [ "" => 50 ]
	],

    50 => [
		"name" => "chooseMove",
		"description" => clienttranslate('${actplayer} must choose a Move card [ST50]'),
		"descriptionmyturn" => clienttranslate('${you} must choose a Move card [ST50]'),
		"action" => "stChooseMove",
		"type" => "multipleactiveplayer", //multipleactiveplayer //activeplayer
		"transitions" => [ "evaluateMoves" => 60 ] // Should be 60 but need to make the choosing of cards work
		// Transition is triggered from game.php after all players have chosen
    ],
    60 => [
		"name" => "evaluateMoves",
		"description" => clienttranslate('[ST60] evaluateMoves [ST60]'),
		"type" => "game",
		"action" => "stEvaluateMoves",
		"transitions" => [ "" => 20 ] // delete this line until the evaluate works
		// "transitions" => [ "scramble" => 70, "endGame" => 99, "newRound" => 40, "newPeriod" => 80 ]
    ],
    70 => [
		"name" => "scrambleGame",
		"description" => clienttranslate('Scramble Game [ST70]'),
		"type" => "multipleactiveplayer",
		"action" => "stScrambleGame",
		"transitions" => [ "endScramble" => 90 ]
    ],
    80 => [
		"name" => "updatePeriod",
		"description" => clienttranslate('updatePeriod [ST80]'),
		"type" => "game",
		"action" => "stUpdatePeriod",
		"transitions" => [ "" => 40 ]
    ],
    90 => [
		"name" => "evaluateScramble",
		"description" => clienttranslate('evaluateScramble [ST90]'),
		"type" => "game",
		"action" => "stEvaluateScramble",
		"transitions" => [ "endGame" => 99, "newRound" => 40, "newPeriod" => 80 ]
    ],
*/

    // 3 => [
        // "name" => "nextPlayer",
        // "description" => '',
        // "type" => "game",
        // "action" => "stNextPlayer",
        // "updateGameProgression" => true,
        // "transitions" => ["endGame" => 99, "nextPlayer" => 2]
    // ],

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


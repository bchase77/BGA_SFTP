<?php
/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * MatRetDev implementation : © Mike & Jack McKeever and Bryan Chase bryanchase@yahoo.com
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 * 
 * states.inc.php
 *
 * MatRetDev game states description
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
                      method on both client side (Javacript: this.checkAction) and server side (PHP: self::checkAction).
   _ transitions: the transitions are the possible paths to go from a game state to another. You must name
                  transitions in order to use transition names in "nextState" PHP method, and use IDs to
                  specify the next game state for each transition.
   _ args: name of the method to call to retrieve arguments for this gamestate. Arguments are sent to the
           client side to be used on "onEnteringState" or to set arguments in the gamestate description.
   _ updateGameProgression: when specified, the game progression is updated (=> call to your getGameProgression
                            method).
*/

//    !! It is not a good idea to modify this file when a game is running !!

 
$machinestates = array(

    // The initial state. Please do not modify.
    1 => array(
        "name" => "gameSetup",
        "description" => "",
        "type" => "manager",
        "action" => "stGameSetup",
        "transitions" => array( "" => 10 )
    ),
    
	// States:
	// 
	// Hey Ben this is a comment in the states file and I'll do a PULL REQUEST. Tell me what you see.
	//
	//  Player Setup:
	//  	Choose wrestlers and Special Moves
	//  Game Setup:
	//		Set period and rounds
	//		Take stats from chosen cards to set gameboard
	//		Player with higher conditioning goes first
	//  ActivePlayer chooses Offense or Defense
	//     Take stats from chosen cards to adjust gameboard (temporary)
	//	Both players choose a card; Reveal
	//  Game adjusts conditioning based on cards chosen
	//  Both players roll 1 die, either red or blue
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
	//        If ActivePlayer won:
	//			Score the points from the card.
	//			Start on TOP.
	//			Other player's turn is skipped.
	//		  If ActivePlayer got burst and it's Pin Round 1:
	//			Score 2 points.
	//			Go to Pin Round 2.
	//			Draw another Scramble card.
	//		  If ActivePlayer got burst and it's Pin Round 2:
	//			Score 2 points.
	//			Go to Pin Round 3.
	//		  If ActivePlayer got burst and it's Pin Round 3:
	//			Score 2 points.
	//			Go to Pin Round 3.
	//	
	//	
	//	
	//	
	
    // Note: ID=10 => your first state
    10 => array(
    		"name" => "chooseWrestler",
    		"description" => clienttranslate('${actplayer} must choose a wrestler'),
    		"descriptionmyturn" => clienttranslate('${you} must choose a wrestler'),
    		"type" => "activeplayer",
    		"possibleactions" => array( "chooseWrestler" ),
    		"transitions" => array( "chooseWrestler" => 20 )
    ),
    20 => array(
    		"name" => "chooseSpecialMoves",
    		"description" => clienttranslate('${actplayer} must chooose Special Moves cards'),
    		"descriptionmyturn" => clienttranslate('${you} must chooose Special Moves cards'),
    		"type" => "activeplayer",
    		"possibleactions" => array( "chooseMoves" ),
    		"transitions" => array( "chooseMoves" => 30 )
    ),
    30 => array(
    		"name" => "playerTurn",
    		"description" => clienttranslate('${actplayer} must play a card or pass'),
    		"descriptionmyturn" => clienttranslate('${you} must play a card or pass'),
    		"type" => "activeplayer",
    		"possibleactions" => array( "playCard", "pass" ),
    		"transitions" => array( "playCard" => 2, "pass" => 2 )
    ),
    10 => array(
    		"name" => "playerTurn",
    		"description" => clienttranslate('${actplayer} must play a card or pass'),
    		"descriptionmyturn" => clienttranslate('${you} must play a card or pass'),
    		"type" => "activeplayer",
    		"possibleactions" => array( "playCard", "pass" ),
    		"transitions" => array( "playCard" => 2, "pass" => 2 )
    ),
/*
    Examples:
    
    2 => array(
        "name" => "nextPlayer",
        "description" => '',
        "type" => "game",
        "action" => "stNextPlayer",
        "updateGameProgression" => true,   
        "transitions" => array( "endGame" => 99, "nextPlayer" => 10 )
    ),
    
    10 => array(
        "name" => "playerTurn",
        "description" => clienttranslate('${actplayer} must play a card or pass'),
        "descriptionmyturn" => clienttranslate('${you} must play a card or pass'),
        "type" => "activeplayer",
        "possibleactions" => array( "playCard", "pass" ),
        "transitions" => array( "playCard" => 2, "pass" => 2 )
    ), 

*/    
   
    // Final state.
    // Please do not modify (and do not overload action/args methods).
    99 => array(
        "name" => "gameEnd",
        "description" => clienttranslate("End of game"),
        "type" => "manager",
        "action" => "stGameEnd",
        "args" => "argGameEnd"
    )

);




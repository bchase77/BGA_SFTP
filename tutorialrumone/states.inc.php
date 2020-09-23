<?php
/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * TutorialRumOne implementation : © Bryan Chase <bryanchase@yahoo.com>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 * 
 * states.inc.php
 *
 * TutorialRumOne game states description
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
        "description" => clienttranslate("Game setup"),
        "type" => "manager",
        "action" => "stGameSetup",
        "transitions" => array( "" => 10 )
    ),
    
    // Create new deck and deal
    10 => array(
        "name" => "deckSetup",
        "description" => clienttranslate("State10: Deck Setup"),
        "type" => "game",
        "action" => "stDeckSetup",
        "transitions" => array( "" => 20 )
    ),

    /// New hand
    20 => array(
        "name" => "newHand",
        "description" => clienttranslate('State20: New Hand'),
        "type" => "game", // game
        "action" => "stNewHand",
        "updateGameProgression" => true,   
        "transitions" => array( "" => 31 ) 
    ),    
    

    31 => array(
        "name" => "playerTurnDraw",
//        "description" => clienttranslate('State 31a: ${currentPlayer} must draw a card. Others can buy, some day.'),
		"description" => clienttranslate('State 31a: ${handTarget}. ${actplayer} must draw a card. Others can buy, some day.'),
		"descriptionmyturn" => clienttranslate('State 31b: ${handTarget}. ${you} must draw a card.'),
//        "description" => clienttranslate('State 31a: ${actplayer} must draw a card. Others can buy, some day.'),
//        "descriptionmyturn" => clienttranslate('State 31b: ${you} must draw a card.'),
        "type" => "activeplayer", //multipleactiveplayer
		"args" => "argPlayerTurn", // Set the handtarget and who can play
        "possibleactions" => array( "drawCard", "drawDiscard" ),
        "transitions" => array( "" => 33 )
//        "transitions" => array( "drawCard" => 33, "drawDiscard" => 33)
    ), 

    33 => array(
        "name" => "playerTurnPlay",
//        "description" => clienttranslate('State 33a: ${currentPlayer} must discard, play or go down.'),
//        "descriptionmyturn" => clienttranslate('State 33b: ${you} must play a card.'),
		"description" => clienttranslate('State 33a: ${handTarget}. ${actplayer} must play or discard a card.'),
		"descriptionmyturn" => clienttranslate('State 33b: ${handTarget}. ${you} must play or discard a card.'),
        "type" => "activeplayer", //multipleactiveplayer
		"args" => "argPlayerTurn",
        "possibleactions" => array( "playSeveralCards", "discardCard" ),
        "transitions" => array( "playSeveralCards" => 70, "discardCard" => 35 )
        //"possibleactions" => array( "playSeveralCards", "goDown", "discardCard" ),
        //"transitions" => array( "playSeveralCards" => 70 , "goDown" => 60, "discardCard" => 35 )
    ), 
	
//	"buyCard" => 50, "pass" => 33, "playCard" => 35
    35 => array(
        "name" => "nextPlayer",
        "description" => "State 35",
        "type" => "game",
        "action" => "stNextPlayer",
        "transitions" => array( "nextPlayer" => 31, "endHand" => 40 )
    ), 
    
    // End of the hand (scoring, etc...)
	// This state will increment through the types of goals: 2 sets, 1 run 1 set...
    40 => array(
        "name" => "endHand",
        "description" => "State 40",
        "type" => "game",
        "action" => "stEndHand",
        "transitions" => array( "newHand" => 20, "endGame" => 99 )
    ),     
    
    // Someone wants to buy a discarded card
    50 => array(
        "name" => "buyCard",
        "description" => "State 50: Someone has bought the discard.",
        "descriptionmyturn" => clienttranslate('State 50b: ${you} bought the discard.'),
        "type" => "activeplayer",
        "action" => "stBuyCard",
        "transitions" => array( "" => 35 )
    ),     

    // Someone is going down
    60 => array(
        "name" => "goDown",
        "description" => "State 60: Someone is going down!",
        "descriptionmyturn" => clienttranslate('State 60b: ${you} are going down.'),
        "type" => "activeplayer",
        "action" => "stBuyCard",
        "transitions" => array( "" => 35 )
    ),     
    // Someone is playing a card
    70 => array(
        "name" => "playSeveralCards",
        "description" => "State 70: Someone is playing cards.",
        "descriptionmyturn" => clienttranslate('State 70b: ${you} are playing cards.'),
        "type" => "activeplayer",
		"action" => "stPlayCards",
        "transitions" => array( "" => 33 )
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

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
    
    // Create new deck
    10 => array(
        "name" => "deckSetup",
        "description" => clienttranslate("Deckss setup"),
        "type" => "manager",
        "action" => "stDeckSetup",
        "transitions" => array( "" => 20 )
    ),

    /// New hand
    20 => array(
        "name" => "newHand",
        "description" => "This is machinestates 20fs",
        "type" => "game",
        "action" => "stNewHand",
        "updateGameProgression" => true,   
        "transitions" => array( "" => 31 )
    ),    

      
    
    // I don't think we need Trick. Players just play until someone goes out.
    
/*
    30 => array(
        "name" => "newTrick",
        "description" => "",
        "type" => "game",
        "action" => "stNewTrick",
        "transitions" => array( "" => 31 )
    ),       
*/
    31 => array(
        "name" => "playerTurnDraw",
        "description" => clienttranslate('${actplayer} must draw a card. Others can buy.'),
        "descriptionmyturn" => clienttranslate('${you} must draw a card.'),
        "type" => "multipleactiveplayer",
        "possibleactions" => array( "drawCard", "buyCard", "pass" ),
        "transitions" => array( "drawCard" => 33, "buyCard" => 50, "pass" => 33 )
    ), 
    33 => array(
        "name" => "playerTurnPlay",
        "description" => clienttranslate('${actplayer} must play a card.'),
        "descriptionmyturn" => clienttranslate('${you} must play a card.'),
        "type" => "activeplayer",
        "possibleactions" => array( "playCard" ),
        "transitions" => array( "playCard" => 35 )
    ), 
    35 => array(
        "name" => "nextPlayer",
        "description" => "",
        "type" => "activeplayer",
        "action" => "stNextPlayer",
        "transitions" => array( "nextPlayer" => 31, "endHand" => 40 )
    ), 
    
    
    // End of the hand (scoring, etc...)
    40 => array(
        "name" => "endHand",
        "description" => "",
        "type" => "game",
        "action" => "stEndHand",
        "transitions" => array( "nextHand" => 20, "endGame" => 99 )
    ),     
    
    // Someone wants to buy a discarded card
    50 => array(
        "name" => "buyCard",
        "description" => "Someone has bought the discard.",
        "type" => "game",
        "action" => "stBuyCard",
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

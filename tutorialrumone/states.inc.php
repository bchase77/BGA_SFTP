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
        "action" => "stGameSetup", // ACTION: Do this upon entering the state
        "transitions" => array( "" => 10 )
    ),
    
    // Create new deck and deal
    10 => array(
        "name" => "deckSetup",
        "description" => clienttranslate("State10: Deck Setup"),
        "type" => "game",
        "action" => "stDeckSetup", // ACTION: Do this upon entering the state
        "transitions" => array( "" => 20 )
    ),

    /// New hand
    20 => array(
        "name" => "newHand",
        "description" => clienttranslate('State20: New Hand'),
        "type" => "game", // game
        "action" => "stNewHand", // ACTION: Do this upon entering the state
        "updateGameProgression" => true,   
        "transitions" => array( "" => 30 ) 
    ),    
    30 => array(
        "name" => "playerTurnDraw",
		"description" => clienttranslate('St 30a: ${handTarget}. TP: ${turnPlayerName} must draw from deck or discard pile. Others might buy.'),
		"descriptionmyturn" => clienttranslate('State 30b: ${handTarget}. TP: ${turnPlayerName} must draw from the deck or the discard pile. Others might buy.'),
        //"type" => "activeplayer", //multipleactiveplayer
        "type" => "multipleactiveplayer",
        "action" => "stShowBUYButtons", // ACTION: Do this upon entering the state
		"args" => "argPlayerTurnDraw", // Set the handtarget and who can play
        "possibleactions" => array( "drawCard", "drawDiscard", "buyRequest", "notBuyRequest" ),
        "transitions" => array( "resolveBuyers" => 50 )
//        "transitions" => array( "drawCard" => 33, "drawDiscard" => 33)
//        "transitions" => array( "drawCard" => 55, "drawDiscard" => 33, "buyRequest" => 50, "notBuyRequest" => 53)
    ), 
    32 => array(
        "name" => "checkEmptyDeck",
        "description" => "St 32",
        "type" => "game",
        "action" => "stCheckEmptyDeck", // ACTION: Do this upon entering the state
        "transitions" => array( "drawAndLetPlayerPlay" => 35, "letPlayerDrawAfterBuy" => 57 )
    ), 
    33 => array(
        "name" => "drawDiscard",
        "description" => "State 33",
        "type" => "game",
        "action" => "stDrawDiscard", // ACTION: Do this upon entering the state
        "transitions" => array( "" => 35 ) 
    ), 
    35 => array(
        "name" => "playerTurnPlay",
//        "description" => clienttranslate('State 35a: ${currentPlayer} must discard, play or go down.'),
//		"descriptionmyturn" => clienttranslate('State 35b: ${handTarget}. ${you} must play, discard or go down.'),
		"description" => clienttranslate('St 35a: ${handTarget}. TP: ${turnPlayerName} must ${thingsCanDo}'),
		"descriptionmyturn" => clienttranslate('St 35b: ${handTarget}. ${you} must ${thingsCanDo}'),
        "type" => "activeplayer", //multipleactiveplayer
		"action" => "stPlayerTurnPlay",
		"args" => "argPlayerTurnPlay",
        "possibleactions" => array( "playerGoDown", "discardCard", 'playCard'),
        "transitions" => array( "playerGoDown" => 70, "discardCard" => 37, "playCard" => 35 )
    ), 
    37 => array(
        "name" => "nextPlayer",
        "description" => "St 37",
        "type" => "game",
        "action" => "stNextPlayer", // ACTION: Do this upon entering the state
        "transitions" => array( "nextPlayer" => 30, "endHand" => 40 )
    ), 
    // End of the hand (scoring, etc...)
	// This state will increment through the types of goals: 2 sets, 1 run 1 set...
    40 => array(
        "name" => "endHand",
        "description" => "St 40",
        "type" => "game",
        "action" => "stEndHand", // ACTION: Do this upon entering the state
        "transitions" => array( "newHand" => 20, "endGame" => 99 )
    ),
    50 => array(    // Resolve the potential buyers of the discard card
        "name" => "resolveBuyers",
        "description" => clienttranslate('State 50: Resolve discard buyers.'),
        "descriptionmyturn" => clienttranslate('State 50b: Resolve discard buyers.'),
        "type" => "game",
        "action" => "stResolveBuyers", // ACTION: Do this upon entering the state
        "transitions" => array( "checkEmptyDeck" => 32, "drawDiscard" => 33, "other" => 35 )
    ),

/*
    50 => array(    // Someone wants to buy a discarded card
        "name" => "playerWantsToBuy",
        "description" => clienttranslate('State 50: Someone wants to buy the discard.'),
        "descriptionmyturn" => clienttranslate('State 50b: ${you} want to buy the discard.'),
        "type" => "game",
        "action" => "stBuyRequest", // ACTION: Do this upon entering the state
        "transitions" => array( "" => 30 )
    ),
*/
/*
    53 => array(    // Someone does not want to buy a discarded card
        "name" => "playerDoesNotWantToBuy",
        "description" => clienttranslate('State 53: Someone DOES NOT want to buy the discard.'),
        "descriptionmyturn" => clienttranslate('State 50b: ${you} DO NOT WANT to buy the discard.'),
        "type" => "game",
        "action" => "stNotBuyCard", // ACTION: Do this upon entering the state
        "transitions" => array( "" => 30 )
    ),
*/
/*
    55 => array(    // The turn-player is drawing a card from the deck, first check if there's a buyer
        "name" => "turnPlayerDrawingResolveBuyers",
        "description" => clienttranslate('State 55: ${actplayer} is drawing from the deck. Resolve buyers first.'),
        "descriptionmyturn" => clienttranslate('State 55b: ${you} are drawing from the deck. Resolve buyers first.'),
        "type" => "game",
        "action" => "stResolveBuyers", // ACTION: Do this upon entering the state
        "transitions" => array( "" => 32 )
    ),     
    57 => array(    // The turn-player is drawing a card from the deck
        "name" => "turnPlayerDrawFromDeck",
        "description" => clienttranslate('State 57: ${actplayer} is drawing from the deck.'),
        "descriptionmyturn" => clienttranslate('State 57b: ${you} are drawing from the deck.'),
        "type" => "game",
        "action" => "stDrawDeck", // ACTION: Do this upon entering the state
        "transitions" => array( "" => 32 )
    ),   
*/	
    // Someone is trying to play a card
     60 => array(
         "name" => "playCard",
         "description" => "St 60: Someone is trying to play a card.",
         "descriptionmyturn" => clienttranslate('State 60b: ${you} are trying to play a card.'),
         "type" => "activeplayer",
         "action" => "stPlayCard", // ACTION: Do this upon entering the state
         "transitions" => array( "" => 33 )
    ),     
    // Someone is going down
    70 => array(
        "name" => "playerGoDown",
        "description" => clienttranslate('State 70: ${actplayer} is going down.'),
        "descriptionmyturn" => clienttranslate('State 70b: ${you} are going down.'),
        "type" => "activeplayer",
		"action" => "stPlayCards", // ACTION: Do this upon entering the state
        "transitions" => array( "" => 33 )
    ),     
    // Someone is playing a card
    // 70 => array(
        // "name" => "playSeveralCards",
        // "description" => "State 70: Someone is playing cards.",
        // "descriptionmyturn" => clienttranslate('State 70b: ${you} are playing cards.'),
        // "type" => "activeplayer",
		// "action" => "stPlayCards",
        // "transitions" => array( "" => 33 )
    // ),     
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

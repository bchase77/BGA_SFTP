<?php
/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * LiverpoolRummy implementation : © Bryan Chase <bryanchase@yahoo.com>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 * 
 * states.inc.php
 *
 * LiverpoolRummy game states description
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
        "description" => clienttranslate("Deck Setup[ST10]"),
        "type" => "game",
        "action" => "stDeckSetup", // ACTION: Do this upon entering the state
        "transitions" => array( "" => 20 )
    ),

    /// New hand
    20 => array(
        "name" => "newHand",
        "description" => clienttranslate('New Hand <span style:"color:gray"></span>'),
        "type" => "game", // game
        "action" => "stNewHand", // ACTION: Do this upon entering the state
        "updateGameProgression" => true,   
        "transitions" => array( "" => 30 ) 
    ),    
    30 => array(
        "name" => "playerTurnDraw",
		"description" => clienttranslate('${buyMessage}${turnPlayerName} must draw from deck or discard pile. Others might buy.'),
		"descriptionmyturn" => clienttranslate('${turnPlayerName} must draw from deck or discard pile. Others might buy.'),
        "type" => "activeplayer",
        "action" => "stShowBUYButtons", // ACTION: Do this upon entering the state
		"args" => "argPlayerTurnDraw", // Set the handtarget and who can play
        "possibleactions" => array( "drawCard", "buyRequest", "notBuyRequest", "zombiePass" ),
        "transitions" => array( "drawCard" => 35, "zombiePass" => 37 )
    ), 
    // 32 => array(
        // "name" => "checkEmptyDeck",
        // "description" => '<span style:"color:gray"></span>',
        // "type" => "game",
        // "action" => "stCheckEmptyDeck", // ACTION: Do this upon entering the state
        // "transitions" => array( "drawAndLetPlayerPlay" => 35, "letPlayerDrawAfterBuy" => 57 )
    // ), 

    // 33 => array(
        // "name" => "drawDiscard",
        // "description" => "[ST33]",
        // "type" => "game",
        // "action" => "stDrawDiscard", // ACTION: Do this upon entering the state
        // "transitions" => array( "" => 35 ) 
    // ),

    35 => array(
        "name" => "playerTurnPlay",
		"description" => clienttranslate('${turnPlayerName} must ${thingsCanDo}'),
		"descriptionmyturn" => clienttranslate('${you} must ${thingsCanDo}'),
        "type" => "activeplayer", //multipleactiveplayer
		"action" => "stPlayerTurnPlay", // ACTION: Do this upon entering the state
		"args" => "argPlayerTurnPlay",
        "possibleactions" => array( "playerGoDown", "discardCard", 'playCard', 'playCardMultiple', "zombiePass", "buyRequest", "notBuyRequest"),
        "transitions" => array( "discardCard" => 36, "playCard" => 35, "playCardMultiple" => 35, "buyRequest" => 60, "notBuyRequest" => 61, "zombiePass" => 37 )
    ), 
	36 => array(
		"name" => "afterDiscard",
		"description" => "[ST36]",
        "type" => "game",
        "action" => "stWaitForAll", // ACTION: Do this upon entering the state
        "possibleactions" => array( "zombiePass" ),
		"transitions" => array( "fullyResolved" => 37 )
    ), 
    37 => array(
        "name" => "nextPlayer",
        "description" => "[ST37]",
        "type" => "game",
        "action" => "stNextPlayer", // ACTION: Do this upon entering the state
        "transitions" => array( "nextPlayer" => 30, "endHand" => 40 )
    ), 
    // End of the hand. Let players look at their hand and the board
    40 => array(
        "name" => "wentOut",
        "description" => clienttranslate('${player_name} went out!'),
		"descriptionmyturn" => clienttranslate('${player_name} went out!'),
        "type" => "multipleactiveplayer",
        //"type" => "activeplayer", //multipleactiveplayer
        //"type" => "game", // GAME doesn't work "invalid end game state" or something
        "action" => "stWentOut", // ACTION: Do this upon entering the state
		"args" => "argWentOut",
		"possibleactions" => array( "playerHasReviewedHand", 'endgame' ),
        "transitions" => array( "playerHasReviewedHand" => 45, 'endgame' => 99 )
    ),
    45 => array(
        "name" => "endHand",
        "description" => "(st45)",
        "type" => "game",
        "action" => "stEndHand", // ACTION: Do this upon entering the state
        "transitions" => array( "newHand" => 20, "endGame" => 99 )
    ),
    // 50 => array(    // Resolve the potential buyers of the discard card
        // "name" => "resolveBuyers",
        // "description" => clienttranslate('Resolve discard buyers.'),
        // "descriptionmyturn" => clienttranslate('Resolve discard buyers.'),
        // "type" => "game",
        // "action" => "stResolveBuyers", // ACTION: Do this upon entering the state
////        "transitions" => array( "checkEmptyDeck" => 32, "drawDiscard" => 33, "other" => 35 )
        // "transitions" => array( "checkEmptyDeck" => 32, "buyNotAllowed" => 37, "other" => 35 )
    // ),
    // 57 => array(    // The turn-player is drawing a card from the deck
        // "name" => "turnPlayerDrawFromDeck",
        // "description" => clienttranslate('${actplayer} is drawing from the deck.'),
        // "descriptionmyturn" => clienttranslate('${you} are drawing from the deck.'),
        // "type" => "game",
        // "action" => "stDrawDeck", // ACTION: Do this upon entering the state
        // "transitions" => array( "" => 35 )
    // ),   
	
	// Someone is trying to buy a card
    60 => array(
        "name" => "buyTryFromPTP",
        "description" => clienttranslate("Someone is trying to buy the discard during PlayerTurnPlay."),
        "descriptionmyturn" => clienttranslate('${you} are trying to buy the discard during PlayerTurnPlay.'),
        "type" => "activeplayer",
        "action" => "stBuyTry", // ACTION: Do this upon entering the state
        "transitions" => array( "" => 35 )
    ),     
    61 => array(
        "name" => "notBuyTryFromPTP",
        "description" => clienttranslate("Someone is trying to NOT buy the discard during PlayerTurnPlay."),
        "descriptionmyturn" => clienttranslate('${you} are trying to NOT buy the discard during PlayerTurnPlay.'),
        "type" => "activeplayer",
        "action" => "stNotBuyTry", // ACTION: Do this upon entering the state
        "transitions" => array( "" => 35 )
    ),     
    65 => array(
        "name" => "buyTryFromPTD",
        "description" => clienttranslate("Someone is trying to buy the discard during PlayerTurnDraw."),
        "descriptionmyturn" => clienttranslate('${you} are trying to buy the discard during PlayerTurnDraw.'),
        "type" => "activeplayer",
        "action" => "stBuyTry", // ACTION: Do this upon entering the state
        "transitions" => array( "" => 30 )
    ),     
    66 => array(
        "name" => "notBuyTryFromPTD",
        "description" => clienttranslate("Someone is trying to NOT buy the discard during PlayerTurnDraw."),
        "descriptionmyturn" => clienttranslate('${you} are trying to NOT buy the discard during PlayerTurnDraw.'),
        "type" => "activeplayer",
        "action" => "stNotBuyTry", // ACTION: Do this upon entering the state
        "transitions" => array( "" => 30 )
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

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
        "description" => clienttranslate("Deck Setup[ST10]"),
        "type" => "game",
        "action" => "stDeckSetup", // ACTION: Do this upon entering the state
        "transitions" => array( "" => 20 )
    ),

    /// New hand
    20 => array(
        "name" => "newHand",
        "description" => clienttranslate('New Hand <span style:"color:gray">(st20)</span>'),
        "type" => "game", // game
        "action" => "stNewHand", // ACTION: Do this upon entering the state
        "updateGameProgression" => true,   
        "transitions" => array( "" => 30 ) 
    ),    
    30 => array(
        "name" => "playerTurnDraw",
//		"description" => clienttranslate('Target: ${handTarget}. ${buyMessage}${turnPlayerName} must draw from deck or discard pile. Others might buy.  (st30a)'),
//		"descriptionmyturn" => clienttranslate('Target: ${handTarget}. ${turnPlayerName} must draw from the deck or the discard pile. Others might buy.  (st30b)'),
		"description" => clienttranslate('${buyMessage}${turnPlayerName} must draw from deck or discard pile. Others might buy. <span style:"color:gray">(st30a)</span>'),
		"descriptionmyturn" => clienttranslate('${turnPlayerName} must draw from the deck or the discard pile. Others might buy. <span style:"color:gray">(st30b)</span>'),
        "type" => "multipleactiveplayer",
        "action" => "stShowBUYButtons", // ACTION: Do this upon entering the state
		"args" => "argPlayerTurnDraw", // Set the handtarget and who can play
        "possibleactions" => array( "drawCard", "drawDiscard", "buyRequest", "notBuyRequest", "zombiePass" ),
        "transitions" => array( "resolveBuyers" => 50, "zombiePass" => 37 )
    ), 
    32 => array(
        "name" => "checkEmptyDeck",
        "description" => '<span style:"color:gray">(st32)</span>',
        "type" => "game",
        "action" => "stCheckEmptyDeck", // ACTION: Do this upon entering the state
        "transitions" => array( "drawAndLetPlayerPlay" => 35, "letPlayerDrawAfterBuy" => 57 )
    ), 
    33 => array(
        "name" => "drawDiscard",
        "description" => "[ST33]",
        "type" => "game",
        "action" => "stDrawDiscard", // ACTION: Do this upon entering the state
        "transitions" => array( "" => 35 ) 
    ), 
    35 => array(
        "name" => "playerTurnPlay",
//		"description" => clienttranslate('Target: ${handTarget}. ${turnPlayerName} must ${thingsCanDo}  (st35a)'),
//		"descriptionmyturn" => clienttranslate('Target: ${handTarget}. ${you} must ${thingsCanDo}  (st35b)'),
		"description" => clienttranslate('${turnPlayerName} must ${thingsCanDo}  (st35a)'),
		"descriptionmyturn" => clienttranslate('${you} must ${thingsCanDo}  (st35b)'),
        "type" => "activeplayer", //multipleactiveplayer
		"action" => "stPlayerTurnPlay", // ACTION: Do this upon entering the state
		"args" => "argPlayerTurnPlay",
//        "possibleactions" => array( "playerGoDown", "discardCard", 'playCard', 'playCardMultiple'),
//        "transitions" => array( "playerGoDown" => 70, "discardCard" => 36, "playCard" => 35, "playCardMultiple" => 35 )
        "possibleactions" => array( "playerGoDown", "discardCard", 'playCard', 'playCardMultiple', "zombiePass", "notBuyRequest", "buyRequest"),
        "transitions" => array( "discardCard" => 36, "playCard" => 35, "playCardMultiple" => 35, "zombiePass" => 37 )
    ), 
	36 => array(
		"name" => "waitForAll",
		"description" => "(st36)",
        "type" => "game",
        "action" => "stWaitForAll", // ACTION: Do this upon entering the state
//		"possibleactions" => array( "notFullyResolved", "fullyResolved" ),
//		"transitions" => array( "notFullyResolved" => 36, "fullyResolved" => 37 )
		"transitions" => array( "fullyResolved" => 37 )
    ), 
    37 => array(
        "name" => "nextPlayer",
        "description" => "(st36)",
        "type" => "game",
        "action" => "stNextPlayer", // ACTION: Do this upon entering the state
        //"transitions" => array( "nextPlayer" => 30, "endHand" => 40, "waitForAllBuyers" => 37 )
        "transitions" => array( "nextPlayer" => 30, "endHand" => 40 )
    ), 
    // End of the hand. Let players look at their hand and the board
    40 => array(
        "name" => "wentOut",
        "description" => clienttranslate('${player_name} went out!'),
		"descriptionmyturn" => clienttranslate('${player_name} went out!'),
        "type" => "multipleactiveplayer",
        "action" => "stWentOut", // ACTION: Do this upon entering the state
		"args" => "argWentOut",
		"possibleactions" => array( "playerHasReviewedHand" ),
        "transitions" => array( "" => 45 )
    ),
	// Update the scoring; Shuffle the cards; 
	// Increment through the types of goals: 2 sets, 1 run & 1 set, etc...
    45 => array(
        "name" => "endHand",
        "description" => "(st45)",
        "type" => "game",
        "action" => "stEndHand", // ACTION: Do this upon entering the state
        "transitions" => array( "newHand" => 20, "endGame" => 99 )
    ),
    50 => array(    // Resolve the potential buyers of the discard card
        "name" => "resolveBuyers",
        "description" => clienttranslate('Resolve discard buyers. (st50a)'),
        "descriptionmyturn" => clienttranslate('Resolve discard buyers.  (st50b)'),
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
*/    57 => array(    // The turn-player is drawing a card from the deck
        "name" => "turnPlayerDrawFromDeck",
        "description" => clienttranslate('${actplayer} is drawing from the deck.(st57a)'),
        "descriptionmyturn" => clienttranslate('${you} are drawing from the deck.(st57b)'),
        "type" => "game",
        "action" => "stDrawDeck", // ACTION: Do this upon entering the state
        "transitions" => array( "" => 35 )
    ),   

    // Someone is trying to play a card
     60 => array(
         "name" => "playCard",
         "description" => "Someone is trying to play a card.(st60a)",
         "descriptionmyturn" => clienttranslate('${you} are trying to play a card.(st60b)'),
         "type" => "activeplayer",
         "action" => "stPlayCard", // ACTION: Do this upon entering the state
         "transitions" => array( "" => 33 )
    ),     
    // Someone is going down: Nope, stay in the PLAY state.
/*    70 => array(
        "name" => "playerGoDown",
        "description" => clienttranslate('${actplayer} is going down.(st70a)'),
        "descriptionmyturn" => clienttranslate('${you} are going down.(st70b)'),
        "type" => "activeplayer",
		"action" => "stPlayCards", // ACTION: Do this upon entering the state
        "transitions" => array( "" => 33 )
    ),     
*/    // Someone is playing a card
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

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
  * tutorialrumone.game.php
  *
  * This is the main file for your game logic.
  *
  * In this PHP file, you are going to defines the rules of the game.
  *
  */

require_once( APP_GAMEMODULE_PATH.'module/table/table.game.php' );

class TutorialRumOne extends Table
{
	function __construct( )
	{
        // Your global variables labels (scalars only, not arrays):
        //  Here, you can assign labels to global variables you are using for this game.
        //  You can use any number of global variables with IDs between 10 and 99.
        //  If your game has options (variants), you also have to associate here a label to
        //  the corresponding ID in gameoptions.inc.php.
        // Note: afterwards, you can get/set the global variables with getGameStateValue/setGameStateInitialValue/setGameStateValue
        parent::__construct();
        
        self::initGameStateLabels( array( 
            //    "my_first_global_variable" => 10,
            //    "my_second_global_variable" => 11,
            //      ...
            //    "my_first_game_variant" => 100,
            //    "my_second_game_variant" => 101,
            //      ...
          // NOTE: Hand Types Order: 2 sets
          //                         1 set 1 run
          //                         2 runs
          //                         3 sets
          //                         2 sets 1 run
          //                         1 set 2 runs
          //                         3 runs

            "currentHandType" => 10,
			"bob" => 11

        ) );
	
        $this->handTypes = array(
		  0 => "2 Sets",
		  1 => "1 Set and 1 Run",
		  2 => "2 Runs",
		  3 => "3 Sets",
		  4 => "2 Sets and 1 Run",
		  5 => "1 Set and 2 Runs",
		  6 => "3 Runs"
		);
		  
        $this->cards = self::getNew( "module.common.deck" );
        $this->cards->init( "card" );
	}
	
    protected function getGameName( )
    {
		// Used for translations and stuff. Please do not modify.
        return "tutorialrumone";
    }	

    /*
        setupNewGame:
        
        This method is called only once, when a new game is launched.
        In this method, you must setup the game according to the game rules, so that
        the game is ready to be played.
    */
    protected function setupNewGame( $players, $options = array() )
    {
		self::trace("bmc: !!setupNewGame!!");

        // Don't do a lot here since server side hasn't been set up yet and so it's hard to debug.
		// Set the colors of the players with HTML color code
        // The default below is red/green/blue/orange/brown
        // The number of colors defined here must correspond to the maximum number of players allowed for the gams
        $gameinfos = self::getGameinfos();
        $default_colors = $gameinfos['player_colors'];
 
        // Create players
        // Note: if you added some extra field on "player" table in the database (dbmodel.sql), you can initialize it there.
        $sql = "INSERT INTO player (player_id, player_color, player_canal, player_name, player_avatar) VALUES ";
        $values = array();
        foreach( $players as $player_id => $player )
        {
            $color = array_shift( $default_colors );
            $values[] = "('".$player_id."','$color','".$player['player_canal']."','".addslashes( $player['player_name'] )."','".addslashes( $player['player_avatar'] )."')";
        }
        $sql .= implode( $values, ',' );
        self::DbQuery( $sql );
        self::reattributeColorsBasedOnPreferences( $players, $gameinfos['player_colors'] );
        self::reloadPlayersBasicInfos();

        self::setGameStateInitialValue( 'currentHandType', 0 );


        // Activate first player (which is in general a good idea :) )
        $this->activeNextPlayer();
		
        /************ Start the game initialization *****/

        // Init global values with their initial values
        //self::setGameStateInitialValue( 'my_first_global_variable', 0 );
        
        // Init game statistics
        // (note: statistics used in this file must be defined in your stats.inc.php file)
        //self::initStat( 'table', 'table_teststat1', 0 );    // Init a table statistics
        //self::initStat( 'player', 'player_teststat1', 0 );  // Init a player statistics (for all players)

        /************ End of the game initialization *****/
    }

	function stDeckSetup()
	{
		self::warn("bmc: !!setupNewDeck!!");
        // The game is played with multiple standard 52-pack plus the jokers.
        // 2 decks for three to five players. 3 decks for more players. 

        // Variants:
        //   3 runs: Go down with no remaining cards in hand, no final discard (12 cards)
        //   First one to click BUY gets it, not the first in line
        //   Runs must include at least 3 non-wildcards in an original 4 card grouping.
        //   Sets must include at least 2 non-wildcards.
        //   Replace that other player's laid Joker from within a run (or a set)
        //   Discard must not fit into either their own or any other player's laid cards.
        //     (or else draw extra card and whoever called Liverpool can discard a card)
		
		// Create cards
        $cards = array ();

//        foreach ( $this->colors as $color_id => $color ) {
		for ($colors = 1; $colors <=4; $colors ++) {
			$color_id = $colors;
            // spade, heart, diamond, club
            foreach ( $this->values_label as $value => $type_arg ) {
//            for ($value = 1; $value <= 13; $value ++) {
                //  A, 2, 3, 4, ... K
				if ( !array_key_exists("51", $cards)) {
					$cards [] = array ('type' => $color_id, 'type_arg' => $value, 'nbr' => 1 );
				}
            }
        }

		self::trace("bmc: !!makingCards!!");
		
        // Add jokers
        $jokers = array ();
		$color = 5;
		for ($value = 1; $value <= 2; $value ++) {
			//$jokers [] = array ('type' => $color, 'type_arg' => $value, 'nbr' => 1 );
			array_push( $cards, array ('type' => $color, 'type_arg' => $value, 'nbr' => 1 ));
		}

        $this->cards->createCards( $cards, 'deck' );
		// Go to the next game state
        $this->gamestate->nextState();	
	}


// WAS IN NEW DECK:
        // Shuffle deck
//        $this->cards->shuffle('deck');
		
        // Deal some cards to each players

//        $players = self::loadPlayersBasicInfos();

//		$dbg1st = 0;
//        foreach ( $players as $player_id => $player ) {
//			if ($dbg1st == 0) {
//				$dbg1st = 1;
//				$cards = $this->cards->pickCards( 5, 'deck', $player_id );
//			} else {
//				$cards = $this->cards->pickCards( 1, 'deck', $player_id );
//			}
 //       } 
//var_dump($cards);
//die('okSev');

		// Put 1 card into the discard pile
		//getCardOnTop( $location ); // Gets information
		//insertCardOnExtremePosition( $card_id, $location, $bOnTop ); //
		//moveCard( $card_id, $location, $location_arg=0 )
		
//		$bill = $this->cards->getCardOnTop ( 'deck' );

//var_dump($bill['id']);
//var_dump($bill);
//die('okSev');
		
//		self::warn("bmc: this->cards->getCardOnTop");
//		self::dump("ontop", $this->cards->getCardOnTop ( 'deck' )[ 'id' ]);

		// Put 1 card from the deck into the discard pile and give it a starting weight of 100
//		$this->cards->moveCard( $this->cards->getCardOnTop ( 'deck' )[ 'id' ], 'discard', 100); 

//		$cardLocations = $this->cards->countCardsInLocations();
//var_dump($cardLocations); // this accurately shows an array of the number of cards in each location
//die('okSev');

		// Go to the next game state
//        $this->gamestate->nextState();	
//	}
	
    /*
        getAllDatas: 
        
        Gather all informations about current game situation (visible by the current player).
        
        The method is called each time the game interface is displayed to a player, ie:
        _ when the game starts
        _ when a player refreshes the game page (F5)
    */
	
    protected function getAllDatas()
    {
		// This returns data to the JS code in gamedatas datastructure
		self::trace("bmc: !!getAllDatas!!");

        $result = array();
    
        $current_player_id = self::getCurrentPlayerId();    // !! We must only return informations visible by this player !!
    
		$result['currentPlayerId'] = $current_player_id;
		$result['activePlayerId'] = self::getActivePlayerId();
		
        // Get information about players
        // Note: you can retrieve some extra field you added for "player" table in "dbmodel.sql" if you need it.
        $sql = "SELECT player_id id, player_score score FROM player ";
        $result['players'] = self::getCollectionFromDb( $sql );
  
        // TODO: Gather all information about current game situation (visible by player $current_player_id).

        // Cards in player hand
        $result['hand'] = $this->cards->getCardsInLocation( 'hand', $current_player_id );

//        $result['deckCount'] = $this->cards->countCardInLocation( 'deck' );
		
		$result['deckIDs'] = array_keys($this->cards->getCardsInLocation( 'deck' ));
		
		// TODO: don't return all the IDs of the cards in the deck
		//$result['deck'] = $this->cards->getCardsInLocation ('deck');
		
		$result['allHands'] = $cardsByLocation = $this->cards->countCardsByLocationArgs( 'hand' );
		
		$countCardsByLocation = $this->cards->countCardsByLocationArgs( 'hand' );
		$result['dbgcountA'] = $countCardsByLocation ;
		$result['dbgcountV'] = count($countCardsByLocation);

		$playersNumber = self::getPlayersNumber();
		$result['dbgPlayersNumber'] = $playersNumber ;
		
		$result['handTypes'] = $this->handTypes;

//var_dump($result);
//die('okSev');
		
		$result['currentHandType'] = self::getGameStateValue( 'currentHandType' );

		// TODO: Order discarded cards in time-order discarded. for now, just show them.
		// I think this works now and the sorting doesn't matter. The JS looks up the location_arg.
        //this works but doesn't order the cards according to time discarded:
		//$result['discardPile'] = $this->cards->getCardsInLocation( 'discard' );
//        $result['discardPile'] = $this->cards->getCardsInLocation( 'discard', $order_by = '`card_location_arg` ASC');
        //$result['discardPile'] = $this->cards->getCardsInLocation( 'discard', $order_by = 'ASC');

//var_dump("<div>bmc: 1</div>");
//		$toBeSortedDiscard = $this->cards->getCardsInLocation( 'discard' );
//var_dump($toBeSortedDiscard);

//		$result['discardPileRaw'] = $toBeSortedDiscard;

//		$sortOneWay = $toBeSortedDiscard;
//		uasort($sortOneWay, function($aa, $ba) {
//			return strcmp($aa['location_arg'], $ba['location_arg']);
//		});
		
//var_dump("<div>bmc: 2</div>");
//var_dump($sortOneWay);

//		$discardKeys = array_column($toBeSortedDiscard, 'location_arg');

//var_dump($discardKeys);
//		array_multisort($discardKeys, SORT_ASC, $toBeSortedDiscard);
//var_dump("bmc: 3");
//var_dump($discardKeys);
		
//		$result['discardPile'] = $sortOneWay;
		$result['discardPile'] = $this->cards->getCardsInLocation( 'discardPile' );
		
		self::trace("bmc:discardPile:");
//var_dump($result['discardPile']);
//print_r($result['discardPile']);
//die('okSev');
		//self::trace(implode("}", $result['discardPile']));



//        $result['deckCards'] = $this->cards->getCardsInLocation( 'deck' );

// 54 cards looks good up to here! [bmc 8/29/2020]
//var_dump($result);
//die('okSev');
        
        // Cards played on the table
        $result['cardsontable'] = $this->cards->getCardsInLocation( 'cardsontable' );

		$debugCount = $this->cards->countCardsInLocations();
		self::trace("bmc: !!cardsinlocations!!");
		//self::trace(implode(",", $debugCount));
  
        return $result;
    }

// BMC JUST ADDED THIS, working on it
	function argPlayerTurn()
	{
        return array(
//	            '_private' => array(  // Using "_private" keyword, all data inside this array will be made private
//
//                    'active' => array(    // Using "active" keyword inside "_private", you select active player(s)
//                      'somePrivateData' => self::getSomePrivateData()   // will be send only to active player(s)
//                    )
//                )
//,            'possibleMoves' => self::getPossibleMoves()
        );
    }

    /*
        getGameProgression:
        
        Compute and return the current game progression.
        The number returned must be an integer beween 0 (=the game just started) and
        100 (= the game is finished or almost finished).
    
        This method is called each time we are in a game state with the "updateGameProgression" property set to true 
        (see states.inc.php)
    */
    function getGameProgression()
    {
        // TODO: compute and return the game progression

        return 0;
    }


//////////////////////////////////////////////////////////////////////////////
//////////// Utility functions
////////////    

    /*
        In this space, you can put any utility methods useful for your game logic
    */

    function argMyArgumentMethod()
    {
//         $currentPlayer = array_search($this; // $this->getCurrentPlayerId();
		$currentPlayer = $this->getActivePlayerName();
		$currentHandType = $this->getGameStateValue( 'currentHandType' );

		return array(
//            'currentPlayer' => $this->getCurrentPlayerId() //2 //$currentPlayer  // In this case ${nbr} in the description will be replaced by "2"
            'currentPlayer' => $currentPlayer,
			'handTarget' => $this->handTypes[$currentHandType]
        );    
    }

//////////////////////////////////////////////////////////////////////////////
//////////// Player actions
//////////// 

    /*
        Each time a player is doing some game action (i.e. clicking something), one of the methods below is called.
        (note: each method below must match an input method in tutorialrumone.action.php)
    */

// Possible actions for current active player:
//   Draw card
//   Discard card
//   Play card (only if already gone down)
//   Go down
//
// Possible actions for other players:
//   Buy discarded card
//   Call Liverpool on another player


// TODO: Check that this player can really click the PLAY button.
//     Check if the active player really has the played card in their hand.
// AT the end, disable all players and go to the next state with this:
//     $this->gamestate->setAllPlayersNonMultiactive( $next_state )

    function playCard($card_id) {
		self::trace("bmc: !!playCard!!");
        $thisPlayerId = self::getActivePlayerId();
		$currentPlayerId = self::getCurrentPlayerId();
		self::trace("bmc: <div>");
		self::trace($thisPlayerId);
		self::trace($currentPlayerId);
		self::trace("</div>");
		
		if ($thisPlayerId == $currentPlayerId) { // Allow play if it's that player's turn
			$discardWeight = $this->cards->countCardInLocation('discardPile') + 100;
			$this->cards->moveCard($card_id, 'discardPile', $discardWeight);

//var_dump($thisPlayerId);
//die('okSev');

//        self::checkAction("playCard");
		
//		self::trace("bmc: discardWeight<div>");
//		self::trace($discardWeight);
//		self::trace("</div>");
		
        // XXX check rules here
			$currentCard = $this->cards->getCard($card_id);
//      self::trace("bmc: currentCard: <div>");
//		self::trace($currentCard);
//		self::trace("</div>");

        // And notify
			self::notifyAllPlayers(
				'playCard',
				clienttranslate('${player_name} plays ${value_displayed} ${color_displayed}'),
				array (
					'i18n' => array ('color_displayed',
									 'value_displayed' ),
					'card_id' => $card_id,
					'player_id' => $thisPlayerId,
					'player_name' => self::getActivePlayerName(),
					'value' => $currentCard ['type_arg'],
					'value_displayed' => $this->values_label [$currentCard ['type_arg']],
					'color' => $currentCard ['type'],
					'color_displayed' => $this->colors [$currentCard ['type']] ['name']
				)
			);
		}
		// Next state
        $this->gamestate->nextState('playCard');
    }

    function drawCard($card_id)
	{
		self::trace("bmc: !! drawCard!!");
        self::checkAction("drawCard");
        $player_id = self::getActivePlayerId();

        $this->cards->moveCard($card_id, 'myhand', $player_id);

		self::trace($card_id);

        $currentCard = $this->cards->getCard($card_id);

        // And notify
        self::notifyAllPlayers('drawCard',
			clienttranslate('${player_name} draws a card.'),
			array (
				'i18n' => array ('color_displayed',
								 'value_displayed' ),
				'card_id' => $card_id,
				'player_id' => $player_id,
				'player_name' => self::getActivePlayerName(),
				'value' => $currentCard ['type_arg'],
				'value_displayed' => $this->values_label [$currentCard ['type_arg']],
				'color' => $currentCard ['type'],
				'color_displayed' => $this->colors [$currentCard ['type']] ['name']
			)
		);

// CHECK HOW THINGS WORK:
//      //self::setGameStateInitialValue('currentHandType', 1);
//		self::incGameStateValue( 'currentHandType', 6 );
//		//self::setGameStateValue( 'currentHandType', 5 ); // 0 is 2 Sets
		
		// Next State
        $this->gamestate->nextState('playCard');
	}

    /*
    
    Example:

    function playCard( $card_id )
    {
        // Check that this is the player's turn and that it is a "possible action" at this game state (see states.inc.php)
        self::checkAction( 'playCard' ); 
        
        $player_id = self::getActivePlayerId();

        throw new BgaUserException(self::_("Not implemented: ") . "$player_id plays $card_id");
        
        // Add your game logic to play a card there 
        ...
        
        // Notify all players about the card played
        self::notifyAllPlayers( "cardPlayed", clienttranslate( '${player_name} plays ${card_name}' ), array(
            'player_id' => $player_id,
            'player_name' => self::getActivePlayerName(),
            'card_name' => $card_name,
            'card_id' => $card_id
        ) );
          
    }
    
    */

    
//////////////////////////////////////////////////////////////////////////////
//////////// Game state arguments
////////////

    /*
        Here, you can create methods defined as "game state arguments" (see "args" property in states.inc.php).
        These methods function is to return some additional information that is specific to the current
        game state.
    */


//TODO: I messed up the states and transitions somehow, so it wont go through always playing cards.


    function stPassThrough() {
        // New trick: active the player who wins the last trick, or the player who own the club-2 card
        // Reset trick color to 0 (= no color)
        self::trace("bmc: stPassThrough");
        $this->gamestate->nextState("");
    }

    function stNewHand() {
		self::debug("bmc: !!stNewHand!!");
		
        // Take back all cards (from any location => null) to deck
        $this->cards->moveAllCardsInLocation(null, "deck");

		$bob = $this->cards->countCardInLocation( 'deck' );
		
		self::debug("<div>");
		self::debug($bob);
		self::debug("</div>");
		//die("bmc: BAAAAA");
		
		// Make sure the deck is the same size!
		/*
		if( $this->cards->countCardInLocation( 'deck' ) != 54 ) {
			var_dump("<div>");
			var_dump(implode(",", $this->cards->countCardInLocation( 'deck' )));
			var_dump("</div>");
			self::trace("<div>bmc: DECK SIZE NOT RIGHT!</div>");
		}
		*/

        // Shuffle deck
        $this->cards->shuffle('deck');

        // Deal some cards to each players

        $players = self::loadPlayersBasicInfos();
		
		// Deal 11 cards to each player
		// Put 1 card in the discard pile
		// Put the rest into the draw deck
		// Notify players of the situation
		
		foreach ( $players as $player_id => $player ) {
			$this->cards->pickCards( 2, 'deck', $player_id );
		}
		
		// Put 1 card from the deck into the discard pile and give it a starting weight of 100
		$this->cards->moveCard( $this->cards->getCardOnTop ( 'deck' )[ 'id' ], 'discardPile', 100); 
		
		// The rest of the cards are in 'deck'
		
		// TODO: This returns all the cards in the deck. Instead it should show
		//   show those cards as type 1 (red backs).
		
		//Notify all players of their cards plus the deck and the discard pile
		foreach ( $players as $player_id => $player ) {
			self::notifyPlayer(
				$player_id,
				'newHand',
				clienttranslate('Dealing cards'),
				array(
					'hand' => $this->cards->getPlayerHand($player_id),
					'deck' => array_keys($this->cards->getCardsInLocation('deck')),
					'discardPile' => $this->cards->getCardsInLocation('discardPile')
				)
			);
		}

/*		
var_dump("<div>bmc:");
var_dump($players);
var_dump("</div>");

		$dbg1st = 0;
        foreach ( $players as $player_id => $player ) {
			if ($dbg1st == 0) {
				$dbg1st = 1;
				$cards = $this->cards->pickCards( 5, 'deck', $player_id );
			} else {
				$cards = $this->cards->pickCards( 1, 'deck', $player_id );
			}
        } 

		// Put 1 card from the deck into the discard pile and give it a starting weight of 100
		$this->cards->moveCard( $this->cards->getCardOnTop ( 'deck' )[ 'id' ], 'discard', 100); 

		$GSV = self::getGameStateValue('currentHandType');
		self::trace("bmc: GSV");
		self::trace($GSV);
var_dump("<div>bmc:");
var_dump(implode ("{", $cards));
var_dump("</div>");
die("bmc:OK Sev");
*/
		// Notify players
//        self::notifyAllPlayers( "newHand", '', $this->cards) );
//        foreach ( $players as $player_id => $player ) {
//			self::notif	$cards = $this->cards->pickCards( 5, 'deck', $player_id );
//				$cards = $this->cards->pickCards( 1, 'deck', $player_id );
//        } 

		// Go to the next game state
        $this->gamestate->nextState("");	
    }

    function stNextPlayer() {
		self::trace("bmc: !!stNextPlayer!!");
		$countCardsByLocation = count($this->cards->countCardsByLocationArgs( 'hand' ));
		$playersNumber = self::getPlayersNumber();

		if ($countCardsByLocation != $playersNumber) {
			// Someone has gone out
			$this->gamestate->nextState("endHand");
		} else {
			// Next player can draw and play etc...
			$current_player_id = self::getCurrentPlayerId();    // !! We must only return informations visible by this player !!
			$player_id = self::activeNextPlayer();
			$activePlayerId = self::getCurrentPlayerId();
			$this->gamestate->nextState('nextPlayer');			
		}
	}

/*		
		if ($this->cards->countCardInLocation('hand') == 0) {
			// End of the hand
			$this->gamestate->nextState("endHand");
		} else {
			// Next player can draw and play etc...
			$current_player_id = self::getCurrentPlayerId();    // !! We must only return informations visible by this player !!
			$player_id = self::activeNextPlayer();
			$activePlayerId = self::getCurrentPlayerId();
			$this->gamestate->nextState('nextPlayer');
		}		
*/

/* This next used to be part of stNextPlayer

//		self::setGameStateInitialValue('currentHandType', 17);

        // Active next player OR end the trick and go to the next trick OR end the hand
        if ($this->cards->countCardInLocation('cardsontable') == 4) {
            // This is the end of the trick
            $cards_on_table = $this->cards->getCardsInLocation('cardsontable');
            $best_value = 0;
            $best_value_player_id = null;
            $currentTrickColor = self::getGameStateValue('trickColor');
            foreach ( $cards_on_table as $card ) {
                // Note: type = card color
                if ($card ['type'] == $currentTrickColor) {
                    if ($best_value_player_id === null || $card ['type_arg'] > $best_value) {
                        $best_value_player_id = $card ['location_arg']; // Note: location_arg = player who played this card on table
                        $best_value = $card ['type_arg']; // Note: type_arg = value of the card
                    }
                }
            }
            
            // Active this player => he's the one who starts the next trick
            $this->gamestate->changeActivePlayer( $best_value_player_id );
            
            // Move all cards to "cardswon" of the given player
            $this->cards->moveAllCardsInLocation('cardsontable', 'cardswon', null, $best_value_player_id);
 
            // Notify
            // Note: we use 2 notifications here in order we can pause the display during the first notification
            //  before we move all cards to the winner (during the second)
            $players = self::loadPlayersBasicInfos();
            self::notifyAllPlayers( 'trickWin', clienttranslate('${player_name} wins the trick'), array(
                'player_id' => $best_value_player_id,
                'player_name' => $players[ $best_value_player_id ]['player_name']
            ) );            
			
			// TODO: Can get rid of this next function
            self::notifyAllPlayers( 'giveAllCardsToPlayer','', array(
                'player_id' => $best_value_player_id
            ) );
        
            if ($this->cards->countCardInLocation('hand') == 0) {
                // End of the hand
                $this->gamestate->nextState("endHand");
            } else {
                // End of the trick
                $this->gamestate->nextState("nextTrick");
            }
        } else {
            // Standard case (not the end of the trick)
            // => just active the next player
            $player_id = self::activeNextPlayer();
            self::giveExtraTime($player_id);
            $this->gamestate->nextState('nextPlayer');
        }
    }
*/

    function stEndHand() {
		self::trace("bmc: !!stEndHand!!");
        // Count and score points, then end the game or go to the next hand.
        $players = self::loadPlayersBasicInfos();
        // Cards 2 - 9 are 5 points each
		// Cards 10, J, Q, K are 10 points each
		// Cards A are 15 points each
		// Cards Joker is 20 points each

		$player_to_points = array ();
		foreach ( $players as $player_id => $player) {
            $player_to_points [$player_id] = 0;
		}
		$cards = $this->cards->getCardsInLocation("hand");
		
		foreach ( $cards as $card ) {
			$player_id = $card ['location_arg'];
			if ($card['type'] >= 1 and $card['type'] <= 4) { // If non-Joker
				switch ( true ) {
					case ($card['type_arg'] >= 2 and $card['type_arg'] <= 9 ): // 5 points
						self::trace("bmc: 2-9");
						$player_to_points [$player_id] += 5;
						break;
					case ($card['type_arg'] >= 10 and $card['type_arg'] <= 13 ): // 10 points
						self::trace("bmc: 10,J,Q,K");
						$player_to_points [$player_id] += 10;
						break;
					case ($card['type_arg'] === 1 ): // 15 points	
						self::trace("bmc: Ace");
						$player_to_points [$player_id] += 15;
						break;
				}
			} else { // It must be a joker, 20 points
				self::trace("bmc: Joker");
				$player_to_points [$player_id] += 20;
			}
		}
//var_dump("bmc: TALLY!");
//var_dump($player_to_points);
//var_dump($cards);

        // Apply scores to player
        foreach ( $player_to_points as $player_id => $points ) {
            if ($points != 0) {
                $sql = "UPDATE player SET player_score=player_score-$points  WHERE player_id='$player_id'";
                self::DbQuery($sql);
                $heart_number = $player_to_points [$player_id];
                self::notifyAllPlayers("points", clienttranslate('${player_name} gets ${nbr} points'), array (
                        'player_id' => $player_id,'player_name' => $players [$player_id] ['player_name'],
                        'nbr' => $heart_number ));
            } else {
                // No point lost (just notify)
                self::notifyAllPlayers("points", clienttranslate('${player_name} did not get any points'), array (
                        'player_id' => $player_id,'player_name' => $players [$player_id] ['player_name'] ));
            }
        }
        $newScores = self::getCollectionFromDb("SELECT player_id, player_score FROM player", true );
        self::notifyAllPlayers( "newScores", '', array( 'newScores' => $newScores ) );

        // Next hand target
		self::incGameStateValue( 'currentHandType', 1 );

		// Notify players to go to the next target hand
		
//		TODO: HERE INSTEAD OF GAMEDATAS, shuffle and deal, SEND IN EACH PLAYERS HAND, THE DISCARD PILE AND THE DECK SIZE.
        //self::notifyAllPlayers( "newHand", '', PUTTHREEARGSHERE???) );

        ///// Test if this is the end of the game
		$currentHandType = $this->getGameStateValue( 'currentHandType' );
		if ($currentHandType == count($this->handTypes)) {
			$this->gamestate->nextState("endGame");
		} else {
			$this->gamestate->nextState("newHand");
		}
/*
        foreach ( $newScores as $player_id => $score ) {
            if ($score >= 100) {
                // Trigger the end of the game !
                $this->gamestate->nextState("endGame");
                return;
            }
        }
        $this->gamestate->nextState("nextHand");
*/
    }

    /*
    
    Example for game state "MyGameState":
    
    function argMyGameState()
    {
        // Get some values from the current game situation in database...
    
        // return values:
        return array(
            'variable1' => $value1,
            'variable2' => $value2,
            ...
        );
    }    
    */

//////////////////////////////////////////////////////////////////////////////
//////////// Game state actions
////////////

    /*
        Here, you can create methods defined as "game state actions" (see "action" property in states.inc.php).
        The action method of state X is called everytime the current game state is set to X.
    */
    
    /*
    
    Example for game state "MyGameState":

    function stMyGameState()
    {
        // Do some stuff ...
        
        // (very often) go to another gamestate
        $this->gamestate->nextState( 'some_gamestate_transition' );
    }    
    */

//////////////////////////////////////////////////////////////////////////////
//////////// Zombie
////////////

    /*
        zombieTurn:
        
        This method is called each time it is the turn of a player who has quit the game (= "zombie" player).
        You can do whatever you want in order to make sure the turn of this player ends appropriately
        (ex: pass).
        
        Important: your zombie code will be called when the player leaves the game. This action is triggered
        from the main site and propagated to the gameserver from a server, not from a browser.
        As a consequence, there is no current player associated to this action. In your zombieTurn function,
        you must _never_ use getCurrentPlayerId() or getCurrentPlayerName(), otherwise it will fail with a "Not logged" error message. 
    */

    function zombieTurn( $state, $active_player )
    {
    	$statename = $state['name'];
    	
        if ($state['type'] === "activeplayer") {
            switch ($statename) {
                default:
                    $this->gamestate->nextState( "zombiePass" );
                	break;
            }

            return;
        }

        if ($state['type'] === "multipleactiveplayer") {
            // Make sure player is in a non blocking status for role turn
            $this->gamestate->setPlayerNonMultiactive( $active_player, '' );
            
            return;
        }

        throw new feException( "Zombie mode not supported at this game state: ".$statename );
    }
    
///////////////////////////////////////////////////////////////////////////////////:
////////// DB upgrade
//////////

    /*
        upgradeTableDb:
        
        You don't have to care about this until your game has been published on BGA.
        Once your game is on BGA, this method is called everytime the system detects a game running with your old
        Database scheme.
        In this case, if you change your Database scheme, you just have to apply the needed changes in order to
        update the game database and allow the game to continue to run with your new version.
    
    */
    
    function upgradeTableDb( $from_version )
    {
        // $from_version is the current version of this game database, in numerical form.
        // For example, if the game was running with a release of your game named "140430-1345",
        // $from_version is equal to 1404301345
        
        // Example:
//        if( $from_version <= 1404301345 )
//        {
//            // ! important ! Use DBPREFIX_<table_name> for all tables
//
//            $sql = "ALTER TABLE DBPREFIX_xxxxxxx ....";
//            self::applyDbUpgradeToAllDB( $sql );
//        }
//        if( $from_version <= 1405061421 )
//        {
//            // ! important ! Use DBPREFIX_<table_name> for all tables
//
//            $sql = "CREATE TABLE DBPREFIX_xxxxxxx ....";
//            self::applyDbUpgradeToAllDB( $sql );
//        }
//        // Please add your future database scheme changes here
//
//

    }    
}

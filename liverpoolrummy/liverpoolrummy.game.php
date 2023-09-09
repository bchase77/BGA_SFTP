<?php
 /**
  *------
  * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
  * LiverpoolRummy implementation : © Bryan Chase <bryanchase@yahoo.com>
  * 
  * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
  * See http://en.boardgamearena.com/#!doc/Studio for more information.
  * 
  * MIT License
  * 
  * Copyright (c) 2020 Bryan Chase
  * 
  * Permission is hereby granted, free of charge, to any person obtaining a copy
  * of this software and associated documentation files (the "Software"), to deal
  * in the Software without restriction, including without limitation the rights
  * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
  * copies of the Software, and to permit persons to whom the Software is
  * furnished to do so, subject to the following conditions:
  * 
  * The above copyright notice and this permission notice shall be included in all
  * copies or substantial portions of the Software.
  * 
  * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
  * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
  * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
  * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
  * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
  * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
  * SOFTWARE.
  * 
  * -----
  * 
  * liverpoolrummy.game.php
  *
  * This is the main file for your game logic.
  *
  * In this PHP file, you are going to defines the rules of the game.
  *
  */

require_once( APP_GAMEMODULE_PATH.'module/table/table.game.php' );


class LiverpoolRummy extends Table
{
	function __construct( )
	{
        // Your global variables labels:
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
		  // To track buyers realtime, instead of using the database which can lock, use a gamestate. Each player gets 1 variable.


            "currentHandType" => 10,
//			"area_A_target" => 11,
//			"area_B_target" => 12,
//			"area_C_target" => 13,
			"forJokerCard_id" => 20,
			"forJokerBoardArea" => 21,
			"forJokerBoardPlayer" => 22,
			"forJokerTheJoker_id" => 23,
			"forJokerPlayerID" => 24,
			"activeTurnPlayer_id" => 25,
			"previous_player_id" => 26,
			"drawSourceValue" => 27,
			"dealer" => 28,
			"discardWeightHistory" => 29,
			"discardSize" => 30,
			"shuffleCount" => 31,
			"skipFirstDeal" => 32,
			"findBuyerFailsafe" => 33,
			"numberOfDecks" => 100,
			"gameLengthOption" => 102,
			"numberOfJokers" => 104,
			"numberOfBuys" => 105,
			"alwaysDeal11" => 106,
			"outReason" => 107,
			"enableWishList" => 108,
			"allowJokerSwapping" => 109,
			"buyer0WantsGS" => 110,
			"buyer1WantsGS" => 111,
			"buyer2WantsGS" => 112,
			"buyer3WantsGS" => 113,
			"buyer4WantsGS" => 114,
			"buyer5WantsGS" => 115,
			"buyer6WantsGS" => 116,
			"buyer7WantsGS" => 117,
			"buyer8WantsGS" => 118,
			"buyer9WantsGS" => 119,
			"buyer10WantsGS" => 120,
			"buyer11WantsGS" => 121,
			"buyer12WantsGS" => 122,
			"buyer13WantsGS" => 123,
			"buyer14WantsGS" => 124,
			"buyer15WantsGS" => 125,
			"liverpoolExists" => 126,
			"playerFindingLP" => 127,
			"playerInterrupted" => 128,
			"liverpoolFoundYN" => 129,

//
			"buyer0CountGS" => 130,
			"buyer1CountGS" => 131,
			"buyer2CountGS" => 132,
			"buyer3CountGS" => 133,
			"buyer4CountGS" => 134,
			"buyer5CountGS" => 135,
			"buyer6CountGS" => 136,
			"buyer7CountGS" => 137,
			"buyer8CountGS" => 138,
			"buyer9CountGS" => 139,
			"buyer10CountGS" => 140,
			"buyer11CountGS" => 141,
			"buyer12CountGS" => 142,
			"buyer13CountGS" => 143,
			"buyer14CountGS" => 144,
			"buyer15CountGS" => 145,
			"countDeck" => 146,
			"countDiscardPile" => 147,
			"theBuyer" => 148,
			"isBuyingAllowed" => 149 // 0 == false; 1 == true

        ) );
	
        $this->cards = self::getNew( "module.common.deck" );
        $this->cards->init( "card" );
	}
	
    protected function getGameName( )
    {
		// Used for translations and stuff. Please do not modify.
        return "liverpoolrummy";
    }	

    /*
        setupNewGame:
        
        This method is called only once, when a new game is launched.
        In this method, you must setup the game according to the game rules, so that
        the game is ready to be played.
    */
    protected function setupNewGame( $players, $options = array() )
    {
//		self::trace("[bmc] !!setupNewGame!!"); // Doesn't appear in log!

        // Don't do a lot here since server side hasn't been set up yet and so it's hard to debug.
		// Set the colors of the players with HTML color code
        // The default below is red/green/blue/orange/brown
        // The number of colors defined here must correspond to the max number of players allowed for game
        $gameinfos = self::getGameinfos();
        $default_colors = $gameinfos['player_colors'];
		
		$this->options = $options;

        // Create players
        // Note: if you added some extra field on "player" table in the database (dbmodel.sql), you can initialize it there.
        $sql = "INSERT INTO player (player_id, player_color, player_canal, player_name, player_avatar) VALUES ";
        $values = array();
		
        foreach( $players as $player_id => $player )
        {
            $color = array_shift( $default_colors );
            $values[] = "('".$player_id."','$color','".$player['player_canal']."','".addslashes( $player[ 'player_name' ] )."','".addslashes( $player['player_avatar'] )."')";
        }
        $sql .= implode( $values, ',' );
        self::DbQuery( $sql );

        self::reattributeColorsBasedOnPreferences( $players, $gameinfos['player_colors'] );
        self::reloadPlayersBasicInfos();

        self::setGameStateInitialValue( 'currentHandType', 0 );

        self::setGameStateInitialValue( 'liverpoolFoundYN', 0 ); // 0 = false; 1 = true


		$currentHandType = $this->getGameStateValue( 'currentHandType' );
		
		//self::dump("[bmc] handTypes(line145):", $this->handTypes[$currentHandType]);

		self::setGameLength();

		// Show the wishlist to players if the gamestate was set for it
		if ( $this->getGameStateValue( 'enableWishList' ) == 1 ) { // 0 == No. 1 == Yes.
			// Not sure what to do. I wanted to set the style to display or not but that's a JS thing, not PHP.
		}

        // Activate first player (which is in general a good idea :) )
        $player_id = $this->activeNextPlayer();

		self::setGameStateInitialValue( 'drawSourceValue', 2 ); // 0 = deck, 1 = discardPile. This should be set 
		// every time drawCard is called but including it here for completeness.
		//self::dump("[bmc] currentTurnPlayer_id:", $currentTurnPlayer_id );

		self::setGameStateInitialValue( 'isBuyingAllowed', 1 ); // 0 == false; 1 == true

		$activePlayerId = $this->getActivePlayerId();
//		self::dump("[bmc] activeTurnPlayer_id:", $activePlayerId );

		// Store which player's turn it really is for access during the multipleactiveplayer state.
		self::setGameStateInitialValue( 'activeTurnPlayer_id', $activePlayerId );
		
		$dealer = $this->getPlayerBefore( $activePlayerId );
		
		self::setGameStateInitialValue( 'dealer', $dealer );

		// Clear out the BUY counters for all players
		self::clearPlayersBuyCount();

		self::setGameStateInitialValue( 'discardWeightHistory', 300 ); // Start higher than any game will have qty of cards

        self::setGameStateInitialValue( 'skipFirstDeal', 1 ); // 0 is false. 1 is true. It seems to want integers.

		$this->waiting = false; // This keeps the state machine from getting out of sync by requiring
		
		// Set up failsafe in case the recursion doesn't work
		self::setGameStateInitialValue( 'findBuyerFailsafe', 0 );

		// Probably not necessary, but all variables should at least be defined before used.
		self::setGameStateInitialValue( 'outReason', 2 ); // 0 = Someone out, 1 = Overshuffled, 2 = AllCardsPlayed
		
		// all players to select BUY / NOT BUY before a player discards a 2nd time.

		self::setGameStateInitialValue( 'liverpoolExists', 0 ); // 0 = not exist; 1 = exist
		self::setGameStateInitialValue( 'playerFindingLP', 0 ); // 0 = no player

        /************ Start the game initialization *****/

        // Init global values with their initial values
        //self::setGameStateInitialValue( 'my_first_global_variable', 0 );
        
        // Init game statistics
        // (note: statistics used in this file must be defined in your stats.inc.php file)
        //self::initStat( 'table', 'table_teststat1', 0 );    // Init a table statistics
        //self::initStat( 'player', 'player_teststat1', 0 );  // Init a player statistics (for all players)
        self::initStat( 'table',  'turns_number', 0 );
        self::initStat( 'player', 'buys_number', 0 );
        self::initStat( 'player', 'jokers_number', 0 );

        /************ End of the game initialization *****/
    }


	// PHP program to search for multiple
	// key=>value pairs in array
  
	protected function searchForCard($array, $search_list) {
  
		// Create the result array
		//$result = array();
		$result = "";
		//self::dump( "[bmc] search_list : ", $search_list );
	  
		// Iterate over each array element
		foreach ($array as $key => $value) {
			// echo 'key: ' . $key . '<br>';
			// echo 'value/rollNo: ' . $value['rollNo'] . '<br>';
			// echo 'value/name: ' . $value['name'] . '<br>';
			// echo 'value/section: ' . $value['section'] . '<br>';
			// echo 'value/id: ' . $value['id'] . '<br>';
			// echo 'value/type: ' . $value['type'] . '<br>';
			// echo 'value/type_arg: ' . $value['type_arg'] . '<br>';
		   // self::dump( "[bmc] Looking at id      : ", $value['id'] );
//		    self::dump( "[bmc] Looking at type    : ", $value['type'] );
		    // self::dump( "[bmc] Looking at type_arg: ", $value['type_arg'] );
			
			// Iterate over each search condition
			foreach ($search_list as $k => $v) {
				// echo 'kkkkkkkkkkkkkkkkkkkkkkkk : ' . $k . '<br>';
				// echo 'vvvvvvvvvvvvvvvvvvvvvvvv : ' . $v . '<br>';
				//echo 'vvvvvvvvvvvvvvvvv/name   : ' . $v['name'] . '<br>';
				//echo 'vvvvvvvvvvvvvvvvv/section: ' . $v['section'] . '<br>';
				// self::dump( "[bmc] Looking FOR type    : ", $v['type'] );
				// self::dump( "[bmc] Looking FOR type_arg: ", $v['type_arg'] );
				// self::dump( "[bmc] Looking FOR k : ", $k );
				// self::dump( "[bmc] Looking FOR v: ", $v );
				// self::dump( "[bmc] Looking FOR value: ", $value );
				if( isset( $value[$k] )) {
					//self::dump( "[bmc] Looking FOR value[k]: ", $value[$k] );
				}
				// If the array element does not meet
				// the search condition then continue
				// to the next element
				if (!isset($value[$k]) || $value[$k] != $v)
				{
					// self::trace("[bmc] !!NOT!!");
					// echo '.................................not the one above! ' . '<br>';
					// Skip two loops
					continue 2;
				} else {
					// self::trace("[bmc] !!FOUND PART KEEP LOOKING!!");
					// echo 'FOUND PART, KEEP LOOKING! ' . '<br>';
				}
			}
		  
			// Append array element's key to the result array
			//$result[] = $value;
			// echo '!!FOUND ONE-name!! ' . $value['name'] . '<br>';
			// echo '!!FOUND ONE-card!! ' . $value['id'] . '<br>';
		    //self::dump( "[bmc] FOUND ONE-card!! id: ", $value['id'] );
			return $value;
			//break; // Break out of the outer foreach since a card was found
		}
	  
		return $result; // Empty if nothing found
	}

	function stDeckSetup()
	{
		self::trace("[bmc] !!setupNewDeck!!");
        // The game is played with multiple standard 52-pack plus 2 jokers.
        // 2 decks for three to five players. 3 decks for more players. 

        // Variants (from Wikipedia):
        //   3 runs: Go down with no remaining cards in hand, no final discard (12 cards)
        //   First one to click BUY gets it, not the first in line
        //   Runs must include at least 3 non-wildcards in an original 4 card grouping.
        //   Sets must include at least 2 non-wildcards.
        //   Replace that other player's laid Joker from within a run (or a set)
        //   Discard must not fit into either their own or any other player's laid cards.
        //     (or else draw extra card and whoever called Liverpool can discard a card)
		
		// Create cards
        $cards = array ();

		$numberOfDecks = self::getGameStateValue( 'numberOfDecks' );
		//self::dump( "[bmc] numberOfDecks", $numberOfDecks );

// 'type' means 1=C; 2=S; 3=H;4=D

//        foreach ( $this->colors as $color_id => $color ) {
		for ($colors = 1; $colors <=4; $colors ++) {
			$color_id = $colors;
            // spade, heart, diamond, club
            foreach ( $this->values_label as $value => $type_arg ) {
                //  A, 2, 3, 4, ... K
				if ( !array_key_exists("51", $cards)) {
					$cards [] = array ('type' => $color_id, 'type_arg' => $value, 'nbr' => $numberOfDecks );
				}
            }
        }

//		self::trace("[bmc] !!makingCards!!");
		
        // Add jokers

		$optionNumJokers =  self::getGameStateValue( 'numberOfJokers' );
		if ( $optionNumJokers != 10) {
			$numJokerDecks = intdiv( $optionNumJokers, 2 );
		} else {
			$numJokerDecks = $numberOfDecks;
		}
		
        $jokers = array ();
		$color = 5;
		for ($value = 1; $value <= 2; $value ++) {
			array_push( $cards, array ('type' => $color, 'type_arg' => $value, 'nbr' => $numJokerDecks ));
		}
		
		// If number of jokers chosen is odd then add 1 more joker to the 
		if ( $optionNumJokers % 2 != 0 ) {
			array_push( $cards, array ('type' => $color, 'type_arg' => 1, 'nbr' => 1 ));
		}

        $this->cards->createCards( $cards, 'deck' );

		$allCardsDebug = $this->cards->getCardsInLocation( 'deck' );
		
		self::dump( "[bmc] AllCardsDebug: ", $allCardsDebug );

        self::setGameStateInitialValue( 'countDiscardPile', 1 );
        //self::setGameStateInitialValue( 'countDeck', $allCardsDebug );

		// Set to autoreshuffle discardPile into deck, but I don't think this worked
	
		$this->cards->autoreshuffle_custom = array('deck' => 'discardPile');

		// Go to the next game state
        $this->gamestate->nextState();	
	}
	
    /*
        getAllDatas: 
        
        Gather all informations about current game situation (visible by the current player).
        
        The method is called each time the game interface is displayed to a player, ie:
        _ when the game starts
        _ when a player refreshes the game page (F5)
    */
////
////
////
    protected function getAllDatas()
    {
		// This returns data to the JS code in gamedatas datastructure
		self::trace("[bmc] ENTER getAllDatas");

		/*
		//WISHLIST DATABASE START: Below this code adds some example cards to the wishlist database
		//  and successfully retrieves them, and then deletes all for 1 player:v

		$wishLists = array(
			0 => array(
				'player_id' => 2333744,
				'card_type' => '3', // Suit
				'card_type_arg' => '1' // Value
				),
			1 => array(
				'player_id' => 2333742,
				'card_type' => '2', // Suit
				'card_type_arg' => '2' // Value
				),
			2 => array(
				'player_id' => 2333744,
				'card_type' => '3', // Suit
				'card_type_arg' => '3' // Value
				),
			3 => array(
				'player_id' => 2333743,
				'card_type' => '3', // Suit
				'card_type_arg' => '4' // Value
				),
			4 => array(
				'player_id' => 2333742,
				'card_type' => '2', // Suit
				'card_type_arg' => '5' // Value
				),
			5 => array(
				'player_id' => 2333744,
				'card_type' => '2', // Suit
				'card_type_arg' => '6' // Value
				),
			6 => array(
				'player_id' => 2333743,
				'card_type' => '4', // Suit
				'card_type_arg' => '7' // Value
				)
			);

		self::dump("[bmc] wishLists:", $wishLists);

		$players = self::loadPlayersBasicInfos();

		foreach( $wishLists as $entry ) {
			self::dump("[bmc] wishList entry:", $entry);
				
			self::dump("[bmc] wishList SQL:", $entry['player_id']);

		    $sql = "INSERT INTO wishList (player_id, card_type, card_type_arg) VALUES ";
			$sql_values = "( " . $entry['player_id'] . "," . $entry['card_type'] . "," . $entry['card_type_arg'] . ")";
			
			self::DbQuery( $sql . $sql_values);
		}

		$sql = "SELECT id id, player_id, card_type, card_type_arg FROM wishList ";
		
		$result = self::getCollectionFromDb( $sql );

		self::dump("[bmc] wishList after SQL SELECT:", $result);

		//WISHLIST DATABASE END: Above this code adds 2 example cards to the wishlist database and successfully retrieves them:^

		// Delete all entries for the wishList for that player

		$player_id = 2333742;
		
	    $sql = "DELETE FROM wishList WHERE player_id = '";
		$sql_command = $player_id . "'";
		self::DbQuery( $sql . $sql_command );

		$sql = "SELECT id id, player_id, card_type, card_type_arg FROM wishList ";
		
		$result = self::getCollectionFromDb( $sql );

		self::dump("[bmc] wishList after SQL SELECT:", $result);
*/

		$dpCard = $this->cards->getCardsInLocation( 'discardPile' );
		
		if ( isset( reset( $dpCard )[ 'id' ])) {

			self::dump("[bmc] dpCard:", reset( $dpCard )[ 'id' ]);

			$currentCard = $this->cards->getCard( reset( $dpCard )[ 'id' ] );

			self::dump("[bmc] currentCardInDP:", $currentCard);

			if ( $currentCard[ 'type' ] == 5 ) {
				$value_displayed = 'Joker';
				$color_displayed = '';
			} else {
				$value_displayed = $this->values_label[ $currentCard[ 'type_arg' ]];
				$color_displayed = $this->colors[ $currentCard[ 'type' ]][ 'name' ];
			}


			// if ( $currentCard[ 'type' ] == 5 ) {
				// $value_displayed = self::_(' joker');
				// $color_displayed = '';
			// } else {
				// $value_displayed = self::_('The ') . self::_($this->values_label[ $currentCard[ 'type_arg' ]]) . self::_(' of ');
				// $color_displayed = self::_($this->colors[ $currentCard[ 'type' ]][ 'name' ] . 's');
			// }

			self::dump("[bmc] vd:", $value_displayed);
			self::dump("[bmc] cd:", $color_displayed);
			$dealer = 'bob';
			$handTarget = 'food';
		}

        $result = array();
    
        $current_player_id = self::getCurrentPlayerId();    // !! We must only return informations visible by this player !!
    
		$APL = $this->gamestate->getActivePlayerList();
		self::dump( "[bmc] APL:", $APL);
		
		$result['currentPlayerId'] = $current_player_id;
		$result['discardingPlayer_id'] = $this->getPlayerBefore( self::getActivePlayerId() );
		
		if ( $this->getGameStateValue( 'enableWishList' ) == 1 ) {
			$result['enableWishList'] = true;
		} else {
			$result['enableWishList'] = false;
		}
		
        // Get information about players
        // Note: you can retrieve some extra field you added for "player" table in "dbmodel.sql" if you need it.
		
        $sql = "SELECT player_id id, player_score score FROM player ";
        $result['players'] = self::getCollectionFromDb( $sql );

		$playersHand = $this->cards->getCardsInLocation( 'hand', $current_player_id );
		self::dump( "[bmc] playersHand:", $playersHand );

        // Return cards in player hand
        $result['hand'] = $playersHand;

		$result['deckIDs'] = array_keys($this->cards->getCardsInLocation( 'deck' ));

		$deckTopCard = $this->cards->getCardOnTop( 'deck' );
		$result['deckTopCard'] = $this->cards->getCardOnTop( 'deck' )[ 'id' ];
		
		$result['allHands'] = $cardsByLocation = $this->cards->countCardsByLocationArgs( 'hand' );
		
		self::dump( "[bmc] allHands:", $cardsByLocation = $this->cards->countCardsByLocationArgs( 'hand' ));

		$countCardsByLocation = $this->cards->countCardsByLocationArgs( 'hand' );
		$result['dbgcountA'] = $countCardsByLocation ;
		$result['dbgcountV'] = count( $countCardsByLocation );

		$playersNumber = self::getPlayersNumber();
		$result['dbgPlayersNumber'] = $playersNumber ;
		
		self::setGameLength();

		$result['handTypes']["Target"] = $this->handTypes; // Pull the description
		
		$buyers = self::getPlayerBuying();

		self::dump("[bmc] gamedatas buyers:", $buyers);

		$players = self::loadPlayersBasicInfos();
		
		// self::dump( "[bmc] players:", $players );

        $playerIDList = [];

		foreach ( $players as $playerIDOnly ) {
			$playerIDList[] = $playerIDOnly[ "player_id" ];
		}
		
		// self::dump( "[bmc] playerIDList:", $playerIDList );

        $playerGoneDown = self::getPlayerGoneDown(); // It's an array, one for each player.
		
		$buyCount = self::getPlayersBuyCount();
		
		self::dump("[bmc] gamedatas buyCount:",   $buyCount);

		$discardSize = count( $this->cards->countCardsByLocationArgs( 'discardPile' ));
		
		self::setGameStateValue( 'discardSize', $discardSize );
		
		$discardTopCard = $this->cards->getCardOnTop( 'discardPile' );
		$result['discardTopCard'] = $this->cards->getCardOnTop( 'discardPile' );
		
		$result['discardSize'] = $discardSize;

		foreach ( $players as $player_id => $player ) {
			$result[ 'downArea_A_' ][ $player_id ] = $this->cards->getCardsInLocation( 'playerDown_A' , $player_id );
			$result[ 'downArea_B_' ][ $player_id ] = $this->cards->getCardsInLocation( 'playerDown_B' , $player_id );
			$result[ 'downArea_C_' ][ $player_id ] = $this->cards->getCardsInLocation( 'playerDown_C' , $player_id );
			$result[ 'goneDown' ][ $player_id ] = $playerGoneDown[ $player_id ];		
			$result[ 'buyers' ][ $player_id ] = $buyers[ $player_id ];
			$result[ 'buyCount'][ $player_id ] = $buyCount[ $player_id ];
		}

		// Put the jokers on the top of the deck
		$cardsForDeck = array(
			0 => array(
				'type' => '5', // Suit
				'type_arg' => '1' // Value
				),
			1 => array(
				'type' => '5', // Suit
				'type_arg' => '2' // Value
				),
			2 => array(
				'type' => '5', // Suit
				'type_arg' => '1' // Value
				),
			3 => array(
				'type' => '5', // Suit
				'type_arg' => '2' // Value
				)
			);

		$jokerLoc = 100; // Higher numbers are closer to the top of the deck
		
		foreach ( $cardsForDeck as $cardToAdd ) {
			$cardsInDeck = $this->cards->getCardsInLocation( 'deck' );

			$presetHandCard = self::searchForCard( $cardsInDeck, $cardToAdd );
			//self::dump("[bmc] findJoker:", $presetHandCard );
			
			$jokerLoc = $jokerLoc - 1;
			//self::dump("[bmc] jokerLoc:", $jokerLoc );
		};
//		$allCardsDebug =  $this->cards->getCardsInLocation( 'deck' );
//		self::dump("[bmc] allCardsInDeck:", $allCardsDebug );


//		$allCardsDebug = $this->cards->getCardsInLocation( 'jokerPile' );
//		//self::dump("[bmc] allCardsInJokerPile:", $allCardsDebug );
		
		$result[ 'currentHandType' ] = self::getGameStateValue( 'currentHandType' );

		$result[ 'totalHandCount' ] = count( $this->handTypes );

		$result[ 'discardPile' ] = $this->cards->getCardsInLocation( 'discardPile' );
        
        // Cards played on the table
        $result['cardsontable'] = $this->cards->getCardsInLocation( 'cardsontable' );

		$debugCount = $this->cards->countCardsInLocations();
//		self::trace("[bmc] !!cardsinlocations!!");
		
		$activeTurnPlayer_id = $this->getGameStateValue( 'activeTurnPlayer_id' );
		
		$result[ 'activeTurnPlayer_id' ] = $activeTurnPlayer_id ;
  
//		self::dump("[bmc] activeTurnPlayer_id", $activeTurnPlayer_id );

		$playerOrder = self::getNextPlayerTable();
//		self::dump( "[bmc] playerOrder: ", $playerOrder );

		$result['playerOrderTrue'] = $playerOrder;

		// Just make sure it stuck!
		$activePlayerId = $this->getActivePlayerId();
//		self::dump( "[bmc] GETALLDATAS activePlayerId:", $activePlayerId );

		// Show State
		$state = $this->gamestate->state();
		self::dump("[bmc] GETALLDATAS state:", $state);
		
		$cardsInHd = $this->cards->getCardsInLocation( 'hand' );
		$cardsInDk = $this->cards->getCardsInLocation( 'deck' );
		$cardsInDp = $this->cards->getCardsInLocation( 'discardPile' );
		$cardsInBa = $this->cards->getCardsInLocation( 'playerDown_A' );
		$cardsInBb = $this->cards->getCardsInLocation( 'playerDown_B' );
		$cardsInBc = $this->cards->getCardsInLocation( 'playerDown_C' );

		self::dump("[bmc] cardsInHd:", $cardsInHd);
		// self::dump("[bmc] cardsInDk:", $cardsInDk);
		// self::dump("[bmc] cardsInDp:", $cardsInDp);
		// self::dump("[bmc] cardsInBa:", $cardsInBa);
		// self::dump("[bmc] cardsInBb:", $cardsInBb);
		// self::dump("[bmc] cardsInBc:", $cardsInBc);

		$numberOfDecks = self::getGameStateValue( 'numberOfDecks' );
		
		// self::dump( "[bmc] numberOfDecks", $numberOfDecks );
		
		$result[ 'options' ][ 'numberOfDecks' ] = $numberOfDecks;

		$currentHandType = $this->getGameStateValue( 'currentHandType' );
		self::dump( "[bmc] 711 currentHandType:", $currentHandType);
		
		// self::dump( "[bmc} count of handtypes:", count( $this->handTypes ));
		
		if ( $currentHandType != null ) {
			$result[ 'handTarget' ] = $this->handTypes[ $currentHandType ]["Target"];
		}
		
		$sql = "SELECT id id, card_type, card_type_arg FROM wishList WHERE player_id = '";
		$sql_command = $current_player_id . "'";
		
		// self::dump( "[bmc] sql:", $sql . $sql_command );

		$wishListAll = self::getCollectionFromDb( $sql . $sql_command );
		
		$result[ 'wishList' ] = self::getCollectionFromDb( $sql . $sql_command );
		// self::dump( "[bmc] wishListAll:", $wishListAll );
		
		self::trace("[bmc] EXIT getAllDatas");
        return $result;
    }
////////
////////
////////
	function presetHands( $players, $debug ) {
		self::trace("[bmc] ENTER presetHands");
		$testPlayerHandArray = [];

		$testPlayerHandArray[3] = array(
			13 => array(
				'type' => '3', // Suit
				'type_arg' => '12' // Value
				),
			14 => array(
				'type' => '3', // Suit
				'type_arg' => '13' // Value
				)
			);
		$testPlayerHandArray[1] = array(
			0 => array(
				'type' => '1', // Suit
				'type_arg' => '1' // Value
				),
			1 => array(
				'type' => '1', // Suit
				'type_arg' => '2' // Value
				),
			2 => array(
				'type' => '1', // Suit
				'type_arg' => '3' // Value
				),
			3 => array(
				'type' => '1', // Suit
				'type_arg' => '4' // Value
				),
			4 => array(
				'type' => '1', // Suit
				'type_arg' => '5' // Value
				),
			5 => array(
				'type' => '5', // Suit
				'type_arg' => '2' // Value
				),
			6 => array(
				'type' => '5', // Suit
				'type_arg' => '1' // Value
				),
			7 => array(
				'type' => '3', // Suit
				'type_arg' => '6' // Value
				),
			8 => array(
				'type' => '3', // Suit
				'type_arg' => '7' // Value
				),
			9 => array(
				'type' => '3', // Suit
				'type_arg' => '8' // Value
				),
			10 => array(
				'type' => '3', // Suit
				'type_arg' => '9' // Value
				),
			11 => array(
				'type' => '2', // Suit
				'type_arg' => '12' // Value
				),
			12 => array(
				'type' => '2', // Suit
				'type_arg' => '13' // Value
				),
			13 => array(
				'type' => '2', // Suit
				'type_arg' => '1' // Value
				),
			14 => array(
				'type' => '5', // Suit
				'type_arg' => '2' // Value
				),
			15 => array(
				'type' => '1', // Suit
				'type_arg' => '4' // Value
				),
			16 => array(
				'type' => '1', // Suit
				'type_arg' => '3' // Value
				),

			);
		$testPlayerHandArray[2] = array(
			0 => array(
				'type' => '2', // Suit
				'type_arg' => '1' // Value
				),
			1 => array(
				'type' => '2', // Suit
				'type_arg' => '1' // Value
				),
			2 => array(
				'type' => '2', // Suit
				'type_arg' => '1' // Value
				),
			3 => array(
				'type' => '2', // Suit
				'type_arg' => '1' // Value
				),
			4 => array(
				'type' => '1', // Suit
				'type_arg' => '1' // Value
				),
			5 => array(
				'type' => '3', // Suit
				'type_arg' => '1' // Value
				),
			6 => array(
				'type' => '4', // Suit
				'type_arg' => '1' // Value
				),
			7 => array(
				'type' => '3', // Suit
				'type_arg' => '1' // Value
				),
			8 => array(
				'type' => '3', // Suit
				'type_arg' => '7' // Value
				),
			9 => array(
				'type' => '3', // Suit
				'type_arg' => '7' // Value
				),
			10 => array(
				'type' => '3', // Suit
				'type_arg' => '7' // Value
				),
			11 => array(
				'type' => '2', // Suit
				'type_arg' => '7' // Value
				),
			12 => array(
				'type' => '2', // Suit
				'type_arg' => '13' // Value
				),
			13 => array(
				'type' => '2', // Suit
				'type_arg' => '1' // Value
				),
			14 => array(
				'type' => '4', // Suit
				'type_arg' => '11' // Value
				)
			);

		$testPlayerHandArray[0] = array(
			0 => array(
				'type' => '4', // Suit
				'type_arg' => '7' // Value
				),
			1 => array(
				'type' => '4', // Suit
				'type_arg' => '13' // Value
				),
			2 => array(
				'type' => '4', // Suit
				'type_arg' => '12' // Value
				),
			3 => array(
				'type' => '3', // Suit
				'type_arg' => '7' // Value
				),
			4 => array(
				'type' => '3', // Suit
				'type_arg' => '8' // Value
				),
			5 => array(
				'type' => '3', // Suit
				'type_arg' => '9' // Value
				),
			6 => array(
				'type' => '3', // Suit
				'type_arg' => '10' // Value
				),
			7 => array(
				'type' => '3', // Suit
				'type_arg' => '11' // Value
				),
			8 => array(
				'type' => '2', // Suit
				'type_arg' => '7' // Value
				),
			9 => array(
				'type' => '2', // Suit
				'type_arg' => '8' // Value
				),
			10 => array(
				'type' => '2', // Suit
				'type_arg' => '9' // Value
				),
/*
,
			14 => array(
				'type' => '1', // Suit
				'type_arg' => '7' // Value
				),
			15 => array(
				'type' => '2', // Suit
				'type_arg' => '8' // Value
				),
			16 => array(
				'type' => '1', // Suit
				'type_arg' => '13' // Value
				)
*/
/*,
			8 => array(
				'type' => '1', // Suit
				'type_arg' => '3' // Value
				),
			9 => array(
				'type' => '1', // Suit
				'type_arg' => '4' // Value
				),
			10 => array(
				'type' => '5', // Suit
				'type_arg' => '1' // Value
				),
			11 => array(
				'type' => '2', // Suit
				'type_arg' => '7' // Value
				),
			12 => array(
				'type' => '3', // Suit
				'type_arg' => '7' // Value
				),
			13 => array(
				'type' => '3', // Suit
				'type_arg' => '9' // Value
				),
			14 => array(
				'type' => '3', // Suit
				'type_arg' => '7' // Value
				)
*/
			);
/*
			0 => array(
				'type' => '3', // Suit
				'type_arg' => '7' // Value
				),
			1 => array(
				'type' => '1', // Suit
				'type_arg' => '1' // Value
				),
			2 => array(
				'type' => '1', // Suit
				'type_arg' => '2' // Value
				),
			3 => array(
				'type' => '1', // Suit
				'type_arg' => '3' // Value
				),
			4 => array(
				'type' => '1', // Suit
				'type_arg' => '4' // Value
				),
			5 => array(
				'type' => '1', // Suit
				'type_arg' => '5' // Value
				),
			6 => array(
				'type' => '1', // Suit
				'type_arg' => '6' // Value
				),
			7 => array(
				'type' => '3', // Suit
				'type_arg' => '13' // Value
				)
			);
		$testPlayerHandArray[1] = array(
			0 => array(
				'type' => '1', // Suit
				'type_arg' => '1' // Value
				),
			1 => array(
				'type' => '2', // Suit
				'type_arg' => '1' // Value
				),
			2 => array(
				'type' => '3', // Suit
				'type_arg' => '1' // Value
				),
			3 => array(
				'type' => '4', // Suit
				'type_arg' => '5' // Value
				),
			4 => array(
				'type' => '1', // Suit
				'type_arg' => '8' // Value
				),
			5 => array(
				'type' => '2', // Suit
				'type_arg' => '8' // Value
				),
			6 => array(
				'type' => '3', // Suit
				'type_arg' => '8' // Value
				)
			);
		$testPlayerHandArray[2] = array(
			0 => array(
				'type' => '1', // Suit
				'type_arg' => '7' // Value
				),
			1 => array(
				'type' => '2', // Suit
				'type_arg' => '7' // Value
				),
			2 => array(
				'type' => '3', // Suit
				'type_arg' => '7' // Value
				),
			3 => array(
				'type' => '4', // Suit
				'type_arg' => '8' // Value
				),
			4 => array(
				'type' => '1', // Suit
				'type_arg' => '6' // Value
				),
			5 => array(
				'type' => '2', // Suit
				'type_arg' => '6' // Value
				),
			6 => array(
				'type' => '3', // Suit
				'type_arg' => '6' // Value
				)
			);
		$testPlayerHandArray[3] = array(
			0 => array(
				'type' => '1', // Suit
				'type_arg' => '3' // Value
				),
			1 => array(
				'type' => '2', // Suit
				'type_arg' => '3' // Value
				),
			2 => array(
				'type' => '3', // Suit
				'type_arg' => '3' // Value
				),
			3 => array(
				'type' => '4', // Suit
				'type_arg' => '9' // Value
				),
			4 => array(
				'type' => '1', // Suit
				'type_arg' => '2' // Value
				),
			5 => array(
				'type' => '2', // Suit
				'type_arg' => '2' // Value
				),
			6 => array(
				'type' => '3', // Suit
				'type_arg' => '2' // Value
				)
			);
*/
/*
		$testPlayerHandArray[0] = array(
			0 => array(
				'type' => '3', // Suit
				'type_arg' => '7' // Value
				),
			1 => array(
				'type' => '1', // Suit
				'type_arg' => '1' // Value
				),
			2 => array(
				'type' => '1', // Suit
				'type_arg' => '2' // Value
				),
			3 => array(
				'type' => '1', // Suit
				'type_arg' => '3' // Value
				),
			4 => array(
				'type' => '1', // Suit
				'type_arg' => '4' // Value
				),
			5 => array(
				'type' => '1', // Suit
				'type_arg' => '5' // Value
				),
			6 => array(
				'type' => '1', // Suit
				'type_arg' => '6' // Value
				),
			7 => array(
				'type' => '1', // Suit
				'type_arg' => '7' // Value
				),
			8 => array(
				'type' => '1', // Suit
				'type_arg' => '8' // Value
				),
			9 => array(
				'type' => '1', // Suit
				'type_arg' => '5' // Value
				),
			10 => array(
				'type' => '1', // Suit
				'type_arg' => '10' // Value
				),
			11 => array(
				'type' => '1', // Suit
				'type_arg' => '11' // Value
				),
			12 => array(
				'type' => '1', // Suit
				'type_arg' => '12' // Value
				),
			13 => array(
				'type' => '1', // Suit
				'type_arg' => '13' // Value
				),
			14 => array(
				'type' => '3', // Suit
				'type_arg' => '13' // Value
				)
			);
		$testPlayerHandArray[1] = array(
			0 => array(
				'type' => '4', // Suit
				'type_arg' => '5' // Value
				),
			1 => array(
				'type' => '1', // Suit
				'type_arg' => '1' // Value
				),
			2 => array(
				'type' => '1', // Suit
				'type_arg' => '2' // Value
				),
			3 => array(
				'type' => '1', // Suit
				'type_arg' => '3' // Value
				),
			4 => array(
				'type' => '1', // Suit
				'type_arg' => '4' // Value
				),
			5 => array(
				'type' => '1', // Suit
				'type_arg' => '9' // Value
				),
			6 => array(
				'type' => '1', // Suit
				'type_arg' => '6' // Value
				),
			7 => array(
				'type' => '1', // Suit
				'type_arg' => '7' // Value
				),
			8 => array(
				'type' => '1', // Suit
				'type_arg' => '8' // Value
				),
			9 => array(
				'type' => '1', // Suit
				'type_arg' => '9' // Value
				),
			10 => array(
				'type' => '1', // Suit
				'type_arg' => '10' // Value
				),
			11 => array(
				'type' => '1', // Suit
				'type_arg' => '11' // Value
				),
			12 => array(
				'type' => '1', // Suit
				'type_arg' => '12' // Value
				),
			13 => array(
				'type' => '1', // Suit
				'type_arg' => '13' // Value
				),
			14 => array(
				'type' => '3', // Suit
				'type_arg' => '12' // Value
				)
			);
		$testPlayerHandArray[2] = array(
			0 => array(
				'type' => '3', // Suit
				'type_arg' => '4' // Value
				),
			1 => array(
				'type' => '2', // Suit
				'type_arg' => '1' // Value
				),
			2 => array(
				'type' => '2', // Suit
				'type_arg' => '2' // Value
				),
			3 => array(
				'type' => '2', // Suit
				'type_arg' => '3' // Value
				),
			4 => array(
				'type' => '2', // Suit
				'type_arg' => '4' // Value
				),
			5 => array(
				'type' => '2', // Suit
				'type_arg' => '5' // Value
				),
			6 => array(
				'type' => '2', // Suit
				'type_arg' => '6' // Value
				),
			7 => array(
				'type' => '2', // Suit
				'type_arg' => '7' // Value
				),
			8 => array(
				'type' => '2', // Suit
				'type_arg' => '8' // Value
				),
			9 => array(
				'type' => '2', // Suit
				'type_arg' => '5' // Value
				),
			10 => array(
				'type' => '2', // Suit
				'type_arg' => '10' // Value
				),
			11 => array(
				'type' => '2', // Suit
				'type_arg' => '11' // Value
				),
			12 => array(
				'type' => '2', // Suit
				'type_arg' => '12' // Value
				),
			13 => array(
				'type' => '2', // Suit
				'type_arg' => '13' // Value
				),
			14 => array(
				'type' => '4', // Suit
				'type_arg' => '11' // Value
				)
			);
		$testPlayerHandArray[3] = array(
			0 => array(
				'type' => '4', // Suit
				'type_arg' => '3' // Value
				),
			1 => array(
				'type' => '2', // Suit
				'type_arg' => '1' // Value
				),
			2 => array(
				'type' => '2', // Suit
				'type_arg' => '2' // Value
				),
			3 => array(
				'type' => '2', // Suit
				'type_arg' => '3' // Value
				),
			4 => array(
				'type' => '2', // Suit
				'type_arg' => '4' // Value
				),
			5 => array(
				'type' => '2', // Suit
				'type_arg' => '9' // Value
				),
			6 => array(
				'type' => '2', // Suit
				'type_arg' => '6' // Value
				),
			7 => array(
				'type' => '2', // Suit
				'type_arg' => '7' // Value
				),
			8 => array(
				'type' => '2', // Suit
				'type_arg' => '8' // Value
				),
			9 => array(
				'type' => '2', // Suit
				'type_arg' => '9' // Value
				),
			10 => array(
				'type' => '2', // Suit
				'type_arg' => '10' // Value
				),
			11 => array(
				'type' => '2', // Suit
				'type_arg' => '11' // Value
				),
			12 => array(
				'type' => '2', // Suit
				'type_arg' => '12' // Value
				),
			13 => array(
				'type' => '2', // Suit
				'type_arg' => '13' // Value
				),
			14 => array(
				'type' => '4', // Suit
				'type_arg' => '10' // Value
				)
			);
*/

		$cardsInDeck = $this->cards->getCardsInLocation( 'deck' );
//		$debug_cards = $this->cards->getCardsInLocation("hand");
		self::dump("[bmc] Cards In Deck:", $cardsInDeck );
		
		$playerListTemp = $players;
		// Find the cards and put them in each players' hand:
		foreach ( $testPlayerHandArray as $handsToAdd ) {
			foreach ( $handsToAdd as $cardToAdd ) {
				$cardsInDeck = $this->cards->getCardsInLocation( 'deck' );
				self::dump("[bmc] cardToAdd:", $cardToAdd );
				$presetHandCard = self::searchForCard( $cardsInDeck, $cardToAdd );
				self::dump("[bmc] presetHandCard:", $presetHandCard );
				if (isset( $presetHandCard[ "id" ] )) {
					self::dump("[bmc] presetHandCard[id]:", $presetHandCard[ "id" ] );
				}
				//self::dump("[bmc] current( $playerListTemp )['id']:", current( $playerListTemp ) );
			
				if (isset( $presetHandCard[ "id" ] )) {
					if ( !$debug ) {
						$this->cards->moveCard( $presetHandCard[ "id" ], 'hand',  current( $playerListTemp )['player_id']);
					}
				}
			}
			//self::dump("[bmc] playerListTemp:", current( $playerListTemp ));
			self::setPlayerGoneDown( current( $playerListTemp )[ "player_id" ], 0 ); /* 0 (not gone down) or 1 (gone down) */

			next( $playerListTemp );
		}
		// Put the jokers on the top of the deck
		$cardsForDeck[0] = array(
			0 => array(
				'type' => '5', // Suit
				'type_arg' => '1' // Value
				),
			1 => array(
				'type' => '5', // Suit
				'type_arg' => '2' // Value
				),
			2 => array(
				'type' => '5', // Suit
				'type_arg' => '1' // Value
				),
			3 => array(
				'type' => '5', // Suit
				'type_arg' => '2' // Value
				)
			);

		$jokerLoc = 200; // Higher numbers are closer to the top of the deck
		
		foreach ( $cardsForDeck as $cardToAdd ) {
			$cardsInDeck = $this->cards->getCardsInLocation( 'deck' );
			$presetHandCard = self::searchForCard( $cardsInDeck, $cardToAdd );
			if (isset( $presetHandCard[ "id" ] )) {
//				$this->cards->moveCard( $presetHandCard[ 'id' ], 'deck',  $location_arg = $jokerLoc );
				//$this->cards->moveCard( $presetHandCard[ 'id' ], 'jokerPile' );
				$this->cards->insertCardOnExtremePosition( $presetHandCard[ 'id' ], 'jokerPile', true );

				};
			$jokerLoc = $jokerLoc - 1;
		};
		//$allCardsDebug = $this->cards->getCardsInLocation( 'deck' );
		//self::dump("[bmc] allCardsInDeck:", $allCardsDebug );
		//$allCardsDebug = $this->cards->getCardsInLocation( 'jokerPile' );
		//self::dump("[bmc] allCardsInJokerPile:", $allCardsDebug );
		self::trace("[bmc] EXIT presetHands");
	}
////////
////////
////////
	function argPlayerTurnDraw() {
		self::trace("[bmc] ENTER argPlayerTurnDraw");
		
		$currentHandType = $this->getGameStateValue( 'currentHandType' );
		
		$playerGoneDown = self::getPlayerGoneDown(); // It's an array, one for each player.
		
		self::dump("[bmc] playerGoneDown:", $playerGoneDown );

		$gtActivePlayerId = $this->getActivePlayerId();
		self::dump("[bmc] GAME THINKS ACTIVE PLAYER:", $gtActivePlayerId );
		
		$activeTurnPlayer_id = self::getGameStateValue( 'activeTurnPlayer_id' );
		
		$players = self::loadPlayersBasicInfos();
		$activePlayer = $players[ $activeTurnPlayer_id ][ 'player_name' ];

		// Check for liverpoolexist
		$liverpoolFoundYN = self::getGameStateValue( 'liverpoolFoundYN' );

		if (( self::getGameStateValue( 'liverpoolExists')) && ( $liverpoolFoundYN == 1 )) {
			self::trace("[bmc] argPTD: liverpoolExists");
			self::trace("[bmc] argPTD: liverpoolFoundYN==Y");
			 
			$playerFindingLP = self::getGameStateValue( 'playerFindingLP' );

			$tpn = '<span style="color:#' . $players[ $playerFindingLP ]["player_color"] . ';">' . $players[ $playerFindingLP ]["player_name"] . '</span>';

			$message = " has found Liverpool! They can play that card and discard another.";
			$thingsCanDo = 'play or discard.';
			
			$this->gamestate->nextState( 'liverpool' );
			
		} else { 
			// No liverpool so proceed normally
			self::dump("[bmc] activeTurnPlayer_id:", $activeTurnPlayer_id );

			if ( $playerGoneDown[ $activeTurnPlayer_id ] == 1 ) {
				$thingsCanDo = 'play or discard.';
			} else {
				$thingsCanDo = 'play, discard or go down.';
			}
			$tpn = '<span style="color:#' . $players[ $activeTurnPlayer_id ]["player_color"] . ';">' . $players[ $activeTurnPlayer_id ]["player_name"] . '</span>';
			
			$message = " must draw from deck or discard pile. Others might buy.";
			
		}
		$buyers = self::getPlayerBuying();

		self::dump("[bmc] argPlayerTurnDraw buyers (PTD):", $buyers);
			
		self::setGameLength(); // This is here or else $this->handTypes is unknown
		
		self::dump("[bmc] tpn: ", $tpn );
	
		self::trace("[bmc] abouttoEXIT argPlayerTurnDraw");
		return array(
			'handTarget' => $this->handTypes[ $currentHandType ][ "Target" ], // Pull the description
			'thingsCanDo' => $thingsCanDo,
			'turnPlayerName' => $tpn,
			'message' => $message,
			'buyers' => $buyers,
//			'where' => 'PTD'
		);
		self::trace("[bmc] EXIT argPlayerTurnDraw");
    }
////////
////////
////////
	function argLiverpool() {
		self::trace("[bmc] ENTER argLiverpool");
		$players = self::loadPlayersBasicInfos();

		$playerFindingLP = $players[ self::getGameStateValue( 'playerFindingLP' )];

		self::trace("[bmc] EXIT argLiverpool");
        return array(
			'playerFindingLP' => $playerFindingLP
		);
	}
////////
////////
////////
	function argLiverpoolDraw() {
		self::trace("[bmc] ENTER argLiverpoolDraw");
		$players = self::loadPlayersBasicInfos();

		$playerFindingLP = $players[ self::getGameStateValue( 'playerFindingLP' )][ 'player_name' ];

		self::trace("[bmc] EXIT argLiverpoolDraw");
        return array(
			'playerFindingLP' => $playerFindingLP
		);
	}
////////
////////
////////
	function argWentOut() {
		self::trace("[bmc] ENTER argWentOut");
		$activeTurnPlayer_id = self::getGameStateValue( 'activeTurnPlayer_id' );
		$players = self::loadPlayersBasicInfos();
		$activePlayer = $players[ $activeTurnPlayer_id ][ 'player_name' ];
		self::trace("[bmc] EXIT argWentOut");
        return array(
			'player_name' => $activePlayer,
			'player_id' => $activeTurnPlayer_id
		);
	}
////////
////////
////////
	function argPlayerTurnPlay() {
		self::trace("[bmc] ENTER argPlayerTurnPlay");

		$currentHandType = $this->getGameStateValue( 'currentHandType' );

		$playerGoneDown = self::getPlayerGoneDown(); // It's an array, one for each player.

		$gtActivePlayerId = $this->getActivePlayerId();
		self::dump("[bmc] GAME THINKS ACTIVE PLAYER:", $gtActivePlayerId );
		
		$activeTurnPlayer_id = self::getGameStateValue( 'activeTurnPlayer_id' );
		
		$players = self::loadPlayersBasicInfos();
		$activePlayer = $players[ $activeTurnPlayer_id ][ 'player_name' ];

		self::dump("[bmc] activeTurnPlayer_id(argPTP):", $activeTurnPlayer_id );

//		$buyers = self::getPlayerBuying();
		//self::dump("[bmc] argPlayerTurnPlay buyers(PTP):", $buyers);

		if ( $playerGoneDown[ $activeTurnPlayer_id ] == 1 ) {
			$thingsCanDo = clienttranslate( 'play or discard');
		} else {
			$thingsCanDo = clienttranslate( 'discard or go down (must go down to play on other melds)');
		}
		
		//self::dump("[bmc] currentHandType argPlayerTurnPlay:", $this->handTypes[$currentHandType]["Target"] );
		//self::dump("[bmc] thingsCanDo:", $thingsCanDo );
		//self::dump("[bmc] activePlayer(PTP):", $activePlayer );

		self::setGameLength();

		$tpn = '<span style="color:#' . $players[ $activeTurnPlayer_id ]["player_color"] . ';">' . $players[ $activeTurnPlayer_id ]["player_name"] . '</span>';
		
		self::dump("[bmc] tpn: ", $tpn );

		self::trace("[bmc] EXIT argPlayerTurnPlay");

        return array(
			'handTarget' => $this->handTypes[ $currentHandType ][ "Target" ], // Pull the description
			'thingsCanDo' => $thingsCanDo,
			'turnPlayerName' => $tpn,
			'where' => 'PTP'

        );
		self::trace("[bmc] EXIT argPlayerTurnPlay");
	}
	
    /*
        getGameProgression:
        
        Compute and return the current game progression.
        The number returned must be an integer beween 0 (=the game just started) and
        100 (= the game is finished or almost finished).
    
        This method is called each time we are in a game state with the "updateGameProgression" property set to true 
        (see states.inc.php)
    */
    function getGameProgression() {
		self::trace("bmc] ENTER getGameProgression");
		$currentHandType = $this->getGameStateValue( 'currentHandType' );
		
		self::setGameLength();

		self::dump("[bmc] Progression:", $currentHandType );
		self::dump("[bmc] Progression:", count( $this->handTypes ));
		
		$ret = 100 * ( floatval( $currentHandType / count( $this->handTypes )));
		
		if( $ret < 1 ) {
            $ret = 1;
        }
        if( $ret > 99 ) {
            $ret = 99;
        }
		self::trace("bmc] EXIT getGameProgression");
		return $ret;
    }

//////////////////////////////////////////////////////////////////////////////
//////////// Utility functions
//////////// 
    /*
        In this space, you can put any utility methods useful for your game logic
    */

	function setGameLength() {
		$gameLengthOption = $this->getGameStateValue( 'gameLengthOption' );

		if ( $gameLengthOption == 1 ) {
			$this->handTypes = $this->handTypes2S0R;
		} else if ( $gameLengthOption == 2 ) {
			$this->handTypes = $this->handTypes1S1R;
		} else if ( $gameLengthOption == 3 ) {
			$this->handTypes = $this->handTypes0S2R;
		} else if ( $gameLengthOption == 4 ) {
			$this->handTypes = $this->handTypes3S0R;
		} else if ( $gameLengthOption == 5 ) {
			$this->handTypes = $this->handTypes2S1R;
		} else if ( $gameLengthOption == 6 ) {
			$this->handTypes = $this->handTypes1S2R;
		} else if ( $gameLengthOption == 7 ) {
			$this->handTypes = $this->handTypes0S3R;
		} else if ( $gameLengthOption == 8 ) {
			$this->handTypes = $this->handTypesTwo;
		} else if ( $gameLengthOption == 9 ) {
			$this->handTypes = $this->handTypesThree;
		} else if ( $gameLengthOption == 10 ) {
			$this->handTypes = $this->handTypesShort;
		} else if ( $gameLengthOption == 12 ) {
			$this->handTypes = $this->handTypesMayI;
		} else {
			$this->handTypes = $this->handTypesFull;
		}
	}
	
    function argMyArgumentMethod() {
		self::trace("bmc] ENTER argMyArgumentMethod");

		$currentPlayer = $this->getActivePlayerName();
		$currentHandType = $this->getGameStateValue( 'currentHandType' );
		self::dump("[bmc] currentHandType argMyArgumentMethod:", $this->handTypes[$currentHandType]["Target"]);
		
		return array(
            'currentPlayer' => $currentPlayer,
			'handTarget' => $this->handTypes[ $currentHandType ][ "Target" ] // Pull the description
        );    
    }
/////
/////
/////
	function getPlayerGoneDown() {
        $sql = "SELECT player_id, gone_down FROM player ";
        return self::getCollectionFromDB($sql, true);
	}
	function setPlayerGoneDown($player_id, $goneDown /* 0 or 1 */) {
        $sql = "UPDATE player SET gone_down = $goneDown WHERE player_id = $player_id ";
        self::DbQuery( $sql );
	}
    // function clearPlayersGoneDown() {
        // $sql = "UPDATE player SET gone_down = 0 ";
        // self::DbQuery( $sql );
    // }
/////
/////
/////
	function getPlayerBuying() {
       $sql = "SELECT player_id, buying FROM player ";
       return self::getCollectionFromDB($sql, true);
	}
	function setPlayerBuying( $player_id, $buying ) { // (0==unknown, 1==Not buying 2==Buying)
       $sql = "UPDATE player SET buying = $buying WHERE player_id = $player_id ";
       self::DbQuery( $sql );
	}
	function clearPlayersBuying() {
       $sql = "UPDATE player SET buying = 0 ";
       self::DbQuery( $sql );
	}

/////
/////
/////
    function getPlayersBuyCount() {
		self::trace( "[bmc] ENTER getPlayersBuyCount" );

		$numberOfBuys =  self::getGameStateValue( 'numberOfBuys' );

		if ( $numberOfBuys == 1 ) { // 0 == 3; 1 == Infinite buys
			$players = self::loadPlayersBasicInfos();
			$infiniteBuys = array();
			
			foreach ( $players as $player ) {
				self::dump("[bmc] infiniteBuys(player): ", $player[ "player_id" ] );
				
				$infiniteBuys[ $player[ "player_id" ] ] = 99 ;
			}
			self::dump("[bmc] InfiniteBuys: ", $infiniteBuys );
			return $infiniteBuys;
				
		} else {
			$sql = "SELECT player_id, buy_count FROM player ";
			$buy_countDB = self::getCollectionFromDB($sql, true);
			self::dump("[bmc] buy_countDB: ", $buy_countDB );
			return $buy_countDB;
		}
    }

/////
/////
/////
    function decPlayerBuyCount( $player_id ) { // Track how many times they bought per hand
		self::dump("[bmc] ENTER decPlayerBuyCount", $player_id );
		$numberOfBuys =  self::getGameStateValue( 'numberOfBuys' );

		if ( $numberOfBuys != 1 ) { // 0 == 3; 1 == Infinite buys
			$sql = "SELECT player_id, buy_count FROM player ";
			$buy_count = self::getCollectionFromDB( $sql, true );

			self::dump("[bmc] buy_count[ player_id ] (decPlayerBuyCount): ", $buy_count );
			
			if ( $buy_count[ $player_id ] > 0 ) {
				$bcUpdate = $buy_count[ $player_id ] - 1;
				$sql = "UPDATE player SET buy_count = $bcUpdate WHERE player_id = $player_id ";
				self::DbQuery( $sql );
			} else {
				throw new BgaUserException( self::_("You cannot buy any more this hand.") );
				self::trace( "[bmc] BGA Exception: Cannot buy any more(decPlayerBuyCount)" );
			}
		} // If it == 1 then don't decrement
    }
/////
/////
/////
    function clearPlayersBuyCount() {
        $sql = "UPDATE player SET buy_count = 3 ";
        self::DbQuery( $sql );
    }
/////
/////
/////

//////////////////////////////////////////////////////////////////////////////
//////////// Player actions
//////////// 

    /*
        Each time a player is doing some game action (i.e. clicking something), one of the methods below is called.
        (note: each method below must match an input method in tutorialrumone.action.php)
    */


//TODO: Thinking to cut discardCard into 2.. First discard the card, resolve the buy counts and all that. Then return the web transaction. Then read the buy counts and notify the players as a second step. This should let the database resolve.


	function discardCard( $card_id, $player_id ) {
		self::trace( "[bmc] ENTER discardCard (from JS via action.php)" );

		self::checkAction("discardCard");
		
		$activeTurnPlayer_id = self::getGameStateValue( 'activeTurnPlayer_id' );
		self::dump("[bmc] activeTurnPlayer_id:", $activeTurnPlayer_id );

    	// Turns played statistics, match it to number of discards
    	self::incStat( 1, 'turns_number' );
		
		$currentHandType = $this->getGameStateValue( 'currentHandType' );

		self::dump("[bmc] currentHandType discardCard:", $currentHandType );

		self::dump("[bmc] Discard attempt by player id:", $player_id );
		self::dump("[bmc] Trying to discard card_id:", $card_id );
		self::dump("[bmc] getActivePlayerName:", self::getActivePlayerName() );
		
		if ( $activeTurnPlayer_id == $player_id ) { // Allow discard if it's that player's turn

		// Allow buying again (trying to resolve bug where someone buys after discard picked up
		self::setGameStateValue( 'isBuyingAllowed', 1 ); // 0 == false; 1 == true

		self::trace("[bmc] YESBUYINGALLOWEDYES" );

		// These next 2 active lines were previously outside the IF. However, if the player
		//    is not allowed to buy, then the game state should not change.
		// First resolve the buyers, then process the discard
			$this->resolveBuyers();
		
			self::clearPlayersBuying();
			//self::clearPlayersBuyingGS();

			// Notifying players potentially buying the previous one that they were too slow!
			self::notifyAllPlayers(
				'clearBuyers',
				'',
				array (
//					'discardWeight' => $discardWeight
				)
			);
			
			self::trace( "[bmc] Discarding the card." );
			
			$discardWeight = self::incGameStateValue( 'discardWeightHistory', 1 );

			self::dump("[bmc] discardweight:", $discardWeight );

			// Put the card on top of the discard pile
			$bOnTop = true;
			
			$this->cards->insertCardOnExtremePosition( $card_id, 'discardPile', $bOnTop );
	
			//$cardsInDp = $this->cards->getCardsInLocation( 'discardPile' );
			
			//self::dump("[bmc] cardsInDp:", $cardsInDp );

			$nextTurnPlayer = $this->getPlayerAfter( $player_id );
			
			self::dump("[bmc] nextTurnPlayer:", $nextTurnPlayer );

			$cardsByLocationHand  = $this->cards->countCardsByLocationArgs( 'hand' );
			$discardSize = count( $this->cards->countCardsByLocationArgs( 'discardPile' ));
			self::setGameStateValue( 'discardSize', $discardSize );
			
			self::dump("[bmc] discardSize(DP):", $discardSize );

			$this->checkEmptyDeck(); // Make sure the deck has cards
			$drawDeckSize = count( $this->cards->countCardsByLocationArgs( 'deck' ));

			$currentCard = $this->cards->getCard( $card_id );

			// <Player> Discard: <jack> of <clubs>
			// <Player> Discard: <joker>
			
			if ( $currentCard[ 'type' ] == 5 ) {
				$value_displayed = 'Joker';
				$color_displayed = '';
				$connector = '';
			} else {
				$value_displayed = $this->values_label[ $currentCard[ 'type_arg' ]];
				$color_displayed = $this->colors[ $currentCard[ 'type' ]][ 'name' ];
				$connector = ' of ';
			}
			
			$player_name = self::getActivePlayerName();

			self::notifyAllPlayers(
				'discardCard',
				clienttranslate( '${player_name} Discard: ${value_displayed} ${connector} ${color_displayed}' ),
				array (
					'i18n' => array( 'color_displayed', 'value_displayed', 'connector' ),
					'player_id' => $activeTurnPlayer_id,
					'player_name' => self::getActivePlayerName(),
					'color' => $currentCard [ 'type' ],
					'color_displayed' => $color_displayed,
					'value' => $currentCard [ 'type_arg' ],
					'value_displayed' => $value_displayed,
					'card_id' => $card_id,
					'nextTurnPlayer' => $nextTurnPlayer,
					'allHands' => $cardsByLocationHand,
					'discardSize' => $discardSize,
					'drawDeckSize' => $drawDeckSize,
					'connector' => $connector
				)
			);

// Example from BGA website
// https://en.doc.boardgamearena.com/Tutorial_hearts
        // self::notifyAllPlayers(
			// 'playCard',
			// clienttranslate('${player_name} plays ${value_displayed} ${color_displayed}'),
			// array (
				// 'i18n' => array ('color_displayed','value_displayed' ),
				// 'card_id' => $card_id,
				// 'player_id' => $player_id,
				// 'player_name' => self::getActivePlayerName(),
				// 'value' => $currentCard ['type_arg'],
				// 'value_displayed' => $this->values_label [$currentCard ['type_arg']],
				// 'color' => $currentCard ['type'],
				// 'color_displayed' => $this->colors [$currentCard ['type']] ['name']
			// )
		// );
        // Next player

			self::trace( "[bmc] About to EXIT discardCard (via nextState'discardCard')." );

			// Discarded the card, move on
			$this->gamestate->nextState( 'discardCard' );

		} else {
			throw new BgaUserException( self::_("You cannot discard, it's not your turn.") );
		}
		
		self::trace("[bmc] EXIT discardCard (from JS)");
    }
////////
////////
////////
    function drawCard( $card_id, $drawSource, $player_id ) {
		self::trace("[bmc] ENTER Draw Card (from JS)"); // Just see that we got here from the Javascript.
		self::dump("card id:", $card_id ); // Probably no longer need to send in card_id from JS
		self::dump("drawSource:", $drawSource );
		self::dump("Drawing player id:", $player_id );
        self::checkAction("drawCard");
		
		$activeTurnPlayer_id = $this->getGameStateValue( 'activeTurnPlayer_id' );
		
		self::dump("[bmc] activeTurnPlayer_id", $activeTurnPlayer_id );
		
		if ( $player_id != $activeTurnPlayer_id ) {
			throw new BgaUserException( self::_("You cannot draw, it's not your turn.") );
		}

		$countCardsByLocation = $this->cards->countCardsByLocationArgs( 'hand' );
		//self::dump("[bmc] CCBL:", $countCardsByLocation);

		// drawSource Sources (OLD):
		// 0 == 'deck' (buyer gets it + 1 down card; Increment buy counter)
		// 1 == 'discardPile' (buyer gets nothing)
		// 2 == Other sources (other conditions like playing a card for a joker)
		
		// drawSourceValue needs to be set properly before resolveBuyers
		if ( $drawSource == 'discardPile' ) {
			self::trace("[bmc] drawSource == discardPile" );
			self::setGameStateValue( 'drawSourceValue', 1 );
		} else if ( $drawSource == 'deck' ) {
			self::trace("[bmc] drawSource == deck" );
			self::setGameStateValue( 'drawSourceValue', 0 );
		} else {
			self::trace("[bmc] drawSource == other, some place on the board" );
			self::setGameStateValue( 'drawSourceValue', 2 );
		}

		// If drawing from the discard pile then the player get the top card, not necessarily the one they clicked
		if ( $drawSource == 'discardPile' ) {

			// If the player whose turn it is draws the discard then don't allow buying
			self::setGameStateValue( 'isBuyingAllowed', 0 ); // 0 == false; 1 == true
			self::trace("[bmc] NOLONGERBUYINGALLOWEDNOLONGER" );

			$topDiscard = $this->cards->getCardOnTop( 'discardPile' );
			self::dump( "[bmc] topDiscard: ", $topDiscard );
			
			$card_id = $topDiscard[ 'id' ];
		} else { // else use the top card of the deck
		
			$this->checkEmptyDeck(); // Make sure the deck has cards

			// If the most senior player wants to buy then resolve it immediately without waiting for next discard
			$activeTurnPlayer_id = self::getGameStateValue( 'activeTurnPlayer_id' );
			$bossBuyer = $this->getPlayerAfter( $activeTurnPlayer_id );
			
			$buyers = self::getPlayerBuying();
			// $buyers = self::getPlayerBuyingGS();
		
			self::dump("[bmc] buyers(drawCard):", $buyers);
			
			$players = self::loadPlayersBasicInfos();

			$buyingPlayers = [];

			// Change to p_id because the loop will change the value
			foreach( $buyers as $p_id => $buyChoice ) {
				self::dump("bmc] player_id(p_id): ", $p_id);
				self::dump("bmc] buyChoice: ", $buyChoice);
				
				if ( $buyChoice == 2 ) { // 0=Not decided, 1=Not buying, 2=Buying
					self::dump("[bmc] player_id(p_id)", $p_id);
					self::dump("[bmc] ATPI", $activeTurnPlayer_id);
					self::dump("[bmc] bossBuyer", $bossBuyer);

					if ( $p_id == $bossBuyer ) {
						self::trace("[bmc] Resolving Buyers early!");
						$this->resolveBuyers();
						
					}
				} else {
					self::dump("[bmc]  This player is not buying::", $p_id);
				}
			}

			$topDeck = $this->cards->getCardOnTop( 'deck' );
			self::dump( "[bmc] topDeck: ", $topDeck );
			
			$card_id = $topDeck[ 'id' ];
		}
		
        $this->cards->moveCard( $card_id, 'hand', $player_id );

		$countCardsByLocation = $this->cards->countCardsByLocationArgs( 'hand' );
		//self::dump("[bmc] CCBL aftermove:", $countCardsByLocation);

        $currentCard = $this->cards->getCard( $card_id );
		
		if ( $currentCard[ 'type' ] == 5 ) { // If joker
			self::trace("[bmc] Drew a joker.");
			self::incStat( -1, 'jokers_number', $player_id ); // Track joker play for stats
		}

		$activeTurnPlayer_id = $this->getGameStateValue( 'activeTurnPlayer_id' );
		$this->drawNotify( $currentCard, $player_id, $drawSource, $player_id, $activeTurnPlayer_id );
		self::trace("bmc] EXIT drawCard");

	}
////////
////////
////////
	function notifyPlayerWantsToNotBuy( $player_id ) {
		self::dump("[bmc] ENTER notifyPlayerWantsToNotBuy:",  $player_id);
		
		// self::setBuyTimerStatus( $player_id, 0 ); // 0 = Not running. 1 = Running.

		$players = self::loadPlayersBasicInfos();

		$player_name = $players[ $player_id ][ 'player_name' ];

		self::notifyAllPlayers(
			'playerWantsToNotBuy',
			clienttranslate( '${player_name} no longer wants to buy' ),
			array(
				'player_id' => $player_id,
				'player_name' => $players[ $player_id ][ 'player_name' ]
			)
		);
		self::trace("[bmc] EXIT notifyPlayerWantsToNotBuy");
	}
////////
////////
////////
	function notifyPlayerWantsToBuy( $player_id ) {
		self::dump("[bmc] ENTER notifyPlayerWantsToBuy:",  $player_id);
		$players = self::loadPlayersBasicInfos();
		$activeTurnPlayer_id = $this->getGameStateValue( 'activeTurnPlayer_id' );
		self::dump("[bmc] ATPI", $activeTurnPlayer_id );
		$currentCard = $this->cards->getCardOnTop( 'discardPile' );
		self::dump( "[bmc] cardToBeBought[id]:",  $currentCard['id'] );
		
		if ( $currentCard != null ) {
			if ( $currentCard[ 'type' ] == 5 ) {
				$value_displayed = 'Joker';
				$color_displayed = '';
				$connector = '';
			} else {
				$value_displayed = $this->values_label[ $currentCard[ 'type_arg' ]];
				$color_displayed = $this->colors[ $currentCard[ 'type' ]][ 'name' ];
				$connector = ' of ';
			}

			// if ( $currentCard[ 'type' ] == 5 ) {
				// $value_displayed = self::_(' joker');
				// $color_displayed = '';
			// } else {
				// $value_displayed = self::_('the ') . self::_($this->values_label[ $currentCard[ 'type_arg' ]]) . self::_(' of ');
				// $color_displayed = self::_($this->colors[ $currentCard[ 'type' ]][ 'name' ] . 's.');
			// }
			
			$player_name = $players[ $player_id ][ 'player_name' ];

			self::notifyAllPlayers(
				'playerWantsToBuy',
				clienttranslate( '${player_name} Wants to Buy: ${value_displayed} ${connector} ${color_displayed}'),
				array(
					'i18n' => array( 'color_displayed', 'value_displayed', 'connector' ),
					'player_id' => $player_id,
					'activeTurnPlayer_id' => $activeTurnPlayer_id,
					'player_name' => $players[ $player_id ][ 'player_name' ],
					'cardToBeBought' => $currentCard,
					'value_displayed' => $value_displayed,
					'color_displayed' => $color_displayed,
					'connector' => $connector
				)
			);
		} else {
			self::trace("[bmc] Yikes! No card was found to buy!");
		}
		self::trace("[bmc] EXIT notifyPlayerWantsToBuy");
	}
////////
////////
////////
	function notifyPlayerWantsToBuy_orig( $player_id ) {
		self::trace("[bmc] ENTER notifyPlayerBuy-WANT");
		self::dump("[bmc] player_id:",  $player_id);

		$players = self::loadPlayersBasicInfos();
		$activeTurnPlayer_id = $this->getGameStateValue( 'activeTurnPlayer_id' );
		self::dump("[bmc] ATPI", $activeTurnPlayer_id );

//TODO: MOVE THIS TO LATER, after the notif 
		self::setPlayerBuying( $player_id, 2 );
		// self::setPlayerBuyingGS( $player_id, 2 );

		$drawSourceValue = self::getGameStateValue( 'drawSourceValue' );
		self::dump("[bmc] drawSourceValue(notifyPlayerWantsToBuy):", $drawSourceValue );

//		if ( $drawSourceValue != 1 ) { // if !=1 then it's ok to try to buy
self::trace("[bmc] Deadlock:2174");

			$currentCard = $this->cards->getCardOnTop( 'discardPile' );
self::trace("[bmc] Deadlock:2177");
			self::dump( "[bmc] cardToBeBought:",  $currentCard );
self::trace("[bmc] Deadlock:2179");
			
			if ( $currentCard != null ) {
				
				if ( $currentCard[ 'type' ] == 5 ) {
					$value_displayed = 'Joker';
					$color_displayed = '';
					$connector = '';
				} else {
					$value_displayed = $this->values_label[ $currentCard[ 'type_arg' ]];
					$color_displayed = $this->colors[ $currentCard[ 'type' ]][ 'name' ];
					$connector = ' of ';
				}

				$player_name = $players[ $player_id ][ 'player_name' ];

				self::notifyAllPlayers(
					'playerWantsToBuy',
					clienttranslate('${player_name} Wants to Buy: ${value_displayed} ${connector} ${color_displayed}'),
					array(
					'i18n' => array( 'color_displayed', 'value_displayed', 'connector' ),
						'player_id' => $player_id,
						'activeTurnPlayer_id' => $activeTurnPlayer_id,
						'player_name' => $players[ $player_id ][ 'player_name' ],
						'cardToBeBought' => $currentCard,
						'value_displayed' => $value_displayed,
						'color_displayed' => $color_displayed,
						'connector' => $connector
					)
				);
			} else {
				self::trace("[bmc] Yikes! No card was found to buy!");
			}
//		}
		self::trace("[bmc] EXIT notifyPlayerBuy-WANT");
	}
////////
////////
////////
	function drawNotify( $currentCard, $playingPlayer_id, $drawSource, $drawPlayer, $activeTurnPlayer_id ) {
		self::trace("[bmc] ENTER drawNotify");

		self::dump("[bmc] currentCard    :",  $currentCard);
		
		$card_id = $currentCard['id'];
		
		$players = self::loadPlayersBasicInfos();
		$activePlayer = $players[ $playingPlayer_id ][ 'player_name' ];

//		self::dump("[bmc] card_id          :",  $card_id );
//		self::dump("[bmc] drawSource       :",  $drawSource );
		self::dump('[bmc] playingPlayer_id :',  $playingPlayer_id );
		self::dump('[bmc] player_name      :',  $activePlayer );
//		self::dump('[bmc] card_id          :',  $card_id );
//		self::dump('[bmc] value            :',  $currentCard ['type_arg'] );
//		self::dump('[bmc] value_displayed  :',  $this->values_label [$currentCard ['type_arg']] );
//		self::dump('[bmc] color            :',  $currentCard ['type']);
//		self::dump('[bmc] color_displayed  :',  $this->colors [$currentCard ['type']] ['name'] );
		self::dump('[bmc] drawSource       :',  $drawSource );
		self::dump('[bmc] drawPlayer       :',  $drawPlayer );

		// Notify players of the source of the draw

		if ( $drawSource == 'discardPile' ) {
			$drawSourceText = 'discard pile';
		} else if ( $drawSource == 'deck' ) {
			$drawSourceText = 'deck';
		} else {
			$drawSourceText = 'board';
		}

		$cardsByLocation = $this->cards->countCardsByLocationArgs( 'hand' );

		$drawDeckSize = count( $this->cards->countCardsByLocationArgs( 'deck' ));
		
		$discardSize = count( $this->cards->countCardsByLocationArgs( 'discardPile' ));
		self::setGameStateValue( 'discardSize', $discardSize );
		
		// Show text differently to players for a joker.
		
		if ( $currentCard[ 'type' ] == 5 ) {
			$value_displayed = 'Joker';
			$color_displayed = '';
			$connector = '';
		} else {
			$value_displayed = $this->values_label[ $currentCard[ 'type_arg' ]];
			$color_displayed = $this->colors[ $currentCard[ 'type' ]][ 'name' ];
			$connector = ' of ';
		}

		foreach ( $players as $player_id => $player ) {
			
			if ( $player_id == $activeTurnPlayer_id ) {
				self::notifyPlayer(
					$player_id,
					'drawCard',
					clienttranslate( 'You drew from the ${drawSourceText}: ${value_displayed} ${connector} ${color_displayed}' ),
					array(
						'i18n' => array( 'value_displayed', 'color_displayed', 'drawSourceText', 'connector' ),
						'player_id' => $player_id,
						'player_name' => $activePlayer,
						'card_id' => $card_id,
						'value' => $currentCard[ 'type_arg' ],
						'value_displayed' => $value_displayed,
						'color' => $currentCard [ 'type' ],
						'color_displayed' => $color_displayed,
						'drawSource' => $drawSource,
						'drawSourceText' => $drawSourceText,
						'drawPlayer' => $drawPlayer,
						'allHands' => $cardsByLocation,
						'discardSize' => $discardSize,
						'drawDeckSize' => $drawDeckSize,
						'connector' => $connector
					)
				);
			} else {
				self::notifyPlayer(
					$player_id,
					'drawCard',
					// Commenting this out or else 2 entries will appear in the log. This is duplicated by the drawCardSpect function.
//					'${player_name} draws a card from the ${drawSourceText}.',
					'',
					array(
						'player_id' => $playingPlayer_id,
						'player_name' => $activePlayer,
						'card_id' => $card_id,
						'value' => $currentCard[ 'type_arg' ],
						'value_displayed' => '',
						'color' => $currentCard [ 'type' ],
						'color_displayed' => '',
						'drawSource' => $drawSource,
						'drawSourceText' => $drawSourceText,
						'drawPlayer' => $drawPlayer,
						'allHands' => $cardsByLocation,
						'discardSize' => $discardSize,
						'drawDeckSize' => $drawDeckSize
					)
				);
			}
		}

		$player_name = $activePlayer;

//09/02/2023

		self::notifyAllPlayers(
			'drawCardSpect',
			// WHY IS NEXT LINE COMMENTED OUT???
			// It was commented out because 2 entries appear in the log if it's not blank. Now I commented out the per-player one above.
			clienttranslate( '${player_name} drew a card from the ${drawSourceText}' ),
			array(
				'i18n' => array( 'drawSourceText' ),
				'player_id' => $activeTurnPlayer_id,
				'player_name' => $activePlayer,
				'card_id' => $card_id,
				'color' => '',
				'value' => '',
				'drawSource' => $drawSource,
				'drawPlayer' => $drawPlayer,
				'allHands' => $cardsByLocation,
				'discardSize' => $discardSize,
				'drawSourceText' => $drawSourceText,
				'drawDeckSize' => $drawDeckSize
			)
		);

		self::trace("[bmc] EXIT (almost) drawNotify");

		// Next State
		$state = $this->gamestate->state();
//		self::dump("[bmc] state:", $state);

		if ( $state['name'] == 'playerTurnPlay' ) { // Got the joker from a board play, so keep playing.
			$this->gamestate->nextState( 'playCard' );

		} else if ( $state['name'] == 'resolveBuyers' ) {
			// If notifying of a buy then don't change state
			return;
		} else {
			// Else got card from a true draw (deck or discard), so let the player play.
			
			self::trace("[bmc] MAYBE ERROR AREA IN DRAWNOTIFY");
			$this->gamestate->nextState( 'drawCard' );
		}
		self::trace("[bmc] EXIT drawNotify");
	}
////////
////////
////////
	function playerGoDown( $cardIDGroupA, $cardIDGroupB, $cardIDGroupC, $boardCardId, $boardArea, $boardPlayer, $handItemIds ) {
		self::trace("[bmc] ENTER playerGoDown");
		
		$active_player_id = self::getActivePlayerId();
		self::dump("[bmc] playerGoDown: ", $active_player_id);
		self::checkAction('playerGoDown');

		// Add restriction to go down with no more than 1 joker (or not)
		$cntCardGroupA = count( $cardIDGroupA );
		$cntCardGroupB = count( $cardIDGroupB );
		$cntCardGroupC = count( $cardIDGroupC );
		$cntCardGroupJ = count( $handItemIds );
		
//		$jokerCount = 0;

		self::dump("[bmc] cardIDGroupA:", $cardIDGroupA);
		self::dump("[bmc] cardIDGroupB:", $cardIDGroupB);
		self::dump("[bmc] cardIDGroupC:", $cardIDGroupC);
		self::dump("[bmc] handItemIds:", $handItemIds);
				
		self::dump("[bmc] cntCardGroupA:", $cntCardGroupA);
		self::dump("[bmc] cntCardGroupB:", $cntCardGroupB);
		self::dump("[bmc] cntCardGroupC:", $cntCardGroupC);
		self::dump("[bmc] cntCardGroupJ:", $cntCardGroupJ);

		$cardGroupA = $this->cards->getCards( $cardIDGroupA );
		$cardGroupB = $this->cards->getCards( $cardIDGroupB );
		$cardGroupC = $this->cards->getCards( $cardIDGroupC );
		
		// Make sure there is > 1 card left in hand
		$countCardsInPlayerHand = intval($this->cards->countCardsByLocationArgs( 'hand' )[$active_player_id]);
		self::dump("CCIPH:", $countCardsInPlayerHand);
		
		//$countCardsToPlay = $cntCardGroupA + $cntCardGroupB + $cntCardGroupC;
		$countCardsToPlay = $cntCardGroupA + $cntCardGroupB + $cntCardGroupC + $cntCardGroupJ;
		//count($cardGroupA) + count($cardGroupB) + count($cardGroupC);
		self::dump("CCTP:", $countCardsToPlay);
		
		$remainingCardCount = abs($countCardsInPlayerHand - $countCardsToPlay);
		self::dump("[bmc] Remaining Cards:", $remainingCardCount);

		if ($remainingCardCount < 1 ) {
			throw new BgaUserException( self::_('You cannot empty your hand.') );
			return;
		}
		
		// Swap out the joker if there is one
		// Get the cards in the selected pile
		// if a hand item and board card are both included then check for the joker/swap check.
		//   Check the selected card to see if it's joker.
		//   If it is, then get the type of area it is (either set or run)

		self::dump( "[bmc] BCI: ", $boardCardId );
		
		$handItems = $this->cards->getCards( $handItemIds );
		$handItems = reset( $handItems ); // Just use the first value in the array
		
		$boardCard = $this->cards->getCard( $boardCardId );

		$currentHandType = $this->getGameStateValue( 'currentHandType' );
		$areaTitle = "Area" . substr( $boardArea, -2 ); // Last 2 should be "_A" or "_B" or "_C"
		
		self::dump("[bmc] BC: ", $boardCard);
		self::dump("[bmc] BA: ", $boardArea);
		self::dump("[bmc] BP: ", $boardPlayer);
		self::dump("[bmc] HI: ", $handItems);
		self::dump("[bmc] CHT:", $currentHandType);
		self::dump("[bmc] AT: ", $areaTitle);
		
		$joker = array ('id' => 'None'); // Start by assuming no joker being swapped
		$targetArea = 'None'; // Start by assuming no target area for the going-down-joker

		if ( empty( $handItems )){
			self::trace( "[bmc] No Joker Swap Needed." );
			//... and then continue to try to go down...

		} else { // Player has prepped a joker swap card. This means try to use joker to go down.

			// Even though they didn't select a board card, see if they prepped for a joker swap.
			// If they did, and if there is 1 joker on the board, then select it for the player.
				
				$cardsInPDA = $this->cards->getCardsInLocation( 'playerDown_A' );
				$cardsInPDB = $this->cards->getCardsInLocation( 'playerDown_B' );
				$cardsInPDC = $this->cards->getCardsInLocation( 'playerDown_C' );
			
				self::dump("[bmc] PDA: ", $cardsInPDA );
				self::dump("[bmc] PDB: ", $cardsInPDB );
				self::dump("[bmc] PDC: ", $cardsInPDC );
				
				$jokersInA = $this->checkForJoker( $cardsInPDA );
				$jokersInB = $this->checkForJoker( $cardsInPDB );
				$jokersInC = $this->checkForJoker( $cardsInPDC );
				
				$countJokersInA = $this->countJokers( $cardsInPDA );
				$countJokersInB = $this->countJokers( $cardsInPDB );
				$countJokersInC = $this->countJokers( $cardsInPDC );
				
				self::dump("[bmc] JinA: ", $countJokersInA );
				self::dump("[bmc] JinB: ", $countJokersInB );
				self::dump("[bmc] JinC: ", $countJokersInC );

				// If there is only 1 joker, try swapping for it
//				if ( $jokersInA || $jokersInB || $jokersInC ){
				if (( $countJokersInA + $countJokersInB + $countJokersInC ) == 1) {
					if ( $jokersInA != null ) {
						$boardCard = $jokersInA;
						$boardArea = $jokersInA[ "location" ];
						$boardPlayer = $jokersInA[ "location_arg" ];
					} else if ( $jokersInB != null ) {
						$boardCard = $jokersInB;
						$boardArea = $jokersInB[ "location" ];
						$boardPlayer = $jokersInB[ "location_arg" ];
					} else {
						$boardCard = $jokersInC;
						$boardArea = $jokersInC[ "location" ];
						$boardPlayer = $jokersInC[ "location_arg" ];
					}
					self::dump("[bmc] game-selected board Joker: ", $boardCard );
				}
		
			$jokerSwapResult = $this->tryJokerSwap( $handItems['id'], $active_player_id, $boardArea, $boardPlayer );
			self::dump('[bmc] jokerSwapResult', $jokerSwapResult);
		
			// Move the new joker into the deficient area

			$targetArea = $this->findDeficientArea( $cardGroupA, $cardGroupB, $cardGroupC );
			self::dump("[bmc] targetArea: ", $targetArea );

			if (( $targetArea == false ) ||
				( $jokerSwapResult == false )) {
				throw new BgaUserException( self::_('Make a partial set (only 2 cards) or run (only 3 cards) for the swapping joker and select one joker to swap.') );
			} else {
				$playerHand = $this->cards->getCardsInLocation( 'hand', $active_player_id );

				$joker = $jokerSwapResult;
				self::dump("[bmc] Played Joker:", $joker);

				// Now we know where to put the joker so add it there and finish going down
				// TODO: Not sure why this isn't needed for a run (or is it???) or it's done in playcardfinish
				$this->cards->moveCard( $joker['id'], 'hand', $active_player_id);
				
				
				switch ( $targetArea ) {
					case "playerDown_A":
						$cardGroupA[ $joker[ 'id' ]] = $joker;
						$cardGroupA[ $joker[ 'id' ]][ 'location' ] = 'hand';
						$cardGroupA[ $joker[ 'id' ]][ 'location_arg' ] = $active_player_id;
						break;
					case "playerDown_B":
						$cardGroupB[ $joker[ 'id' ]] = $joker;
						$cardGroupB[ $joker[ 'id' ]][ 'location' ] = 'hand';
						$cardGroupB[ $joker[ 'id' ]][ 'location_arg' ] = $active_player_id;
						break;
					case "playerDown_C":
						$cardGroupC[ $joker[ 'id' ]] = $joker;
						$cardGroupC[ $joker[ 'id' ]][ 'location' ] = 'hand';
						$cardGroupC[ $joker[ 'id' ]][ 'location_arg' ] = $active_player_id;
						break;
				}
			}
		}
	
		self::dump("[bmc] cardGroupA:", $cardGroupA );
		self::dump("[bmc] cardGroupB:", $cardGroupB );
		self::dump("[bmc] cardGroupC:", $cardGroupC );
		self::dump("[bmc] joker:", $joker );
		self::dump("[bmc] targetArea:", $targetArea );

		$this->playerGoDownFinish( $cardGroupA, $cardGroupB, $cardGroupC, $joker, $targetArea );
		self::trace("[bmc] EXIT playerGoDown");
	}

////////
////////
////////
	function findDeficientArea ( $cardGroupA, $cardGroupB, $cardGroupC ) {
		self::trace("[bmc] ENTER findDeficientArea");
		self::dump( "[bmc] FDA CGA: ", count( $cardGroupA ));
		self::dump( "[bmc] FDA CGB: ", count( $cardGroupB ));
		self::dump( "[bmc] FDA CGC: ", count( $cardGroupC ));

		if        (( count( $cardGroupA ) == 2 ) ||
			( $this->isShortRun( $cardGroupA ))) {
			$targetArea = 'playerDown_A';
		} else if (( count( $cardGroupB ) == 2 ) ||
				   ( $this->isShortRun( $cardGroupB ))) {
			$targetArea = 'playerDown_B';
		} else if (( count( $cardGroupC ) == 2 ) || 
				   ( $this->isShortRun( $cardGroupC ))) {
			$targetArea = 'playerDown_C';
		} else {
		self::trace("[bmc] EXIT findDeficientArea (false)");
		return false;
		}
		self::dump("[bmc] EXIT findDeficientArea", $targetArea );
		return $targetArea;
	}
////////
////////
////////
	function isShortRun( $cardGroup ) {
//		self::dump( "[bmc] isShortRun: ", $cardGroup );
		$oneValue = 0;
		
		if ( count( $cardGroup ) > 3 ) { // if > 3 then it's not short
			return false;
		} else {
			// Go through each card; Compare 2 non-jokers. If same value then it's
			// trying to be a set. If different values, then it's trying to be a run.
			foreach( $cardGroup as $card ) {
				if( $card['type'] != 5 ) {
					if ( empty( $oneValue )) {
						$oneValue = $card[ 'type_arg' ];
						self::dump( "[bmc] oneValue: ", $oneValue );
					} else if ( $oneValue == $card[ 'type_arg' ] ) {
						self::trace("[bmc] isShortRun false");
						return false;
					} else {
						self::trace("[bmc] isShortRun true");
						return true;
					}
				}
			}
		}
	}
////////
////////
////////
	function playerGoDownFinish( $cardGroupA, $cardGroupB, $cardGroupC, $joker, $targetArea ) {
		self::trace("[bmc] ENTER playerGoDownFinish");
self::dump("[bmc] cardGroupA", $cardGroupA);
self::dump("[bmc] cardGroupB", $cardGroupB);
self::dump("[bmc] cardGroupC", $cardGroupC);
		
		// Verify the number of needed sets and runs is met

		$currentHandType = $this->getGameStateValue( 'currentHandType' );

		self::dump("[bmc] CHT", $currentHandType);

		$setsNeeded = $this->handTypes[ $currentHandType ][ "QtySets" ];
		$runsNeeded = $this->handTypes[ $currentHandType ][ "QtyRuns" ];
		
		self::dump("[bmc] SN", $setsNeeded);
		self::dump("[bmc] RN", $runsNeeded);

		$setsHave = 0;
		$runsHave = 0;
		$eitherHave = 0;
		$notSetRun = 0;

		$groups = array ($cardGroupA, $cardGroupB, $cardGroupC);
		
		self::dump("[bmc] GODOWNFINISH: groups:", $groups);

		foreach ( $groups as $group ) {
//			self::dump("[bmc] group:", $group );
			if ( $this->checkSetOrRun( $group ) == true ) {
				$eitherHave++;
			} else if ( $this->checkSet( $group ) == true ) {
				$setsHave++;
			} else if ( $this->checkRun( $group, true ) == true ) {
				$runsHave++;
			} else {
				if ( count( $group ) > 0 ) {
					$notSetRun++;
				}
			}
		}
		if  ( $notSetRun > 0 ) {
			throw new BgaUserException( self::_('Cannot go down with those cards. Check for sequential values. Did you select a joker from the board?') );
		}

		self::dump("[bmc] setsHave:", $setsHave);
		self::dump("[bmc] runsHave:", $runsHave);
		self::dump("[bmc] eitherHave:", $eitherHave);

		if (( $setsHave + $runsHave + $eitherHave) != ( $setsNeeded + $runsNeeded )) {
			throw new BgaUserException( self::_('Cannot go down. Incorrect number of melds.') );
		}

		if ( $runsHave > $runsNeeded ) {
			throw new BgaUserException( self::_('Cannot go down. Too many Runs and not enough Sets.') );
		} else if ( $setsHave > $setsNeeded ) {
			throw new BgaUserException( self::_('Cannot go down. Too many Sets and not enough Runs.') );
		}

		$active_player_id = self::getActivePlayerId();

		// If all cards besides the board joker are in the hand, then continue

		self::dump("[bmc] joker before checkifreallyinhand:", $joker);

		// $cardGroupAMJoker = $cardGroupA;
		// $cardGroupBMJoker = $cardGroupB;
		// $cardGroupCMJoker = $cardGroupC;
		// unset( $cardGroupAMJoker[ $joker[ 'id' ]]);
		// unset( $cardGroupBMJoker[ $joker[ 'id' ]]);
		// unset( $cardGroupCMJoker[ $joker[ 'id' ]]);

		// self::dump("[bmc] cardGroupAMJoker:", $cardGroupAMJoker);
		// self::dump("[bmc] cardGroupBMJoker:", $cardGroupBMJoker);
		// self::dump("[bmc] cardGroupCMJoker:", $cardGroupCMJoker);

		// list( $card_typeA, $card_type_argA ) = $this->checkIfReallyInHand( $cardGroupAMJoker, $active_player_id );
		// list( $card_typeB, $card_type_argB ) = $this->checkIfReallyInHand( $cardGroupBMJoker, $active_player_id );
		// list( $card_typeC, $card_type_argC ) = $this->checkIfReallyInHand( $cardGroupCMJoker, $active_player_id );
		list( $card_typeA, $card_type_argA ) = $this->checkIfReallyInHand( $cardGroupA, $active_player_id );
		list( $card_typeB, $card_type_argB ) = $this->checkIfReallyInHand( $cardGroupB, $active_player_id );
		list( $card_typeC, $card_type_argC ) = $this->checkIfReallyInHand( $cardGroupC, $active_player_id );

		self::trace("[bmc] Cards are in hand!");
		
		$cardIDGroupA = $this->makeCardIdsFromCards( $cardGroupA );
		$cardIDGroupB = $this->makeCardIdsFromCards( $cardGroupB );
		$cardIDGroupC = $this->makeCardIdsFromCards( $cardGroupC );
		
		// It's all good, they can go down
		
		// Make player assign any 'extra' jokers

		$this->assignExtraJokers( $cardGroupA );
		$this->assignExtraJokers( $cardGroupB );
		$this->assignExtraJokers( $cardGroupC );
		
		// Put the cards into the down area
		$this->cards->moveCards( $cardIDGroupA, 'playerDown_A', $active_player_id );
		$this->cards->moveCards( $cardIDGroupB, 'playerDown_B', $active_player_id );
		$this->cards->moveCards( $cardIDGroupC, 'playerDown_C', $active_player_id );
		//$this->cards->moveCard( $joker[ 'id' ], $targetArea, $active_player_id );
		
		// Keep track of how many jokers the player is using
		// Count number of jokers in cards about to go down
		// For each joker, increment the counter for that PLAYER
		// 08/26/2023
		// self::incStat( 1, 'jokers_number', $player_id );

		$maybeJokersInA = $this->cards->getCards( $cardIDGroupA );
		$maybeJokersInB = $this->cards->getCards( $cardIDGroupB );
		$maybeJokersInC = $this->cards->getCards( $cardIDGroupC );

		$countJokersInA = $this->countJokers( $maybeJokersInA );
		$countJokersInB = $this->countJokers( $maybeJokersInB );
		$countJokersInC = $this->countJokers( $maybeJokersInC );

		self::dump("[bmc] countJokersInA:", $countJokersInA);
		self::dump("[bmc] countJokersInB:", $countJokersInB);
		self::dump("[bmc] countJokersInC:", $countJokersInC);

		self::incStat( $countJokersInA, 'jokers_number', $active_player_id );
		self::incStat( $countJokersInB, 'jokers_number', $active_player_id );
		self::incStat( $countJokersInC, 'jokers_number', $active_player_id );

		// Notify all players about the cards played Area A
		self::notifyAllPlayers('playerGoDown',
			'',
			array(
				'player_name' => self::getActivePlayerName(),
				'player_id' => $active_player_id,
				'card_ids' => $cardIDGroupA,
				'card_type' => $card_typeA,
				'card_type_arg' => $card_type_argA,
				'player_down' => 'playerDown_A_'
			)
		);
		// Notify all players about the cards played Area B
		self::notifyAllPlayers('playerGoDown',
			'',
			array(
				'player_name' => self::getActivePlayerName(),
				'player_id' => $active_player_id,
				'card_ids' => $cardIDGroupB,
				'card_type' => $card_typeB,
				'card_type_arg' => $card_type_argB,
				'player_down' => 'playerDown_B_'
			)
		);
		// Notify all players about the cards played Area C
		self::notifyAllPlayers('playerGoDown',
			'',
			array(
				'player_name' => self::getActivePlayerName(),
				'player_id' => $active_player_id,
				'card_ids' => $cardIDGroupC,
				'card_type' => $card_typeC,
				'card_type_arg' => $card_type_argC,
				'player_down' => 'playerDown_C_'
			)
		);
		
		$cardsByLocation = $this->cards->countCardsByLocationArgs( 'hand' );
		$player_name = self::getActivePlayerName();

		// Notify all players about the cards played
		self::notifyAllPlayers('playerGoDown',
			clienttranslate('${player_name} went down'),
			array(
				'player_name' => self::getActivePlayerName(),
				'player_id' => $active_player_id,
				'joker' => $joker,
				'targetArea' => $targetArea,
				'allHands' => $cardsByLocation
			)
		);
		
		// Clear out the prep areas in the database
		
		$player_id = $active_player_id;

		$sql = "DELETE FROM prepAreas WHERE player_id = '";
		$sql_command = $player_id . "'";
		self::DbQuery( $sql . $sql_command );

		$cpn = self::getActivePlayerName();		
		self::dump( "[bmc] colored player_name", $cpn );

		self::trace("[bmc] GO DOWN DONE!");

		self::setPlayerGoneDown( $active_player_id, 1 /* 0 (not gone down) or 1 (gone down) */ );
		self::trace("[bmc] EXIT playerGoDownFinish");

	}
////////
////////
////////
	function assignExtraJokers ( $cardGroup ){
		self::trace("[bmc] ENTER assignExtraJokers");
		self::dump( "[bmc] cardGroup", $cardGroup );
		// If it's a run and it has jokers then ask the player which values to make the jokers
		
		// For now just exit -otherwise it causes an error on the two error lines noted below
		
		return true;
		
		if ( $this->checkRun( $cardGroup, true )) {
			self::trace("[bmc] It's a run");

			if ( $this->checkforJoker( $cardGroup, true )) {
				self::trace("[bmc] It has joker(s)");
				
				// Identify if there are extra jokers
				
				$jokerCount = 0;
				$thereIsAnAce = false;
				$cardValuesHard = array();
				$jokers = array();
				
				$lowestCard = 13;
				$highestCard = 1;
				
				foreach( $cardGroup as $card ){
					self::dump("[bmc] card: ", $card);
					
					// If a joker then keep track of how many jokers
					// If not a joker then track it as a 'hard' card value
					// Presume ace is low until proven it must be designated as high
					if( $card[ 'type' ] == 5 ) {
						$jokerCount++;
						array_push( $jokers, $card );
						
					} else if( $card[ 'type' ] == 1 ) {
						$thereIsAnAce = true;
						$cardValuesHard[ $card[ "type_arg" ]] = $card[ "type_arg" ];
					} else {
						$cardValuesHard[ $card[ "type_arg" ]] = $card[ "type_arg" ];
						if( $card[ "type_arg" ] < $lowestCard ) {
							$lowestCard = $card[ "type_arg" ];
						}
						if( $card[ "type_arg" ] > $highestCard ) {
							$highestCard = $card[ "type_arg" ];
						}
					}
				}
				self::dump( "[bmc] jokerCount", $jokerCount );
				self::dump( "[bmc] thereIsAnAce", $thereIsAnAce );
				self::dump( "[bmc] lowestCard", $lowestCard );
				self::dump( "[bmc] highestCard", $highestCard );
				self::dump( "[bmc] cardValuesHard", $cardValuesHard );
				
				// Now we know the 'hard' values. Count the gaps. If there are more
				//   jokers than gaps then there are 'extra' jokers.
				
				$firstCard = true;
				$jokerIndex = 0;
				
				for( $position = $lowestCard; $position < 14; $position++ ){
					self::dump( "[bmc] position", $position );
					
					// ignore comparing the first card to any previous
					if( $firstCard ) {
						$firstCard = false;
					} else {
						if( $card[ $position ] ){
							// There's a hard card no need for a joker
							self::dump( "[bmc] hardcard", $card[ $position ] ); // THIS LINE CAUSES ERROR (NULL)
						} else {
							// Assign a joker
							self::dump( "[bmc] assigning a joker", $card[ $position ] ); // THIS LINE CAUSES ERROR (NULL)
							//$jokers[ $jokerIndex ][ 'type_arg' ] = $position;
						}
					}
				}
				
				// Make the player assign any extra jokers
				
			} else {
				self::trace("[bmc] It does not have a joker");
			}
		} else {
			self::trace("[bmc] It's a set");
		}
	}
////////
////////
////////
	function checkSetOrRun ( $cardGroup ) {
		self::trace("[bmc] ENTER checkSetOrRun");
		
		// If true, it means the cardGroup will evaluate true as a set or a run. So the
		//   cards are both a set and a run.

// start new code
		if ( $this->checkSet( $cardGroup )) {
			if ( $this->checkRun( $cardGroup, true )) {
				return true;
			} else {
				return false;
			}
// end new code

		} else {
			return false;
		}
	}
////////
////////
////////
	function tryJokerSwap ( $card_id, $player_id, $boardArea, $boardPlayer) {
		self::trace("[bmc] ENTER tryJokerSwap");

		if ($this->checkSet($this->cards->getCardsInLocation( $boardArea, $boardPlayer ))) {
			// TODO: Might need try / catch here, i'm not sure how BgaUserException works
			self::trace("[bmc] Swapping joker in SET while going down - verify it!");
			
			// Try to play the card for the joker as part of going down
			$mightBeJoker = $this->playCardFinish( $card_id, $player_id, $boardArea, $boardPlayer, true );
			
			// PLAYCARDFINISH PUTS THE JOKER IN THE HAND. So, have playCardFinish return the pulled joker.
			
			self::trace("[bmc] Pulled the joker off the table(set)");

			self::trace("[bmc] Replace with the card.");

			// $cardsInArea = $this->cards->getCardsInLocation( 'hand', $player_id );
			// $mightBeJoker = $this->checkForJoker( $cardsInArea );

			//self::dump("[bmc] cardsInArea:", $cardsInArea);
			self::dump("[bmc] mightBeJoker:", $mightBeJoker);
			//$this->cards->moveCard($mightBeJoker['id'], 'hand', $player_id);

			// Now finish going down
			self::trace("[bmc] EXIT tryJokerSwap(1)");
			return $mightBeJoker;
			
		} else { // Check if run
			self::trace("[bmc] Swapping joker in RUN while going down - verify it!");

			// If the cards w/o joker are still a run then take the joker, if not then don't
			
			$cardsInArea = $this->cards->getCardsInLocation( $boardArea, $boardPlayer);
			
			if ( count( $cardsInArea ) != 0 ) {
				$mightBeJoker = $this->checkForJoker( $cardsInArea );
			} else {
				return false; // Exit early because no down area was selected
			}

			self::dump("[bmc] cardsInArea:", $cardsInArea);
			self::dump("[bmc] mightBeJoker:", $mightBeJoker);
			
			$maybeNewRun = $cardsInArea;
			
			// Potentially remove the joker TODO Aug03: PROTECT THIS UNSET from bool
			unset( $maybeNewRun[ $mightBeJoker['id']] );
			
	        $card = $this->cards->getCard( $card_id );

			// Potentially add the hand card

			$maybeNewRun[$card['id']] = $card; // Keep the index of the new potential card

			if ( $this->checkRun( $maybeNewRun, false )) { // If it's still a run, take the joker
				self::trace("[bmc] Take the joker, haven't used it yet.");

//				if ( $boardCard['type'] == 5 ) { // If a joker is there

				// $usedTheJoker = true;
		
				// TODO: Might need to use the returned joker value from playCardFinish instead of the 'found' one (i.e. do it like sets does it).
				//$this->cards->moveCard($card_id, $boardArea, $boardPlayer);
				$this->playCardFinish( $card_id, $player_id, $boardArea, $boardPlayer, false );
		
				self::trace("[bmc] Replace with the card.");
				$this->cards->moveCard($mightBeJoker['id'], 'hand', $player_id);
		
				// And notify of the joker being 'drawn' from the down area
				$activeTurnPlayer_id = $this->getGameStateValue( 'activeTurnPlayer_id' );
				$this->drawNotify( $mightBeJoker, $player_id, $boardArea, $boardPlayer, $activeTurnPlayer_id );

				// Store the joker and card swapped in case we need to undo
				// self::setGameStateValue( "forJokerCard_id", $card_id );
				// self::setGameStateValue( "forJokerBoardArea", ord(substr( $boardArea, -1) )); // Must store int
				// self::setGameStateValue( "forJokerBoardPlayer", $boardPlayer );
				// self::setGameStateValue( "forJokerTheJoker_id", $mightBeJoker['id'] );
				// self::setGameStateValue( "forJokerPlayerID", $boardPlayer );
			} 

			self::trace("[bmc] Pulled the joker off the table (run)");

			// Now finish going down
			self::trace("[bmc] EXIT tryJokerSwap(2)");
			return $mightBeJoker;
		}
	}
////////
////////
////////
	function checkIfReallyInHand( $cards, $player_id ) {
		$card_type = array();
		$card_type_arg = array();
		foreach( $cards as $card ) {
			self::dump("[bmc] CheckInHand: ", $card);
			
			if( $card['location'] != 'hand' || $card['location_arg'] != $player_id ) {
				throw new BgaUserException( self::_('You cannot play a card that is not in your hand.') );
			} else {
				//self::trace("[bmc] Card is really in hand!");
				array_push($card_type, $card['type']);
				array_push($card_type_arg, $card['type_arg']);
			}
		}
		return array($card_type, $card_type_arg);
	}
////////
////////
////////
	function getColorValueFromId( $ids ) {
		self::dump("[bmc] ENTER getColorValue IDs: ", $ids);
		// self::dump("[bmc] ENTER count: ", count( $ids ));
		// self::dump("[bmc] ENTER [0]: ",   $ids[0]);
		// self::dump("[bmc] ENTER count(reset): ", count( reset($ids)));
		
		
		foreach ( $ids as $id ){
			self::dump("[bmc] ENTER GET A CARDw/ID: ", $id);
			self::dump("[bmc] ENTER GET A CARD: ", $this->cards->getCard( $id ));
		}
		
		if ( $ids[0] != 0 ){
			// self::trace(" ids not empty " );

			$cards = $this->cards->getCards( $ids );
			self::dump("[bmc] cards: ", $cards);
			
			$card_type = array();
			$card_type_arg = array();
			$card_id = array();
			
			foreach( $cards as $key => $card ) {
				self::dump("[bmc] getColorValue: ", $card );
				self::dump("[bmc] key: ", $key );
				
				$card = $this->cards->getCard( $card['id'] );
				self::dump("[bmc] card: ", $card['id']);
				self::dump("[bmc] type: ", $card['type']);
				self::dump("[bmc] type_arg: ", $card['type_arg']);
				
				array_push( $card_type,     $card[ 'type' ]);
				array_push( $card_type_arg, $card[ 'type_arg' ]);
				array_push( $card_id, 	   $key ); // Track the IDs also since these seem to get out of order

			}
			return array( $card_id, $card_type, $card_type_arg );
		} else {
			// self::trace(" ids empty ");
		}
	}
////////
////////
////////
	function checkRun( $cards, $silent ) {
		// A run is 4 or more cards of the same suit with sequential values or
		//   3 cards plus 1 joker. A234 and JQKA are both valid.
		
		// It's not a run if:
		//   It has <3 cards
		//   The non-jokers have non-unique values
		//   The non-jokers have >1 suit
		
		// Set return value to true so if it makes it out, it returns true. But if 
		//   is silently finds it's not a run, return false.
		$crReturnValue = true;
		
//		self::trace("[bmc] ENTER checkRun");
		self::dump("[bmc] ENTER checkRun: ", $silent);
		
		// self::dump("[bmc] checkRun cards: ", $cards);
		
		$cardCount = count( $cards );
		self::dump("[bmc] cardCount: ", $cardCount);

		if ( $cardCount < 4) {
			self::trace("[bmc] checkRun FALSE (not enough cards)");
			return false;
		}

		$jokerCount = $this->countJokers( $cards );

		$nonJokers = array();
		
		// Check if all non jokers are different values
		foreach ( $cards as $card ) {
			if ( $card[ 'type' ] != 5 ) {
				$nonJokers[] = $card[ 'type_arg' ];
			}
		}
		self::dump("[bmc] nonJokers: ", $nonJokers );
		self::dump("[bmc] Jokers: ", $jokerCount );
		
		$valueCount = $this->countNonJokerValues( $cards ) + $jokerCount;

		self::dump("[bmc] valueCount: ", $valueCount );
		if ( $cardCount != $valueCount ) {
			self::dump("[bmc] ThrowingException for NOT A RUN!: ", $cardCount );
			$crReturnValue = false;

			if ( !$silent ) {
				throw new BgaUserException( self::_("Not a run. Run cards must be unique.") );
			}
		} else {
			self::trace("[bmc] Number of cards is correct for a run.");
		}
		$countValues = array_count_values( $nonJokers );

		self::dump("[bmc] countValues: ", $countValues );

		foreach ( $countValues as $type => $qty ) {
			self::dump("[bmc] type:", $type );
			self::dump("[bmc] qty:", $qty );
			
			if ( $qty > 1 ) {
				// If there are 14 cards and 2 aces then allow it
				if (( $cardCount == 14 ) &&
				    ( $type == 1 ) && 
					( $countValues[ 1 ] == 2 )) {
				} else {
					$crReturnValue = false;
					if ( !$silent ) {
						throw new BgaUserException( self::_("Not a run. Run cards must be sequential and unique.") );
					}
				}
			}
		} // 05/09 07:17:17 [notice] [T502009]
		
		// If there is an ace, also create a '14' because ace can be high or low
		
		$aceLowCards = $cards; // PHP makes a completely new copy of an array
		$aceHighCards = $cards; // PHP makes a completely new copy of an array
		
		foreach ( $cards as $card ) {
//			self::dump("[bmc] card: ", $card );
			
			if ( $card[ 'type_arg' ] == 1 ) {
				$aceKey = $card[ 'id' ];
//				self::dump("[bmc] aceKey: ", $aceKey);
				
				$aceHighCards[ $card[ 'id' ]] = [
					'id' => $card['id'],
					'type' => $card['type'],
					'type_arg' => "14", // Ace is considered 14
					'location' => $card['location'],
					'location_arg' => $card['location_arg']
				];
			}
		}
		
		// self::dump("[bmc] New cards (cards):", $cards);
		// self::dump("[bmc] New cards (aceLowCards):", $aceLowCards);
		// self::dump("[bmc] New cards (aceHighCards):", $aceHighCards);

		$tryAceLow  = $this->checkRunWithAce( $aceLowCards );
		$tryAceHigh = $this->checkRunWithAce( $aceHighCards );
		
		self::dump("[bmc] Check Run aceLowCards",  $tryAceLow );
		self::dump("[bmc] Check Run aceHighCards", $tryAceHigh );
		
		if (( $tryAceLow == 0 ) or ( $tryAceHigh == 0 )){
			// This is good, one of them is a run, so just fall through with default==yes
			self::trace("[bmc] One of the combinations with the ace is a run, allow it.");
		} else {
			// Maybe not, so check other conditions
			$aceCheckResult = $tryAceLow + $tryAceHigh ;

			self::dump("[bmc] Check Run aceHighCards", $aceCheckResult );
			
			switch ( $aceCheckResult ) {
				case 0:
				case 1:
					break; // With ace high or low, one is a run and all the same suit; Use default 'true'
				case 2:
					$crReturnValue = false;
					if ( !$silent ) {
						throw new BgaUserException( self::_("Not a run. It doesn't reach!") );
					}
					break;
				case 10:
				case 11:
				case 20:
					$crReturnValue = false;
					if ( !$silent ) {
						throw new BgaUserException( self::_('Not a run. Run cards must all be the same suit.') );
					}
					break;
				default :
					$crReturnValue = false;
					if ( !$silent ) {
						throw new BgaUserException( self::_("Ace Check error (should never happen).") );
					}
					break;
			}
		}

		// self::dump("[bmc] checkRun cards: ", $cards );
		
		self::trace("[bmc] EXIT checkRun. Might be true or false.");
		return $crReturnValue; // Made it through, so the cards are a run
	}
////
////
////
	function checkRunWithAce ( $cards ) {
		// Check all the same suit and that the jokers bridge the gaps
		$cardType = 0;
		$cardValueMax = 0;  // Track the larget and smallest in the group
		$cardValueMin = 20; // Track the larget and smallest in the group
		// $jokerCount = 0; // Count the jokers to know if the cards all reach
		//
		// Don't need to count jokers since we're looking at min, max and card count

		// self::dump("[bmc] checkRunWithAce: cards", $cards );

		foreach ( $cards as $card ) {
			if ( $card['type'] == "5") {
				// Ignore Joker (type == 5)
				// $jokerCount += 1;
			} else {
				if ($card['type_arg'] > $cardValueMax) { // Find the largest card value
					$cardValueMax = $card['type_arg'];
				}
				
				if ($card['type_arg'] < $cardValueMin) { // Find the smallest card value
					$cardValueMin = $card['type_arg'];
				}

		// self::dump("[bmc] Max", $cardValueMax);
		// self::dump("[bmc] Min", $cardValueMin);
				if ( $cardType == 0 ) {
					// Get the suit of the first card which is not a joker
					$cardType = $card['type'];
					// self::dump("[bmc] cardType:", $cardType);
				} else {
					// self::dump("[bmc] card: ", $card);
					if ( $card['type'] != $cardType ) {
						self::trace("[bmc] checkRun FALSE (different suits)");
						//throw new BgaUserException( self::_('Run cards must all be the same suit.') );

						return 10;
					}
				}
			}
		}
		// Made it through, so the cards are all the same suit
		// Check if they are close enough together
		$cardCount = count( $cards );
		self::dump("[bmc] cardCount:", $cardCount );
		self::dump("[bmc] Max:", $cardValueMax );
		self::dump("[bmc] Min:", $cardValueMin );
		// self::dump("[bmc] jokerCount:", $jokerCount );

		if ( $cardValueMax - $cardValueMin + 1  <= $cardCount ) {
			self::trace("[bmc] checkRun TRUE");
			return 0;

		} else {
			self::trace("[bmc] checkRun FALSE (Doesn't reach)");

			return 1;
		}
	}
////
////
////
	function countJokers( $cards ) {
		self::dump("[bmc] ENTER countJokers: ", $cards);
		$jokerCount = 0; // Count the jokers
		foreach ( $cards as $card ) {
			if ( $card['type'] == "5") {
				$jokerCount += 1;
			}
		}
		return $jokerCount;
	}
////
////
////
	function countNonJokerValues( $cards ) {
		//self::dump("[bmc] ENTER countNonJokerValues: ", $cards );
		$nonJokerCount = 0; // Count the unique values of the nonJokers
		$cardValues = [];
		
		foreach ( $cards as $card ) {
			if ( $card[ 'type' ] != "5") {
				if ( !in_array( $card[ 'type_arg' ], $cardValues )) {
				$cardValues[] = $card[ 'type_arg' ];
				}
			}
		}
		$numberOfValues = count( $cardValues );
		
		self::dump("[bmc] ENTER cardValues: ", $cardValues);
		self::dump("[bmc] ENTER numberOfValues: ", $numberOfValues);
		
		return $numberOfValues;
	}
////
////
////
	// function checkForJokerIsOneCard( $card ) {
		// self::dump("[bmc] ENTER check for joker in card: ", $card);
		// if ( $card['type'] == "5") {
			// self::dump("[bmc] EXIT checkforJoker with 1 card", $card);
			// return $card;
		// }
		// self::trace("[bmc] EXIT check for joker is one card (none found)");
		// return false;
	// }
////
////
////
	function checkForJoker( $cards ) {
		self::dump("[bmc] ENTER check for joker in card: ", $cards);

		foreach ( $cards as $card ) {
	//			self::dump("[bmc] checkForJoker card:",  $card );
				//self::dump("[bmc] checkForJoker card type:", $card['type']);
			if ( $card['type'] == "5") {
				self::dump("[bmc] EXIT checkforJoker", $card);
				return $card;
			}
		}

		self::trace("[bmc] EXIT check for joker in cards (none found)");
		return false;
	}
////
////
////
	function checkSet( $cards ) {
		self::trace("[bmc] ENTER checkSet");
		// A set is 3 or more cards of the same number or
		//   2 cards of the same value plus 1 joker.
		self::dump("[bmc] checkSet Cards: ", $cards);
		
		if ( count( $cards ) < 3) {
			self::trace("[bmc] EXIT checkSet: FALSE. Not enough cards.");
			return false;
		}
		
		$cardValue = 0;
		
		foreach( $cards as $card ) {
			if ($cardValue == 0 ) {
				// Get the value of the first card which is not a joker
				if ( $card['type'] == "5") {
					// Ignore Joker (type == 5)
					//self::trace("[bmc] checkSet: Found Joker before others.");
				} else {
					$cardValue = $card['type_arg'];
					//self::dump("[bmc] cardValue:", $cardValue);
				}
			} else {
//				self::dump("[bmc] card: ", $card);
				if ( $card['type'] == "5") {
					// Ignore Joker (type == 5)
					//self::trace("[bmc] checkSet: Found Joker after another.");
				} else if ($card['type_arg'] != $cardValue) {
					self::trace("[bmc] EXIT checkSet: FALSE. Values not the same.");
					return false;
				}
			}
		}
		self::trace("[bmc] EXIT checkSet: TRUE");
		return true; // Made it through, so they are the same
	}
////
////
////
	function playCard( $card_id, $player_id, $boardArea, $boardPlayer ) {
		self::trace( "[bmc] ENTER playCard (from ACTION from JS)" );
		// Validate the player has the card in hand
		// validate the card can be played there
		//   If the target card is a joker, take the joker & replace
		// Move the card(s) around
		// Notify the players
		self::checkAction("playCard");

		// Validate the player has already gone down
		$playerGoneDown = self::getPlayerGoneDown(); // It's an array, one for each player.
		$currentPlayer = $this->getActivePlayerId();

		if ( $playerGoneDown[$currentPlayer] != 1 ) {
			throw new BgaUserException( self::_('You can play only after you go down.') );
		}
		
		$cardsInHand = $this->cards->countCardsByLocationArgs( 'hand' )[$currentPlayer];
		
		if ( $cardsInHand < 2 ) {
			throw new BgaUserException( self::_('You cannot empty your hand.') );
			return;
		}
		
		$this->playCardFinish( $card_id, $player_id, $boardArea, $boardPlayer, false );
		self::trace( "[bmc] EXIT playCard" );
	}
////
////
////
	function playCardMultiple( $card_ids, $player_id, $boardArea, $boardPlayer ) {
		self::trace( "[bmc] ENTER playCardMultiple (from ACTION from JS)" );
		
		// The problem is, even if it's determined that the whole thing is a run, it
		// calls PLAYCARDFINISH which tries to play each card 1 at a time. So, it fails.
		
		// Validate the player has the card in hand
		// validate the card can be played there
		//   If the target card is a joker, take the joker & replace
		// Move the card(s) around
		// Notify the players
		self::checkAction("playCardMultiple");

		// Validate the player has already gone down
		$playerGoneDown = self::getPlayerGoneDown(); // It's an array, one for each player.
		$currentPlayer = $this->getActivePlayerId();

		if ( $playerGoneDown[$currentPlayer] != 1 ) {
			throw new BgaUserException( self::_('You can play only after you go down.') );
		}
		
		$cardsInHand = $this->cards->countCardsByLocationArgs( 'hand' )[$currentPlayer];

		self::dump("[bmc] cardInHand:", $cardsInHand );
		self::dump("[bmc] card_ids:", $card_ids );
		self::dump("[bmc] count(card_ids):", count( $card_ids ));
		
		if (( $cardsInHand - count( $card_ids )) < 1 ) {
			throw new BgaUserException( self::_('You cannot empty your hand.') );
			return;
		}
		
		$boardCards = $this->cards->getCardsInLocation( $boardArea , $boardPlayer );
		self::dump("[bmc] boardCards before:", $boardCards );
		
		$handCards = $this->cards->getCards( $card_ids );
		self::dump("[bmc] handCards:", $handCards );
		
		$boardPlusHandCards = array_merge( $boardCards, $handCards );
		
		self::dump("[bmc] boardPlusHandCards After:", $boardPlusHandCards );
		
		// If all the cards are a run then keep trying to play them until it works
		
		// TODO Oct 2022: Fix this so it plays 56 onto 890*
		// Method: Try to play them in all the orders possible and see if 1 goes through
		
//		$multipleCardsAreRun = $this->checkRun( $boardPlusHandCards, false );

// Start change for playing multiple Nov 2022.
		if ( $this->checkRun( $boardPlusHandCards, true ) == true ) {
			self::trace( "[bmc] Playing multiple on run." );
			foreach( $handCards as $card ) {
				self::dump("[bmc] PlayMultiple:", $card );
				self::dump("[bmc] PlayMultiple:", $card['id'] );
				$this->playCardFinish( $card['id'], $player_id, $boardArea, $boardPlayer, false );
			}
		} else if ( $this->checkSet( $boardPlusHandCards ) == true ) {
			self::trace( "[bmc] Playing multiple on set." );
			foreach( $handCards as $card ) {
				self::dump("[bmc] PlayMultiple:", $card );
				self::dump("[bmc] PlayMultiple:", $card['id'] );
				$this->playCardFinish( $card['id'], $player_id, $boardArea, $boardPlayer, false );
			}
		} else {
			// Throw exception that it's not a set nor a run
			throw new BgaUserException( self::_('Cannot play those cards on that meld.') );
			return;
		}

		self::trace( "[bmc] EXIT playCardMultiple" );
	}
////
////
////
	function getCardNotJoker( $boardArea, $boardPlayer ) {
		self::trace("[bmc] ENTER getCardNotJoker");
		$cardsInArea = $this->cards->getCardsInLocation( $boardArea, $boardPlayer);

		foreach( $cardsInArea as $card ) {
			if( $card['type'] != 5 ) {
				self::dump("[bmc] EXIT getCardNotJoker:", $card);
				return $card;
			} else {
				self::trace("[bmc] Found a joker, keep looking for a non-joker.");
			}
		}
		$card = reset( $cardsInArea ); 
		self::dump("[bmc] All Jokers! Returning one of them:", $card);
		
		self::trace("[bmc] EXIT getCardNotJoker");
		return $card;
	}
////
////
////
	function playCardFinish( $card_id, $player_id, $boardArea, $boardPlayer, $dontSwapForJoker ) {
		self::trace( "[bmc] ENTER playCardFinish" );
		// Validate the player has the card in hand
		// validate the card can be played there
		//   If the target card is a joker, take the joker & replace
		// Move the card(s) around
		// Keep track of number of jokers played
		// Notify the players

		$currentCard = $this->cards->getCard( $card_id );

		list($card_typeA, $card_type_argA) = $this->checkIfReallyInHand( [$currentCard], $player_id );

		self::dump("[bmc] Playing card:", $card_id );
		self::dump("[bmc] Cards on board BOARD AREA:", $boardArea);
		self::dump("[bmc] Cards on board BOARD PLAYER:", $boardPlayer);

		$cardsInArea = $this->cards->getCardsInLocation( $boardArea, $boardPlayer);
		
		self::dump("[bmc] Count of cards in area:", count( $cardsInArea ));
		
		if ( count( $cardsInArea ) > 14 ) { // Could have A234 up to JQKA, so 14 cards is OK
			throw new BgaUserException( self::_('Cannot play there. That board area is full.') );
		}
		
		// self::dump("[bmc] Cards on board CARDS IN AREA:", $cardsInArea );
		// self::dump("[bmc] Card being played CARD TYPE:", $card_typeA );
		// self::dump("[bmc] Card being played CARD TYPE:", $card_typeA[0] );
		// self::dump("[bmc] Card being played CARD TYPE ARG:", $card_type_argA );
		self::dump("[bmc] currentCard:", $currentCard );

		// Count the jokers being played for stats
		// self::incStat( 1, 'jokers_number', $player_id );
		// 08/26/2023

		// if ( $this->checkForJokerIsOneCard( $currentCard )) {
			// self::incStat( 1, 'jokers_number', $player_id );
		// }

		$mightBeJoker = $this->checkForJoker( $cardsInArea );
		self::dump("[bmc] Might Be Joker", $mightBeJoker );
		
		if ( $mightBeJoker ) {
			$boardCard = $mightBeJoker; // Find a joker on the board if possible
		} else { // Get a representative card from the group
			$boardCard = $this->getCardNotJoker( $boardArea, $boardPlayer );
		}

		self::dump("[bmc] boardCard:", $boardCard );
		self::dump("[bmc] area plus player: ", $boardArea . "_" . $boardPlayer);
		
		$usedTheJoker = false;
		
		// TODO: Reduce this section of IFs which has some duplication
		$playWeight = $this->cards->countCardInLocation($boardArea) + 100;

		// If playing same value, or if a joker is there, or if playing a joker, then play
		if ( $this->checkSet( $cardsInArea ) == true ) {
			self::trace("[bmc] Trying to play onto a set.");
			
			if (( $boardCard['type_arg'] === $card_type_argA[0] ) or // If same value
				( $boardCard['type'] == 5 ) 					  or // If board has a joker
				( $card_typeA[0] == 5))							  {  // If playing a joker

				self::trace("[bmc] Playing the card onto the set.");
				
				// If playing a joker then just play it
				if ( $card_typeA[0] == 5 ) {
					self::trace("[bmc] Play joker on set.");
					$this->cards->moveCard( $card_id, $boardArea, $boardPlayer, $playWeight);

					self::incStat( 1, 'jokers_number', $player_id ); // Track joker play for stats

				} else if ($mightBeJoker != false) { 
					self::trace("[bmc] 3771: mightbejoker != false.");

					if ( $this->getCardNotJoker( $boardArea, $boardPlayer )['type_arg'] == $card_type_argA[ 0 ] ) {
						self::trace("[bmc] 3774 getCardNotJoker is true");
						
						// Add new option to not take jokers (uncomment the next line)
						//if ( $this->getGameStateValue( 'allowJokerSwapping' ) == 1 ) { // 0 == No. 1 == Yes.
							
							
							
							
							
							
							
						// Need to also add this in the RUN check area	
							
							
							
							
							
							
							
							
							
							$this->takeTheJoker( $mightBeJoker, $player_id, $card_id, $boardArea, $boardPlayer );
						//}
					} else {
						self::trace("[bmc] 2354: Not same values for set.");
						throw new BgaUserException( self::_('Cannot play that card on that set.') );
					}
				} else {
					// Made it this far, so play it 
					self::trace("[bmc] Play card on set.");
//					$playWeight = $this->cards->countCardInLocation($boardArea) + 100;
					$this->cards->moveCard( $card_id, $boardArea, $boardPlayer, $playWeight);
				}

				// And notify of the played card
				$debug_cards = $this->cards->getCardsInLocation("hand");
				//self::dump("[bmc] Cards In Hand:", $debug_cards );

				self::trace("[bmc] Notify of played card (set)");

				if ( $currentCard[ 'type' ] == 5 ) {
					$value_displayed = 'Joker';
					$color_displayed = '';
					$connector = '';
				} else {
					$value_displayed = $this->values_label[ $currentCard[ 'type_arg' ]];
					$color_displayed = $this->colors[ $currentCard[ 'type' ]][ 'name' ];
					$connector = ' of ';
				}

				$cardsByLocation = $this->cards->countCardsByLocationArgs( 'hand' );
				$player_name = self::getActivePlayerName();

				self::notifyAllPlayers(
					'cardPlayed',
					clienttranslate( '${player_name} Plays: ${value_displayed} ${connector} ${color_displayed}' ),
					array (
						'i18n' => array( 'color_displayed', 'value_displayed', 'connector' ), 
						'card_id' => $card_id,
						'player_id' => $player_id,
						'player_name' => self::getActivePlayerName(),
						'value' => $currentCard ['type_arg'],
						'value_displayed' => $value_displayed,
						'color' => $currentCard ['type'],
						'color_displayed' => $color_displayed,
						'boardArea' => $boardArea,
						'boardPlayer' => $boardPlayer,
						'allHands' => $cardsByLocation,
						'connector' => $connector
					)
				);

			} else {
				self::trace("[bmc] 3782 not same values for set.");
				throw new BgaUserException( self::_('Cannot play that card on that set.') );
			}
		} else if ( $this->checkRun( $cardsInArea, false ) == true ) {
			self::trace("[bmc] Trying to play onto a run.");
			
			// If playing a joker, then just play it
			if ( $card_typeA[0] == 5) {
				self::trace("[bmc] Play the joker.");
				$this->playOnRunAndNotify( $card_id, $boardArea, $boardPlayer, $playWeight, $player_id, $currentCard );
				
				self::incStat( 1, 'jokers_number', $player_id ); // Track joker play for stats

			// if there is no joker on the board, then try to play the card
			} else if ( $mightBeJoker == false) {
				self::trace("[bmc] No joker on board, try to play the card.");
				self::dump("[bmc] cardsInArea: ", $cardsInArea);
				
				$potentialNewRun = $cardsInArea;
				
				 // Add card to area cards to try it as a run
				$potentialNewRun[ $currentCard[ 'id' ]] = $currentCard;
				self::dump("[bmc] checking the potential run is still a run.", $potentialNewRun );
				
				if ( $this->checkRun( $potentialNewRun, false ) == true ) {
					self::trace("[bmc] Playing the card onto the run.");
					
					$this->playOnRunAndNotify( $card_id, $boardArea, $boardPlayer, $playWeight, $player_id, $currentCard );
				} else {
					self::trace("[bmc] With that card, the cards are not a run.");
					throw new BgaUserException( self::_('Cannot play that card on that run.') );
				}
			} else {
				// Plan to add option to not allow joker swapping (except when going down).
				// Check for that and don't swap if not allowed.
				//if ( $this->getGameStateValue( 'allowJokerSwapping' ) == 1 ) { // 0 == No. 1 == Yes.











				self::trace("[bmc] YES joker on board, try to play the card.");
				// If here, then there's a joker on the board.
				// Try adding card and remove the joker; check if a run, if yes, then swap.
				// Try adding card and check if it's a run, if yes then play it.
				$potentialNewRun = $cardsInArea;
				
				unset( $potentialNewRun[ $mightBeJoker[ 'id' ]]);
				$potentialNewRun[ $currentCard[ 'id' ]] = $currentCard;
				
				try {
					self::trace("[bmc] First try playing without the joker.");
					
					$this->checkRun( $potentialNewRun, false );
					
					// If we get to here then it's a run
					self::trace("[bmc] YES can do swap.");

					// Play the card
					$this->playOnRunAndNotify( $card_id, $boardArea, $boardPlayer, $playWeight, $player_id, $currentCard );
					
					// Take the joker
					$this->takeTheJoker( $mightBeJoker, $player_id, $card_id, $boardArea, $boardPlayer );
					
				} catch ( Exception $e ) {
					self::trace("[bmc] NO, try the new card including the board joker.");

					// Add the card back and try it as a run
					$potentialNewRun[ $mightBeJoker[ 'id' ]] = $mightBeJoker;

					self::dump("[bmc] Added joker back:", $potentialNewRun );
					self::dump("[bmc] mightbejoker:", $mightBeJoker );

					if ( $this->checkRun( $potentialNewRun, false ) == true ) {
						$this->playOnRunAndNotify( $card_id, $boardArea, $boardPlayer, $playWeight, $player_id, $currentCard );
					}
				}
			}
		} else {
			self::trace("[bmc] Not a Set and Not a Run!");
				throw new BgaUserException( self::_("Not a Set and Not a Run (shouldn't happen!)."));
		}

//		$buyers = self::getPlayerBuying();
//		self::dump("[bmc] Buyers Status(playCardFinish):", $buyers);
		self::trace( "[bmc] EXIT playCardFinish" );
		return $mightBeJoker; // 
	}
////
////
////
	function playOnRunAndNotify( $card_id, $boardArea, $boardPlayer, $playWeight, $player_id, $currentCard ) {
		self::trace("[bmc] ENTER playOnRunAndNotify.");
		// TODO: Fix the play weight on the board for runs:
		$playWeight = $this->cards->countCardInLocation( $boardArea ) + 100;
		$this->cards->moveCard( $card_id, $boardArea, $boardPlayer, $playWeight );
		
		$cardsByLocationHand  = $this->cards->countCardsByLocationArgs( 'hand' );

		// And notify of the played card
	
		self::trace("[bmc] Notify of played card");

		if ( $currentCard[ 'type' ] == 5 ) {
			$value_displayed = 'Joker';
			$color_displayed = '';
			$connector = '';
		} else {
			$value_displayed = $this->values_label[ $currentCard[ 'type_arg' ]];
			$color_displayed = $this->colors[ $currentCard[ 'type' ]][ 'name' ];
			$connector = ' of ';
		}

		$player_name = self::getActivePlayerName();

		self::notifyAllPlayers(
			'cardPlayed',
			clienttranslate( '${player_name} Played: ${value_displayed} ${connector} ${color_displayed}'),
			array (
				'i18n' => array( 'color_displayed', 'value_displayed', 'connector' ),
				'card_id' => $card_id,
				'player_id' => $player_id,
				'player_name' => self::getActivePlayerName(),
				'value' => $currentCard ['type_arg'],
				'value_displayed' => $value_displayed,
				'color' => $currentCard ['type'],
				'color_displayed' => $color_displayed,
				'boardArea' => $boardArea,
				'allHands' => $cardsByLocationHand,
				'boardPlayer' => $boardPlayer,
				'connector' => $connector
			)
		);
		self::trace("[bmc] EXIT playOnRunAndNotify.");
	}
////
////
////
	function takeTheJoker( $mightBeJoker, $player_id, $card_id, $boardArea, $boardPlayer ) {
		self::trace("[bmc] ENTER Take the joker.");
		$usedTheJoker = true;
		
		$this->cards->moveCard( $card_id, $boardArea, $boardPlayer );

		self::trace("[bmc] Replace with the card.");
		$this->cards->moveCard($mightBeJoker['id'], 'hand', $player_id);

		// And notify of the joker being 'drawn' from the down area
		$activeTurnPlayer_id = $this->getGameStateValue( 'activeTurnPlayer_id' );
		$this->drawNotify( $mightBeJoker, $player_id, $boardArea, $boardPlayer, $activeTurnPlayer_id);

		// Store the joker and card swapped in case we need to undo
		// self::setGameStateValue( "forJokerCard_id", $card_id );
		// self::setGameStateValue( "forJokerBoardArea", ord(substr( $boardArea, -1) )); // Must store int
		// self::setGameStateValue( "forJokerBoardPlayer", $boardPlayer );
		// self::setGameStateValue( "forJokerTheJoker_id", $mightBeJoker['id'] );
		// self::setGameStateValue( "forJokerPlayerID", $boardPlayer );
		self::trace("[bmc] EXIT Take the joker.");
	}
    /*
    Example:

    // function playCard( $card_id )
    // {
        Check that this is the player's turn and that it is a "possible action" at this game state (see states.inc.php)
        // self::checkAction( 'playCard' ); 
        
        // $player_id = self::getActivePlayerId();

        // throw new BgaUserException(self::_("Not implemented: ") . "$player_id plays $card_id");
        
        Add your game logic to play a card there 
        // ...
        
        Notify all players about the card played
        // self::notifyAllPlayers( "cardPlayed", self::_( '${player_name} plays ${card_name}' ), array(
            // 'player_id' => $player_id,
            // 'player_name' => self::getActivePlayerName(),
            // 'card_name' => $card_name,
            // 'card_id' => $card_id
        // ) );
          
    // }
    */
    
//////////////////////////////////////////////////////////////////////////////
//////////// Game state arguments
////////////

    /*
        Here, you can create methods defined as "game state arguments" (see "args" property in states.inc.php).
        These methods function is to return some additional information that is specific to the current
        game state.
    */
    function stWentOut() {
		self::trace("[bmc] ENTER stWentOut");

// todo MAYBE NOT INCLUDE THIS
// Update all the clients after the final discard
		$cardsByLocationHand  = $this->cards->countCardsByLocationArgs( 'hand' );

		$currentHandType = self::getGameStateValue( 'currentHandType' );
		$handTarget = $this->handTypes[ $currentHandType ][ "Target" ];
		self::dump("[bmc] handTarget stEndHand:", $handTarget );
		
		// Notify players to go to the next target hand
		
		$newScores = self::getCollectionFromDb("SELECT player_id, player_score FROM player", true );

		self::notifyAllPlayers(
			"newScores",
			'',
			array(
				'newScores' => $newScores,
				'handTarget' => $handTarget,
				'allHands' => $cardsByLocationHand,
				'currentHandType' => $currentHandType
			)
		);
// END MAYBE NOT INCLUDE THIS


		// Notify players to review their hands and click to continue
		$activeTurnPlayer_id = $this->getGameStateValue( 'activeTurnPlayer_id' );
		$players = self::loadPlayersBasicInfos();
		$player_name = $players[ $activeTurnPlayer_id ][ 'player_name' ];

        self::notifyAllPlayers(
			'wentOut',
			clienttranslate( '${player_name} went out' ),
			array(
				'player_id'   => $activeTurnPlayer_id,
				'player_name' => $player_name
			)
		); 

		// Check end of game condition here. Message and route the players accordingly.

        // Next hand target
		$gameLengthOption = self::getGameStateValue( 'gameLengthOption' );
		self::dump( "[bmc] gameLengthOption:", $gameLengthOption );

		$currentHandType = $this->getGameStateValue( 'currentHandType' );
		self::dump( "[bmc] currentHandType:", $currentHandType );

		self::incGameStateValue( 'currentHandType', 1 );
		$currentHandType = $this->getGameStateValue( 'currentHandType' );
		self::dump( "[bmc] currentHandType:", $currentHandType );
		
		$countHandTypes = count( $this->handTypes );

		self::dump( "[bmc] countHandTypes:", $countHandTypes );

		if ( $currentHandType >= $countHandTypes ) {
			self::debug("[bmc] Game Over!");
			$scoreMessage = "Game Over!";
			$this->calcDisplayScoreDialog( $scoreMessage );
			$this->gamestate->setAllPlayersNonMultiactive( 'endgame' );
			//$this->game->playerHasReviewedHand();
			// $this->gamestate->setAllPlayersNonMultiactive( 'endgame' );
		} else {
			self::debug("[bmc] On To The Next!");
			$scoreMessage = "On to the next!";
			$this->calcDisplayScoreDialog( $scoreMessage );
			$this->gamestate->setAllPlayersMultiactive();
		}
		self::debug("[bmc] EXIT stWentOut");
	}
////
////
////
	function calcDisplayScoreDialog( $scoreMessage ) {
		self::trace("[bmc] ENTER calcDisplayScoreDialog");
		
        // Count and score points, then end the game or go to the next hand.
        $players = self::loadPlayersBasicInfos();
        // Cards 2 - 9 are 5 points each
		// Cards 10, J, Q, K are 10 points each
		// Cards A are 15 points each
		// Cards Joker is 20 points each

		$player_to_points = array ();
		foreach ( $players as $player_id => $player) {
            $player_to_points[ $player_id ] = 0;
		}
		$cards = $this->cards->getCardsInLocation("hand");
		
		foreach ( $cards as $card ) {
			$player_id = $card[ 'location_arg' ];
			self::dump("[bmc] Scoring: ", $card );
			if ( $card[ 'type' ] >= 1 and $card[ 'type' ] <= 4) { // If non-Joker
				switch ( true ) {
					case ( $card[ 'type_arg' ] >= 2 and $card[ 'type_arg' ] <= 9 ): // 5 points
						self::trace("[bmc] 2-9");
						$player_to_points[ $player_id ] += 5;
						break;
					case ( $card[ 'type_arg' ] >= 10 and $card[ 'type_arg' ] <= 13 ): // 10 points
						self::trace("[bmc] 10,J,Q,K");
						$player_to_points[ $player_id ] += 10;
						break;
					case ( $card[ 'type_arg' ] == 1 ): // 15 points	
						self::trace("[bmc] Ace");
						$player_to_points[ $player_id ] += 15;
						break;
				}
			} else { // It must be a joker, 20 points
				self::trace("[bmc] Joker");
				$player_to_points [$player_id] += 20;
			}
		}

        // Apply scores to players
        foreach ( $player_to_points as $player_id => $points ) {
            if ( $points != 0 ) {
                $sql = "UPDATE player SET player_score=player_score-$points  WHERE player_id='$player_id'";
                self::DbQuery( $sql );
                $point_number = $player_to_points[ $player_id ];

				$player_name = $players[ $player_id ][ 'player_name' ];

                self::notifyAllPlayers(
					"points",
    				clienttranslate( '${player_name} got ${nbr} points' ),
					array (
                        'player_id' => $player_id,
						'player_name' => $player_name,
                        'nbr' => $point_number
						)
				);
            } else {
		        // No points lost (just notify)
				$player_name = $players[ $player_id ][ 'player_name' ];
				
                self::notifyAllPlayers(
					"points",
					clienttranslate( '${player_name} got zero points'),
					array (
                        'player_id' => $player_id,
						'player_name' => $player_name
					)
				);
            }
        }
		
		// Get the score totals
		$pn = array();
		$sql = "SELECT player_id, player_score FROM player ";
		$pn = self::getCollectionFromDB($sql, true);
	
		self::dump( "[bmc] pn: ", $pn );

		// Show the scoring dialog box
		
		$firstRow = array( '' );
		$secondRow = array( self::_( 'This Hand' ));
		$thirdRow = array( self::_( 'Total' ));

        foreach( $players as $player_id => $player ) {
            $firstRow[] = array( 'str' => '${player_name}',
                                 'args' => array( 'player_name' => $player[ 'player_name' ] ),
                                 'type' => 'header'
                               );
			$secondRow[] = - $player_to_points[ $player_id ];
			$thirdRow[] = $pn[ $player_id ];
        }
        $table = array( 
			$firstRow,
			$secondRow,
			$thirdRow
		);
		self::dump( "[bmc] table: ", $table );
		$player_id = $this->getCurrentPlayerId();

		$activeTurnPlayer_id = $this->getGameStateValue( 'activeTurnPlayer_id' );

		// Show the right message when the hand ends
		// $outReason = 'SomeoneWentOut'; // 0
		// $outReason = 'DeckOverShuffled'; // 1
		// $outReason = 'AllCardsPlayed'; // 2
		$outReason = self::getGameStateValue( 'outReason' );
			
		if( $outReason == 1 ) {
			$outMsg1 = clienttranslate( "Deck has been shuffled 5 times. Ending the hand." );
			$outMsg2 = clienttranslate( $outMsg1 );
		} else if ($outReason == 2 ) {
			$outMsg1 = clienttranslate( "All playable cards have been played. Ending the hand." );
			$outMsg2 = clienttranslate( $outMsg1 );
		} else { // Someone went out normally
			$outMsg1 = clienttranslate( "Woot! You went out! You want the most positive score." );
			$outMsg2_player = $players[ $activeTurnPlayer_id ][ 'player_name' ];
			$outMsg2_msg    = clienttranslate( " went out. You want the most positive score." );
			
			$outMsg2 = $outMsg2_player . $outMsg2_msg;
			
//082023
			// $outMsg2_raw  = self::_( "Bummer! " );
			// $outMsg2a_raw = self::_( " went out. You want the most positive score." );

			// $outMsg2 = $outMsg2_raw . $outMsg2_player . $outMsg2a_raw;

//			. $outMsg2_player . " went out! You want the most positive score:";
//			$outMsg2 = self::_( $outMsg2_raw );
		}

		$titleMessage = clienttranslate( $outMsg1 );


		$playerOut = $players[ $activeTurnPlayer_id ][ 'player_name' ];
		// $otherMessage = $outMsg2;
		
		// Show a dialog of the scores for each player for this hand
        foreach ( $player_to_points as $player_id => $points ) {
			if ( $player_id == $activeTurnPlayer_id ) {
				$this->notifyPlayer(
					$player_id,
					"tableWindow", '', array(
						"id" => 'handScoring',
						"title" => $outMsg1,
						"table" => $table,
						"closing" => clienttranslate( $scoreMessage )
					)
				); 
			} else {
				$this->notifyPlayer(
					$player_id,
					"tableWindow", '', array(
						"id" => 'handScoring',
						"title" => $outMsg2,
						"table" => $table,
						"closing" => clienttranslate( $scoreMessage )
					)
				); 
			}
		}
		self::trace("[bmc] EXIT calcDisplayScoreDialog");
	}
////
////
////
	function playerHasReviewedHand() {
		self::trace("[bmc] playerHasReviewedHand");
		// May not need to pass the player_id to the function
		self::checkAction('playerHasReviewedHand');

		$player_id = $this->getCurrentPlayerId(); // CURRENT!!! not active
		 
        self::notifyAllPlayers(
			'wentOut',
			'',
			array(
				'ackPlayer' => $player_id
			)
		); 
		// Deactivate player; if none left, transition to next '' state
		// IF THIS LINE IS NOT THERE THE GAME WON'T GO TO THE NEXT HAND
		$this->gamestate->setPlayerNonMultiactive( $player_id, 'playerHasReviewedHand' );
	}
////
////
////
	function stNewHand() {
		self::debug("[bmc] ENTER stNewHand");
		
        // Take back all cards (from any location => null) to deck
        $this->cards->moveAllCardsInLocation( null, "deck" );

		$cardsInDeck = $this->cards->getCardsInLocation( 'deck' );
		
        // Shuffle deck
        $this->cards->shuffle( 'deck' );
		
		self::setGameStateValue( 'shuffleCount', 0 ); // Reset the shuffle count every hand

        // Deal some cards to each players
        $players = self::loadPlayersBasicInfos();
		
		// Deal 10 or 11 or 12 cards to each player
		// Put 1 card in the discard pile
		// Put the rest into the draw deck
		// Notify players of the situation
		
		$currentHandType = $this->getGameStateValue( 'currentHandType' );
		self::dump( "[bmc] currentHandType:", $currentHandType );
		self::dump( "[bmc] this->handTypes[]:", $this->handTypes );

		$alwaysDeal11 = self::getGameStateValue( 'alwaysDeal11' );

		if ( $alwaysDeal11 == 1 ) {
			$qtyToDeal = 11;
		} else {
			$qtyToDeal = $this->handTypes[ $currentHandType ][ "deal" ];
		}
		
		self::dump( "[bmc] qtyToDeal:", $qtyToDeal );

		// If testing, use cards specifically for testing purposes
		//$presetSetupHands = false;
		$presetSetupHands = true;
		
		if ( $presetSetupHands ) { //
			self::presetHands( $players, false ); // debug true or false
		} else {
			// Pick cards from the shuffled deck

			foreach ( $players as $player_id => $player ) {
				$this->cards->pickCards( $qtyToDeal, 'deck', $player_id );
				self::setPlayerGoneDown( $player_id, 0 ); /* 0 (not gone down) or 1 (gone down) */
				
				if ( $this->getGameStateValue( 'enableWishList' ) == 1 ) { // 0 == No. 1 == Yes.
					$this->disableWishList( $player_id );
				}
			}
		}
		
		// Disable everyone's wish list between hands
		foreach ( $players as $player_id => $player ) {
			if ( $this->getGameStateValue( 'enableWishList' ) == 1 ) { // 0 == No. 1 == Yes.
				$this->disableWishList( $player_id );
			}
		}

		// Put 1 card from the deck into the discard pile and give it a starting weight of 100
		$this->cards->moveCard( $this->cards->getCardOnTop ( 'deck' )[ 'id' ], 'discardPile', 100); 
		
		// The rest of the cards are in 'deck'

		//Notify all players of their cards plus the deck and the discard pile
		$handTarget = $this->handTypes[$currentHandType]["Target"]; // Pull the description
		
		self::dump("[bmc] currentHandType handTarget stNewHand:", $handTarget);
		
		$cardsByLocation = $this->cards->countCardsByLocationArgs( 'hand' );

		$discardSize = count( $this->cards->countCardsByLocationArgs( 'discardPile' ));

		$drawDeckSize = count( $this->cards->countCardsByLocationArgs( 'deck' ));

		self::setGameStateValue( 'discardSize', $discardSize );

		self::trace( "[bmc] stNewHand clearing buyers.");
		// Clear the buyers
		$this->clearBuyers();
		
		// Clear out the BUY counters for all players
		self::clearPlayersBuyCount();

		$buyCount = self::getPlayersBuyCount();
		self::dump("[bmc] buyCount 4313:", $buyCount);

		// Determine who is the next dealer: It's the next in the associative array
		$playerOrder = self::getNextPlayerTable();

		$dealer = $this->getGameStateValue( 'dealer' );
		self::dump('[bmc] newhand dealer:',  $dealer );

		// Change the dealer
		if ( $dealer == 0 ) { // If it ever got set to zero, choose the first real number
			self::setGameStateValue( 'dealer', $playerOrder[ $playerOrder[ $dealer ]]);
		} else {
			self::setGameStateValue( 'dealer', $playerOrder[ $dealer ]);
		}
		
		$dealer_name = '<span style="color:#' . $players[ $dealer ]["player_color"] . ';">' . $players[ $dealer ]["player_name"] . '</span>';

		$this->gamestate->changeActivePlayer( $playerOrder[ $dealer ] );
		
		self::setGameStateValue( 'activeTurnPlayer_id', $playerOrder[ $dealer ] );

		$dpCard = $this->cards->getCardsInLocation( 'discardPile' );
		self::dump("[bmc] dpCard:", reset( $dpCard )[ 'id' ]);

		$currentCard = $this->cards->getCard( reset( $dpCard )[ 'id' ] );

		self::dump("[bmc] currentCardInDP:", $currentCard);

		if ( $currentCard[ 'type' ] == 5 ) {
			$value_displayed = 'Joker';
			$color_displayed = '';
			$connector = '';
		} else {
			$value_displayed = $this->values_label[ $currentCard[ 'type_arg' ]];
			$color_displayed = $this->colors[ $currentCard[ 'type' ]][ 'name' ];
			$connector = ' of ';
		}

		// Update the hand count number when there is a new hand
		
		$updCurrentHandType = self::getGameStateValue( 'currentHandType' );
		$updTotalHandCount = count( $this->handTypes );
		
		self::notifyAllPlayers( // Including spectators
			'newHand',
			clienttranslate('New Hand! ${dealer} dealt the cards. New target is ${handTarget}. In Discard Pile: ${value_displayed} ${connector} ${color_displayed}'),
			array(
				'i18n' => array( 'handTarget', 'color_displayed', 'value_displayed', 'connector' ), 

				'deck' => array_keys($this->cards->getCardsInLocation( 'deck' )),
				'discardPile' => $this->cards->getCardsInLocation( 'discardPile' ),
				'discardSize' => $discardSize,
				'handTarget' => $handTarget,
				'allHands' => $cardsByLocation,
				'buyCount' => $buyCount,
				'dealer' => $dealer_name,
				'drawDeckSize' => $drawDeckSize,
				'updCurrentHandType' => $updCurrentHandType,
				'updTotalHandCount' => $updTotalHandCount,
				'value_displayed' => $value_displayed,
				'color_displayed' => $color_displayed,
				'connector' => $connector
			)
		);
		
//TODO: Here either add another notification to ALL players with a non-empty hand (so the JS triggers). Or/and change the JS function notif_newhand that all players have checked in and dealt cards.

		foreach ( $players as $player_id => $player ) {
			self::notifyPlayer(
				$player_id,
				'newHand',
				'',
				array(
					'hand' => $this->cards->getPlayerHand( $player_id ),
					'deck' => array_keys($this->cards->getCardsInLocation( 'deck' )),
					'discardPile' => $this->cards->getCardsInLocation( 'discardPile' ),
					'handTarget' => $handTarget,
					'allHands' => $cardsByLocation,
					'buyCount' => $buyCount,
					'dealer' => $dealer_name,
					'dealer_id' => $dealer,
					'updCurrentHandType' => $updCurrentHandType,
					'updTotalHandCount' => $updTotalHandCount,
					'discardSize' => $discardSize,
					'drawDeckSize' => $drawDeckSize
				)
			);
		}

		// Go to the next game state (draw) with all players active
		
		self::setGameStateValue( "previous_player_id", 0 ); // After the deal everyone else can buy.

		self::debug("[bmc] (almost) EXIT stNewHand");
		
	    $this->gamestate->nextState("");	
    }
////
////
////
// function stCheckEmptyDeck() {
	function checkEmptyDeck() { // Just make this a function to shuffle the deck if needed
//		self::trace("[bmc] ENTER stCheckEmptyDeck");
		self::trace("[bmc] ENTER checkEmptyDeck");
		
		$activePlayerId = $this->getActivePlayerId();
		self::dump("[bmc] activePlayerId:", $activePlayerId );
		
		$countCardsInDeck = count( $this->cards->countCardsByLocationArgs( 'deck' ));

//		$countCardsInDeck = $this->cards->countCardInLocation( 'deck' );
		self::dump("[bmc] Card in deck:", $countCardsInDeck );
		
		if ( $countCardsInDeck == 0 ) {
			$discards = $this->cards->getCardsInLocation('discardPile');
			
			self::dump("[bmc] Shuffling. Discards: ", $discards);
			//self::dump("[bmc] card on top:", $this->cards->getCardOnTop ( 'deck' )[ 'id' ]); // TODO Aug03: Throws undefined offset, so commenting it out
			
			$card_ids = array_keys($discards);

			self::dump("[bmc] card_ids: ", $card_ids);
			
			$this->cards->moveCards($card_ids, 'deck');
	
			$this->cards->shuffle('deck');

			$shuffleCount = self::incGameStateValue( 'shuffleCount', 1 ); // Keep track of shuffles

			// Trigger the auto-shuffle by trying to draw a card:
			// Put 1 card from the deck into the discard pile and give it a starting weight of 100
			
			self::notifyAllPlayers(
				"deckShuffled",
				clienttranslate( 'Discard pile shuffled into deck' ),
				array(
					'deck' => array_keys( $this->cards->getCardsInLocation('deck'))
				)
			);
		}
		
		$drawDeckSize = count( $this->cards->countCardsByLocationArgs( 'deck' ));
		
		self::trace("[bmc] EXIT (true) stCheckEmptyDeck");
	}
////
////
////
	function stDrawDeck() {
		self::trace( "[bmc] ENTER stDrawDeck:" );
        $this->gamestate->nextState("");
		self::trace( "[bmc] EXIT stDrawDeck:" );
	}
////
////
////
	// function stBuyTry() {
		// self::trace( "[bmc] ENTER stBuyTry:" );
   		// self::setPlayerBuyingGS( $player_id, 2 ); // (0==unknown, 1==Not buying 2==Buying)

		// self::trace( "[bmc] EXIT stBuyTry:" );
	// }
////
////
////
	function stDrawDiscard() {
		self::trace( "[bmc] ENTER stDrawDiscard:" );
        $this->gamestate->nextState("");
		self::trace( "[bmc] EXIT stDrawDiscard:" );
	}
//	function stResolveBuyers() {
//		self::trace( "[bmc] ENTER stResolveBuyers:" );
////
////
////
	function resolveBuyers() {
		self::trace( "[bmc] ENTER resolveBuyers:" );
		
		// Retrieve the buying status gathered during this player's turn
		
		$this->checkEmptyDeck(); // Make sure the deck has cards

		// If source is deck or discard then resolve appropriately.
		// If not either of those then stay in this state since we're swapping a joker

		$drawSourceValue = self::getGameStateValue( 'drawSourceValue' );

		self::dump("[bmc] drawSourceValue:", $drawSourceValue );
		
		// Clear the variable for the next player, because this discard has been handled
		self::setGameStateValue( 'drawSourceValue', 2 );
		
		$buyCount = self::getPlayersBuyCount();
		self::dump("[bmc] buyCount:", $buyCount );

		$someoneIsBuying = false;
		
		$buyers = self::getPlayerBuying();
		// $buyers = self::getPlayerBuyingGS();

		self::dump("[bmc] buyers(resolveBuyers):", $buyers);

		$players = self::loadPlayersBasicInfos();

		$buyingPlayers = [];
		
		foreach( $buyers as $player_id => $buyChoice ) {
			self::dump("bmc] player_id: ", $player_id);
			self::dump("bmc] buyChoice: ", $buyChoice);
			
			if ( $buyChoice == 2 ) { // 0=Not decided, 1=Not buying, 2=Buying
				$someoneIsBuying = $player_id ;
				$buyingPlayers[] = $players[ $player_id ][ 'player_id' ];
				}
		}
		// self::dump("[bmc] someoneIsBuying: ", $someoneIsBuying);
		// self::dump("[bmc] buyingPlayers: ", $buyingPlayers);

		// drawSource Sources:
		// 0 == 'deck' (buyer gets it + 1 down card; Increment buy counter)
		// 1 == 'discardPile' (buyer gets nothing)
		// 2 == Other sources (other conditions like playing a card for a joker)
	
		if ( $drawSourceValue == 0 ) {
			self::trace( "[bmc] TurnPlayer Drawing from deck, so a buy will go through if it exists.");
			
			//self::dump("[bmc] Buyers Status(stResolveBuyers):", $buyers);
			
//				throw feException("Someone is buying but no one requested!");
			if ( $someoneIsBuying != false ) {
				
				$playerOrder = self::getNextPlayerTable();

				self::dump( "[bmc] playerOrder: ", $playerOrder );

// Maybe delete these 5, moved to waitforall
						// Find the right buyer - start with the player after the active player
						$activeTurnPlayer_id = self::getGameStateValue( 'activeTurnPlayer_id' );
				
						self::dump( "[bmc] activeTurnPlayer_id(resolveBuyers):", $activeTurnPlayer_id );
						self::dump( "[bmc] findSTART:", $playerOrder[ $activeTurnPlayer_id ] );
				
						$buyer_id = $this->findBuyer( $buyingPlayers, $playerOrder[ $activeTurnPlayer_id ] );
				
				self::setGameStateValue( 'theBuyer', $buyer_id );
				
				self::dump( "[bmc] buyer_id Function Return:", $buyer_id );

				// If there is a buyer then move the cards and notify everyone
				
				if ( $buyer_id != null ) {
					self::decPlayerBuyCount( $buyer_id );

					self::incStat( 1, 'buys_number', $buyer_id );

					self::setGameStateValue( 'findBuyerFailsafe', 0 );

					self::clearPlayersBuying();

					$buyCount = self::getPlayersBuyCount();

					self::dump("bmc] 4373: ", $buyCount);

					//Move the cards for the buyer (the turnPlayer will get their cards in drawCard)
					
					// Notify of the deck card (i.e. the price to pay for the discarded card)
					$currentCard = $this->cards->getCardOnTop( 'deck' );
					self::dump("bmc] Card from deck: ", $currentCard);
					$this->cards->moveCard( $currentCard[ 'id' ], 'hand', $buyer_id );
				
					$this->drawNotify( $currentCard, $buyer_id, 'deck', $buyer_id, $buyer_id );

					// Notify of the discarded card (notify the buyer of the details, not the current turn player
					$currentCard = $this->cards->getCardOnTop( 'discardPile' );
					self::dump("bmc] Card Bought: ", $currentCard);
					$this->cards->moveCard( $currentCard[ 'id' ], 'hand', $buyer_id );
					
					$this->drawNotify( $currentCard, $buyer_id, 'discardPile', $buyer_id, $buyer_id );
					
					if ( $currentCard[ 'type' ] == 5 ) {
						$value_displayed = 'Joker';
						$color_displayed = '';
						$connector = '';
					} else {
						$value_displayed = $this->values_label[ $currentCard[ 'type_arg' ]];
						$color_displayed = $this->colors[ $currentCard[ 'type' ]][ 'name' ];
						$connector = ' of ';
					}
					
					$cardsByLocation = $this->cards->countCardsByLocationArgs( 'hand' );

					$players = self::loadPlayersBasicInfos();
					
					$player_name = $players[ $buyer_id ][ 'player_name' ];
					
					self::notifyAllPlayers(
						'playerBought',
						clienttranslate( '${player_name} Bought: ${value_displayed} ${connector} ${color_displayed}' ),
						array (
							'i18n' => array( 'color_displayed', 'value_displayed', 'connector' ), 
							'color_displayed' => $color_displayed,
							'value_displayed' => $value_displayed,
							'player_id' => $buyer_id,
							'buyCount' => $buyCount,
							'player_name' => $players[ $buyer_id ][ 'player_name' ],
							'allHands' => $cardsByLocation,
							'connector' => $connector
						)
					);

					// This is usually fired from JS, but need to give the player an entry in their log that the wishlist is disabled
					
					if ( $this->getGameStateValue( 'enableWishList' ) == 1 ) { // 0 == No. 1 == Yes.
						$this->disableWishList( $buyer_id );	 
					}
				}
			} else {
				// There is no buyer
				self::setGameStateValue( 'theBuyer', 0 );
			}
			self::setGameStateValue( 'findBuyerFailsafe', 0 );

			// Drawing from deck, so make sure it's not empty
//			$this->gamestate->nextState("checkEmptyDeck");

		} else if ( $drawSourceValue == 1 ) {
			self::trace( "[bmc] TurnPlayer Drawing from discard, so buy will NOT go through.");
			
			// Set players buy status to NOT BUY
			$players = self::loadPlayersBasicInfos();
			foreach ( $players as $player_id => $player ) {
				self::setPlayerBuying(   $player_id, 1 );
				// self::setPlayerBuyingGS( $player_id, 1 );
			}

			$players = self::loadPlayersBasicInfos();

			foreach ( $buyingPlayers as  $pid ) {
				if ( $someoneIsBuying != false ) {
					self::notifyAllPlayers(
						'playerDidNotBuy',
						clienttranslate( '${buyingPlayerName} tried but could not buy' ),
						array (
							'buyingPlayers' => $buyingPlayers,
							'buyingPlayerName' => $players[ $pid ][ 'player_name' ]
						)
					);
				}
			}
		} else {
			self::trace( "[bmc] Resolve Buyers Other path" );
		}
		self::trace( "[bmc] EXIT (truly) ResolveBuyers:" );
	}
/////
/////
/////
	function stShowBUYButtons() {
		self::trace( "[bmc] ENTER stShowBUYButtons:" );
		
		// Check the real state
		$state = $this->gamestate->state();
		//self::dump("[bmc] state:", $state);
		
		$this->gamestate->setAllPlayersMultiactive();
		
		// Find the previous player (who discarded) make them not active.
		
		$activeTurnPlayer_id = self::getGameStateValue( 'activeTurnPlayer_id' );
		
		$discardingPlayer_id = $this->getPlayerBefore( $activeTurnPlayer_id );
		
		self::dump("[bmc] activeTurnPlayer_id(stShowBUYButtons):", $activeTurnPlayer_id );
		self::dump("[bmc] discardingPlayer_id:", $discardingPlayer_id );

		// If the dealer cannot buy the card, then uncomment this. If they are OK to buy then comment it out.
//		$this->gamestate->setPlayerNonMultiactive( $discardingPlayer_id, 'discardCard' );

		//$this->clearBuyers();

		// TODO: Might have to allow for dealer to buy here in the first deal:
		self::setPlayerBuying(   $activeTurnPlayer_id, 1 ) ; // 1 = not buying, they can get it for free
		// self::setPlayerBuyingGS( $activeTurnPlayer_id, 1 );

		$skipFirstDeal = self::getGameStateValue( 'skipFirstDeal' );
		
		if ( $skipFirstDeal == 0 ) {  // 0 is false. 1 is true. It seems to want integers.
			self::setPlayerBuying(   $discardingPlayer_id, 1 ) ; // 1 = not buying, they just discarded it
			// self::setPlayerBuyingGS( $discardingPlayer_id, 1 );
			
			// Discarding player is no longer active
			$this->gamestate->setPlayerNonMultiactive( $discardingPlayer_id, '' );
		
		} else {
			self::setGameStateValue( 'skipFirstDeal' , 0 ); // 0 is false. 1 is true. It seems to want integers.
		}
		
		$APL = $this->gamestate->getActivePlayerList();
		self::dump( "[bmc] APL(stShowBUYButtons):", $APL);
		//exit(0);
		
		// Set the # of cards in deck and discard pile into game states instead of having buyers hit the database, to avoid deadlock

		$countDeck = count( $this->cards->countCardsByLocationArgs( 'deck' ) );
		$countDiscardPile = count ($this->cards->countCardsByLocationArgs( 'discardPile' ) );

		self::setGameStateValue( 'countDeck', $countDeck );
		self::setGameStateValue( 'countDiscardPile', $countDiscardPile );

		self::trace( "[bmc] EXIT stShowBUYButtons:" );
	}
/////
/////
/////
	function notBuyRequest( $player_id ) { 
		self::dump("[bmc] ENTER NotBuyRequest:", $player_id );
		self::setPlayerBuying( $player_id, 1 ); // (0==unknown, 1==Not buying 2==Buying)
		// self::setPlayerBuyingGS( $player_id, 1 ); // (0==unknown, 1==Not buying 2==Buying)
		$this->notifyPlayerWantsToNotBuy( $player_id );
		self::trace("[bmc] EXIT NotBuyRequest" );
	}
/////
/////
/////
	function submitWishList( $player_id, $wishList_type, $wishList_type_arg ) {
		self::trace("[bmc] ENTER submitWishList");
		self::dump("[bmc] wishList_type:", $wishList_type);
		self::dump("[bmc] wishList_type_arg:", $wishList_type_arg);

		// Check if really a player or a spectator
		$players = self::loadPlayersBasicInfos();
        $playerIDList = [];

		foreach ( $players as $playerIDOnly ) {
			$playerIDList[] = $playerIDOnly[ "player_id" ];
		}

		// self::dump("[bmc] player_id:", $player_id);
		// self::dump("[bmc] players from submitWishList:", $playerIDList);
		// self::dump("[bmc] in_array:", in_array( $player_id, $playerIDList));

		if ( in_array( $player_id, $playerIDList )) {
			// Delete all entries for the wishList for that player
			
			$sql = "DELETE FROM wishList WHERE player_id = '";
			$sql_command = $player_id . "'";
			self::DbQuery( $sql . $sql_command );

			$wishList = [];
			
			$index = 0;
			
			$numberOfBuys =  self::getGameStateValue( 'numberOfBuys' );

			if ( $numberOfBuys != 1 ) { // 0 == 3; 1 == Infinite buys
				$sql = "SELECT player_id, buy_count FROM player ";
				$buy_count = self::getCollectionFromDB( $sql, true );

				self::dump("[bmc] buy_count[ player_id ] (buyRequestFinish): ", $buy_count );
				
				if ( $buy_count[ $player_id ] < 1 ) {
					throw new BgaUserException( self::_("All your wishes for this hand have already come true!") );
					self::trace( "[bmc] BGA Exception: Cannot buy any more(buyRequestFinish)" );

				}
			} // If it > 0 then keep processing the buy
		
			// $testPlayerHandArray[3] = array(
				// 14 => array(
					// 'type' => '3', // Suit
					// 'type_arg' => '13' // Value
					// )
				// );

			foreach( $wishList_type as $wlt ){
				$wishList[] = array(
					'type' => $wishList_type[ $index ],
					'type_arg' => $wishList_type_arg[ $index ] );
				$index++;
			}

	//		$wishList = array_combine( $wishList_type, $wishList_type_arg );

			self::dump("[bmc] wishList_total:", $wishList);

			foreach( $wishList as $wl ) {
				self::dump("[bmc] wishList type:", $wl['type']);
				self::dump("[bmc] wishList type_arg:", $wl['type_arg']);

				$sql = "INSERT INTO wishList (player_id, card_type, card_type_arg) VALUES ";
				$sql_command = "( " . $player_id . "," . $wl['type'] . "," . $wl['type_arg'] . " )";
				
				self::DbQuery( $sql . $sql_command );
			}

			$this->notifyPlayer(
				$player_id,
				"wishListSubmitted",
				clienttranslate( 'Your wish list was received and is active.' ),
				array (
					'player_id' => $player_id
				)
			); 
		}
		self::trace("[bmc] EXIT submitWishList");
	}
////
////
////
	function savePrep( $player_id, $area_A_Items, $area_B_Items, $area_C_Items, $area_J_Items ) {
		self::trace("[bmc] ENTER savePrep");
		self::dump("[bmc] player_id:", $player_id);
		self::dump("[bmc] areaA:", $area_A_Items );
		self::dump("[bmc] areaB:", $area_B_Items );
		self::dump("[bmc] areaC:", $area_C_Items );
		self::dump("[bmc] areaJ:", $area_J_Items );

		$sql = "DELETE FROM prepAreas WHERE player_id = '";
		$sql_command = $player_id . "'";
		self::DbQuery( $sql . $sql_command );

		$sql = "INSERT INTO prepAreas (player_id, areaA, areaB, areaC, areaJ) VALUES ";
		$sql_command = "( " . $player_id . ", '" . 
			implode( ",", $area_A_Items ) . "', '" . 
			implode( ",", $area_B_Items ) . "', '" . 
			implode( ",", $area_C_Items ) . "', '" . 
			implode( ",", $area_J_Items ) . "' )";

		self::dump( "[bmc] sql:", $sql . $sql_command );

		self::DbQuery( $sql . $sql_command );

		self::notifyPlayer(
			$player_id,
			'savePrepDone',
			clienttranslate("Your prep areas were saved"),
			array(
				'player_id' => $player_id,
			)
		);
		self::trace("[bmc] EXIT savePrep");

	}
////
////
////
	function loadPrep( $player_id ) {
		self::trace("[bmc] ENTER loadPrep");
		self::dump("[bmc] player_id:", $player_id);
	
		$sql = "SELECT areaA areaA, areaB areaB, areaC areaC, areaJ areaJ FROM prepAreas WHERE player_id = '";
		$sql_command = $player_id . "'";
		
		self::dump( "[bmc] sql:", $sql . $sql_command );

		$prepListAll = self::getCollectionFromDb( $sql . $sql_command );

		self::dump( "[bmc] prepListAll:", $prepListAll );
		self::dump( "[bmc] prepListFirst:", reset( $prepListAll ));
		
		if ( reset( $prepListAll )) { // If there is something in the prep areas process it
			
			self::dump( "[bmc] prepListA:", reset( $prepListAll )[ 'areaA' ]);
			self::dump( "[bmc] prepListB:", reset( $prepListAll )[ 'areaB' ]);
			self::dump( "[bmc] prepListC:", reset( $prepListAll )[ 'areaC' ]);
			self::dump( "[bmc] prepListJ:", reset( $prepListAll )[ 'areaJ' ]);

			$prepAreaAItems = explode( ",", reset( $prepListAll )[ 'areaA' ]);
			$prepAreaBItems = explode( ",", reset( $prepListAll )[ 'areaB' ]);
			$prepAreaCItems = explode( ",", reset( $prepListAll )[ 'areaC' ]);
			$prepAreaJItems = explode( ",", reset( $prepListAll )[ 'areaJ' ]);

			self::dump( "[bmc] A:", $prepAreaAItems );
			self::dump( "[bmc] B:", $prepAreaBItems );
			self::dump( "[bmc] C:", $prepAreaCItems );
			self::dump( "[bmc] J:", $prepAreaJItems );

			// getColorValueFromId doesn't get the right colors and values:
			
			list( $card_idsA, $card_typeA, $card_type_argA ) = $this->getColorValueFromId( $prepAreaAItems );
			list( $card_idsB, $card_typeB, $card_type_argB ) = $this->getColorValueFromId( $prepAreaBItems );
			list( $card_idsC, $card_typeC, $card_type_argC ) = $this->getColorValueFromId( $prepAreaCItems );
			list( $card_idsJ, $card_typeJ, $card_type_argJ ) = $this->getColorValueFromId( $prepAreaJItems );

			self::dump( "[bmc] Aids:", $card_idsA );
			self::dump( "[bmc] Atype:", $card_typeA );
			self::dump( "[bmc] Atypearg:", $card_type_argA );

			self::dump( "[bmc] Bids:", $card_idsB );
			self::dump( "[bmc] Btype:", $card_typeB );
			self::dump( "[bmc] Btypearg:", $card_type_argB );

			self::dump( "[bmc] Cids:", $card_idsC );
			self::dump( "[bmc] Ctype:", $card_typeC );
			self::dump( "[bmc] Ctypearg:", $card_type_argC );

			self::dump( "[bmc] Jids:", $card_idsJ );
			self::dump( "[bmc] Jtype:", $card_typeJ );
			self::dump( "[bmc] Jtypearg:", $card_type_argJ );
		} else {
			$card_idsA	     = '';
			$card_typeA	     = '';
			$card_type_argA	 = '';
			$card_idsB	     = '';
			$card_typeB	     = '';
			$card_type_argB	 = '';
			$card_idsC	     = '';
			$card_typeC	     = '';
			$card_type_argC	 = '';
			$card_idsJ	     = '';
			$card_typeJ	     = '';
			$card_type_argJ	 = '';
		}

		self::notifyPlayer(
			$player_id,
			'loadPrepDone',
			clienttranslate("Your prep areas were loaded in"),
			array(
				'player_id'      => $player_id,
				'card_idsA'      => $card_idsA,
				'card_typeA'     => $card_typeA,
				'card_type_argA' => $card_type_argA,
				'card_idsB'      => $card_idsB,
				'card_typeB'     => $card_typeB,
				'card_type_argB' => $card_type_argB,
				'card_idsC'      => $card_idsC,
				'card_typeC'     => $card_typeC,
				'card_type_argC' => $card_type_argC,
				'card_idsJ'      => $card_idsJ,
				'card_typeJ'     => $card_typeJ,
				'card_type_argJ' => $card_type_argJ,
			)
		);
		self::trace("[bmc] EXIT loadPrep");
	}
	
	function buyRequestFinish( $player_id ) {
		self::trace("[bmc] ENTER buyRequestFinish");
		self::dump("[bmc] player_id:", $player_id);
				
		// If there aren't enough cards, don't allow it
		$countDeck = self::getGameStateValue( 'countDeck' );
		$countDiscardPile = self::getGameStateValue( 'countDiscardPile' );

		$checkIfBuyingAllowed = $this->getGameStateValue( 'isBuyingAllowed' );
		self::dump("[bmc] CHECKBUYINGALLOWED", $checkIfBuyingAllowed ); // 0 == false; 1 == true
		
		if ( $checkIfBuyingAllowed == 0 ) { // 0 == false; 1 == true
			self::trace("[bmc] CHECKBUYINGALLOWEDYIELDEDNOTTRUE" );
			throw new BgaUserException( self::_("That card cannot be bought.") );
		}

		if (( $countDeck + $countDiscardPile ) < 2 ) {
			throw new BgaUserException( self::_('There are not enough down cards for you to buy.') );
		}
	
		// Check if it's the player's turn, so no need to buy (TODO: Or buy it for free???)
		$activeTurnPlayer_id = self::getGameStateValue( 'activeTurnPlayer_id' );
		self::dump("[bmc] activeTurnPlayer_id:(buyRequestFinish)", $activeTurnPlayer_id);
		
		$currentPlayerId = self::getCurrentPlayerId();
		self::dump("[bmc] currentPlayerId:", $currentPlayerId);
		
		$nextPlayer = $this->getPlayerAfter( $currentPlayerId ); 
		self::dump("[bmc] nextPlayer:", $nextPlayer);

		if ( $player_id == $nextPlayer ) {
			self::dump("[bmc] It's your turn so no need to buy:",  $player_id);
			
			self::trace("[bmc] Sending notif for itsYourTurn");
			self::notifyPlayer(
				$player_id,
				'itsYourTurn',
				clienttranslate("It's your turn"),
				array()
			);
		
		// Might be a good buy, so continue trying to process it; Ignore if it's the player who discarded
		} else if ( $player_id != $activeTurnPlayer_id ) {

			self::trace("[bmc] Registering the buy request");

			$numberOfBuys =  self::getGameStateValue( 'numberOfBuys' );

			if ( $numberOfBuys != 1 ) { // 0 == 3; 1 == Infinite buys
				$sql = "SELECT player_id, buy_count FROM player ";
				$buy_count = self::getCollectionFromDB( $sql, true );

				self::dump("[bmc] buy_count[ player_id ] (buyRequestFinish): ", $buy_count );
				
				if ( $buy_count[ $player_id ] < 1 ) {
					throw new BgaUserException( self::_("You cannot buy any more this hand.") );
					self::trace( "[bmc] BGA Exception: Cannot buy any more(buyRequestFinish)" );
				}
			} // If it > 0 then keep processing the buy

			self::setPlayerBuying( $player_id, 2 ); // (0==unknown, 1==Not buying 2==Buying)
			// self::setPlayerBuyingGS( $player_id, 2 ); // (0==unknown, 1==Not buying 2==Buying)

			self::dump("[bmc] ENTER notifyPlayerWantsToBuy(embedded):",  $player_id);
			$players = self::loadPlayersBasicInfos();

			$currentCard = $this->cards->getCardOnTop( 'discardPile' );
			
			if ( $currentCard != null ) {
				self::dump( "[bmc] cardToBeBought[id]:",  $currentCard['id'] );

				// if ( $currentCard[ 'type' ] == 5 ) {
					// $value_displayed = self::_(' a joker');
					// $color_displayed = '!';
				// } else {
					// $value_displayed = $this->values_label[ $currentCard[ 'type_arg' ]];
					// $color_displayed = $this->colors[ $currentCard[ 'type' ]][ 'name' ];
				// }

				if ( $currentCard[ 'type' ] == 5 ) {
					$value_displayed = 'Joker';
					$color_displayed = '';
					$connector = '';
				} else {
					$value_displayed = $this->values_label[ $currentCard[ 'type_arg' ]];
					$color_displayed = $this->colors[ $currentCard[ 'type' ]][ 'name' ];
					$connector = ' of ';
				}
				
				$player_name = $players[ $player_id ][ 'player_name' ];
						
				self::notifyAllPlayers(
					'playerWantsToBuy',
					clienttranslate( '${player_name} Wants to Buy: ${value_displayed} ${connector} ${color_displayed}'),
					array(
						'i18n' => array( 'color_displayed', 'value_displayed', 'connector' ), 
						'player_id' => $player_id,
						'activeTurnPlayer_id' => $activeTurnPlayer_id,
						'player_name' => $players[ $player_id ][ 'player_name' ],
						'cardToBeBought' => $currentCard,
						'value_displayed' => $value_displayed,
						'color_displayed' => $color_displayed,
						'connector' => $connector
					)
				);				
			} else {
				self::trace("[bmc] Yikes! No card was found to buy!");
			}
		} // Else the discarder tried to buy so just ignore
		self::trace("[bmc] EXIT buyRequestFinish");
	}
////
////
////
	function liverpool( $player_id ) { // From JS
		self::trace("[bmc] ENTER liverpool");
		self::dump("[bmc] player_id:", $player_id);
				
		// Check if there really is a liverpool or not
		// If there is, then register the first player
		// Block out other players
		// Store the current player
		// Draw the card to the player
		// Go to a state where that card can be played by the player finding liverpool
		// After it's played, let that player discard
		// After discard, go back to whoever's turn it was before the liverpool
		
		$liverpoolFoundYN = self::getGameStateValue( 'liverpoolFoundYN' );

		// Check the real stateInterrupted

		// $stateInterrupted = $this->gamestate->state();
		$playerInterrupted = self::getActivePlayerId(); 
//		$playerFindingLP = $player_id;
		$playerFindingLP = self::getCurrentPlayerId();
		$liverpoolExists = self::getGameStateValue( 'liverpoolExists');
		
		self::dump("[bmc] liverpoolFoundYN:", $liverpoolFoundYN);
		// self::dump("[bmc] stateInterrupted:", $stateInterrupted);
		self::dump("[bmc] playerInterrupted:", $playerInterrupted);
		self::dump("[bmc] playerFindingLP:", $playerFindingLP);
		self::dump("[bmc] liverpoolExists:", $liverpoolExists);

		// Check for liverpoolexist
		if ( $liverpoolExists == 1 ) { // 0 = Not exist; 1 = Exist
			self::trace("[bmc] Liverpool Exists");

			// And it was found by this Player, so make them active player
			self::setGameStateValue( 'activeTurnPlayer_id', $playerFindingLP );

			if ( $liverpoolFoundYN == 0 ) {
				self::setGameStateValue( 'liverpoolFoundYN', 1 ); // 0 = false; 1 = true
				self::setGameStateValue( 'playerFindingLP',   $playerFindingLP );
				self::setGameStateValue( 'playerInterrupted', $playerInterrupted );
//				self::setGameStateValue( 'stateInterrupted',  $stateInterrupted );
			}

			$players = self::loadPlayersBasicInfos();
			
			self::notifyAllPlayers(
				'liverpoolDeclared',
				clienttranslate( '${player_name} declared Liverpool! They draw the card and can play it.'),
				array(
					'player_id' => $player_id,
					'player_name' => $players[ $player_id ][ 'player_name' ]
				)
			);

			$dpCard = $this->cards->getCardsInLocation( 'discardPile' );
			
			if ( isset( reset( $dpCard )[ 'id' ])) {
				self::dump("[bmc] dpCard:", reset( $dpCard )[ 'id' ]);

				$currentCard = $this->cards->getCard( reset( $dpCard )[ 'id' ] );

				self::dump("[bmc] currentCardInDP:", $currentCard );

				// Cannot do drawcard here because it's not this players turn. Do it after active player draws.
//				$this->drawCard( $currentCard, 'discardPile', $playerFindingLP );

				// When drawCard finishes it goes to playCard.
//				After that, check if was liverpool and change players accordingly.

//				self::trace("[bmc] After LP playCard:" );

				$this->gamestate->nextState( 'liverpool' );

			} else {
				self::trace("[bmc] liverpool Error. No discard?");
				// Not supposed to happen, there's nothing in the discard!
			}
		} else {
			self::trace("[bmc] No liverpool exists, do nothing");
			// Do nothing. They pushed the button but there is no liverpool condition
			// I could add some penalty here...
		}
		self::trace("[bmc] EXIT liverpool");
	}
////
////
////
	function stLiverpool() {
		self::trace("[bmc] ENTER stLiverpool");
		
		// Set the next player to be the one who clicked first
		// Cannot draw the card here because it's a game state
		
		$playerFindingLP = self::getGameStateValue( 'playerFindingLP' );

		$this->gamestate->changeActivePlayer( $playerFindingLP );
		
		$this->gamestate->nextState( ); // Go to next state to draw the discarded card

		self::trace("[bmc] EXIT stLiverpool");
	}
////
////
////
	function stLiverpoolDraw() {
		self::trace("[bmc] ENTER stLiverpoolDraw");
		
		// Draw the discarded card to the player finding liverpool
		// In this state we can draw it (but cannot change the player; did that in the previous state)
		$playerFindingLP = self::getGameStateValue( 'playerFindingLP' );

		$currentCard = $this->cards->getCardOnTop( 'discardPile' );
		
		$state = $this->gamestate->state();
		self::dump("[bmc] state(stLiverpoolDraw):", $state);

		$this->drawCard( $currentCard, 'discardPile', $playerFindingLP );
	
		self::trace("[bmc] drawCard within stLiverpoolDraw is finished.");

//		$this->playCard( $currentCard, 'discardPile', $playerFindingLP );

//		$this->gamestate->nextState( 'playCard' ); // Go to play state with new player

//		self::trace("[bmc] playCard within stLiverpoolDraw is finished.");

		$this->gamestate->nextState( 'playCard' ); // Go to playerTurnPlay because a card was drawn
		
		// The discard function must check if we are in a liverpool play, then
		// go back to the interrupted player

		self::trace("[bmc] EXIT stLiverpoolDraw");
	}
////
////
////
	function stLiverpoolReturn() {
		self::trace("[bmc] ENTER stLiverpoolReturn");
		
		// Change the player back
		$playerInterrupted = self::getGameStateValue( 'playerInterrupted' );
		self::dump("[bmc] playerInterrupted:", $playerInterrupted);

		$this->gamestate->changeActivePlayer( $playerInterrupted );

		$state = $this->gamestate->state();
		self::dump("[bmc] state(stLiverpoolReturn):", $state);

		self::trace("[bmc] drawCard and play within stLiverpoolDraw is finished.");

		$this->gamestate->nextState('playerTurnPlay' ); // Go back to interrupted player

		self::trace("[bmc] EXIT stLiverpoolReturn");
	}
////
////
////
	function disableWishList( $player_id ) {
		self::trace("[bmc] ENTER disableWishList");
		
	    $sql = "DELETE FROM wishList WHERE player_id = '";
		$sql_command = $player_id . "'";
		self::DbQuery( $sql . $sql_command );

		self::notifyPlayer(
			$player_id,
			'wishListDisabled',
			clienttranslate("Your wish list is now disabled"),
			array(
				'player_id' => $player_id,
			)
		);
		self::trace("[bmc] EXIT disableWishList");
	}
////
////
////
	function buyRequest() { // This is from JS
		self::trace("[bmc] ENTER buyRequest_fromJS");
		$player_id = $this->getCurrentPlayerId(); // CURRENT!!! not active
		self::dump("[bmc] player_id:", $player_id);	
		$this->buyRequestFinish( $player_id );
		self::trace("[bmc] EXIT buyRequest_fromJS");
	}
////
////
////
	function buyRequest_fromPHP ( $player_id ) { // This is needed for the wishlist to buy
		self::trace("[bmc] ENTER buyRequest_fromPHP");
		$this->buyRequestFinish( $player_id );
		self::trace("[bmc] EXIT buyRequest_fromPHP");
	}
////
////
////
/*
	function buyRequest_orig( $player_id ) {
		self::trace("[bmc] ENTER buyRequest_orig");
		//self::checkAction("buyRequest"); // Cannot do checkAction or the server blocks with "It's not your turn"

		$player_id = $this->getCurrentPlayerId(); // CURRENT!!! not active
		self::dump("[bmc] player_id:", $player_id);
		
		// Check if it's the player's turn, so no need to buy (TODO: Or buy it for free???)
		$activeTurnPlayer_id = self::getGameStateValue( 'activeTurnPlayer_id' );
		self::dump("[bmc] activeTurnPlayer_id:", $activeTurnPlayer_id);
		
		if ( $player_id == $activeTurnPlayer_id ) {
			self::trace("[bmc] Sending notif for itsYourTurn");
			self::notifyPlayer(
				$player_id,
				'itsYourTurn',
				clienttranslate("It's your turn"),
				array()
			);
		}

		// If there aren't enough cards, don't allow it
		$countDeck = self::getGameStateValue( 'countDeck' );
		$countDiscardPile = self::getGameStateValue( 'countDiscardPile' );

		self::dump("[bmc] countDeckNEWWAY:", $countDeck );
		self::dump("[bmc] countDiscardPileNEWWAY:", $countDiscardPile );

		if (( $countDeck + $countDiscardPile ) < 2 ) {
			throw new BgaUserException( self::_('There are not enough down cards for you to buy.') );
		}

		// Check who was first, if that game mode was chosen
		// $buyMethod = self::getGameStateValue( 'buyMethod' );
		// self::dump("[bmc] buyMethod:", $buyMethod);
 
		// if ( $buyMethod == '1' ) { // 1==Fastest player. 2==Seat order.
			// foreach( $buyers as $buyer ) {
				// if ( $buyer == 2 ) {
					// throw new BgaUserException( self::_('Oops! Someone else beat you to it!') );
				// }
			// }
		// } // else $buyMethod == Seat order, so just keep track and resolve later

		// Check that this player can still buy this hand
		$playersBuyCount = self::getPlayersBuyCount();

		self::dump("[bmc] 4610:", $playersBuyCount);
		
		// $playersBuyCount = $playersBuyCountGS;

		if ( $playersBuyCount[ $player_id ] < 1 ) {
			throw new BgaUserException( self::_("You cannot buy. You already bought 3 times this hand.") );
		}

		$this->notifyPlayerWantsToBuy( $player_id );
		self::trace("[bmc] EXIT (almost) buyRequest-NEW");

		self::trace("[bmc] EXIT buyRequest-NEW");
	}
*/
////
////
////
	function findBuyer( $buyingPlayers, $fromPlayer ) {
		self::trace("[bmc] ENTER findBuyer");
		
		// Failsafe for too many recursive entries
		
		self::incGameStateValue( 'findBuyerFailsafe', 1 );
		$FBFailsafe = self::getGameStateValue( 'findBuyerFailsafe' );
		self::dump("[bmc] FBFailsafe", $FBFailsafe);

		if ( $FBFailsafe > 16 ) { // TODO: Adjust this to number of players
			throw new BgaUserException( self::_("FBFailsafe. Yikes!") );
		}
		
		if ( empty( $buyingPlayers )) {
			return false; // No buyers, so return false. Otherwise return the buying player
		}
		
		$activeTurnPlayer_id = self::getGameStateValue( 'activeTurnPlayer_id' );

		// self::dump( "[bmc] activeTurnPlayer_id: ", $activeTurnPlayer_id );
		// self::dump( "[bmc] fromPlayer: ", $fromPlayer );
		// self::dump( "[bmc] buyingPlayers: ", $buyingPlayers );

		// If it has wrapped to us, end the search
		if ( $fromPlayer == $activeTurnPlayer_id ) {
			return;
		} else {
			
			$playerOrder = self::getNextPlayerTable();

			if ( in_array( $fromPlayer, $buyingPlayers )) {
				self::dump("[bmc] FOUND First Buyer!", $fromPlayer);
				return $fromPlayer;
			} else {
				self::dump("[bmc] Buyer is not ", $fromPlayer );
				return $this->findBuyer( $buyingPlayers, $playerOrder[ $fromPlayer ]);
			}
		}
		self::trace("[bmc] EXIT findBuyer");
	}
////
////
////	
	function clearBuyers() {
		self::trace("[bmc] ENTER clearBuyers.");
		$players = self::loadPlayersBasicInfos();
		foreach ( $players as $player_id => $player) {
			// Every discard clear the buyers (0==unknown, 1==Not buying 2==Buying)
			self::setPlayerBuying(   $player_id, 0 );
			// self::setPlayerBuyingGS( $player_id, 0 );
		}
		self::trace("[bmc] EXIT clearBuyers.");
	}
////
////
////
    function stPlayerTurnPlay() {
		self::trace("[bmc] ENTER stPlayerTurnPlay");
		self::trace("[bmc] EXIT stPlayerTurnPlay");
	}
////
////
////
    function checkLiverpool() {
		self::trace( "[bmc] ENTER checkLiverpool" );

		$dpCard = $this->cards->getCardOnTop( 'discardPile' );

		self::dump("[bmc] waitForAll dpCard:", $dpCard);

		$players = self::loadPlayersBasicInfos();
		
		$liverpoolExists = false;
		
		foreach ( $players as $player_id => $player) {
			// Check Area A

			$areaCards = $this->cards->getCardsInLocation( 'playerDown_A' , $player_id );
			self::dump("[bmc] waitForAll areaCards:", $areaCards);
			
			if ( count( $areaCards ) > 0 ){
				$areaCards[ $dpCard['id'] ] = $dpCard; // Keep the index of the new potential card
				self::dump("[bmc] waitForAll areaCards:", $areaCards);

				if ( $this->checkSet( $areaCards ) == true ) {
					$liverpoolExists = true;
				} else if ( $this->checkRun( $areaCards, true ) == true ) {	// Check for run silently (no BGA error)
					$liverpoolExists = true;
				}
			}

			// Check Area B

			$areaCards = $this->cards->getCardsInLocation( 'playerDown_B' , $player_id );
			if ( count( $areaCards ) > 0 ){

				$areaCards[ $dpCard['id'] ] = $dpCard; // Keep the index of the new potential card
				self::dump("[bmc] waitForAll areaCards:", $areaCards);

				if ( $this->checkSet( $areaCards ) == true ) {
					$liverpoolExists = true;
				} else if ( $this->checkRun( $areaCards, true ) == true ) {	// Check for run silently (no BGA error)
					$liverpoolExists = true;
				}
			}
			
			// Check Area C

			$areaCards = $this->cards->getCardsInLocation( 'playerDown_C' , $player_id );

			if ( count( $areaCards ) > 0 ){
				$areaCards[ $dpCard['id'] ] = $dpCard; // Keep the index of the new potential card
				self::dump("[bmc] waitForAll areaCards:", $areaCards);

				if ( $this->checkSet( $areaCards ) == true ) {
					$liverpoolExists = true;
				} else if ( $this->checkRun( $areaCards, true ) == true ) {	// Check for run silently (no BGA error)
					$liverpoolExists = true;
				}
			}
		}
		
		// If made it this far then nothing matched, the discard cannot be playhed anywhere (no Liverpool)
		return $liverpoolExists;
		
		self::trace( "[bmc] EXIT checkLiverpool" );
	}
////
////
////
    function stWaitForAll() {
		self::trace( "[bmc] ENTER stWaitForAll" );

		// If a Liverpool exists and is being processed then set play back to the one interrupted
		
		$liverpoolFoundYN = self::getGameStateValue( 'liverpoolFoundYN' );

		if ( $liverpoolFoundYN == 1 ) {
			self::trace( "[bmc] stWaitForAll and $ liverpoolFoundYN == 1" );

			self::setGameStateValue( 'liverpoolFoundYN', 0 ); // 0 = false; 1 = true
		
			$playerInterrupted = self::getGameStateValue( 'playerInterrupted' );
			$this->gamestate->changeActivePlayer( $playerInterrupted );

			$this->gamestate->nextState( 'fullyResolved' );

			// Not sure if I need to notify JS that Liverpool play is over
			// self::notifyAllPlayers(
				// 'liverpoolExists',
				// 'Liverpool!', // Put it in the log
				// array (
				// )
			// );

		} else {
			$buyer_id = self::getGameStateValue( 'theBuyer' );

			self::dump("[bmc] waitForAll buyer_id:", $buyer_id);

			if( self::checkLiverpool() == true ){
				// Nofity all players there's a liverpool on the board
				
				self::setGameStateValue( 'liverpoolExists', 1 ); // 0=not exist; 1=exist
				self::trace( "[bmc] Stored liverpoolExists=1" );
				
				self::notifyAllPlayers(
					'liverpoolExists',
					'Liverpool!', // Put it in the log
					array ()
				);
				
			} else { // or if not then note it doesn't exist and continue
				self::setGameStateValue( 'liverpoolExists', 0 ); // 0=not exist; 1=exists
				self::trace( "[bmc] Stored liverpoolExists=0" );
			}
			
			// I think these next 2 lines do nothing:
			$activeTurnPlayer_id = self::getGameStateValue( 'activeTurnPlayer_id' );
			$nextTurnPlayer = $this->getPlayerAfter( $activeTurnPlayer_id );
				
			// Adding clearPlayersBuying to try to fix deadlock issue.
			self::clearPlayersBuying();

			$this->gamestate->nextState( 'fullyResolved' );
		}
		self::trace( "[bmc] EXIT  stWaitForAll" );
	}
////
////
////
    function stNextPlayer() {
		self::trace("[bmc] ENTER NextPlayer");
		
		// Notify clients all buyers have resolved, so allow buying again
		$activeTurnPlayer_id = self::getGameStateValue( 'activeTurnPlayer_id' );
		$nextTurnPlayer = $this->getPlayerAfter( $activeTurnPlayer_id );
		$buyerCount = self::getPlayersBuyCount();
		// $buyerCount = self::getPlayersBuyCountGS();
		
		self::notifyAllPlayers(
			'updateBuyers',
			'', // Silent in the log, just update the buyers
			array (
				'player_id' => $activeTurnPlayer_id,
				'nextTurnPlayer' => $nextTurnPlayer,
				'buyers' => $buyerCount
			)
		);
		
		// Discard cannot continue until all player NOT BUY counters have registered
				
		$waiting = false;
		$countCardsByLocation = $this->cards->countCardsByLocationArgs( 'hand' );
		$countCCBL = count($this->cards->countCardsByLocationArgs( 'hand' ));
		$playersNumber = self::getPlayersNumber();

		$countCCBLDeck = count($this->cards->countCardsByLocationArgs( 'deck' ));
		$countCCBLDiscardPile = count($this->cards->countCardsByLocationArgs( 'discardPile' ));
		
		//self::dump("[bmc] CCBL (hands):", $countCardsByLocation );
		//self::dump("[bmc] CCCBL:", $countCCBL );
		//self::dump("[bmc] PN:", $playersNumber );
		//self::dump("[bmc] CCBLDeck:", $countCCBLDeck );
		//self::dump("[bmc] CCBLDiscardPile:", $countCCBLDiscardPile );

		$allCards = $this->cards->countCardsInLocations();
		
		//self::dump("[bmc] allCards:", $allCards );
		
		$countDownCards = 0;
		
		if ( array_key_exists( 'playerDown_A', $allCards )) {
			$countDownCards += $allCards[ 'playerDown_A' ];
		}
		if ( array_key_exists( 'playerDown_B', $allCards )) {
			$countDownCards += $allCards[ 'playerDown_B' ];
		}
		if ( array_key_exists( 'playerDown_C', $allCards )) {
			$countDownCards += $allCards[ 'playerDown_C' ];
		}

		// Check if there are still playable cards
		
        $players = self::loadPlayersBasicInfos();

		$shuffleCount = self::getGameStateValue( 'shuffleCount' ); // Reset the shuffle count every hand
		
		// $outReason = 'SomeoneWentOut'; // 0
		// $outReason = 'DeckOverShuffled'; // 1
		// $outReason = 'AllCardsPlayed'; // 2

		if ( $countCCBL != $playersNumber ) {  		// Someone has gone out
			//$outReason = "SomeoneWentOut" ;
			self::setGameStateValue( "outReason" , 0 ); // Someone went out
			$this->gamestate->nextState( "endHand" );
		} else if ( $shuffleCount > 5 ) {	// The deck has been shuffled too much
			//$outReason = "DeckOverShuffled" ;
			self::setGameStateValue( "outReason" , 1 ); // Shuffled more than 5 times
			$this->gamestate->nextState( "endHand" );
		} else if ( $this->checkPlayable() != true ) {	    // All playable cards have been played
			//$outReason = "AllCardsPlayed";			
			self:setGameStateValue( "outReason", 2 ); // All playable cards have been played
			$this->gamestate->nextState( "endHand" );
		} else {
			// Next player can draw and play etc...

			// Show State
			$state = $this->gamestate->state();
			//self::dump("[bmc] stNextPlayer state:", $state);

			$activePlayerId = $this->getActivePlayerId();
			// self::dump( "[bmc] activePlayerId (before change):", $activePlayerId );

			$discardingPlayer_id = $activePlayerId;

			$previous_player_id = $this->getPlayerBefore( $activePlayerId );
			$next_player_id = $this->getPlayerAfter( $activePlayerId );
			
			// self::dump( "[bmc] previous_player_id:", $previous_player_id );
			// self::dump( "[bmc] next_player_id:", $next_player_id );
			
			//$this->gamestate->changeActivePlayer( $next_player_id );
			$this->activeNextPlayer();
			
			$activePlayerId = $this->getActivePlayerId();
			// self::dump( "[bmc] activePlayerId (after change):", $activePlayerId );
			
			// Give extra time to player
			self::giveExtraTime( $activePlayerId );

			// Store the previous player so they don't get the offer to buy their own discard.
			// Cannot do this in a multiactive state, so we must do it right before it.
			
			self::setGameStateValue( "previous_player_id" , $previous_player_id );
			self::setGameStateValue( 'activeTurnPlayer_id', $activePlayerId );

			// Clear all the buyers
			// self::trace( "[bmc] stNextPlayer clearing buyers.");
			$this->clearBuyers();
		
			//Set above: $discardingPlayer_id = $this->getPlayerBefore( $activePlayerId );

			self::setPlayerBuying(   $discardingPlayer_id, 1 ) ; // 1 = not buying, they just discarded it
			// self::setPlayerBuyingGS( $discardingPlayer_id, 1 ); // 1 = not buying, they just discarded it

			self::setPlayerBuying(   $activePlayerId, 1 ) ; // 1 = not buying, they can get it for free
			// self::setPlayerBuyingGS( $activePlayerId, 1 ); // 1 = not buying, they can get it for free

			// Just make sure it stuck!
			$activePlayerId = $this->getActivePlayerId();
			// self::dump( "[bmc] activePlayerId (exiting nextPlayer inside if):", $activePlayerId );

			// Show State
			$state = $this->gamestate->state();
			//self::dump("[bmc] stNextPlayer state:", $state);

			$sql = "SELECT id id, player_id, card_type, card_type_arg FROM wishList ";
		
			$wishLists = self::getCollectionFromDb( $sql );

			//$dpCard = $this->cards->getCardsInLocation( 'discardPile' );
			$dpCard = $this->cards->getCardOnTop( 'discardPile' );
			// self::dump("[bmc] 5227dpCard:",  $dpCard );

			if ( isset( $dpCard[ 'id' ])) {

				//self::dump("[bmc] dpCard:", $dpCard[ 'id' ]);

				//$currentCard = $this->cards->getCard( $dpCard[ 'id' ] );

				//self::dump("[bmc] currentCardInDP:", $currentCard);
				
				$discardColor = $dpCard[ 'type' ];
				$discardValue = $dpCard[ 'type_arg' ];
				
				foreach( $wishLists as $entry ) {
					// self::dump("[bmc] wishList entry:", $entry);
					// self::dump("[bmc] wishList SQL:", $entry['player_id']);
					
					if (( $entry[ 'player_id' ] != $discardingPlayer_id ) &&
						( $entry[ 'player_id' ] != $next_player_id )) {
							
						if (( $entry[ 'card_type' ] == $discardColor ) &&
							( $entry[ 'card_type_arg' ] == $discardValue )) {
							
							// self::trace("[bmc] BUY MATCH!");
							// Call buyRequest with the player id
							
							$this->buyRequest_fromPHP( $entry[ 'player_id' ]);
						}
					}
				}
			}
			$this->gamestate->nextState( 'nextPlayer' );					
		}
	self::trace("[bmc] EXIT NextPlayer");
	}
////
////
////
	function checkPlayable() {
		self::trace( "[bmc] ENTER checkPlayable" );

		$playedCardArray = [];
		$unplayedHandArray = [];
		$unplayedDeckArray = [];
		$unplayedDPArray = [];
		
		$unplayedHandArray = $this->cards->getCardsInLocation( 'hand' );
		$unplayedDeckArray = $this->cards->getCardsInLocation( 'deck' );
		$unplayedDPArray = $this->cards->getCardsInLocation( 'discardPile' );
		
		$unplayedCardArray = array_merge( $unplayedHandArray, $unplayedDeckArray, $unplayedDPArray );
		
		//self::dump("[bmc] unplayedCardArray:", $unplayedCardArray );
	
		$playedCardArray = array_merge(
			$this->cards->getCardsInLocation( 'playerDown_A' ),
			$this->cards->getCardsInLocation( 'playerDown_B' ),
			$this->cards->getCardsInLocation( 'playerDown_C' )
			);

		self::dump("[bmc] playedCardArray:", $playedCardArray );

		$playedValues = array();
		$jokersPlayed = 0;
		
		$playedSuitArray = [];
		self::dump("[bmc] playedSuitArray :", $playedSuitArray );
		
		foreach( $playedCardArray as $card ) {
//			self::dump("[bmc] foreachplayedcard :", $card );
			if ( count( $card ) != 0 ) {
				if ( $card[ 'type' ] == 5 ) {
					$jokersPlayed++;
				} else {
					if( !in_array( $card[ 'type_arg' ], $playedValues )) {
						$playedValues[] = $card[ 'type_arg' ];
					}
				}
			}
			// Also track the suits on the board
			$playedSuitArray[ $card[ 'type' ]] = 1;
		}
		
		self::dump("[bmc] playedValues :", $playedValues );
		self::dump("[bmc] playedSuitArray :", $playedSuitArray );
			
		$unplayedValues = array();
		
		foreach( $unplayedCardArray as $card ) {
//			self::dump("[bmc] foreachunplayedcard :", $card );
			if ( $card[ 'id' ] != null ) {
				if (!in_array( $card[ 'type_arg' ], $unplayedValues )){
					$unplayedValues[] = $card[ 'type_arg' ];
				}
			}
		}
		self::dump("[bmc] count(UPV) :", count( $unplayedValues ));
		//self::dump("[bmc] unplayedValues :", $unplayedValues );

		// Check if all jokers have been played, if not then keep playing

		$optionNumJokers =  self::getGameStateValue( 'numberOfJokers' );
		
		if( $optionNumJokers == 10 ) {
			$numberOfDecks = self::getGameStateValue( 'numberOfDecks' );
			$numberOfJokers = 2 * $numberOfDecks;
		} else {
			$numberOfJokers = $optionNumJokers;
		}
		
		self::dump("[bmc] numberOfJokers :", $numberOfJokers );
		self::dump("[bmc] jokersPlayed :", $jokersPlayed );

		if ( $jokersPlayed < $numberOfJokers ) {
			self::trace( "[bmc] EXIT checkPlayable - Still a playable joker." );
			return true; // Still can play at least 1 joker
		}

		// If sets are a target and values can still be played then keep playing

		$currentHandType = $this->getGameStateValue( 'currentHandType' );
		$setsNeeded = $this->handTypes[ $currentHandType ][ "QtySets" ];
		$runsNeeded = $this->handTypes[ $currentHandType ][ "QtyRuns" ];

		if( $setsNeeded > 0 ) {
			foreach( $unplayedValues as $value ) {
				if ( in_array( $value, $playedValues )) {
					self::trace( "[bmc] EXIT Still a playable card on a Set." );
					return true; // Still can play some values onto sets
				}
			}
		} else { // else the hand target is only runs
			// For Runs, if there are still cards in anyone's hand or in the deck or in
			// discard pile which match a suit on the board, then there are still playable cards

			foreach( $unplayedCardArray as $card ) {
	//			self::dump("[bmc] foreachunplayedcard :", $card );
				if ( $card[ 'id' ] != null ) {
					if ( in_array( $card[ 'type' ] , $playedSuitArray )) {
						self::dump("[bmc] foundaplayablecard :", $card );
						self::trace( "[bmc] EXIT Still a playable card on a Run." );
						
						return true; // A card with a suit which is on the board is still unplayed
					}
				}
			}
		}

		// If we got here, then there are no unplayable value cards on sets, and no unplayed cards with suits on the board, so the the hand should end because the players cannot play it out.
		
		self::trace( "[bmc] EXIT checkPlayable" );
		return false;
		
	}
////
////
////
	function nextTurnPlayer() {
		self::trace( "[bmc] ENTER nextTurnPlayer" );
		
		$players = self::loadPlayersBasicInfos();
		$activeTurnPlayer_id = $this->getGameStateValue( 'activeTurnPlayer_id' );
		
		$nextPlayer = $this->getPlayerAfter( $activeTurnPlayer_id ); 

		//self::dump( "[bmc] players: ", $players );
		self::dump( "[bmc] activeTurnPlayer_id(nextTurnPlayer):", $activeTurnPlayer_id );
		self::dump( "[bmc] nextPlayer: ", $nextPlayer );

		self::setGameStateValue( setGameStateValue( 'activeTurnPlayer_id', $nextPlayer ));
		
		self::trace( "[bmc] EXIT nextTurnPlayer" );
		return $nextPlayer;
	}
////
////
////
    function makeCardIdsFromCards( $cards ) {
		
		$cardIds = [];
		foreach ( $cards as $card ) {
			$cardIds[] = $card['id'];
		}
		self::dump("[bmc] cardId:", $cardIds );
		return $cardIds;
	}
////
////
////
    function stEndHand() {
		self::trace("[bmc] ENTER stEndHand");
		
		// Notify players and wait for them to confirm to move to the next hand		
		
        ///// Test if this is the end of the game
		$currentHandType = $this->getGameStateValue( 'currentHandType' );
		
		self::setGameLength(); // This sets this->handTypes, not sure why it gets removed.

		self::dump("[bmc] currentHandType stEndHand:", $currentHandType );
		self::dump("[bmc] this->handTypes stEndHand:", $this->handTypes );
		
//		if ( $currentHandType > 6 ) { // The 7 hand numbers are 0 through 6

		$countHandTypes = count( $this->handTypes );
		self::dump("[bmc] countHandTypes stEndHand:", $countHandTypes );
		
		if ( $currentHandType >= $countHandTypes ) {
			$this->gamestate->nextState("endGame");
		} else {

			$gameLengthOption = self::getGameStateValue( 'gameLengthOption' );
			self::dump( "[bmc] gameLengthOption:", $gameLengthOption );
			
			if ( $gameLengthOption == 1 ) {
				// $this->setsRuns = $this->setsRunsFull;
				$this->handTypes = $this->handTypesFull;
			} else {
				// $this->setsRuns = $this->setsRunsShort;
				$this->handTypes = $this->handTypesShort;
			}

			self::dump("[bmc] handTypes stEndHand:", $this->handTypes );
			$currentHandType = self::getGameStateValue( 'currentHandType' );
			$handTarget = $this->handTypes[ $currentHandType ][ "Target" ];
			
			self::dump("[bmc] handTarget stEndHand:", $handTarget );
			
			// Notify players to go to the next target hand
			
			$newScores = self::getCollectionFromDb("SELECT player_id, player_score FROM player", true );

			self::notifyAllPlayers(
				"newScores",
				'',
				array(
					'newScores' => $newScores,
					'handTarget' => $handTarget,
					'currentHandType' => $currentHandType
				)
			);

			self::trace("[bmc] EXIT (almost) stEndHand");
			$this->gamestate->nextState("newHand");
		}
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

		// Don't let the buy timers stop play
		// self::setBuyTimerStatus( $active_player, 0 ); // 0 = Not running. 1 = Running.
    	
        if ($state['type'] === "activeplayer") {
            switch ($statename) {
                default:
                    $this->gamestate->nextState( "zombiePass" );
                	break;
            }

            return;
        }

        if ($state['type'] === "multipleactiveplayer") {
			self::trace( "[bmc] zombiesetNonMultiactive" );
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
//        if( $from_version <= 2309042301 )
	        if( $from_version <= 2308272254 )
        {
            // ! important ! Use DBPREFIX_<table_name> for all tables

            $sql = "CREATE TABLE DBPREFIX_prepAreas (
				`player_id` int(10) unsigned NOT NULL,
				`areaA` varchar(100),
				`areaB` varchar(100),
				`areaC` varchar(100),
				`areaJ` varchar(100),
				PRIMARY KEY (`player_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
			
			self::applyDbUpgradeToAllDB( $sql );
        }
	}
}

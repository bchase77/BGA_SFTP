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
			"numberOfDecks" => 100,
			"buyTimeInSeconds" => 101,
			"gameLengthOption" => 102,
			"buyMethod" => 103
        ) );
	
		// NOTE: This is defined in 2 places and I'm not sure why it won't see the other one
/*
        $this->handTypes = array(
		  0 => "2 Sets",
		  1 => "1 Set and 1 Run",
		  2 => "2 Runs",
		  3 => "3 Sets",
		  4 => "2 Sets and 1 Run",
		  5 => "1 Set and 2 Runs",
		  6 => "3 Runs",
		  7 => "END" // Just put this here to simplify the PHP when checking game end.
		);
$this->handTypes = array( // Qty of Sets, Qty of Runs
  0 => array( "8 Sets", 2, 0),
  1 => array( "1 Set and 1 Run", 1, 1),
  2 => array( "2 Runs", 0, 2),
  3 => array( "3 Sets", 3, 0),
  4 => array( "2 Sets and 1 Run", 2, 1),
  5 => array( "1 Set and 2 Runs", 1, 2),
  6 => array( "3 Runs", 0, 3)
);
*/
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
//		self::trace("[bmc] !!setupNewGame!!"); // Doesn't appear in log!

        // Don't do a lot here since server side hasn't been set up yet and so it's hard to debug.
		// Set the colors of the players with HTML color code
        // The default below is red/green/blue/orange/brown
        // The number of colors defined here must correspond to the maximum number of players allowed for the gams
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
			
			// No buy timers are running after first load.
			self::setBuyTimerStatus( $player_id, 0 ); // 0 = Not running. 1 = Running.
        }
        $sql .= implode( $values, ',' );
        self::DbQuery( $sql );
        self::reattributeColorsBasedOnPreferences( $players, $gameinfos['player_colors'] );
        self::reloadPlayersBasicInfos();

        self::setGameStateInitialValue( 'currentHandType', 0 );
		$currentHandType = $this->getGameStateValue( 'currentHandType' );
		
		//self::dump("[bmc] handtypes(line138):", $this->handTypes[$currentHandType]);

		$gameLengthOption = $this->getGameStateValue( 'gameLengthOption' );

		if ( $gameLengthOption == 1 ) {
			$this->setsRuns = $this->setsRunsFull;
			$this->handTypes = $this->handTypesFull;
		} else {
			$this->setsRuns = $this->handTypesShort;
			$this->handTypes = $this->handTypesShort;
		}
		
//        self::setGameStateInitialValue( 'area_A_target', $this->handTypes[$currentHandType]["Area_A"]);
//        self::setGameStateInitialValue( 'area_B_target', $this->handTypes[$currentHandType]["Area_B"]);
//        self::setGameStateInitialValue( 'area_C_target', $this->handTypes[$currentHandType]["Area_C"]);

        // Activate first player (which is in general a good idea :) )
        $player_id = $this->activeNextPlayer();

		self::setGameStateInitialValue( 'drawSourceValue', 2 ); // 0 = deck, 1 = discardPile. This should be set 
		// every time drawCard is called but including it here for completeness.
		//self::dump("[bmc] currentTurnPlayer_id:", $currentTurnPlayer_id );

		$activePlayerId = $this->getActivePlayerId();
//		self::dump("[bmc] activeTurnPlayer_id:", $activePlayerId );

		// Store which player's turn it really is for access during the multipleactiveplayer state.
		self::setGameStateInitialValue( 'activeTurnPlayer_id', $activePlayerId );
		
		$dealer = $this->getPlayerBefore( $activePlayerId );
		
		self::setGameStateInitialValue( 'dealer', $dealer );

		// Clear out the BUY counters for all players
		self::clearPlayersBuyCount();
		
		self::setGameStateInitialValue( 'discardWeightHistory', 300 ); // Start higher than any game will have qty of cards

        self::setGameStateInitialValue( 'skipFirstDeal', true );

		$this->waiting = false; // This keeps the state machine from getting out of sync by requiring
		
		
		
		
		// TODO: This may not work, but it might fix the missing player names on the board.
		
/*		
		$template = self::getGameName() . "_" . self::getGameName();
        
        // this will inflate our goDownArea block with actual players data
        $this->page->begin_block($template, "goDownArea");
		
        foreach ( $players as $player_id => $info ) {
            //$dir = array_shift($directions);
            $this->page->insert_block("goDownArea", array ("PLAYER_ID" => $player_id,
                    "PLAYER_NAME" => $players [$player_id] ['player_name'],
                    "PLAYER_COLOR" => $players [$player_id] ['player_color']
					));
        }
*/







		// all players to select BUY / NOT BUY before a player discards a 2nd time.

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
        $jokers = array ();
		$color = 5;
		for ($value = 1; $value <= 2; $value ++) {
			array_push( $cards, array ('type' => $color, 'type_arg' => $value, 'nbr' => $numberOfDecks ));
		}

        $this->cards->createCards( $cards, 'deck' );

		$allCardsDebug = $this->cards->getCardsInLocation( 'deck' );
		
//		self::dump( "[bmc] AllCardsDebug: ", $allCardsDebug );

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

        $result = array();
    
        $current_player_id = self::getCurrentPlayerId();    // !! We must only return informations visible by this player !!
    
		$APL = $this->gamestate->getActivePlayerList();
		self::dump( "[bmc] APL:", $APL);
		
		$result['currentPlayerId'] = $current_player_id;
		$result['activePlayerId'] = self::getActivePlayerId();
		
        // Get information about players
        // Note: you can retrieve some extra field you added for "player" table in "dbmodel.sql" if you need it.
        $sql = "SELECT player_id id, player_score score FROM player ";
        $result['players'] = self::getCollectionFromDb( $sql );

        // Cards in player hand
        $result['hand'] = $this->cards->getCardsInLocation( 'hand', $current_player_id );

//        $result['deckCount'] = $this->cards->countCardInLocation( 'deck' );
		
		$result['deckIDs'] = array_keys($this->cards->getCardsInLocation( 'deck' ));
		
		$result['allHands'] = $cardsByLocation = $this->cards->countCardsByLocationArgs( 'hand' );
		
		self::dump( "[bmc] allHands:", $cardsByLocation = $this->cards->countCardsByLocationArgs( 'hand' ));

		$countCardsByLocation = $this->cards->countCardsByLocationArgs( 'hand' );
		$result['dbgcountA'] = $countCardsByLocation ;
		$result['dbgcountV'] = count( $countCardsByLocation );

		$playersNumber = self::getPlayersNumber();
		$result['dbgPlayersNumber'] = $playersNumber ;
		
		$gameLengthOption = $this->getGameStateValue( 'gameLengthOption' );

		if ( $gameLengthOption == 1 ) {
			$this->setsRuns = $this->setsRunsFull;
			$this->handTypes = $this->handTypesFull;
		} else {
			$this->setsRuns = $this->setsRunsShort;
			$this->handTypes = $this->handTypesShort;
		}
		
		$result['handTypes']["Target"] = $this->handTypes; // Pull the description
		
		$players = self::loadPlayersBasicInfos();
		
//		self::dump( "[bmc] players:", $players );
		
        $playerGoneDown = self::getPlayerGoneDown(); // It's an array, one for each player.
		
		$buyers = self::getPlayerBuying();
		//self::dump("[bmc] gamedatas buyers:", $buyers);
		
		$buyCount = self::getPlayersBuyCount();
//		self::dump("[bmc] gamedatas buyCount:", $buyCount);

		$discardSize = count( $this->cards->countCardsByLocationArgs( 'discardPile' ));
		self::setGameStateValue( 'discardSize', $discardSize );
		
		$result['discardSize'] = $discardSize;

		foreach ( $players as $player_id => $player ) {
			$result[ 'downArea_A_' ][ $player_id ] = $this->cards->getCardsInLocation( 'playerDown_A' , $player_id );
			$result[ 'downArea_B_' ][ $player_id ] = $this->cards->getCardsInLocation( 'playerDown_B' , $player_id );
			$result[ 'downArea_C_' ][ $player_id ] = $this->cards->getCardsInLocation( 'playerDown_C' , $player_id );
			$result[ 'goneDown' ][ $player_id ] = $playerGoneDown[ $player_id ];		
			$result[ 'buyers' ][ $player_id ] = $buyers[ $player_id ];
			$result[ 'buyCount'][ $player_id ] = $buyCount[ $player_id ];
		}
		
		$result['currentHandType'] = self::getGameStateValue( 'currentHandType' );

		$result['discardPile'] = $this->cards->getCardsInLocation( 'discardPile' );
        
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
//		self::dump("[bmc] GETALLDATAS state:", $state);
		
		$cardsInHd = $this->cards->getCardsInLocation( 'hand' );
		$cardsInDk = $this->cards->getCardsInLocation( 'deck' );
		$cardsInDp = $this->cards->getCardsInLocation( 'discardPile' );
		$cardsInBa = $this->cards->getCardsInLocation( 'playerDown_A' );
		$cardsInBb = $this->cards->getCardsInLocation( 'playerDown_B' );
		$cardsInBc = $this->cards->getCardsInLocation( 'playerDown_C' );

//		self::dump("[bmc] cardsInHd:", $cardsInHd);
//		self::dump("[bmc] cardsInDk:", $cardsInDk);
//		self::dump("[bmc] cardsInDp:", $cardsInDp);
//		self::dump("[bmc] cardsInBa:", $cardsInBa);
//		self::dump("[bmc] cardsInBb:", $cardsInBb);
//		self::dump("[bmc] cardsInBc:", $cardsInBc);

		$result[ 'options' ][ 'buyTimeInSeconds' ] = self::getGameStateValue( 'buyTimeInSeconds' );
		
		$numberOfDecks = self::getGameStateValue( 'numberOfDecks' );
		
		self::dump( "[bmc] numberOfDecks", $numberOfDecks );
		
		$result[ 'options' ][ 'numberOfDecks' ] = $numberOfDecks;

		$currentHandType = $this->getGameStateValue( 'currentHandType' );
		$result[ 'handTarget' ] = $this->handTypes[ $currentHandType ]["Target"];
		
//		self::trace("[bmc] EXIT getAllDatas");
        return $result;
    }
////////
////////
////////
	function argPlayerTurnDraw() {
		self::trace("[bmc] ENTER argPlayerTurnDraw");
		
		$currentHandType = $this->getGameStateValue( 'currentHandType' );
		
		$playerGoneDown = self::getPlayerGoneDown(); // It's an array, one for each player.
		
		$gtActivePlayerId = $this->getActivePlayerId();
		self::dump("[bmc] GAME THINKS ACTIVE PLAYER:", $gtActivePlayerId );
		
		$activeTurnPlayer_id = self::getGameStateValue( 'activeTurnPlayer_id' );
		
		$players = self::loadPlayersBasicInfos();
		$activePlayer = $players[ $activeTurnPlayer_id ][ 'player_name' ];

		$activePlayerFull = $players[ $activeTurnPlayer_id ];

		//self::dump("[bmc] activePlayerFull:", $activePlayerFull );

		self::dump("[bmc] activeTurnPlayer_id:", $activeTurnPlayer_id );

		$buyers = self::getPlayerBuying();
		//self::dump("[bmc] argPlayerTurnDraw buyers (PTD):", $buyers);

		if ( $playerGoneDown[ $activeTurnPlayer_id ] == 1 ) {
			$thingsCanDo = 'play or discard!!.';
		} else {
			$thingsCanDo = 'play, discard or go down!!.';
		}
		
		$buyMessage = '';
		
		$gameLengthOption = $this->getGameStateValue( 'gameLengthOption' );

		if ( $gameLengthOption == 1 ) {
			$this->setsRuns = $this->setsRunsFull;
			$this->handTypes = $this->handTypesFull;
		} else {
			$this->setsRuns = $this->setsRunsShort;
			$this->handTypes = $this->handTypesShort;
		}

		//self::dump("[bmc] currentHandType argPlayerTurnDraw:", $this->handTypes[$currentHandType]["Target"] );
		//self::dump("[bmc] thingsCanDo:", $thingsCanDo );
		//self::dump("[bmc] activePlayer(PTD):", $activePlayer );
		
		$tpn = '<span style="color:#' . $players[ $activeTurnPlayer_id ]["player_color"] . ';">' . $players[ $activeTurnPlayer_id ]["player_name"] . '</span>';
		
		self::dump("[bmc] tpn: ", $tpn );
	
        return array(
			'handTarget' => $this->handTypes[$currentHandType]["Target"], // Pull the description
			'thingsCanDo' => $thingsCanDo,
			'turnPlayerName' => $tpn,
			'buyMessage' => $buyMessage,
			'buyers' => $buyers,
			'where' => 'PTD'
        );
		self::trace("[bmc] EXIT argPlayerTurnDraw");
    }
////////
////////
////////
	function argWentOut() {
		self::trace("[bmc] ENTER argWentOut");
		$activeTurnPlayer_id = self::getGameStateValue( 'activeTurnPlayer_id' );
		$players = self::loadPlayersBasicInfos();
		$activePlayer = $players[ $activeTurnPlayer_id ][ 'player_name' ];
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

		self::dump("[bmc] activeTurnPlayer_id:", $activeTurnPlayer_id );

		$buyers = self::getPlayerBuying();
		//self::dump("[bmc] argPlayerTurnPlay buyers(PTP):", $buyers);

		if ( $playerGoneDown[ $activeTurnPlayer_id ] == 1 ) {
			$thingsCanDo = 'play or discard.';
		} else {
			$thingsCanDo = 'play, discard or go down.';
		}
		
		//self::dump("[bmc] currentHandType argPlayerTurnPlay:", $this->handTypes[$currentHandType]["Target"] );
		//self::dump("[bmc] thingsCanDo:", $thingsCanDo );
		//self::dump("[bmc] activePlayer(PTP):", $activePlayer );

		$gameLengthOption = $this->getGameStateValue( 'gameLengthOption' );

		if ( $gameLengthOption == 1 ) {
			$this->setsRuns = $this->setsRunsFull;
			$this->handTypes = $this->handTypesFull;
		} else {
			$this->setsRuns = $this->setsRunsShort;
			$this->handTypes = $this->handTypesShort;
		}

		$tpn = '<span style="color:#' . $players[ $activeTurnPlayer_id ]["player_color"] . ';">' . $players[ $activeTurnPlayer_id ]["player_name"] . '</span>';
		
		self::dump("[bmc] tpn: ", $tpn );

		self::trace("[bmc] EXIT argPlayerTurnPlay");

        return array(
			'handTarget' => $this->handTypes[$currentHandType]["Target"], // Pull the description
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
    function getGameProgression()
    {
		$currentHandType = $this->getGameStateValue( 'currentHandType' );
		
		$gameLengthOption = $this->getGameStateValue( 'gameLengthOption' );

		if ( $gameLengthOption == 1 ) {
			$this->setsRuns = $this->setsRunsFull;
			$this->handTypes = $this->handTypesFull;
		} else {
			$this->setsRuns = $this->setsRunsShort;
			$this->handTypes = $this->handTypesShort;
		}

		self::dump("[bmc] Progression:", $currentHandType );
		self::dump("[bmc] Progression:", count( $this->handTypes ));
		
		$ret = 100 * ( floatval( $currentHandType / count( $this->handTypes )));
		
		if( $ret < 1 ) {
            $ret = 1;
        }
        if( $ret > 99 ) {
            $ret = 99;
        }
		return $ret;
    }

//////////////////////////////////////////////////////////////////////////////
//////////// Utility functions
//////////// 
    /*
        In this space, you can put any utility methods useful for your game logic
    */

    function argMyArgumentMethod()
    {
		$currentPlayer = $this->getActivePlayerName();
		$currentHandType = $this->getGameStateValue( 'currentHandType' );
		self::dump("[bmc] currentHandType argMyArgumentMethod:", $this->handTypes[$currentHandType]["Target"]);
		
		return array(
            'currentPlayer' => $currentPlayer,
			'handTarget' => $this->handTypes[$currentHandType]["Target"] // Pull the description
        );    
    }
/////
/////
/////
    function getPlayerGoneDown()
    {
        $sql = "SELECT player_id, gone_down FROM player ";
        return self::getCollectionFromDB($sql, true);
    }
    function setPlayerGoneDown($player_id, $goneDown /* 0 or 1 */)
    {
        $sql = "UPDATE player SET gone_down = $goneDown WHERE player_id = $player_id ";
        self::DbQuery( $sql );
    }
    function clearPlayersGoneDown()
    {
        $sql = "UPDATE player SET gone_down = 0 ";
        self::DbQuery( $sql );
    }
/////
/////
/////
    function getPlayerBuying()
    {
        $sql = "SELECT player_id, buying FROM player ";
        return self::getCollectionFromDB($sql, true);
    }
    function setPlayerBuying( $player_id, $buying ) // (0==unknown, 1==Not buying 2==Buying)
    {
        $sql = "UPDATE player SET buying = $buying WHERE player_id = $player_id ";
        self::DbQuery( $sql );
    }
    function clearPlayersBuying()
    {
        $sql = "UPDATE player SET buying = 0 ";
        self::DbQuery( $sql );
    }
/////
/////
/////
	function setBuyTimerStatus( $player_id, $setTo ) // 0 = Not running, 1 = Running
	{
        $sql = "UPDATE player SET buyTimer = $setTo  WHERE player_id = $player_id ";
        self::DbQuery( $sql );
	}
    function getBuyTimerStatus()
    {
        $sql = "SELECT player_id, buyTimer FROM player ";
        return self::getCollectionFromDB($sql, true);
    }
    function clearBuyTimers()
    {
        $sql = "UPDATE player SET buyTimer = 0 ";
        self::DbQuery( $sql );
    }
/////
/////
/////
    function getPlayersBuyCount()
    {
        $sql = "SELECT player_id, buy_count FROM player ";
        return self::getCollectionFromDB($sql, true);
    }
    function decPlayerBuyCount( $player_id ) // Track how many times they bought per hand
    {
		self::trace("bmc] ENTER decPlayerBuyCount");

        $sql = "SELECT player_id, buy_count FROM player ";
		$buy_count = self::getCollectionFromDB( $sql, true );

		self::dump("bmc] buy_count[ player_id ] (decPlayerBuyCount): ", $buy_count[ $player_id ]);
		
		if ( $buy_count[ $player_id ] > 0 ) {
			$bcUpdate = $buy_count[ $player_id ] - 1;
			$sql = "UPDATE player SET buy_count = $bcUpdate WHERE player_id = $player_id ";
			self::DbQuery( $sql );
		} else {
			throw new BgaUserException( self::_("You cannot buy any more this hand(decPlayerBuyCount).") );
		}
    }
    function clearPlayersBuyCount()
    {
        $sql = "UPDATE player SET buy_count = 3 ";
        self::DbQuery( $sql );
    }
/////
/////
/////
	// Example from lettertycoon:
	//
	// function getPlayersChallenge()
		// {
			// $sql = "SELECT player_id, challenge FROM player ";
			// return self::getCollectionFromDB($sql, true);
		// }
	//
	// function getChallengerId()
		// {
		// $active_player_id = self::getActivePlayerId();
		// $challenge_by_player = self::getPlayersChallenge();

		// $player_id = self::getPlayerAfter($active_player_id);
		// while ($player_id != $active_player_id) {
			// if ($challenge_by_player[$player_id] == 1) {
				// return $player_id;
			// }
			// $player_id = self::getPlayerAfter($player_id);
		// }

		// return NULL;
	// }

//////////////////////////////////////////////////////////////////////////////
//////////// Player actions
//////////// 

    /*
        Each time a player is doing some game action (i.e. clicking something), one of the methods below is called.
        (note: each method below must match an input method in tutorialrumone.action.php)
    */

	function discardCard( $card_id, $player_id ) {
		self::trace( "[bmc] ENTER discardCard (from JS via action.php)" );

		$activeTurnPlayer_id = self::getGameStateValue( 'activeTurnPlayer_id' );

		$currentHandType = $this->getGameStateValue( 'currentHandType' );
		
		self::dump("[bmc] currentHandType stEndHand:", $currentHandType );

		self::dump("[bmc] Discarding player id:", $player_id );
		self::dump("[bmc] Discarding card_id:", $card_id );
		self::dump("[bmc] activeTurnPlayer_id:", $activeTurnPlayer_id );
		self::dump("[bmc] getActivePlayerName:", self::getActivePlayerName() );
		
		if ( $activeTurnPlayer_id == $player_id ) { // Allow discard if it's that player's turn
			// Notifying players potentially buying the previous one that they were too slow!
			self::notifyAllPlayers(
				'clearBuyers',
				clienttranslate(''),
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
	
			$cardsInDp = $this->cards->getCardsInLocation( 'discardPile' );
			
			//self::dump("[bmc] cardsInDp:", $cardsInDp );

			$nextTurnPlayer = $this->getPlayerAfter( $player_id );
			
			self::dump("[bmc] nextTurnPlayer:", $nextTurnPlayer );

			$cardsByLocationHand  = $this->cards->countCardsByLocationArgs( 'hand' );
			$discardSize = count( $this->cards->countCardsByLocationArgs( 'discardPile' ));
			self::setGameStateValue( 'discardSize', $discardSize );
			
			self::dump("[bmc] discardSize(DP):", $discardSize );

			$currentCard = $this->cards->getCard( $card_id );
			
			if ( $currentCard[ 'type' ] == 5 ) {
				$value_displayed = ' a joker';
				$color_displayed = '!';
			} else {
				$value_displayed = 'the ' . $this->values_label[ $currentCard[ 'type_arg' ]] . ' of ';
				$color_displayed = $this->colors[ $currentCard[ 'type' ]][ 'name' ] . 's.';
			}

			// And notify
			self::notifyAllPlayers(
				'discardCard',
				clienttranslate('${player_name} discards ${value_displayed}${color_displayed}'),
				array (
					'player_id' => $activeTurnPlayer_id,
					'player_name' => self::getActivePlayerName(),
					'color' => $currentCard [ 'type' ],
					'color_displayed' => $color_displayed,
					'value' => $currentCard [ 'type_arg' ],
					'value_displayed' => $value_displayed,
					'card_id' => $card_id,
					'nextTurnPlayer' => $nextTurnPlayer,
					'allHands' => $cardsByLocationHand,
					'discardSize' => $discardSize
				)
			);
			
			$buyers = self::getPlayerBuying();
			self::dump("[bmc] Buyers Status(discardCard):", $buyers);

			self::trace( "[bmc] About to EXIT discardCard (via nextState'discardCard')." );

			$buyTimerStatus = $this->getBuyTimerStatus();
			self::dump( "[bmc] buyTimerStatus (stPlayerTurnPlay):", $buyTimerStatus );

			// Discarded the card, move on
			$this->gamestate->nextState( 'discardCard' );

			// Make note that the JS BUY timers have started, but not for the current and next players.
			$this->setAllBuyTimerStatus( $activeTurnPlayer_id, $nextTurnPlayer );

		} else {
			throw new BgaUserException( self::_("You cannot discard, it's not your turn.") );
		}
		self::trace("[bmc] EXIT discardCard (from JS)");
    }
////////
////////
////////
    function drawCard( $card_id, $drawSource, $player_id ) {
		self::trace("[bmc] STAND-IN: Draw Card (from JS)"); // Just see that we got here from the Javascript.
		self::dump("card id:", $card_id );
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

		// If drawing from the discard pile then the player get the top card, not necessarily the one they clicked
		if ( $drawSource == 'discardPile' ) {
			$topDiscard = $this->cards->getCardOnTop( 'discardPile' );
			self::dump( "[bmc] topDiscard: ", $topDiscard );
			
			$card_id = $topDiscard[ 'id' ];
		} // else use the card which was passed in.
		
        $this->cards->moveCard( $card_id, 'hand', $player_id );

		$countCardsByLocation = $this->cards->countCardsByLocationArgs( 'hand' );
		//self::dump("[bmc] CCBL aftermove:", $countCardsByLocation);

        $currentCard = $this->cards->getCard( $card_id );
		
		// drawSource Sources (OLD):
		// 0 == 'deck' (buyer gets it + 1 down card; Increment buy counter)
		// 1 == 'discardPile' (buyer gets nothing)
		// 2 == Other sources (other conditions like playing a card for a joker)
		 
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

		$activeTurnPlayer_id = $this->getGameStateValue( 'activeTurnPlayer_id' );
		$this->drawNotify( $currentCard, $player_id, $drawSource, $player_id, $activeTurnPlayer_id );
	}
////////
////////
////////
	function notifyPlayerIsNotBuying( $player_id ) {
		self::trace("[bmc] ENTER notifyPlayerBuy-NOT");
		self::dump("[bmc] notifyPlayerIsNotBuying - player_id:",  $player_id);
		
		self::setBuyTimerStatus( $player_id, 0 ); // 0 = Not running. 1 = Running.

		$players = self::loadPlayersBasicInfos();

		$buyers = self::getPlayerBuying();

		self::notifyAllPlayers(
			'playerNotBuying',
			'${player_name} does not want the discard.',
			array(
				'player_id' => $player_id,
				'player_name' => $players[ $player_id ][ 'player_name' ],
				'buyers' => $buyers
			)
		);
		self::trace("[bmc] EXIT notifyPlayerBuy-NOT");
	}
////////
////////
////////
	function notifyPlayerWantsToBuy( $player_id ) {
		self::trace("[bmc] ENTER notifyPlayerBuy-WANT");
		self::dump("[bmc] player_id:",  $player_id);

		self::setBuyTimerStatus( $player_id, 0 ); // 0 = Not running. 1 = Running.

		$players = self::loadPlayersBasicInfos();
		$activeTurnPlayer_id = $this->getGameStateValue( 'activeTurnPlayer_id' );
		self::dump("[bmc] ATPI", $activeTurnPlayer_id );

		// TODO Maybe change this to wait for timeout instead of actively setting nonmultiactive??
		foreach ( $players as $pid => $player ){
			self::dump("[bmc] $pid", $pid );
			if ( $pid != $activeTurnPlayer_id ) {
				$this->gamestate->setPlayerNonMultiactive( $pid, '' );
			}
		}

		$currentCard = $this->cards->getCardOnTop( 'discardPile' );
		self::dump( "[bmc] cardToBeBought:",  $currentCard );
		
		if ( $currentCard[ 'type_arg' ] == 5 ) {
			$value_displayed = ' a joker';
			$color_displayed = '!';
		} else {
			$value_displayed = 'the ' . $this->values_label[ $currentCard[ 'type_arg' ]] . ' of ';
			$color_displayed = $this->colors[ $currentCard[ 'type' ]][ 'name' ] . 's.';
		}
		
		// $buyMessage = $players[ $player_id ][ 'player_name' ] . 
			// '<span style="color:#' . $players[ $player_id ][ "player_color" ] . ';">' . $players[ $player_id ][ "player_name" ] . '</span>';
			// ' wants to buy ' . 
			// $value_displayed . 
			// $color_displayed;
		
//		self::dump( "[bmc] buyMessage:",  $buyMessage );

		if ( $currentCard[ 'type' ] == 5 ) {
			$value_displayed = ' a joker';
			$color_displayed = '!';
		} else {
			$value_displayed = 'the ' . $this->values_label[ $currentCard[ 'type_arg' ]] . ' of ';
			$color_displayed = $this->colors[ $currentCard[ 'type' ]][ 'name' ] . 's.';
		}

		self::notifyAllPlayers(
			'playerWantsToBuy',
			'${player_name} wants to buy ${value_displayed}${color_displayed}',
			array(
				'player_id' => $player_id,
//				'player_name' => $players[ $player_id ][ 'player_name' ],
				'player_name' => $players[ $player_id ][ 'player_name' ],
				'cardToBeBought' => $currentCard,
				'value_displayed' => $value_displayed,
				'color_displayed' => $color_displayed
			)
		);
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
//		self::dump('[bmc] playingPlayer_id :',  $playingPlayer_id );
//		self::dump('[bmc] player_name      :',  $activePlayer );
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
			$value_displayed = ' a joker';
			$color_displayed = '!';
		} else {
			$value_displayed = 'the ' . $this->values_label[ $currentCard[ 'type_arg' ]] . ' of ';
			$color_displayed = $this->colors[ $currentCard[ 'type' ]][ 'name' ] . 's.';
		}
		
		foreach ( $players as $player_id => $player ) {
//			self::dump('[bmc] player_id :',  $player_id );
//			self::dump('[bmc] activeTurnPlayer_id :',  $activeTurnPlayer_id );
			
			if ( $player_id == $activeTurnPlayer_id ) {
				self::notifyPlayer(
					$player_id,
					'drawCard',
					'You drew ${value_displayed}${color_displayed}',
					array(
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
						'drawDeckSize' => $drawDeckSize
					)
				);
			} else {
				self::notifyPlayer(
					$player_id,
					'drawCard',
					'${player_name} draws a card from the ${drawSourceText}.',
					//'',
					array(
						'player_id' => $playingPlayer_id,
						'player_name' => $activePlayer,
						'card_id' => $card_id,
						'value' => '',
						'value_displayed' => '',
						'color' => '',
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

		// Notify spectators of the draw too
		self::notifyAllPlayers(
			'drawCardSpect',
			//'${player_name} draws a card from the ${drawSourceText}.',
			'',
			array(
				'player_id' => $playingPlayer_id,
				'player_name' => $activePlayer,
				'card_id' => $card_id,
				'value' => '',
				'value_displayed' => '',
				'color' => '',
				'color_displayed' => '',
				'drawSource' => $drawSource,
				'drawSourceText' => $drawSourceText,
				'drawPlayer' => $drawPlayer,
				'allHands' => $cardsByLocation,
				'discardSize' => $discardSize,
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
			// Else got card from a true draw (deck or discard), so resolve buyers and whatnot.
			self::trace("[bmc] MAYBE ERROR AREA IN DRAWNOTIFY");
			$this->gamestate->nextState( 'resolveBuyers' );
		}
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
		
		$jokerCount = 0;

		self::dump("[bmc] cardIDGroupA:", $cardIDGroupA);
		self::dump("[bmc] cardIDGroupB:", $cardIDGroupB);
		self::dump("[bmc] cardIDGroupC:", $cardIDGroupC);
				
		$cardGroupA = $this->cards->getCards( $cardIDGroupA );
		$cardGroupB = $this->cards->getCards( $cardIDGroupB );
		$cardGroupC = $this->cards->getCards( $cardIDGroupC );
		
		foreach ([ $cardGroupA, $cardGroupB, $cardGroupC ] as $cardGroup ) {
			$jokerCount += ( $this->checkForJoker( $cardGroup ) == false ) ? 0 : 1 ;
		}
		
		self::dump("[bmc] Joker Count while going down:", $jokerCount);
/*
		if ( $jokerCount > 1 ) {
			throw new BgaUserException( self::_('You cannot go down with more than 1 joker.') );
			return;
		}
*/		
		// Make sure there is > 1 card left in hand
		$countCardsInPlayerHand = intval($this->cards->countCardsByLocationArgs( 'hand' )[$active_player_id]);
		self::dump("CCIPH:", $countCardsInPlayerHand);
		
		$countCardsToPlay = $cntCardGroupA + $cntCardGroupB + $cntCardGroupC;
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
		$locationMeanings = $this->setsRuns[ $currentHandType ];
		$key = array_search( $areaTitle, $locationMeanings ); // False if not there. Integer if there.
		
		self::dump("[bmc] BC: ", $boardCard);
		self::dump("[bmc] BA: ", $boardArea);
		self::dump("[bmc] HI: ", $handItems);
		self::dump("[bmc] CHT:", $currentHandType);
		self::dump("[bmc] AT:", $areaTitle);
		self::dump("[bmc] LM:", $locationMeanings);
		self::dump("[bmc] Key:", $key);
		
		$joker = array ('id' => 'None'); // Start by assuming no joker being swapped
		$targetArea = 'None'; // Start by assuming no target area for the going-down-joker

//		if (( $key === false ) ||          // Nothing selected on board;
		if (( $boardArea == null ) ||          // Nothing selected on board;
			( empty( $handItems )) ||      // Nothing selected in hand;
			( $boardCard['type'] < 5  )) { // Board card is not a joker; So no need to do joker swap.
			
			self::trace( "[bmc] No Joker Swap Needed." );
			//... and then continue to try to go down...
			
		} else { // Player has selected board and hand cards. This means try to use joker to go down.
			$jokerSwapResult = $this->tryJokerSwap( $handItems['id'], $active_player_id, $boardArea, $boardPlayer );
			self::dump('[bmc] jokerSwapResult', $jokerSwapResult);
		
			// Move the new joker into the deficient area

			$targetArea = $this->findDeficientArea( $cardGroupA, $cardGroupB, $cardGroupC );
			
			if ( $targetArea == false ) {
				throw new BgaUserException( self::_('There is no deficient area. Should never happen.') );
			}

			$playerHand = $this->cards->getCardsInLocation( 'hand', $active_player_id );
			
			$joker = $this->checkForJoker( $playerHand );
			self::dump("[bmc] Hand Joker:", $joker);

			// Now we know where to put the joker so add it there and finish going down
			//$this->cards->moveCard( $joker['id'], $targetArea, $active_player_id);
			switch ( $targetArea ) {
				case "playerDown_A":
					$cardGroupA[] = $joker;
					break;
				case "playerDown_B":
					$cardGroupB[] = $joker;
					break;
				case "playerDown_C":
					$cardGroupC[] = $joker;
					break;
			}
		}
	
		self::dump("[bmc] cardGroupA:", $cardGroupA );
		self::dump("[bmc] cardGroupB:", $cardGroupB );
		self::dump("[bmc] cardGroupC:", $cardGroupC );
		self::dump("[bmc] joker:", $joker );
		self::dump("[bmc] targetArea:", $targetArea );

		$this->playerGoDownFinish( $cardGroupA, $cardGroupB, $cardGroupC, $joker, $targetArea );
	}

////////
////////
////////
	function findDeficientArea ( $cardGroupA, $cardGroupB, $cardGroupC ) {
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
		return false;
		}
		return $targetArea;
	}
////////
////////
////////
	function isShortRun( $cardGroup ) {
		self::dump( "[bmc] isShortRun: ", $cardGroup );
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
		self::trace("[bmc] playerGoDownFinish");
self::dump("[bmc] cardGroupA", $cardGroupA);
self::dump("[bmc] cardGroupB", $cardGroupB);
self::dump("[bmc] cardGroupC", $cardGroupC);
		
		// Verify the number of needed sets and runs is met

		$currentHandType = $this->getGameStateValue( 'currentHandType' );

		self::dump("[bmc] CHT", $currentHandType);

		$setsNeeded = $this->handTypes[$currentHandType]["QtySets"];
		$runsNeeded = $this->handTypes[$currentHandType]["QtyRuns"];
		
		self::dump("[bmc] SN", $setsNeeded);
		self::dump("[bmc] RN", $runsNeeded);

		$setsHave = 0;
		$runsHave = 0;
		$notSetRun = 0;

		$groups = array ($cardGroupA, $cardGroupB, $cardGroupC);
		
		//self::dump("[bmc] groups:", $groups);

		foreach ( $groups as $group ) {
			self::dump("[bmc] group:", $group );
			
			if ( $this->checkSet( $group ) == true ) {
				$setsHave++;
			} else if ( $this->checkRun( $group ) == true ) {
				$runsHave++;
			} else {
				if ( count( $group ) > 0 ) {
					$notSetRun++;
				}
			}
		}
		
		if  ( $notSetRun > 0 ) {
			throw new BgaUserException( self::_('Cannot go down with those cards.') );
		}
		
		self::dump("[bmc] setsHave:", $setsHave);
		self::dump("[bmc] runsHave:", $runsHave);

		$notEnoughSetRun = 0;
		
		if ( $setsHave < $setsNeeded ) {
			 $notEnoughSetRun += 1;
		}
		if ( $runsHave < $runsNeeded ) {
			 $notEnoughSetRun += 10;
		}

		self::dump("[bmc] notEnoughSetRun:", $notEnoughSetRun);
		
		switch ($notEnoughSetRun) {
			case 0:  // Valid goDown
				break;
			case 1:  // Not enough sets
				throw new BgaUserException( self::_('Not enough Sets.') );
				break;
			case 10: // Not enough runs
				throw new BgaUserException( self::_('Not enough Runs.') );
				break;
			case 11: // Not enough sets and not enough runs
				throw new BgaUserException( self::_('Not enough Sets and not enough Runs.') );
				break;
		}
		
		$active_player_id = self::getActivePlayerId();

		list( $card_typeA, $card_type_argA ) = $this->checkIfReallyInHand( $cardGroupA, $active_player_id );
		list( $card_typeB, $card_type_argB ) = $this->checkIfReallyInHand( $cardGroupB, $active_player_id );
		list( $card_typeC, $card_type_argC ) = $this->checkIfReallyInHand( $cardGroupC, $active_player_id );

		self::trace("[bmc] Cards are in hand!");
		
		$cardIDGroupA = $this->makeCardIdsFromCards( $cardGroupA );
		$cardIDGroupB = $this->makeCardIdsFromCards( $cardGroupB );
		$cardIDGroupC = $this->makeCardIdsFromCards( $cardGroupC );
		
		// Put the cards into the down area
		$this->cards->moveCards( $cardIDGroupA, 'playerDown_A', $active_player_id );
		$this->cards->moveCards( $cardIDGroupB, 'playerDown_B', $active_player_id );
		$this->cards->moveCards( $cardIDGroupC, 'playerDown_C', $active_player_id );
		
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

		// Notify all players about the cards played
		self::notifyAllPlayers('playerGoDown',
			clienttranslate('${player_name} went down.'),
			array(
				'player_name' => self::getActivePlayerName(),
				'player_id' => $active_player_id,
				'joker' => $joker,
				'targetArea' => $targetArea,
				'allHands' => $cardsByLocation
			)
		);
		
		$cpn = self::getActivePlayerName();		
		self::dump( "[bmc] colored player_name", $cpn );

		self::trace("[bmc] GO DOWN DONE!");

		self::setPlayerGoneDown( $active_player_id, 1 /* 0 (not gone down) or 1 (gone down) */ );

	}
////////
////////
////////
	function tryJokerSwap ( $card_id, $player_id, $boardArea, $boardPlayer) {
		self::trace("[bmc] tryJokerSwap");

		if ($this->checkSet($this->cards->getCardsInLocation( $boardArea, $boardPlayer ))) {
			// TODO: Might need try / catch here, i'm not sure how BgaUserException works
			self::trace("[bmc] Swapping joker in SET while going down - verify it!");
			
			// Try to play the card for the joker as part of going down
			$this->playCardFinish( $card_id, $player_id, $boardArea, $boardPlayer );
			
			self::trace("[bmc] Pulled the joker off the table");

			// Now finish going down
			return 1;
			
		} else { // Check if run
			self::trace("[bmc] Swapping joker in RUN while going down - verify it!");

			// If the cards w/o joker are still a run then take the joker, if not then don't
			
			$cardsInArea = $this->cards->getCardsInLocation( $boardArea, $boardPlayer);
			$mightBeJoker = $this->checkForJoker( $cardsInArea );

			self::dump("[bmc] cardsInArea:", $cardsInArea);
			self::dump("[bmc] mightBeJoker:", $mightBeJoker);
			
			$maybeNewRun = $cardsInArea;
			
			// Potentially remove the joker
			unset( $maybeNewRun[ $mightBeJoker['id']] );
			
	        $card = $this->cards->getCard($card_id);

			// Potentially add the hand card

			$maybeNewRun[$card['id']] = $card; // Keep the index of the new potential card

			if ( $this->checkRun( $maybeNewRun )) { // If it's still a run, take the joker
				self::trace("[bmc] Take the joker, haven't used it yet.");

//				if ( $boardCard['type'] == 5 ) { // If a joker is there

				$usedTheJoker = true;
		
				//$this->cards->moveCard($card_id, $boardArea, $boardPlayer);
				$this->playCardFinish( $card_id, $player_id, $boardArea, $boardPlayer );
		
				self::trace("[bmc] Replace with the card.");
				$this->cards->moveCard($mightBeJoker['id'], 'hand', $player_id);
		
				// And notify of the joker being 'drawn' from the down area
				$activeTurnPlayer_id = $this->getGameStateValue( 'activeTurnPlayer_id' );
				$this->drawNotify( $mightBeJoker, $player_id, $boardArea, $boardPlayer, $activeTurnPlayer_id );

				// Store the joker and card swapped in case we need to undo
				self::setGameStateValue( "forJokerCard_id", $card_id );
				self::setGameStateValue( "forJokerBoardArea", ord(substr( $boardArea, -1) )); // Must store int
				self::setGameStateValue( "forJokerBoardPlayer", $boardPlayer );
				self::setGameStateValue( "forJokerTheJoker_id", $mightBeJoker['id'] );
				self::setGameStateValue( "forJokerPlayerID", $boardPlayer );
			} 

			self::trace("[bmc] Pulled the joker off the table");

			// Now finish going down

			return 2;
		}
	}
////////
////////
////////
	function checkIfReallyInHand ($cards, $player_id) {
		$card_type = array ();
		$card_type_arg = array ();
		foreach( $cards as $card ) {
			//self::dump("[bmc] CheckInHand: ", $card);
			
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
	function checkRun( $cards ) {
		// A run is 4 or more cards of the same suit with sequential values or
		//   3 cards plus 1 joker. A234 and JQKA are both valid.
		
		self::dump("[bmc] checkRun cards: ", $cards);
		
		$cardCount = count( $cards );
		if ( $cardCount < 4) {
			self::trace("[bmc] checkRun FALSE (not enough cards)");
			return false;
		}

		$nonJokers = array();
		// Check if all non jokers are different values
		foreach ( $cards as $card ) {
			if ( $card[ 'type' ] != 5 ) {
				$nonJokers[] = $card[ 'type_arg' ];
			}
		}
		self::dump("[bmc] nonJokers: ", $nonJokers );
		
		$countValues = array_count_values( $nonJokers );
		
		self::dump("[bmc] countValues: ", $countValues );
		
		foreach ( $countValues as $type => $qty ) {
			if ( $qty > 1 ) {
				throw new BgaUserException( self::_("Nice try but run values must be sequential and unique.") );
			}
		}
		
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
				// unset($aceHighCards[$aceKey]); // Remove the low ace // No need to remove, we've just changed its value above.
			}
		}
		
		self::dump("[bmc] New cards (cards):", $cards);
		self::dump("[bmc] New cards (aceLowCards):", $aceLowCards);
		self::dump("[bmc] New cards (aceHighCards):", $aceHighCards);

		$tryAceLow = $this->checkRunWithAce( $aceLowCards );
		$tryAceHigh = $this->checkRunWithAce( $aceHighCards );
		
		self::dump("[bmc] Check Run aceLowCards",  $tryAceLow );
		self::dump("[bmc] Check Run aceHighCards", $tryAceHigh );
		
		$aceCheckResult = $tryAceLow + $tryAceHigh ;
		
		switch ( $aceCheckResult ) {
			case 0:
			case 1:
				break; // With ace high or low, one is a run and all the same suit
			case 2:
				throw new BgaUserException( self::_("Nice try but it doesn't reach!") );
				break;
			case 10:
			case 11:
			case 20:
				throw new BgaUserException( self::_('Run cards must all be the same suit.') );
				break;
			default :
				throw new BgaUserException( self::_("Ace Check error.") );
				break;
		}				
		self::dump("[bmc] checkRun cards: ", $cards );
		
		self::trace("[bmc] checkRun is TRUE because we made it this far.");
		return true; // Made it through, so the cards are a run
	}
////
////
////
	function checkRunWithAce ( $cards ) {
		// Check all the same suit and that the jokers bridge the gaps
		$cardType = 0;
		$cardValueMax = 0;  // Track the larget and smallest in the group
		$cardValueMin = 20; // Track the larget and smallest in the group
		$jokerCount = 0; // Count the jokers to know if the cards all reach

		foreach ( $cards as $card ) {
			if ( $card['type'] == "5") {
				// Ignore Joker (type == 5)
				$jokerCount += 1;
			} else {
				if ($card['type_arg'] > $cardValueMax) { // Find the largest card value
					$cardValueMax = $card['type_arg'];
				}
				
				if ($card['type_arg'] < $cardValueMin) { // Find the smallest card value
					$cardValueMin = $card['type_arg'];
				}

//		self::dump("[bmc] Max", $cardValueMax);
//		self::dump("[bmc] Min", $cardValueMin);
				if ( $cardType == 0 ) {
					// Get the suit of the first card which is not a joker
					$cardType = $card['type'];
//					self::dump("[bmc] cardType:", $cardType);
				} else {
//					self::dump("[bmc] card: ", $card);
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
		self::dump("[bmc] jokerCount:", $jokerCount );

		if ( $cardValueMax - $cardValueMin + 1  <= $cardCount ) {
			self::trace("[bmc] checkRun TRUE");
			return 0;

		} else {
			self::trace("[bmc] checkRun FALSE (Doesn't reach)");
			//throw new BgaUserException( self::_("Nice try but it doesn't reach!") );
			return 1;
		}
	}
////
////
////
	function checkForJoker( $cards ) {
		self::dump("[bmc] ENTER check for joker in cards: ", $cards);

			foreach ( $cards as $card ) {
//			self::dump("[bmc] checkForJoker card:",  $card );
			//self::dump("[bmc] checkForJoker card type:", $card['type']);
			if ( $card['type'] == "5") {
				self::trace("[bmc] Found Joker");
				return $card;
			}
		}
		self::trace("[bmc] EXIT check for joker in cards (none).");
		return false;
	}
////
////
////
	function checkSet( $cards ) {
		// A set is 3 or more cards of the same number or
		//   2 cards of the same value plus 1 joker.
		self::dump("[bmc] checkSet Cards: ", $cards);
		
		if ( count( $cards ) < 3) {
			self::trace("[bmc] checkSet: FALSE. Not enough cards.");
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
					self::trace("[bmc] checkSet: FALSE. Values not the same.");
					return false;
				}
			}
		}
		self::trace("[bmc] checkSet: TRUE");
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
		
		$this->playCardFinish( $card_id, $player_id, $boardArea, $boardPlayer );
		self::trace( "[bmc] EXIT playCard" );
	}
////
////
////
	function playCardMultiple( $card_ids, $player_id, $boardArea, $boardPlayer ) {
		self::trace( "[bmc] ENTER playCardMultiple (from ACTION from JS)" );
		// Validate the player has the card in hand
		// validate the card can be played there
		//   If the target card is a joker, take the joker & replace
		// Move the card(s) around
		// Notify the players

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
		
		foreach ( $card_ids as $card_id ) {
			self::dump( "[bmc] (MULTIPLE Playing Card", $card_id );
			$this->playCardFinish( $card_id, $player_id, $boardArea, $boardPlayer );
		}
		self::trace( "[bmc] EXIT playCardMultiple" );
	}
////
////
////
	function getCardNotJoker( $boardArea, $boardPlayer ) {
		$cardsInArea = $this->cards->getCardsInLocation( $boardArea, $boardPlayer);

		foreach( $cardsInArea as $card ) {
			if( $card['type'] != 5 ) {
				self::dump("[bmc] getCardNotJoker:", $card);
				return $card;
			} else {
				self::trace("[bmc] Found a joker, keep looking for a non-joker.");
			}
		}
		$card = reset( $cardsInArea ); 
		self::dump("[bmc] All Jokers! Returning one of them:", $card);
		
		return $card;
	}
////
////
////
	function playCardFinish( $card_id, $player_id, $boardArea, $boardPlayer ) {
		self::trace( "[bmc] ENTER playCardFinish" );
		// Validate the player has the card in hand
		// validate the card can be played there
		//   If the target card is a joker, take the joker & replace
		// Move the card(s) around
		// Notify the players

		$currentCard = $this->cards->getCard( $card_id );

		list($card_typeA, $card_type_argA) = $this->checkIfReallyInHand( [$currentCard], $player_id );

		self::dump("[bmc] Cards on board BOARD AREA:", $boardArea);
		self::dump("[bmc] Cards on board BOARD PLAYER:", $boardPlayer);

		$cardsInArea = $this->cards->getCardsInLocation( $boardArea, $boardPlayer);
		
		self::dump("[bmc] Cards on board CARDS IN AREA:", $cardsInArea );
		self::dump("[bmc] Card being played CARD TYPE:", $card_typeA );
		self::dump("[bmc] Card being played CARD TYPE:", $card_typeA[0] );
		self::dump("[bmc] Card being played CARD TYPE ARG:", $card_type_argA );
		self::dump("[bmc] currentCard:", $currentCard );

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
//					$playWeight = $this->cards->countCardInLocation($boardArea) + 100;
					$this->cards->moveCard( $card_id, $boardArea, $boardPlayer, $playWeight);
				} else if ($mightBeJoker != false) { 

					if ( $this->getCardNotJoker( $boardArea, $boardPlayer )['type_arg'] == $card_type_argA[ 0 ] ) {
						$this->takeTheJoker( $mightBeJoker, $player_id, $card_id, $boardArea, $boardPlayer );					
					} else {
						self::trace("[bmc] 1701: Not same values for set.");
						throw new BgaUserException( self::_('Cannot play that card on that set.') );
					}
				} else {
					// Made it this far, so play it 
					self::trace("[bmc] Play card on set.");
//					$playWeight = $this->cards->countCardInLocation($boardArea) + 100;
					$this->cards->moveCard( $card_id, $boardArea, $boardPlayer, $playWeight);
				}

				// And notify of the played card
				
				self::trace("[bmc] Notify of played card");
				
				$color_displayed = 'the ' . $this->colors[ $currentCard[ 'type' ]][ 'name' ] . ' ';
				$value_displayed = $this->values_label[ $currentCard[ 'type_arg' ]];
				
				if ( $currentCard[ 'type' ]  == 5 ) {
					$color_displayed = 'a joker';
					$value_displayed = '';
				}

				$cardsByLocation = $this->cards->countCardsByLocationArgs( 'hand' );

				self::notifyAllPlayers(
					'cardPlayed',
					clienttranslate('${player_name} plays ${color_displayed}${value_displayed}.'),
					array (
						'card_id' => $card_id,
						'player_id' => $player_id,
						'player_name' => self::getActivePlayerName(),
						'value' => $currentCard ['type_arg'],
						'value_displayed' => $value_displayed,
						'color' => $currentCard ['type'],
						'color_displayed' => $color_displayed,
						'boardArea' => $boardArea,
						'boardPlayer' => $boardPlayer,
						'allHands' => $cardsByLocation
					)
				);
			} else {
				self::trace("[bmc] 1460 not same values for set.");
				throw new BgaUserException( self::_('Cannot play that card on that set.') );
			}
		} else if ( $this->checkRun( $cardsInArea ) == true ) {
			self::trace("[bmc] Trying to play onto a run.");
			
			// If playing a joker, then just play it
			if ( $card_typeA[0] == 5) {
				self::trace("[bmc] Play the joker.");
				$this->playOnRunAndNotify( $card_id, $boardArea, $boardPlayer, $playWeight, $player_id, $currentCard );
				
			// if there is no joker on the board, then try to play the card
			} else if ( $mightBeJoker == false) {
				self::trace("[bmc] No joker on board, try to play the card.");
				self::dump("[bmc] cardsInArea: ", $cardsInArea);
				
				$potentialNewRun = $cardsInArea;
				
				 // Add card to area cards to try it as a run
				$potentialNewRun[ $currentCard[ 'id' ]] = $currentCard;
				self::dump("[bmc] checking the potential run is still a run.", $potentialNewRun );
				
				if ( $this->checkRun( $potentialNewRun ) == true ) {
					self::trace("[bmc] Playing the card onto the run.");
					
					$this->playOnRunAndNotify( $card_id, $boardArea, $boardPlayer, $playWeight, $player_id, $currentCard );
				} else {
					self::trace("[bmc] With that card, the cards are not a run.");
					throw new BgaUserException( self::_('Cannot play that card on that run.') );
				}
			} else {
				self::trace("[bmc] YES joker on board, try to play the card.");
				// If here, then there's a joker on the board.
				// Try adding card and remove the joker; check if a run, if yes, then swap.
				// Try adding card and check if it's a run, if yes then play it.
				$potentialNewRun = $cardsInArea;
				
				unset( $potentialNewRun[ $mightBeJoker[ 'id' ]]);
				$potentialNewRun[ $currentCard[ 'id' ]] = $currentCard;
				
				try {
					self::trace("[bmc] First try playing without the joker.");
					
					$this->checkRun( $potentialNewRun );
					
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

					if ( $this->checkRun( $potentialNewRun ) == true ) {
						$this->playOnRunAndNotify( $card_id, $boardArea, $boardPlayer, $playWeight, $player_id, $currentCard );
					}
				}
			}
		} else {
			self::trace("[bmc] Not a Set and Not a Run!");
				throw new BgaUserException( self::_('Not a Set and Not a Run (shouldnt happen!).'));
		}

		$buyers = self::getPlayerBuying();
		self::dump("[bmc] Buyers Status(playCardFinish):", $buyers);
		self::trace( "[bmc] EXIT playCardFinish" );
	}
////
////
////
	function playOnRunAndNotify( $card_id, $boardArea, $boardPlayer, $playWeight, $player_id, $currentCard ) {
		self::trace("[bmc] ENTER playOnRunAndNotify.");
		// TODO: Fix the play weight on the board for runs:
		$playWeight = $this->cards->countCardInLocation( $boardArea ) + 100;
		$this->cards->moveCard( $card_id, $boardArea, $boardPlayer, $playWeight );

		// And notify of the played card
	
		self::trace("[bmc] Notify of played card");

//		$color_displayed = 'the ' . $this->colors[ $currentCard[ 'type' ]][ 'name' ] . ' ';
//		$value_displayed = $this->values_label[ $currentCard[ 'type_arg' ]];

		if ( $currentCard[ 'type' ] == 5 ) {
			$value_displayed = ' a joker';
			$color_displayed = '!';
		} else {
			$value_displayed = 'the ' . $this->values_label[ $currentCard[ 'type_arg' ]] . ' of ';
			$color_displayed = $this->colors[ $currentCard[ 'type' ]][ 'name' ] . 's.';
		}

		self::notifyAllPlayers(
			'cardPlayed',
//			clienttranslate('${player_name} plays the ${color_displayed} ${value_displayed} '),
			clienttranslate('${player_name} plays ${value_displayed}${color_displayed}.'),
			array (
				'card_id' => $card_id,
				'player_id' => $player_id,
				'player_name' => self::getActivePlayerName(),
				'value' => $currentCard ['type_arg'],
				'value_displayed' => $this->values_label[ $currentCard[ 'type_arg' ]],
				'color' => $currentCard ['type'],
				'color_displayed' => $this->colors[ $currentCard[ 'type' ]][ 'name' ],
				'boardArea' => $boardArea,
				'boardPlayer' => $boardPlayer
			)
		);
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
		self::setGameStateValue( "forJokerCard_id", $card_id );
		self::setGameStateValue( "forJokerBoardArea", ord(substr( $boardArea, -1) )); // Must store int
		self::setGameStateValue( "forJokerBoardPlayer", $boardPlayer );
		self::setGameStateValue( "forJokerTheJoker_id", $mightBeJoker['id'] );
		self::setGameStateValue( "forJokerPlayerID", $boardPlayer );
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
        // self::notifyAllPlayers( "cardPlayed", clienttranslate( '${player_name} plays ${card_name}' ), array(
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
		self::debug("[bmc] ENTER stWentOut");
		$this->gamestate->setAllPlayersMultiactive();

		// Notify players to review their hands and click to continue

		$activeTurnPlayer_id = $this->getGameStateValue( 'activeTurnPlayer_id' );

		$players = self::loadPlayersBasicInfos();

		$player_name = $players[ $activeTurnPlayer_id ][ 'player_name' ];

/*
TODO: Maybe check if there were no more playable cards and show that message.
		$cardsInHd = $this->cards->countCardsByLocationArgs( 'hand' );

		foreach( $cardsInHd as $playerCount ) {
			if ( $playerCount == 0 ) {
				$message = '${player_name} went out!';
			} else {
				$message = 'No more playable cards.';
			}
		}
*/
        self::notifyAllPlayers(
			'wentOut',
			clienttranslate( '${player_name} went out!' ),
			array(
				'player_id' => $activeTurnPlayer_id,
				'player_name' => $players[ $activeTurnPlayer_id ][ 'player_name' ]
			)
		); 
		
		$this->calcDisplayScoreDialog();
		self::debug("[bmc] EXIT stWentOut");
	}
////
////
////
	function calcDisplayScoreDialog() {
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
					case ( $card[ 'type_arg' ] === 1 ): // 15 points	
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

                self::notifyAllPlayers("points", clienttranslate( '${player_name} gets ${nbr} points' ), array (
                        'player_id' => $player_id,'player_name' => $players[ $player_id ][ 'player_name' ],
                        'nbr' => $point_number ));
            } else {
		        // No point lost (just notify)
                self::notifyAllPlayers("points",
					clienttranslate('${player_name} did not get any points'),
					array (
                        'player_id' => $player_id,
						'player_name' => $players[ $player_id ][ 'player_name' ]
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
		$secondRow = array( 'This Hand:' );
		$thirdRow = array( 'Total:' );

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

		// Show a dialog of the scores for each player for this hand
        foreach ( $player_to_points as $player_id => $points ) {
			if ( $player_id == $activeTurnPlayer_id ) {
				$this->notifyPlayer(
					$player_id,
					"tableWindow", '', array(
						"id" => 'handScoring',
						"title" => clienttranslate( "Woot! You went out! You want the most positive score:" ),
						"table" => $table,
						"closing" => clienttranslate( "On to the next!" )
					)
				); 
			} else {
				$this->notifyPlayer(
					$player_id,
					"tableWindow", '', array(
						"id" => 'handScoring',
						"title" => clienttranslate( "Bummer! " . $players[ $activeTurnPlayer_id ][ 'player_name' ] . " went out! Points are bad:" ),
						"table" => $table,
						"closing" => clienttranslate( "On to the next!" )
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
		$player_id = $this->getCurrentPlayerId(); // CURRENT!!! not active
		 
        self::notifyAllPlayers(
			'wentOut',
			'',
			array(
				'ackPlayer' => $player_id
			)
		); 
		// Deactivate player; if none left, transition to next '' state
		$this->gamestate->setPlayerNonMultiactive( $player_id, '' );
	}
////
////
////
    function stNewHand() {
		self::debug("[bmc] ENTER stNewHand");
		
        // Take back all cards (from any location => null) to deck
        $this->cards->moveAllCardsInLocation( null, "deck" );

		$bob = $this->cards->countCardInLocation( 'deck' );
		
        // Shuffle deck
        $this->cards->shuffle( 'deck' );
		
		self::setGameStateValue( 'shuffleCount', 0 ); // Reset the shuffle count every hand

        // Deal some cards to each players
        $players = self::loadPlayersBasicInfos();
		
		// Deal 10 or 12 cards to each player
		// Put 1 card in the discard pile
		// Put the rest into the draw deck
		// Notify players of the situation
		
		$currentHandType = $this->getGameStateValue( 'currentHandType' );
		$qtyToDeal = $this->handTypes[ $currentHandType ][ "deal" ];
		
		self::dump( "[bmc] qtyToDeal:", $qtyToDeal );

		foreach ( $players as $player_id => $player ) {
//			$this->cards->pickCards( 10, 'deck', $player_id );
			$this->cards->pickCards( $qtyToDeal, 'deck', $player_id );
			self::setPlayerGoneDown($player_id, 0 /* 0 (not gone down) or 1 (gone down) */);
		}
		
		// Put 1 card from the deck into the discard pile and give it a starting weight of 100
		$this->cards->moveCard( $this->cards->getCardOnTop ( 'deck' )[ 'id' ], 'discardPile', 100); 
		
		// The rest of the cards are in 'deck'

//		$this->view->build_page();
		
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
		
		self::notifyAllPlayers( // Including spectators
			'newHand',
			clienttranslate('New Hand! ${dealer} has dealt the cards. New target is ${handTarget}.'),
			array(
				'deck' => array_keys($this->cards->getCardsInLocation( 'deck' )),
				'discardPile' => $this->cards->getCardsInLocation( 'discardPile' ),
				'discardSize' => $discardSize,
				'handTarget' => $handTarget,
				'allHands' => $cardsByLocation,
				'buyCount' => $buyCount,
				'dealer' => $dealer_name,
				'drawDeckSize' => $drawDeckSize
			)
		);
		
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

    function stCheckEmptyDeck() {
		self::trace("[bmc] ENTER stCheckEmptyDeck");
		
		$activePlayerId = $this->getActivePlayerId();
		self::dump("[bmc] activePlayerId:", $activePlayerId );
		
		$countCardsInDeck = $this->cards->countCardInLocation( 'deck' );
		self::dump("[bmc] Card in deck:", $countCardsInDeck );
		
		if ( $countCardsInDeck == 0 ) {
			$discards = $this->cards->getCardsInLocation('discardPile');
			
			self::dump("[bmc] Shuffling. Discards: ", $discards);
			self::dump("[bmc] card on top:", $this->cards->getCardOnTop ( 'deck' )[ 'id' ]);
			
			$card_ids = array_keys($discards);

			self::dump("[bmc] card_ids: ", $card_ids);
			
			$this->cards->moveCards($card_ids, 'deck');
	
			$this->cards->shuffle('deck');

			$shuffleCount = self::incGameStateValue( 'shuffleCount', 1 ); // Keep track of shuffles

			// Trigger the auto-shuffle by trying to draw a card:
			// Put 1 card from the deck into the discard pile and give it a starting weight of 100
			
			self::notifyAllPlayers(
				"deckShuffled",
				clienttranslate( 'Discard pile shuffled into deck.' ),
				array(
					'deck' => array_keys( $this->cards->getCardsInLocation('deck'))
				)
			);
		}
		
		$buyers = self::getPlayerBuying();

		//self::dump("[bmc] Buyers Status(playerIsBuying1):", $buyers);
		
		$someoneIsBuying = false;
		
		foreach( $buyers as $buyer ) {
			if ( $buyer == 2 ) {
				$someoneIsBuying = true;
			}
		}

		self::trace("[bmc] EXIT (almost) stCheckEmptyDeck");

		if ( $someoneIsBuying ) {
			$this->gamestate->nextState('letPlayerDrawAfterBuy');
		} else {
			$this->gamestate->nextState('drawAndLetPlayerPlay');
		}
	}
////
////
////
// TODO: Fix this: Now every client shows YOU MUST DRAW A CARD. But JS knows whose turn it really is.
// TODO: Probably should split the states into:
//      1: Draw cards first available players have not yet determined buy / not buy
//      2: As players determine buy / not buy, jump to buy resolution state
//      3: In buy resolution state, keep track of all the players buying and not buying
//      4: In buy resolution, if active player take the discard then no one else can buy it
//      5: If they draw the deck card, then someone else can buy. The main player cannot discard until the buy is resolved.
// 

	function stDrawDeck() {
		self::trace( "[bmc] ENTER stDrawDeck:" );
        $this->gamestate->nextState("");
		self::trace( "[bmc] EXIT stDrawDeck:" );
	}
	function stDrawDiscard() {
		self::trace( "[bmc] ENTER stDrawDiscard:" );
        $this->gamestate->nextState("");
		self::trace( "[bmc] EXIT stDrawDiscard:" );
	}
	function stResolveBuyers() {
		self::trace( "[bmc] ENTER stResolveBuyers:" );
		
		// If source is deck or discard then resolve appropriately.
		// If not either of those then stay in this state since we're swapping a joker

		$drawSourceValue = self::getGameStateValue( 'drawSourceValue' );

		self::dump("[bmc] drawSourceValue:", $drawSourceValue );
		
		$buyCount = self::getPlayersBuyCount();
		self::dump("[bmc] buyCount:", $buyCount );

		$someoneIsBuying = false;
		
		$buyers = self::getPlayerBuying();

		self::dump("[bmc] buyers(stResolveBuyers):", $buyers);
		self::clearPlayersBuying(); // EXP 10/27
		
		foreach( $buyers as $player_id => $buyer ) {
			//self::dump("bmc] player_id: ", $player_id);
			//self::dump("bmc] buyer: ", $buyer);
			
			if ( $buyer == 2 ) {
				$someoneIsBuying = $player_id ;
			}
		}
		self::dump("[bmc] someoneIsBuying: ", $someoneIsBuying);

		// drawSource Sources:
		// 0 == 'deck' (buyer gets it + 1 down card; Increment buy counter)
		// 1 == 'discardPile' (buyer gets nothing)
		// 2 == Other sources (other conditions like playing a card for a joker)
	
		if ( $drawSourceValue == 0 ) {
			self::trace( "[bmc] TurnPlayer Drawing from deck, so a buy will go through if it exists.");
			
			//self::dump("[bmc] Buyers Status(stResolveBuyers):", $buyers);
			
//				throw feException("Someone is buying but no one requested!");
			if ( $someoneIsBuying != false ) {
				self::decPlayerBuyCount( $someoneIsBuying );
				$buyCount = self::getPlayersBuyCount();

				//Move the cards for the buyer (the turnPlayer will get their cards in drawCard)
				
				// Notify of the deck card (i.e. the price to pay for the discarded card)
				$currentCard = $this->cards->getCardOnTop( 'deck' );
				self::dump("bmc] Card from deck: ", $currentCard);
				$this->cards->moveCard( $currentCard[ 'id' ], 'hand', $someoneIsBuying );
			
				$this->drawNotify( $currentCard, $someoneIsBuying, 'deck', $someoneIsBuying, $someoneIsBuying );

				// Notify of the discarded card (notify the buyer of the details, not the current turn player
				$currentCard = $this->cards->getCardOnTop( 'discardPile' );
				self::dump("bmc] Card Bought: ", $currentCard);
				$this->cards->moveCard( $currentCard[ 'id' ], 'hand', $someoneIsBuying );
				
				$this->drawNotify( $currentCard, $someoneIsBuying, 'discardPile', $someoneIsBuying, $someoneIsBuying );
				
				if ( $currentCard[ 'type' ] == 5 ) {
					$value_displayed = ' a joker';
					$color_displayed = '.';
				} else {
					$value_displayed = 'the ' . $this->values_label[ $currentCard[ 'type_arg' ]] . ' of ';
					$color_displayed = $this->colors[ $currentCard[ 'type' ]][ 'name' ] . 's.';
				}

				$cardsByLocation = $this->cards->countCardsByLocationArgs( 'hand' );

				$players = self::loadPlayersBasicInfos();
				self::dump( "[bmc] players:", $players );
				
				self::notifyAllPlayers(
					'playerBought',
					clienttranslate('(${player_name} bought ${value_displayed}${color_displayed})'),
					array (
						'player_name' => $players[ $someoneIsBuying ][ 'player_name' ],
						'color_displayed' => $color_displayed,
						'value_displayed' => $value_displayed,
						'player_id' => $someoneIsBuying,
						'buyCount' => $buyCount,
						'allHands' => $cardsByLocation
					)
				);
			}
			$this->gamestate->nextState("checkEmptyDeck");

		} else if ( $drawSourceValue == 1 ) {
			self::trace( "[bmc] TurnPlayer Drawing from discard, so buy will NOT go through.");
			
			// Set players buy status to NOT BUY
			$players = self::loadPlayersBasicInfos();
			foreach ( $players as $player_id => $player ) {
				self::setPlayerBuying( $player_id, 1 );
			}

			if ( $someoneIsBuying != false ) {
				
				$players = self::loadPlayersBasicInfos();
				
				//self::dump( "[bmc] colored player name:", $players[ $someoneIsBuying ][ 'player_name' ]);
				
				self::notifyAllPlayers(
					'playerDidNotBuy',
					clienttranslate('${player_name} tried but could not buy the discard.'),
					array (
						'player_name' => $players[ $someoneIsBuying ][ 'player_name' ],
						'player_id' => $someoneIsBuying
					)
				);
			}
			
			$this->gamestate->nextState("drawDiscard");
		} else {
			self::trace( "[bmc] Resolve Buyers Other path" );
			$this->gamestate->nextState("other");
		}

		self::trace( "[bmc] EXIT (truly) stResolveBuyers:" );
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
		
		self::dump("[bmc] activeTurnPlayer_id:", $activeTurnPlayer_id );
		self::dump("[bmc] discardingPlayer_id:", $discardingPlayer_id );

		// If the dealer cannot buy the card, then uncomment this. If they are OK to buy then comment it out.
//		$this->gamestate->setPlayerNonMultiactive( $discardingPlayer_id, 'discardCard' );

		$this->clearBuyers();

		// TODO: Might have to allow for dealer to buy here in the first deal:
		self::setPlayerBuying( $activeTurnPlayer_id, 1 ) ; // 1 = not buying, they can get it for free

		$skipFirstDeal = self::getGameStateValue( 'skipFirstDeal' );
		
		if ( $skipFirstDeal == false ) {
			self::setPlayerBuying( $discardingPlayer_id, 1 ) ; // 1 = not buying, they just discarded it
		} else {
			self::setGameStateValue( 'skipFirstDeal' , false );
		}
		
		$buyers = self::getPlayerBuying();
		self::dump("[bmc] Buyers Status(stShowBUYButtons):", $buyers);

		//self::dump("[bmc] Buyers Status Buying (ShowBuyButtons):", $buyers);
		
		$APL = $this->gamestate->getActivePlayerList();
		self::dump( "[bmc] APL(stShowBUYButtons):", $APL);

		self::trace( "[bmc] EXIT stShowBUYButtons:" );
	}
/////
/////
/////
	function notBuyRequest( $player_id ) { 
		self::dump("[bmc] ENTER NotBuyRequest:", $player_id );
		self::checkAction('notBuyRequest');
		
		$activePlayerId = self::getActivePlayerId();
		
		self::dump("[bmc] activePlayerId:", $activePlayerId );
		
		self::setBuyTimerStatus( $player_id, 0 ); // 0 = Not running. 1 = Running.

		// Tell the database there is a buyer (0==unknown, 1==Not buying 2==Buying)
		self::setPlayerBuying( $player_id, 1 );

		//$buyers = self::getPlayerBuying();

		//self::dump("[bmc] Buyers Status Not Buying (ShowBuyButtons):", $buyers);
	
		$this->notifyPlayerIsNotBuying( $player_id );

		$buyers = self::getPlayerBuying();
		self::dump("[bmc] Buyers Status1(notBuyRequest):", $buyers);
		self::trace("[bmc] EXIT (maybe) stNotBuyRequest:" );

		// deactivate player; if none left, transition to process discard
		$this->gamestate->setPlayerNonMultiactive( $player_id, 'resolveBuyers' );

		self::trace("[bmc] EXIT (truly) NotBuyRequest:" );
	}
////
////
////
	function buyRequest( $player_id ) {
		self::trace("[bmc] ENTER buyRequest");
		$player_id = $this->getCurrentPlayerId(); // CURRENT!!! not active
		self::dump("[bmc] player_id:", $player_id);
		// Check if can still buy or if someone else clicked the button

		$players = self::loadPlayersBasicInfos();
		self::dump("[bmc] players (buyRequest):", $players);
		
		self::checkAction('buyRequest');

		self::setBuyTimerStatus( $player_id, 0 ); // 0 = Not running. 1 = Running.

		$activeTurnPlayer_id = self::getGameStateValue( 'activeTurnPlayer_id' );

		if ( $player_id == $activeTurnPlayer_id ) {
			throw new BgaUserException( self::_("You don't need to buy it, it's your turn.") );
		}
		
		// If there aren't enough cards, don't allow it
		$countDeck = count( $this->cards->countCardsByLocationArgs( 'deck' ) );
		$countDiscardPile = count ($this->cards->countCardsByLocationArgs( 'discardPile' ) );

		if (( $countDeck + $countDiscardPile ) < 2 ) {
			throw new BgaUserException( self::_('There are not enough down cards for you to buy.') );
		}
		
		$buyers = self::getPlayerBuying();
		self::dump("[bmc] Buyers Status1(buyRequest):", $buyers);
		
		$buyMethod = self::getGameStateValue( 'buyMethod' );
 
		if ( $buyMethod == '1' ) { // 1==Fastest player. 2==Seat order.
			foreach( $buyers as $buyer ) {
				if ( $buyer == 2 ) {
					throw new BgaUserException( self::_('Sorry someone else wants to buy it.') );
				}
			}
		} else { // $buyMethod == Seat order
			$playerOrder = self::getNextPlayerTable();
			foreach( $buyers as $buyer_id => $buyChoice ) {
				self::dump("[bmc] buyer_id:", $buyer_id);
				self::dump("[bmc] buyChoice:", $buyChoice);

				self::dump("[bmc] p(pid)", $players[ $player_id ]);
				self::dump("[bmc] p(pid)", $players[ $player_id ]['player_no']);
//GOOD
				// If the requester is lower than the discarder, then adjust for the wrap
				if ( $players[ $activeTurnPlayer_id ][ 'player_no' ] > $players[ $player_id ][ 'player_no' ] ) {
					$buyerCompare = $players[ $player_id ][ 'player_no' ] + count( $players );
				} else {
					$buyerCompare = $players[ $player_id ][ 'player_no' ];
				}

				self::dump("[bmc] buyerCompare:", $buyerCompare );
//GOOD
				if ( $buyChoice == 2 ) {
					$deltaSeat = $players[ $buyer_id ][ 'player_no' ] - $buyerCompare;
					self::dump("[bmc] deltaSeat:", $deltaSeat );
					
					if ( $deltaSeat > 0 ) {
						throw new BgaUserException( self::_('Sorry someone ahead of you wants to buy it.') );
					}
				}
			}
		}
		
		// If the foreach exited then no one else is buying, so this player can
		
		$playersBuyCount = self::getPlayersBuyCount();
		
		if ( $playersBuyCount[ $player_id ] < 1 ) {
			throw new BgaUserException( self::_("You cannot buy any more this hand(buyRequest).") );
		}
		// If it passes through to here (i.e. buy is possible) then log the request.
		// Tell the database there is a buyer (0==unknown, 1==Not buying 2==Buying)		
		$players = self::loadPlayersBasicInfos();
		self::dump("[bmc] players (buyRequest):", $players);
		
		foreach ( $players as $pid => $player ) {
			if ( $player_id == $pid ) {
				self::dump("[bmc] SET TO 2", $pid );
				self::setPlayerBuying( $pid, 2 );
			} else {
				self::dump("[bmc] SET TO 1", $pid);
				self::setPlayerBuying( $pid, 1 );
			}
		}			
		
		$buyers = self::getPlayerBuying();

		self::dump("[bmc] Buyers Status2(buyRequest):", $buyers);

		$this->notifyPlayerWantsToBuy( $player_id );
		self::trace("[bmc] EXIT (almost) buyRequest");

		// deactivate player; if none left, transition to process discard
		$this->gamestate->setPlayerNonMultiactive( $player_id, 'resolveBuyers' );

	}
////
////
////	
	function clearBuyers() {
		self::trace("[bmc] ENTER clearBuyers.");
		$players = self::loadPlayersBasicInfos();
		foreach ( $players as $player_id => $player) {
			self::setPlayerBuying( $player_id, 0 ); // Every discard clear the buyers (0==unknown, 1==Not buying 2==Buying)
		}
	}
////
////
////	
	function setAllBuyTimerStatus( $thisPlayer, $nextPlayer ) {
		self::trace("[bmc] ENTER setAllBuyTimerStatus.");
		$players = self::loadPlayersBasicInfos();
		
		foreach ( $players as $player_id => $player) {
			if (( $player_id == $thisPlayer ) ||
			    ( $player_id == $nextPlayer )) {
				// Don't make note of the current and next players because they cannnot buy.
				self::setBuyTimerStatus( $player_id, 0 ); // 0 = Not running. 1 = Running.
			} else {
				self::setBuyTimerStatus( $player_id, 1 ); // 0 = Not running. 1 = Running.
			}
		}
	}
////
////
////
    function stPlayerTurnPlay() {
		self::trace("[bmc] ENTER stPlayerTurnPlay");
				
		// TODO 11/1 Here should wait until all the BUY / NOT BUY requests are in.
		// Only then can continue. But now there is a bug.
		// To duplicate the bug:
		// Discard
		// Draw
		// Discard
		// Let buy timer expire (player is supposed to draw but timer is ticking)
		// Click DRAW (no card is drawn)
		//
		// The structure should be this, but playing cards and going down 
		//   remains in the same state (35) so all we need is to exit when discarding.
		// if ( allbuy timers are not running then play, else stay in this state ) {
			// $this->gamestate->nextState( 'playerGoDown' );
		// } else if (  ) {
			// $this->gamestate->nextState( 'discardCard' );
		// } else {
			// $this->gamestate->nextState( 'playCard' );
		// }
	}
////
////
////
    function stWaitForAll() {
		self::trace( "[bmc] ENTER stWaitForAll" );
		
		$buyers = self::getPlayerBuying();
		self::dump("[bmc] buyers:", $buyers );
		
		$waiting = false;
		
		foreach ( $buyers as $buyer => $buying ) {
			if ( $buying == 0 ) {
				self::dump("[bmc] Wwaaiiting for buyer:", $buyer );
				$waiting = true;
			}
		}
		
		if ( $waiting ) {
			self::trace( "[bmc] Still waiting, loop around (eventually)" );
		} else {
			self::trace( "[bmc] No longer waiting" );
		}
		self::trace( "[bmc] about to go to fullyResolved" );
		$this->gamestate->nextState( 'fullyResolved' );
		self::trace( "[bmc] EXIT (truly - does this ever run?) stWaitForAll" );
	}
////
////
////
    function stNextPlayer() {
		self::trace("[bmc] ENTER NextPlayer");
		
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
		
		if (( $countCCBL != $playersNumber) ||  		// Someone has gone out
			( $shuffleCount > 5 ) || 					// The deck has been shuffled too much
			( $this->checkPlayable() != true )) {	    // All playable cards have been played
				
			$this->gamestate->nextState( "endHand" );
			
		} else {
			// Next player can draw and play etc...

			// Show State
			$state = $this->gamestate->state();
			//self::dump("[bmc] stNextPlayer state:", $state);

			$activePlayerId = $this->getActivePlayerId();
			self::dump( "[bmc] activePlayerId (before change):", $activePlayerId );

			$previous_player_id = $this->getPlayerBefore( $activePlayerId );
			$next_player_id = $this->getPlayerAfter( $activePlayerId );
			
			self::dump( "[bmc] previous_player_id:", $previous_player_id );
			self::dump( "[bmc] next_player_id:", $next_player_id );
			
			//$this->gamestate->changeActivePlayer( $next_player_id );
			$this->activeNextPlayer();
			
			$activePlayerId = $this->getActivePlayerId();
			self::dump( "[bmc] activePlayerId (after change):", $activePlayerId );

			// Store the previous player so they don't get the offer to buy their own discard.
			// Cannot do this in a multiactive state, so we must do it right before it.
			
			self::setGameStateValue( "previous_player_id" , $previous_player_id );
			self::setGameStateValue( 'activeTurnPlayer_id', $activePlayerId );

			// Clear all the buyers
			self::trace( "[bmc] stNextPlayer clearing buyers.");
			$this->clearBuyers();
		
			$discardingPlayer_id = $this->getPlayerBefore( $activePlayerId );

			self::setPlayerBuying( $discardingPlayer_id, 1 ) ; // 1 = not buying, they just discarded it
			self::setPlayerBuying( $activePlayerId, 1 ) ; // 1 = not buying, they can get it for free

			// Just make sure it stuck!
			$activePlayerId = $this->getActivePlayerId();
			self::dump( "[bmc] activePlayerId (exiting nextPlayer inside if):", $activePlayerId );

			// Show State
			$state = $this->gamestate->state();
			//self::dump("[bmc] stNextPlayer state:", $state);

			$this->gamestate->nextState( 'nextPlayer' );					
		}
	}
////
////
////
	function checkPlayable() {
		//self::trace( "[bmc] ENTER checkUnplayable" );

		$playedCardArray = [];
		$unplayedHandArray = [];
		$unplayedDeckArray = [];
		$unplayedDPArray = [];
		
		$unplayedHandArray = $this->cards->getCardsInLocation( 'hand' );
		//$unplayedDeckArray = $this->cards->getCardsInLocation( 'deck' );
		$unplayedDPArray = $this->cards->getCardsInLocation( 'discardPile' );
		
		$unplayedCardArray = array_merge( $unplayedHandArray, $unplayedDeckArray, $unplayedDPArray );
		
		//self::dump("[bmc] unplayedCardArray:", $unplayedCardArray );
	
//		$playedCardArray = $this->cards->getCardsInLocation( 'playerDown_A' );
//		$playedCardArray[] = $this->cards->getCardsInLocation( 'playerDown_B' );
//		$playedCardArray[] = $this->cards->getCardsInLocation( 'playerDown_C' );

		$playedCardArray = array_merge(
			$this->cards->getCardsInLocation( 'playerDown_A' ),
			$this->cards->getCardsInLocation( 'playerDown_B' ),
			$this->cards->getCardsInLocation( 'playerDown_C' )
			);

		//self::dump("[bmc] playedCardArray:", $playedCardArray );

		$playedValues = array();
		$jokersPlayed = 0;
		
		foreach( $playedCardArray as $card ) {
//			self::dump("[bmc] foreachplayedcard :", $card );
			if ( count( $card ) != 0 ) {
				$playedValues[] = $card[ 'type_arg' ];
			
				if ( $card[ 'type' ] == 5 ) {
					$jokersPlayed++;
				}
			}
		}
		//self::dump("[bmc] playedValues :", $playedValues );
			
		$unplayedValues = array();
		
		foreach( $unplayedCardArray as $card ) {
//			self::dump("[bmc] foreachunplayedcard :", $card );
			if ( $card[ 'id' ] != null ) {
				$unplayedValues[] = $card[ 'type_arg' ];
			}
		}
		self::dump("[bmc] count(UPV) :", count( $unplayedValues ));

		//self::dump("[bmc] unplayedValues :", $unplayedValues );

		foreach( $unplayedValues as $value ) {
			
			if ( in_array( $value, $playedValues )) {
				self::trace( "[bmc] Still a playable card." );
				return true; // Still can play some values onto sets
			}
		}

		// Check if all jokers have been played
		$numberOfDecks = self::getGameStateValue( 'numberOfDecks' );
		
		if ( $jokersPlayed < ( 2 * $numberOfDecks )) {
			//self::trace( "[bmc] Still a playable joker." );
			return true; // Still can play at least 1 joker
		}

		// TODO: Might need to check if there are no more playable run cards

		// If we got here, then there are no unplayable cards on sets, which means the hand may end
		//self::trace( "[bmc] EXIT checkUnplayable" );
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

		self::dump( "[bmc] players: ", $players );
		self::dump( "[bmc] activeTurnPlayer_id:", $activeTurnPlayer_id );
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
		self::trace("[bmc] !!stEndHand!!");
// SCORING WAS HERE PREVIOUSLY
		
		// Notify players and wait for them to confirm to move to the next hand		
		
        // Next hand target
		$gameLengthOption = self::getGameStateValue( 'gameLengthOption' ); // 1 is full or 2 is short
		self::dump( "[bmc] gameLengthOption:", $gameLengthOption );
		self::incGameStateValue( 'currentHandType', $gameLengthOption );

        ///// Test if this is the end of the game
		$currentHandType = $this->getGameStateValue( 'currentHandType' );

		self::dump("[bmc] currentHandType stEndHand:", $currentHandType );
		self::dump("[bmc] handTypes stEndHand:", $this->handTypes );
		
		if ( $currentHandType > 6 ) { // The 7 hand numbers are 0 through 6
			$this->gamestate->nextState("endGame");
		} else {

			$gameLengthOption = self::getGameStateValue( 'gameLengthOption' ); // 1 is full or 2 is short
			self::dump( "[bmc] gameLengthOption:", $gameLengthOption );
			
			if ( $gameLengthOption == 1 ) {
				$this->setsRuns = $this->setsRunsFull;
				$this->handTypes = $this->handTypesFull;
			} else {
				$this->setsRuns = $this->setsRunsShort;
				$this->handTypes = $this->handTypesShort;
			}

			$currentHandType = self::getGameStateValue( 'currentHandType' );
			$handTarget = $this->handTypes[ $currentHandType ][ "Target" ]; // Pull the description
			self::dump("[bmc] handTarget stEndHand:", $handTarget );
			
			// Notify players to go to the next target hand
			
			$newScores = self::getCollectionFromDb("SELECT player_id, player_score FROM player", true );

			self::notifyAllPlayers( "newScores", '', array(
				'newScores' => $newScores,
				'handTarget' => $handTarget,
				'currentHandType' => $currentHandType
				));

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
		self::setBuyTimerStatus( $active_player, 0 ); // 0 = Not running. 1 = Running.
    	
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
//
//
    }    
}

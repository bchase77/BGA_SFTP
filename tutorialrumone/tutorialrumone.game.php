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
			"area_A_target" => 11,
			"area_B_target" => 12,
			"area_C_target" => 13,
			"forJokerCard_id" => 20,
			"forJokerBoardArea" => 21,
			"forJokerBoardPlayer" => 22,
			"forJokerTheJoker_id" => 23,
			"forJokerPlayerID" => 24,
			"activeTurnPlayer_id" => 25,
			"previous_player_id" => 26,
			"drawSource" => 27
//			"buyingPlayer" => 25
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
		self::trace("[bmc] !!setupNewGame!!"); // Doesn't appear in log!

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
            $values[] = "('".$player_id."','$color','".$player['player_canal']."','".addslashes( $player[ 'player_name' ] )."','".addslashes( $player['player_avatar'] )."')";
			
			// Initialize which sets and runs have been done by each player
//			$this->setsHave[$player_id] = 0; // Point to the 1st in the array 'setsRuns' (first set location)
//			$this->runsHave[$player_id] = 3; // Point to the 4th in the array 'setsRuns' (first run location)
		
		
// GONEDOWN VARIABLE must be cleared to 0 after each hand.
//			$this->goneDown[$player_id] = 0;
        }
        $sql .= implode( $values, ',' );
        self::DbQuery( $sql );
        self::reattributeColorsBasedOnPreferences( $players, $gameinfos['player_colors'] );
        self::reloadPlayersBasicInfos();

        self::setGameStateInitialValue( 'currentHandType', 0 );
		$currentHandType = $this->getGameStateValue( 'currentHandType' );
		
		//self::dump("[bmc] handtypes(line138):", $this->handTypes[$currentHandType]);
		
        self::setGameStateInitialValue( 'area_A_target', $this->handTypes[$currentHandType]["Area_A"]);
        self::setGameStateInitialValue( 'area_B_target', $this->handTypes[$currentHandType]["Area_B"]);
        self::setGameStateInitialValue( 'area_C_target', $this->handTypes[$currentHandType]["Area_C"]);


        // Activate first player (which is in general a good idea :) )
        $player_id = $this->activeNextPlayer();
		//$players = self::loadPlayersBasicInfos();
		
		//$currentTurnPlayer_id = $this->nextTurnPlayer();

		self::setGameStateInitialValue( 'drawSource', 2 ); // 0 = deck, 1 = discardPile. This should be set 
		// every time drawCard is called but including it here for completeness.
		//self::dump("[bmc] currentTurnPlayer_id:", $currentTurnPlayer_id );

		$activePlayerId = $this->getActivePlayerId();
		self::dump("[bmc] activeTurnPlayer_id:", $activePlayerId );

		// Store which player's turn it really is for access during the multipleactiveplayer state.
		self::setGameStateInitialValue( 'activeTurnPlayer_id', $activePlayerId );
		
		//foreach( $players as $player_id => $player ) {
		//	if ( $currentTurnPlayer_id == $player_id ) {
		//		$currentTurnPlayer_id = $this->nextTurnPlayer(); // Shift it by one
		//	}
		//} // When exiting, they should now be in sync
		//self::setGameStateInitialValue( 'currentTurnPlayer_id', $currentTurnPlayer_id );
		//self::setGameStateInitialValue( 'currentTurnPlayer_id', 0 );
		//self::setGameStateInitialValue( 'currentTurnPlayer_id', $player_id );
		
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

		self::trace("[bmc] !!makingCards!!");
		
        // Add jokers
        $jokers = array ();
		$color = 5;
		for ($value = 1; $value <= 2; $value ++) {
			//$jokers [] = array ('type' => $color, 'type_arg' => $value, 'nbr' => 1 );
			array_push( $cards, array ('type' => $color, 'type_arg' => $value, 'nbr' => 1 ));
		}

        $this->cards->createCards( $cards, 'deck' );

		// Set to autoreshuffle discardPile into deck
	
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
	
    protected function getAllDatas()
    {
		// This returns data to the JS code in gamedatas datastructure
		self::trace("[bmc] !!getAllDatas!!");

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
		
		$result['handTypes']["Target"] = $this->handTypes; // Pull the description
		
		$players = self::loadPlayersBasicInfos();
		
//		self::dump( "[bmc] players:", $players );
		
        $playerGoneDown = self::getPlayerGoneDown(); // It's an array, one for each player.
		
		$buyers = self::getPlayerBuying();
		//self::dump("[bmc] gamedatas buyers:", $buyers);

		foreach ( $players as $player_id => $player ) {
			self::dump("[bmc] getAllDatas - player_id:", $player_id);
			//self::dump("[bmc] player:", $player);
//			self::dump("[bmc] SH:", $this->setsHave[$player_id]);
//			self::dump("[bmc] RH:", $this->runsHave[$player_id]);
			
			$result['downArea_A_'][$player_id] = $this->cards->getCardsInLocation( 'playerDown_A' , $player_id );
			$result['downArea_B_'][$player_id] = $this->cards->getCardsInLocation( 'playerDown_B' , $player_id );
			$result['downArea_C_'][$player_id] = $this->cards->getCardsInLocation( 'playerDown_C' , $player_id );

			$result['goneDown'][$player_id] = $playerGoneDown[$player_id];		
			
			$result['buyers'][$player_id] = $buyers[$player_id];
		}

//var_dump($result);
//die('okSev');
		
		$result['currentHandType'] = self::getGameStateValue( 'currentHandType' );

		$result['discardPile'] = $this->cards->getCardsInLocation( 'discardPile' );
		
		self::trace("[bmc]discardPile:");
//var_dump($result['discardPile']);
//print_r($result['discardPile']);
//die('okSev');
//var_dump($result);
//die('okSev');
        
        // Cards played on the table
        $result['cardsontable'] = $this->cards->getCardsInLocation( 'cardsontable' );

		$debugCount = $this->cards->countCardsInLocations();
		self::trace("[bmc] !!cardsinlocations!!");
		
		$activeTurnPlayer_id = $this->getGameStateValue( 'activeTurnPlayer_id' );
		
		$result[ 'activeTurnPlayer_id' ] = $activeTurnPlayer_id ;
  
		self::dump("[bmc] activeTurnPlayer_id", $activeTurnPlayer_id );

		$playerOrder = self::getNextPlayerTable();
		self::dump( "[bmc] playerOrder: ", $playerOrder );

		$result['playerOrderTrue'] = $playerOrder;

		//$this->getTurnPlayer();

		// Just make sure it stuck!
		$activePlayerId = $this->getActivePlayerId();
		self::dump( "[bmc] GETALLDATAS activePlayerId:", $activePlayerId );

		// Show State
		$state = $this->gamestate->state();
		self::dump("[bmc] GETALLDATAS state:", $state);

        return $result;
    }

	// function getTurnPlayer() {
		// $turnPlayerArray = $this->getNextPlayerTable();
		// $turnPlayerId = $turnPlayerArray[ 0 ];
		// $players = self::loadPlayersBasicInfos();

		// self::dump( "[bmc] turnPlayerArray:", $turnPlayerArray );
// //		self::dump( "[bmc] players:", $players );

		// $turnPlayerName = $players[ $turnPlayerId ][ 'player_name' ];
		// self::dump( "[bmc] turnPlayerName:", $turnPlayerName );
		
		// return array ( $turnPlayerName, $turnPlayerArray );
	// }



	function argPlayerTurnDraw() {
		$currentHandType = $this->getGameStateValue( 'currentHandType' );
		
		$playerGoneDown = self::getPlayerGoneDown(); // It's an array, one for each player.
		//TODO: HOW TO FIND THE CURRENT PLAYER? $currentPlayer = $this->getCurrentPlayerName();
		//$currentPlayerId = $this->getCurrentPlayerId();
		//$currentPlayerId = $this->getActivePlayerId();
		//TODO: HOW TO FIND THE CURRENT PLAYER? $activePlayer = $this->getActivePlayerName();
		//$currentPlayer = $this->getActivePlayerName();
		//$activePlayerId = $this->getActivePlayerId();
		//$activePlayer = $this->getActivePlayerName();
		
		$gtActivePlayerId = $this->getActivePlayerId();
		self::dump("[bmc] GAME THINKS ACTIVE PLAYER:", $gtActivePlayerId );
		
		$activeTurnPlayer_id = self::getGameStateValue( 'activeTurnPlayer_id' );
		
		$players = self::loadPlayersBasicInfos();
		$activePlayer = $players[ $activeTurnPlayer_id ][ 'player_name' ];

		self::dump("[bmc] activeTurnPlayer_id:", $activeTurnPlayer_id );

		//list( $turnPlayerName, $turnPlayerArray ) = $this->getTurnPlayer();
		
		$buyers = self::getPlayerBuying();
		self::dump("[bmc] gamedatas buyers (PTD):", $buyers);

		if ( $playerGoneDown[ $activeTurnPlayer_id ] == 1 ) {
			$thingsCanDo = 'play or discard.';
		} else {
			$thingsCanDo = 'play, discard or go down.';
		}
		
		self::dump("[bmc] currentHandType argPlayerTurnDraw:", $this->handTypes[$currentHandType]["Target"] );
		self::dump("[bmc] thingsCanDo:", $thingsCanDo );
		self::dump("[bmc] activePlayer(PTD):", $activePlayer );
		
        return array(
			'handTarget' => $this->handTypes[$currentHandType]["Target"], // Pull the description
			'thingsCanDo' => $thingsCanDo,
			//'activePlayer' => $activePlayer,
			'turnPlayerName' => $activePlayer,
			'buyers' => $buyers
        );
    }

	function argPlayerTurnPlay() {
		self::trace("[bmc] ENTER argPlayerTurnPlay");

		$currentHandType = $this->getGameStateValue( 'currentHandType' );

		$playerGoneDown = self::getPlayerGoneDown(); // It's an array, one for each player.
		
		// $currentPlayer = $this->getActivePlayerName();
		$gtActivePlayerId = $this->getActivePlayerId();
		// $activePlayer = $this->getActivePlayerName();
		// $activePlayerId = $this->getActivePlayerId();

		self::dump("[bmc] GAME THINKS ACTIVE PLAYER:", $gtActivePlayerId );
		//list( $turnPlayerName, $turnPlayerArray ) = $this->getTurnPlayer();
		
		// I think I could get the active player ID and name from getActivePlayerId() and getActivePlayerName() also because this is not a multipleactiveplayer state.
		
		$activeTurnPlayer_id = self::getGameStateValue( 'activeTurnPlayer_id' );
		
		$players = self::loadPlayersBasicInfos();
		$activePlayer = $players[ $activeTurnPlayer_id ][ 'player_name' ];

		self::dump("[bmc] activeTurnPlayer_id:", $activeTurnPlayer_id );

		$buyers = self::getPlayerBuying();
		self::dump("[bmc] gamedatas buyers(PTP):", $buyers);

		if ( $playerGoneDown[ $activeTurnPlayer_id ] == 1 ) {
			$thingsCanDo = 'play or discard.';
		} else {
			$thingsCanDo = 'play, discard or go down.';
		}
		
		self::dump("[bmc] currentHandType argPlayerTurnPlay:", $this->handTypes[$currentHandType]["Target"] );
		self::dump("[bmc] thingsCanDo:", $thingsCanDo );
		self::dump("[bmc] activePlayer(PTP):", $activePlayer );

		self::trace("[bmc] EXIT argPlayerTurnPlay");

        return array(
			'handTarget' => $this->handTypes[$currentHandType]["Target"], // Pull the description
			'thingsCanDo' => $thingsCanDo,
//			'currentPlayer' => $currentPlayer,
			'turnPlayerName' => $activePlayer,
			//'turnPlayerArray' => $turnPlayerArray,
			'buyers' => $buyers
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
		$currentPlayer = $this->getActivePlayerName();
		$currentHandType = $this->getGameStateValue( 'currentHandType' );
		self::dump("[bmc] currentHandType argMyArgumentMethod:", $this->handTypes[$currentHandType]["Target"]);
		
		return array(
            'currentPlayer' => $currentPlayer,
			'handTarget' => $this->handTypes[$currentHandType]["Target"] // Pull the description
        );    
    }
	
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

	function discardCard( $card_id, $player_id ) {
		self::trace( "[bmc] ENTER discardCard (from JS via action.php)" );
		//$activeTurnPlayer_id_GAP = self::getActivePlayerId();
		$activeTurnPlayer_id = self::getGameStateValue( 'activeTurnPlayer_id' );

		self::dump("[bmc] Discarding player id:", $player_id );
		//self::dump("[bmc] activeTurnPlayer_id_GAP:", $activeTurnPlayer_id_GAP );
		self::dump("[bmc] activeTurnPlayer_id:", $activeTurnPlayer_id );
		self::dump("[bmc] getActivePlayerName:", self::getActivePlayerName() );
		
		if ( $activeTurnPlayer_id == $player_id ) { // Allow discard if it's that player's turn
			
			//list( $turnPlayerName, $turnPlayerArray ) = $this->getTurnPlayer();
		
			$discardWeight = $this->cards->countCardInLocation( 'discardPile' ) + 100;
			$this->cards->moveCard( $card_id, 'discardPile', $discardWeight );
			
			$nextTurnPlayer = $this->getPlayerAfter( $player_id );
			
			self::dump("[bmc] nextTurnPlayer:", $nextTurnPlayer );

//var_dump($thisPlayerId);
//die('okSev');
		
			// XXX check rules here
			$currentCard = $this->cards->getCard($card_id);

			// And notify
			self::notifyAllPlayers(
				'discardCard',
				clienttranslate('${player_name} discards the ${color_displayed} ${value_displayed} '),
				array (
					'player_id' => $activeTurnPlayer_id,
					'player_name' => self::getActivePlayerName(),
					'color' => $currentCard ['type'],
					'color_displayed' => $this->colors [$currentCard ['type']] ['name'],
					'value' => $currentCard ['type_arg'],
					'value_displayed' => $this->values_label [$currentCard ['type_arg']],
					'card_id' => $card_id,
					'nextTurnPlayer' => $nextTurnPlayer
					//'turnPlayer' => $turnPlayerName,
					//'turnPlayerArray' => $turnPlayerArray
				)
			);
			
			
			// TODO: 10/13 State is playerTurnPlay here, so setting nonmultieactive is not meanginful??
			//       I think this setting needs to go in the next game state, which is 37, because
			//       you can set multiactive stuff only in game states, and this PTP is a ACTIVE state.
			// Set the discarding player as non active and go to the next state
			//$this->gamestate->setPlayerNonMultiactive( $player_id, 'discardCard' );
		}
		
		//TODO: This should not be here. It should flow to set all to nonmultiactive, //then go to discard.
		//Compare to HEARTS code.
		self::trace( "[bmc] EXIT discardCard (via nextState( 'discardCard')." );

		$this->gamestate->nextState( 'discardCard' );
		
		// If not then do nothing because the requesting player cannot discard.
		// DO NOT PUT ANOTHER NEXT STATE AFTER THE SETPLAYERNONMULTIACTIVE!
    }
////////
////////
////////
/*
   function drawDiscard() {
		self::trace("[bmc] STAND-IN: Draw Discard "); // Just see that we got here from the Javascript.
		$this->drawDiscard2();
	}
////////
////////
////////
    function drawDiscard2() {
		self::trace("[bmc] !! DrawDiscard2!!");
        self::checkAction("drawDiscard");
        $player_id = self::getActivePlayerId();
		$topDiscard = $this->cards->getCardOnTop('discardPile');
		self::dump("[bmc] topDiscard", $topDiscard);
		
        $this->cards->moveCard($topDiscard, 'hand', $player_id);
		
		// Notify players of the draw from the discard pile
		$drawSource = 'discardPile';
		$this->drawNotify($topDiscard, $player_id, $drawSource);
	}
*/
////////
////////
////////
    function drawCard( $card_id, $drawSource, $player_id ) {
		self::trace("[bmc] STAND-IN: Draw Card "); // Just see that we got here from the Javascript.
		self::dump("card id:", $card_id );
		self::dump("Drawing player id:", $player_id );
        self::checkAction("drawCard");
		
//		TODO: Identify who is truly the active player.
		$activeTurnPlayer_id = $this->getGameStateValue( 'activeTurnPlayer_id' );
		
		self::dump("[bmc] activeTurnPlayer_id", $activeTurnPlayer_id );
		
		if ( $player_id != $activeTurnPlayer_id ) {
			throw new BgaUserException( self::_("You cannot draw, it's not your turn.") );
		}

		$countCardsByLocation = $this->cards->countCardsByLocationArgs( 'hand' );
		//self::dump("[bmc] CCBL:", $countCardsByLocation);

        $this->cards->moveCard( $card_id, 'hand', $player_id );

		$countCardsByLocation = $this->cards->countCardsByLocationArgs( 'hand' );
		self::dump("[bmc] CCBL aftermove:", $countCardsByLocation);

		// Notify players of the draw from the deck
		//$drawSource = 'deck';
		if ($drawSource == 0 ) {
			$drawSource = 'deck';
		} else {
			$drawSource = 'discardPile';
		}
		// 0 == 'deck', 1 == 'discardPile'
        $currentCard = $this->cards->getCard( $card_id );
		
		if ( $drawSource == 'deck' ) {
			self::setGameStateValue( 'drawSource', 0 ); // 0 == 'deck', 1 == 'discardPile'
		} else if ( $drawSource == 'discardPile' ) {
			self::setGameStateValue( 'drawSource', 1 ); // 0 == 'deck', 1 == 'discardPile'
		}
		
		$this->drawNotify( $currentCard, $player_id, $drawSource, '' );
	}
////////
////////
////////
	function notifyPlayerIsNotBuying( $player_id ) {
		self::trace("[bmc] !! notifyPlayerIsNotBuying!!");
		self::dump("[bmc] notifyPlayerIsNotBuying - player_id:",  $player_id);
		
		$players = self::loadPlayersBasicInfos();

		self::notifyAllPlayers(
			'playerNotBuying',
			'${player_name} does not want the discard.',
			array(
				'player_id' => $player_id,
				'player_name' => $players[ $player_id ][ 'player_name' ]
			)
		);
	}
////////
////////
////////
	function notifyPlayerWantsToBuy( $player_id ) {
		self::trace("[bmc] !! notifyPlayerWantsToBuy!");
		self::dump("[bmc] player_id:",  $player_id);

		$players = self::loadPlayersBasicInfos();

		self::notifyAllPlayers(
			'playerWantsToBuy',
			'${player_name} wants to buy the discard.',
			array(
				'player_id' => $player_id,
				'player_name' => $players[ $player_id ][ 'player_name' ]
			)
		);
	}
////////
////////
////////
    function drawNotify( $currentCard, $player_id, $drawSource, $drawPlayer ) {
		self::trace("[bmc] !! drawNotify!!");

		self::dump("[bmc] currentCard    :",  $currentCard);
		
		$card_id = $currentCard['id'];
		
		$players = self::loadPlayersBasicInfos();
		$activePlayer = $players[ $player_id ][ 'player_name' ];

		self::dump("[bmc] card_id        :",  $card_id );
		self::dump("[bmc] drawSource     :",  $drawSource );
		self::dump('[bmc] player_id      :',  $player_id );
		self::dump('[bmc] player_name    :',  $activePlayer );
		self::dump('[bmc] card_id        :',  $card_id );
		self::dump('[bmc] value          :',  $currentCard ['type_arg'] );
		self::dump('[bmc] value_displayed:',  $this->values_label [$currentCard ['type_arg']] );
		self::dump('[bmc] color          :',  $currentCard ['type']);
		self::dump('[bmc] color_displayed:',  $this->colors [$currentCard ['type']] ['name'] );
		self::dump('[bmc] drawSource     :',  $drawSource );
		self::dump('[bmc] drawPlayer     :',  $drawPlayer );

		self::notifyAllPlayers(
			'drawCard',
			'${player_name} draws a card from the ${drawSource}.',
			array(
				'player_id' => $player_id,
				'player_name' => $activePlayer,
				'card_id' => $card_id,
				'value' => $currentCard [ 'type_arg' ],
				'value_displayed' => $this->values_label[ $currentCard[ 'type_arg' ]],
				'color' => $currentCard [ 'type' ],
				'color_displayed' => $this->colors[ $currentCard[ 'type' ]][ 'name' ],
				'drawSource' => $drawSource,
				'drawPlayer' => $drawPlayer
			)
		);

		self::notifyPlayer(
			$player_id,
			'drawCard',
			'You drew the ${color_displayed} ${value_displayed}.',
			array(
				'player_id' => $player_id,
				'player_name' => $activePlayer,
				'card_id' => $card_id,
				'value' => $currentCard[ 'type_arg' ],
				'value_displayed' => $this->values_label[ $currentCard[ 'type_arg' ]],
				'color' => '', // Just put it in the game history log
				'color_displayed' => $this->colors[ $currentCard[ 'type' ]][ 'name' ],
				'drawSource' => $drawSource,
				'drawPlayer' => $drawPlayer
			)
		);
		// Next State
		$state = $this->gamestate->state();
		self::dump("[bmc] state:", $state);
		
		if ( $state['name'] == 'playerTurnPlay' ) { // Got the joker from a board play, so keep playing.
			$this->gamestate->nextState( 'playCard' );
			
		} else { // Got card from a true draw (deck or discard) so resolve buyers and whatnot.
		
			// Set all players except drawer to be active
		
			//$this->gamestate->nextState('drawCard');
			$this->gamestate->nextState( 'resolveBuyers' );
		}
	}
////////
////////
////////
	function playerGoDown($cardIDGroupA, $cardIDGroupB, $cardIDGroupC, $boardCardId, $boardArea, $boardPlayer, $handItemIds) {
		self::trace("[bmc] ENTER playerGoDown");
		$active_player_id = self::getActivePlayerId();
		self::dump("[bmc] playerGoDown: ", $active_player_id);
		self::checkAction('playerGoDown');

		$countCardsInPlayerHand = intval($this->cards->countCardsByLocationArgs( 'hand' )[$active_player_id]);
		self::dump("CCIPH:", $countCardsInPlayerHand);
		
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

		if ( $jokerCount > 1 ) {
			throw new BgaUserException( self::_('You cannot go down with more than 1 joker.') );
			return;
		}
		
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
/*
		TODO GET THE JOKER SWAP WHILE GOING DOWN TO WORK WITH SETS, and then with runs
*/				
		// Get the cards in the selected pile
		// if a hand item and board card are both included then check for the joker/swap check.
		//   Check the selected card to see if it's joker.
		//   If it is, then get the type of area it is (either set or run)

		self::dump("[bmc] BCI: ", $boardCardId);
		
		$handItems = $this->cards->getCards( $handItemIds );
		$handItems = reset( $handItems ); // Just use the first value in the array
		
		$boardCard = $this->cards->getCard( $boardCardId );

		$currentHandType = $this->getGameStateValue( 'currentHandType' );
		$areaTitle = "Area" . substr( $boardArea, -2 ); // Last 2 should be "_A" or "_B" or "_C"
		$locationMeanings = $this->setsRuns[$currentHandType];
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

		if (( $key === false ) ||             // Nothing selected on board;
		    ( empty( $handItems )) ||      // Nothing selected in hand;
			( $boardCard['type'] < 5  )) { // Board card is not a joker; So no need to do joker swap.
			
			self::trace( "[bmc] No Joker Swap Needed." );
			//... and then continue to try to go down...
			
		} else { // Player has selected board and hand cards. This means try to use joker to go down.
			$jokerSwapResult = $this->tryJokerSwap( $handItems['id'], $active_player_id, $boardArea, $boardPlayer );
			self::dump('[bmc] jokerSwapResult', $jokerSwapResult);
		
			// Move the new joker into the deficient area
			
			foreach ( $this->setsRuns[$currentHandType] as $idx => $Area ) {
	//			echo "$idx = $Area<br>";
				$cGCount[$idx] = ( $Area != "None") * ( 3 + ($idx > 2 )); // 3 if not 'None' or 4 if Run
	//			echo "$cGCount[$idx]<br>";
			}
			self::dump("[bmc] cGCount:", $cGCount );
			
			$keyA = array_search( 'Area_A', $this->setsRuns[$currentHandType] );
			$keyB = array_search( 'Area_B', $this->setsRuns[$currentHandType] );
			$keyC = array_search( 'Area_C', $this->setsRuns[$currentHandType] );

			$needInAreaA = ( $keyA === false ) ? 0 : $cGCount[$keyA];
			$needInAreaB = ( $keyB === false ) ? 0 : $cGCount[$keyB];
			$needInAreaC = ( $keyC === false ) ? 0 : $cGCount[$keyC];
			
			self::dump("[bmc] countA:", $cntCardGroupA);
			self::dump("[bmc] countB:", $cntCardGroupB);
			self::dump("[bmc] countC:", $cntCardGroupC);
			self::dump("[bmc] nA:", $needInAreaA);
			self::dump("[bmc] nB:", $needInAreaB);
			self::dump("[bmc] nC:", $needInAreaC);
			
			if        ( $cntCardGroupA < $needInAreaA ) {
				$targetArea = 'playerDown_A';
			} else if ( $cntCardGroupB < $needInAreaB ) {
				$targetArea = 'playerDown_B';
			} else if ( $cntCardGroupC < $needInAreaC ) {
				$targetArea = 'playerDown_C';
			} else {
				throw new BgaUserException( self::_('There is no place for the joker. Should never happen.') );
			}
			self::dump("[bmc] targetArea:", $targetArea );
			
			$playerHand = $this->cards->getCardsInLocation( 'hand', $active_player_id );
			
			$joker = $this->checkForJoker ($playerHand);
			self::dump("[bmc] Hand Joker:", $joker);

			// Now we know where to put the joker so add it there and finish going down
			//$this->cards->moveCard( $joker['id'], $targetArea, $active_player_id);
			switch ( $targetArea ) {
				case "playerDown_A":
//					$cardGroupA[] = $joker['id'];
					$cardGroupA[] = $joker;
					break;
				case "playerDown_B":
//					$cardGroupB[] = $joker['id'];
					$cardGroupB[] = $joker;
					break;
				case "playerDown_C":
//					$cardGroupC[] = $joker['id'];
					$cardGroupC[] = $joker;
					break;
			}
		}
	
		// Go down with the cards including the new joker
		
//		$goDownGroupA = $this->cards->getCardsInLocation('playerDown_A', $active_player_id);
//		$goDownGroupB = $this->cards->getCardsInLocation('playerDown_B', $active_player_id);
//		$goDownGroupC = $this->cards->getCardsInLocation('playerDown_C', $active_player_id);
		
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
//	function findDeficientArea ( $cardGroupA, $cardGroupB, $cardGroupC, $currentHandType ) {
//		foreach
//		return $deficientArea;
//	}
////////
////////
////////
	function playerGoDownFinish( $cardGroupA, $cardGroupB, $cardGroupC, $joker, $targetArea ) {
		self::trace("[bmc] playerGoDownFinish");
		
		// Verify the number of needed sets and runs is met

		$currentHandType = $this->getGameStateValue( 'currentHandType' );

		self::dump("[bmc] CHT", $currentHandType);

		$setsNeeded = $this->handTypes[$currentHandType]["QtySets"];
		$runsNeeded = $this->handTypes[$currentHandType]["QtyRuns"];
		
		self::dump("[bmc] SN", $setsNeeded);
		self::dump("[bmc] RN", $runsNeeded);

		$setsHave = 0;
		$runsHave = 0;

//		$cardGroupAIds = $this->cards->getCards($cardGroupA);
//		$cardGroupBIds = $this->cards->getCards($cardGroupB);
//		$cardGroupCIds = $this->cards->getCards($cardGroupC);

//		$groups = array ($cardGroupAIds, $cardGroupBIds, $cardGroupCIds);
		$groups = array ($cardGroupA, $cardGroupB, $cardGroupC);
		
		self::dump("[bmc] groups:", $groups);

		foreach ( $groups as $group ) {
			self::dump("[bmc] group:", $group );
			
			if ( $this->checkSet( $group ) == true ) {
				$setsHave++;
			}
			if ( $this->checkRun( $group ) == true ) {
				$runsHave++;
			}
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
		// Notify all players about the cards played
		self::notifyAllPlayers('playerGoDown',
			clienttranslate('${player_name} went down.'),
			array(
				'player_name' => self::getActivePlayerName(),
				'player_id' => $active_player_id,
				'joker' => $joker,
				'targetArea' => $targetArea
			)
		);

		self::trace("[bmc] GO DOWN DONE!");

		self::setPlayerGoneDown( $active_player_id, 1 /* 0 (not gone down) or 1 (gone down) */ );

	}
	
	
	
// TODO: While going down, you illegally get a joker for a run card (it gives you the joker back and shouldn't)
	
	
	
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
			unset( $maybeNewRun[ $mightBeJoker['id']]);
			
	        $card = $this->cards->getCard($card_id);

			// Potentially add the hand card
			//$maybeNewRun[] = $card;
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
				$this->drawNotify($mightBeJoker, $player_id, $boardArea, $boardPlayer);

				// Store the joker and card swapped in case we need to undo
				self::setGameStateValue( "forJokerCard_id", $card_id );
				self::setGameStateValue( "forJokerBoardArea", ord(substr( $boardArea, -1) )); // Must store int
				self::setGameStateValue( "forJokerBoardPlayer", $boardPlayer );
				self::setGameStateValue( "forJokerTheJoker_id", $mightBeJoker['id'] );
				self::setGameStateValue( "forJokerPlayerID", $boardPlayer );
			} 

			// Try to play the card for the joker as part of going down
			//$this->playCardFinish( $card_id, $player_id, $boardArea, $boardPlayer );
			
			self::trace("[bmc] Pulled the joker off the table");

			// Now finish going down

			return 2;
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
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
/*
	function playSeveralCards ($card_ids) {
		$active_player_id = self::getActivePlayerId();
		self::dump("[bmc] Play Several Cards: ", $active_player_id);

		self::checkAction('playSeveralCards');
		
		// Ensure player will not have none after playing
		$countCardsByLocation = $this->cards->countCardsByLocationArgs( 'hand' );
		$countCCBL = count($this->cards->countCardsByLocationArgs( 'hand' ));
		$playersNumber = self::getPlayersNumber();

		self::dump("CCBL:", $countCardsByLocation);
		self::dump("CCCBL:", $countCCBL);
		self::dump("PN:", $playersNumber);

		$countCardsInPlayerHand = intval($this->cards->countCardsByLocationArgs( 'hand' )[$active_player_id]);
		self::dump("CCIPH:", $countCardsInPlayerHand);
		
		$countCardsToPlay = count($card_ids);
		self::dump("CCTP:", $countCardsToPlay);
		
		$remainingCardCount = abs($countCardsInPlayerHand - $countCardsToPlay);
		self::dump("[bmc] Remaining Cards:", $remainingCardCount);

		if ($remainingCardCount < 1 ) {
			throw new BgaUserException( self::_('You cannot empty your hand.') );
			return;
		}
		// TODO: Depending upon the game hand target and what is down, check different combinations
		// TODO: For now, just check for 1 set and 1 run
		// Get what is needed to go down
		
		$currentHandType = $this->getGameStateValue( 'currentHandType' );

		self::dump("[bmc] CHT", $currentHandType);

		$setsNeeded = $this->handTypes[$currentHandType]["QtySets"];
		$runsNeeded = $this->handTypes[$currentHandType]["QtyRuns"];
		
		self::dump("[bmc] SN", $setsNeeded);
		self::dump("[bmc] RN", $runsNeeded);

//		$setsHave = $this->setsHave[$active_player_id];
//		$runsHave = $this->runsHave[$active_player_id];
	
		$setsHave = 0;
		$runsHave = 0;
		
		$cards = $this->cards->getCards($card_ids);
		self::dump("[bmc] cards", $cards);
		
//		if ( $this->checkRun($cards) != true ) {
//			throw new BgaUserException( self::_('Selected cards are not a run.') );
//		}

		if ( $this->checkSet($cards) != true ) {
			throw new BgaUserException( self::_('Selected cards are not a set.') );
		}

		// Ensure cards are in active player's hand
//		if ($runsNeeded >= $runsHave) {
//			throw new BgaUserException( self::_('Run not needed.') );
//		}
		
		if ($this->checkSet ($cards) ) {
			if ($setsNeeded < $setsHave) {
				throw new BgaUserException( self::_('Set not needed.') );
			} else {

				self::trace("[bmc] Loop over each card.");
				$card_type = array ();
				$card_type_arg = array ();
				foreach( $cards as $card ) {
					self::dump("[bmc] card: ", $card);
					
					if( $card['location'] != 'hand' || $card['location_arg'] != $active_player_id ) {
						throw new BgaUserException( self::_('You cannot play a card that is not in your hand.') );
					} else {
						self::trace("[bmc] It's in hand - Adding card to set!");
						array_push($card_type, $card['type']);
						array_push($card_type_arg, $card['type_arg']);
					}
				}

				self::dump("[bmc] card_type", array_keys($card_type));
				self::dump("[bmc] card_type", array_values($card_type));
				self::dump("[bmc] card_type_arg", array_keys($card_type_arg));
				self::dump("[bmc] card_type_arg", array_values($card_type_arg));

				self::dump("[bmc] downArea Location:", $this->setsRuns[$currentHandType][$setsHave]);

				// Put the cards into the down area
				$this->cards->moveCards($card_ids, $this->setsRuns[$currentHandType][$setsHave], $active_player_id);
				
//				$this->setsHave[$active_player_id]++; // Point to the next downArea location

				// Notify all players about the number of cards played
				self::notifyAllPlayers('playSeveralCards',
					clienttranslate('${player_name} played ${num_cards} card(s)'),
					array(
						'player_name' => self::getActivePlayerName(),
						'player_id' => $active_player_id,
						'num_cards' => count($card_ids),
						'card_ids' => $card_ids,
						'card_type' => $card_type,
						'card_type_arg' => $card_type_arg				
					)
				);
				self::dump("[bmc] Card Locations: ", $this->cards->countCardsInLocations());
				self::dump("[bmc] On board: ", $this->cards->getCardsInLocation('playerDown_A'));
			}
		}
	}
*/
////
////
////
	function checkRun( $cards ) {
		// A run is 4 or more cards of the same suit with sequential values or
		//   3 cards plus 1 joker. A234 and JQKA are both valid.
		
		self::dump("[bmc] checkRun cards: ", $cards);
		
		$cardCount = count( $cards );
		if ( $cardCount < 4) {
			self::trace("[bmc] checkRun FALSE (not enough cards)");
			return false;
		}

		// If there is an ace, also create a '14' because ace can be high or low
		
		$aceLowCards = $cards; // PHP makes a completely new copy of an array
		$aceHighCards = $cards; // PHP makes a completely new copy of an array
		
		foreach ( $cards as $card ) {
			self::dump("[bmc] card: ", $card );
			
			if ( $card['type_arg'] == 1 ) {
				$aceKey = $card['id'];
				self::dump("[bmc] aceKey: ", $aceKey);
				
				$aceHighCards[] = [
					'id' => $card['id'],
					'type' => $card['type'],
					'type_arg' => "14", // Ace is considered 14
					'location' => $card['location'],
					'location_arg' => $card['location_arg']
				];
				unset($aceHighCards[$aceKey]); // Remove the low ace
			}
		}
		
		self::dump("[bmc] New cards (cards):", $cards);
		self::dump("[bmc] New cards (aceLowCards):", $aceLowCards);
		self::dump("[bmc] New cards (aceHighCards):", $aceHighCards);
/*
TODO: MUST REMOVE THE ACE IF it's there.

Here's the log:
01/10 08:42:02 [info] [T192714] [2333743/DodyOaks1] [bmc] checkRun cards: = array(4) {
[6]=>
array(5) {
["id"]=>
string(1) "6"
["type"]=>
string(1) "2"
["type_arg"]=>
string(2) "12"
["location"]=>
string(4) "hand"
["location_arg"]=>
string(7) "2333743"
}
[16]=>
array(5) {
["id"]=>
string(2) "16"
["type"]=>
string(1) "2"
["type_arg"]=>
string(2) "11"
["location"]=>
string(4) "hand"
["location_arg"]=>
string(7) "2333743"
}
[35]=>
array(5) {
["id"]=>
string(2) "35"
["type"]=>
string(1) "2"
["type_arg"]=>
string(2) "13"
["location"]=>
string(4) "hand"
["location_arg"]=>
string(7) "2333743"
}
[43]=>
array(5) {
["id"]=>
string(2) "43"
["type"]=>
string(1) "2"
["type_arg"]=>
string(1) "1"
["location"]=>
string(4) "hand"
["location_arg"]=>
string(7) "2333743"
}}
}0



$cars = array (
	[4]=> array (
		["id"]=>		 "4",
		["type"]=>		 "2",
		["type_arg"]=>		 "2",
		["location"]=>		 "hand",
		["location_arg"]=>		 "2333742"
	),
	[21]=> array (
		["id"]=>	 "21",
		["type"]=>	 "2",
		["type_arg"]=>	 "3",
		["location"]=>	 "hand",
		["location_arg"]=>	 "2333742"
	)
); 


*/
		$tryAceLow = $this->checkRunWithAce( $aceLowCards );
		$tryAceHigh = $this->checkRunWithAce( $aceHighCards );
		
		self::dump("[bmc] Check Run aceLowCards",  $tryAceLow );
		self::dump("[bmc] Check Run aceHighCards", $tryAceHigh );
		
		$aceCheckResult = $tryAceLow + $tryAceHigh;
		
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
		
		self::trace("[bmc] checkRun: TRUE, we made it this far.");
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

		self::dump("[bmc] Max", $cardValueMax);
		self::dump("[bmc] Min", $cardValueMin);
				if ( $cardType == 0 ) {
					// Get the suit of the first card which is not a joker
					$cardType = $card['type'];
					self::dump("[bmc] cardType:", $cardType);
				} else {
					self::dump("[bmc] card: ", $card);
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

//		if ( $cardCount <= ( $cardValueMax - $cardValueMin + $jokerCount ) ) {
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
	function checkForJoker( $cards ){
		self::dump("[bmc] check for joker in cards: ", $cards);

//TODO: When player goes down, that player sees a huge red error "illegal string offset"
//      on the line:
//          if ( $card['type'] == "5") {
//		Figure out why. I think when there is no joker, a 'fake' card is put in 
//          and the format of that fake card is wrong 'type' and all that.
		
		
//		foreach ( $cards as $cardId ) {
			foreach ( $cards as $card ) {
//			$card = $this->cards->getCard( $cardId );
			
//			TODOBMC
			
			self::dump("[bmc] checkForJoker card:",  $card );
			//self::dump("[bmc] checkForJoker card type:", $card['type']);
			if ( $card['type'] == "5") {
				self::trace("[bmc] Found Joker");
				return $card;
			}
		}
		return false;
	}
////
////
////
	function checkSet($cards) {
		// A set is 3 or more cards of the same number or
		//   2 cards of the same value plus 1 joker.
		//self::dump("[bmc] checkSet Cards: ", $cards);
		
		if ( count($cards) < 3) {
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
	function playCard($card_id, $player_id, $boardArea, $boardPlayer) {
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
		
		if ($cardsInHand < 2 ) {
			throw new BgaUserException( self::_('You cannot empty your hand.') );
			return;
		}
		$this->playCardFinish( $card_id, $player_id, $boardArea, $boardPlayer );
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
				self::trace("[bmc] All Jokers! What to do??");
			}
		}
		$card = reset($cardsInArea); 
		self::dump("[bmc] All Jokers! Returning one of them:", $card);
		
		return $card;
	}
////
////
////
	function playCardFinish( $card_id, $player_id, $boardArea, $boardPlayer ) {
		// Validate the player has the card in hand
		// validate the card can be played there
		//   If the target card is a joker, take the joker & replace
		// Move the card(s) around
		// Notify the players

		$currentCard = $this->cards->getCard($card_id);

		list($card_typeA, $card_type_argA) = $this->checkIfReallyInHand( [$currentCard], $player_id );

		self::dump("[bmc] Cards on board BOARD AREA:", $boardArea);
		self::dump("[bmc] Cards on board BOARD PLAYER:", $boardPlayer);

		$cardsInArea = $this->cards->getCardsInLocation( $boardArea, $boardPlayer);
		
		self::dump("[bmc] Cards on board CARDS IN AREA:", $cardsInArea );
		self::dump("[bmc] Card being played CARD TYPE:", $card_typeA );
		self::dump("[bmc] Card being played CARD TYPE:", $card_typeA[0] );
		self::dump("[bmc] Card being played CARD TYPE ARG:", $card_type_argA );

		$mightBeJoker = $this->checkForJoker( $cardsInArea );
		self::dump("[bmc] Might Be Joker", $mightBeJoker );
		
		if ( $mightBeJoker ) {
			$boardCard = $mightBeJoker; // Find a joker on the board if possible
		} else { // Get a representative card from the group
			$boardCard = $this->getCardNotJoker( $boardArea, $boardPlayer );
//			$boardCard = array ( // Set to a card which will not look like a joker
//				'id' => 'None',
//				'type_arg' => 'None',
//				'type' => 'None'
//			);
		}

		self::dump("[bmc] boardCard:", $boardCard );
		self::dump("[bmc] area plus player: ", $boardArea . "_" . $boardPlayer);
		
		$usedTheJoker = false;
		
		// TODO: Reduce this section of IFs which has some duplication
		
		// If playing same value, or if a joker is there, or if playing a joker, then play
		if ( $this->checkSet( $cardsInArea ) == true ) {
			self::trace("[bmc] Trying to play onto a set.");
			
			if (( $boardCard['type_arg'] === $card_type_argA[0] ) or // If same value
				( $boardCard['type'] == 5 ) 					  or // If board has a joker
				( $card_typeA[0] == 5))							  {  // If playing a joker

				self::trace("[bmc] Playing the card onto the set.");
				
				// If there's a joker, take it if the played card is same values as others
				if ($mightBeJoker != false) { 
				
					if ( $this->getCardNotJoker( $boardArea, $boardPlayer )['type_arg'] == $card_type_argA[0] ) {
						self::trace("[bmc] Take the joker.");
						$usedTheJoker = true;
						
						$this->cards->moveCard($card_id, $boardArea, $boardPlayer);

						self::trace("[bmc] Replace with the card.");
						$this->cards->moveCard($mightBeJoker['id'], 'hand', $player_id);

						// And notify of the joker being 'drawn' from the down area
						$this->drawNotify($mightBeJoker, $player_id, $boardArea, $boardPlayer);

						// Store the joker and card swapped in case we need to undo
						self::setGameStateValue( "forJokerCard_id", $card_id );
						self::setGameStateValue( "forJokerBoardArea", ord(substr( $boardArea, -1) )); // Must store int
						self::setGameStateValue( "forJokerBoardPlayer", $boardPlayer );
						self::setGameStateValue( "forJokerTheJoker_id", $mightBeJoker['id'] );
						self::setGameStateValue( "forJokerPlayerID", $boardPlayer );
					} else {
						self::trace("[bmc] 1432: not same values for set.");
						throw new BgaUserException( self::_('Cannot play that card on that set.') );
					}
				} else {
					self::trace("[bmc] Play card on set.");
					$playWeight = $this->cards->countCardInLocation($boardArea) + 100;
					$this->cards->moveCard($card_id, $boardArea, $boardPlayer, $playWeight);
				}
				// And notify of the played card
				
				self::trace("[bmc] Notify of played card");
				
				self::notifyAllPlayers(
					'cardPlayed',
					clienttranslate('${player_name} plays the ${color_displayed} ${value_displayed} '),
					array (
						'card_id' => $card_id,
						'player_id' => $player_id,
						'player_name' => self::getActivePlayerName(),
						'value' => $currentCard ['type_arg'],
						'value_displayed' => $this->values_label [$currentCard ['type_arg']],
						'color' => $currentCard ['type'],
						'color_displayed' => $this->colors [$currentCard ['type']] ['name'],
						'boardArea' => $boardArea,
						'boardPlayer' => $boardPlayer
					)
				);
			} else {
				self::trace("[bmc] 1460 not same values for set.");
				throw new BgaUserException( self::_('Cannot play that card on that set.') );
			}
		} else if ( $this->checkRun($cardsInArea) == true ) {
			self::trace("[bmc] Trying to play onto a run.");

			// If a joker is on board and can be swapped then do it
			if (( $usedTheJoker == false ) && // If haven't used the joker yet, and
				( $mightBeJoker != false ) && // If there is a joker there, and
				( $card_typeA[0] != 5)) { // If not playing a joker, then
				self::trace("[bmc] Joker unused. Joker is there. Not play a joker onto a run. Try to play the card but leave the joker.");

			}
				
			if ( $card_typeA[0] != 5) { // If not playing a joker, verify the potential play is still a run.
				$cardsInArea = $this->cards->getCardsInLocation( $boardArea, $boardPlayer );
				$potentialNewRun = $cardsInArea;
				
				 // Add to area cards to try it as a run (index is currentCard index
				$potentialNewRun[$currentCard['id']] = $currentCard;
				self::trace("[bmc] checking the potential run is still a run.");
				
				if ( $this->checkRun( $potentialNewRun ) == true ) { // If true then the new card is also a run
					self::trace("[bmc] Playing the card onto the run.");
				} else {
					self::trace("[bmc] With that card, the cards are not a run.");
					throw new BgaUserException( self::_('Cannot play that card on that run.') );
				}
			}
			// Made it this far, so play it 
			$playWeight = $this->cards->countCardInLocation( $boardArea ) + 100;
			$this->cards->moveCard( $card_id, $boardArea, $boardPlayer, $playWeight );

			// And notify of the played card
		
			self::trace("[bmc] Notify of played card");
		
			self::notifyAllPlayers(
				'cardPlayed',
				clienttranslate('${player_name} plays the ${color_displayed} ${value_displayed} '),
				array (
					'card_id' => $card_id,
					'player_id' => $player_id,
					'player_name' => self::getActivePlayerName(),
					'value' => $currentCard ['type_arg'],
					'value_displayed' => $this->values_label [$currentCard ['type_arg']],
					'color' => $currentCard ['type'],
					'color_displayed' => $this->colors [$currentCard ['type']] ['name'],
					'boardArea' => $boardArea,
					'boardPlayer' => $boardPlayer
				)
			);
		} else {
			self::trace("[bmc] Not a Set and Not a Run!");
				throw new BgaUserException( self::_('Not a Set and Not a Run (shouldnt happen?).'));
		}
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
    function stNewHand() {
		self::debug("[bmc] !!stNewHand!!");
		
        // Take back all cards (from any location => null) to deck
        $this->cards->moveAllCardsInLocation(null, "deck");

		$bob = $this->cards->countCardInLocation( 'deck' );
		
        // Shuffle deck
        $this->cards->shuffle('deck');

        // Deal some cards to each players

        $players = self::loadPlayersBasicInfos();
		
		// Deal 11 cards to each player
		// Put 1 card in the discard pile
		// Put the rest into the draw deck
		// Notify players of the situation
		
		foreach ( $players as $player_id => $player ) {
			$this->cards->pickCards( 10, 'deck', $player_id );
			self::setPlayerGoneDown($player_id, 0 /* 0 (not gone down) or 1 (gone down) */);
		}
		
		// Put 1 card from the deck into the discard pile and give it a starting weight of 100
		$this->cards->moveCard( $this->cards->getCardOnTop ( 'deck' )[ 'id' ], 'discardPile', 100); 
		
		// The rest of the cards are in 'deck'
		
		//Notify all players of their cards plus the deck and the discard pile
		$currentHandType = $this->getGameStateValue( 'currentHandType' );
		$handTarget = $this->handTypes[$currentHandType]["Target"]; // Pull the description
		
		self::dump("[bmc] currentHandType handTarget stNewHand:", $handTarget);
		
		foreach ( $players as $player_id => $player ) {
			self::notifyPlayer(
				$player_id,
				'newHand',
				clienttranslate('New Hand! Dealing cards. New target is ${handTarget}.'),
				array(
					'hand' => $this->cards->getPlayerHand( $player_id ),
					'deck' => array_keys($this->cards->getCardsInLocation( 'deck' )),
					'discardPile' => $this->cards->getCardsInLocation( 'discardPile' ),
					'handTarget' => $handTarget
				)
			);
		}
		
		// Go to the next game state (draw) with all players active
		
		self::setGameStateValue( "previous_player_id", 0 ); // After the deal everyone else can buy.
		
        $this->gamestate->nextState("");	
    }

    function stCheckEmptyDeck() {
		self::trace("[bmc] ENTER stCheckEmptyDeck");
		
		$activePlayerId = $this->getActivePlayerId();
		self::dump("[bmc] activePlayerId:", $activePlayerId );
		
		//$turnPlayerArray = $this->getNextPlayerTable();
		//self::dump("[bmc] turnPlayerArray:", $turnPlayerArray );
		
		$countCardsInDeck = $this->cards->countCardInLocation( 'deck' );
		self::dump("[bmc] Card in deck:", $countCardsInDeck );
		
		if ( $countCardsInDeck == 0 ) {
			$discards = $this->cards->getCardsInLocation('discardPile');
			
			self::dump("[bmc] Shuffling. Discards: ", $discards);
			self::dump("[bmc] card on top:", $this->cards->getCardOnTop ( 'deck' )[ 'id' ]);
			
//TODO: GET it to auto shuffle
			$card_ids = array_keys($discards);

			self::dump("[bmc] card_ids: ", $card_ids);
			
			$this->cards->moveCards($card_ids, 'deck');
	
			$this->cards->shuffle('deck');

			// Trigger the auto-shuffle by trying to draw a card:
			// Put 1 card from the deck into the discard pile and give it a starting weight of 100
			//$this->cards->moveCard( $this->cards->getCardOnTop ( 'deck' )[ 'id' ], 'discardPile', 100); 
			 
//			$this->cards->moveCards($discards, 'deck');

			self::notifyAllPlayers( "deckShuffled",
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
    function stPlayCards() {
		self::trace("[bmc] ENTER & EXIT stPlayCards. Likely never fires????");
		$this->gamestate->nextState('playerTurnPlay');	
	}
////
////
////







//TODO: Fix this: Now every client shows YOU MUST DRAW A CARD. But JS knows whose turn it really is.


//TODO: Probably should split the states into:
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
		
//		TODO:
//		if drawing deck, allow buyers
//		if drawing discard, deny buyers and do not register buyers.
		
		self::trace( "[bmc] EXIT (almost) stResolveBuyers:" );

		// If source is deck or discard then resolve appropriately.
		// If not either of those then stay in this state since we're swapping a joker

		$drawSource = self::getGameStateValue( 'drawSource' );
		
		if ( $drawSource == 0 ) {
			$this->gamestate->nextState("checkEmptyDeck");
		} else if ( $drawSource == 1 ) {
			$this->gamestate->nextState("drawDiscard");
		} else {
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
		self::dump("[bmc] state:", $state);
		
		$this->gamestate->setAllPlayersMultiactive();
		
		// Find the previous player (who discarded) make them not active.
		
		$activeTurnPlayer_id = self::getGameStateValue( 'activeTurnPlayer_id' );
		
		$discardingPlayer_id = $this->getPlayerBefore( $activeTurnPlayer_id );
		
		self::dump("[bmc] activeTurnPlayer_id:", $activeTurnPlayer_id );
		self::dump("[bmc] discardingPlayer_id:", $discardingPlayer_id );


		$this->gamestate->setPlayerNonMultiactive( $discardingPlayer_id, 'discardCard' );

		$this->clearBuyers();

		self::setPlayerBuying( $discardingPlayer_id, 1 ) ; // 1 = not buying, they just discarded it
		self::setPlayerBuying( $activeTurnPlayer_id, 1 ) ; // 1 = not buying, they can get it for free

		$buyers = self::getPlayerBuying();
		self::dump("[bmc] Buyers Status(stShowBUYButtons):", $buyers);

		//self::dump("[bmc] Buyers Status Buying (ShowBuyButtons):", $buyers);
		self::trace( "[bmc] EXIT stShowBUYButtons:" );
	}
/////
/////
/////
	function notBuyRequest( $player_id ) { 
		self::dump("[bmc] ENTER NotBuyRequest:", $player_id );
		self::checkAction('notBuyRequest');
		
		$activePlayerId = self::getActivePlayerId();
		
		//$turnPlayerArray = $this->getNextPlayerTable();
		//$turnPlayerId = $turnPlayerArray[ 0 ];

//		if ( $turnPlayerId != $player_id ) { // Only process players if it's not their turn
		if ( $activePlayerId != $player_id ) { // Only process players if it's not their turn

			// Tell the database there is a buyer (0==unknown, 1==Not buying 2==Buying)
			self::setPlayerBuying( $player_id, 1 );

			$buyers = self::getPlayerBuying();

			//self::dump("[bmc] Buyers Status Not Buying (ShowBuyButtons):", $buyers);
		
			$this->notifyPlayerIsNotBuying( $player_id );

			self::trace("[bmc] EXIT (maybe) stNotBuyRequest:" );
			// deactivate player; if none left, transition to process discard
			$this->gamestate->setPlayerNonMultiactive( $player_id, 'resolveBuyers' );

		}
		self::trace("[bmc] EXIT (truly) NotBuyRequest:" );
	}
////
////
////
	function buyRequest( $player_id ) {
		self::trace("[bmc] ENTER BuyRequest");
		$player_id = $this->getCurrentPlayerId(); // CURRENT!!! not active
		// Check if can still buy or if someone else clicked the button

		self::checkAction('buyRequest');

		$buyers = self::getPlayerBuying();

		//self::dump("[bmc] Buyers Status(playerIsBuying1):", $buyers);
		
		foreach( $buyers as $buyer ) {
			if ( $buyer == 2 ) { 
				throw new BgaUserException( self::_('Sorry someone else is buying it.') );
			}
		}
		
		// If the foreach exited then no one else is buying, so this player can
		
		// TODO: Check if this player has bought 3 times already and deny them if so.
		
		self::setPlayerBuying( $player_id, 2 ); // Tell the database there is a buyer (0==unknown, 1==Not buying 2==Buying)
		
		$buyers = self::getPlayerBuying();

		//self::dump("[bmc] Buyers Status(playerIsBuying2):", $buyers);

		$this->notifyPlayerIsNotBuying( $player_id );
		self::trace("[bmc] EXIT (almost) stBuyRequest");

		// deactivate player; if none left, transition to process discard
		$this->gamestate->setPlayerNonMultiactive( $player_id, 'resolveBuyers' );

	}
////
////
////	
	function clearBuyers() {
		$players = self::loadPlayersBasicInfos();
		foreach ( $players as $player_id => $player) {
			self::setPlayerBuying( $player_id, 0 ); // Every discard clear the buyers (0==unknown, 1==Not buying 2==Buying)
		}
	}
////
////
////
    function stPlayerTurnPlay() {
		self::trace("[bmc] ENTER stPlayerTurnPlay");
		
		// TODO TODO 10/13/2020
		// The structure should be this, but playing cards and going down 
		//   remains in the same state (35) so all we need is to exit when discarding.
		// if (  ) {
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


/*10/13/2020
TODO: Messing with the state machine and the multiplayer stuff. It is still not done but it is close.
Latest issue is for the true active player, the JS is showing a MOVE RECORDED, WAITING FOR UPDATE.
This means the JS did not interpret the notify from the BUYERS properly. I think it showed count-down
buttons too, which is should not have done.
*/




    function stNextPlayer() {
		self::trace("[bmc] ENTER NextPlayer");
		
		$countCardsByLocation = $this->cards->countCardsByLocationArgs( 'hand' );
		$countCCBL = count($this->cards->countCardsByLocationArgs( 'hand' ));
		$playersNumber = self::getPlayersNumber();

		//self::dump("[bmc] CCBL:", $countCardsByLocation );
		//self::dump("[bmc] CCCBL:", $countCCBL );
		//self::dump("[bmc] PN:", $playersNumber );
		
		if ($countCCBL != $playersNumber) {
			// Someone has gone out
			$this->gamestate->nextState( "endHand" );
		} else {
			// Next player can draw and play etc...

			// Show State
			$state = $this->gamestate->state();
			self::dump("[bmc] stNextPlayer state:", $state);

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

			//$currentTurnPlayer_id = $this->nextTurnPlayer();

			// Store the previous player so they don't get the offer to buy their own discard. Cannot do this in a multiactive state, so we must do it right before it.
			
			//$activePrevPlayer = $this->activePrevPlayer();
			
//			self::dump( "[bmc] activePrevPlayer:", $activePrevPlayer );
			
			self::setGameStateValue( "previous_player_id" , $previous_player_id );
			self::setGameStateValue( 'activeTurnPlayer_id', $activePlayerId );

			// Just make sure it stuck!
			$activePlayerId = $this->getActivePlayerId();
			self::dump( "[bmc] activePlayerId (exiting nextPlayer inside if):", $activePlayerId );

			// Show State
			$state = $this->gamestate->state();
			self::dump("[bmc] stNextPlayer state:", $state);

			$this->gamestate->nextState( 'nextPlayer' );			
		}
		// This next will never execute I think? TODO: Maybe delete this next line? It's nested ENTER/EXITs.
		//self::trace( "[bmc] EXIT NextPlayer" );
		
		// Just make sure it stuck!
		//$activePlayerId = $this->getActivePlayerId();
		//self::dump( "[bmc] activePlayerId (exiting nextPlayer):", $activePlayerId );

	}
////
////
////
	function nextTurnPlayer() {
		self::trace( "[bmc] ENTER nextTurnPlayer" );
		
		$players = self::loadPlayersBasicInfos();
		$activeTurnPlayer_id = $this->getGameStateValue( 'activeTurnPlayer_id' );
		
		// $keys = array_keys( $players );
		// $val = $players[ $keys[ 0 ]];
		// unset( $players[ $keys[ 0 ]]);
		// $nextPlayer = $val;
		// $players[ $keys[ 0 ]] = $val;
		//self::dump( "[bmc] players: ", $players );

		$nextPlayer = $this->getPlayerAfter( $activeTurnPlayer_id ); 

		self::dump( "[bmc] players: ", $players );
		self::dump( "[bmc] activeTurnPlayer_id:", $activeTurnPlayer_id );
		self::dump( "[bmc] nextPlayer: ", $nextPlayer );

		$this->setGameStateValue( 'activeTurnPlayer_id', $nextPlayer );
		
		/*
		TODO: Use this to get and track the next player to play:
		
		$playerOrder = self::getNextPlayerTable();
		
		And then also get it on the JS side and compare it to the browser player.
		*/

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
						self::trace("[bmc] 2-9");
						$player_to_points [$player_id] += 5;
						break;
					case ($card['type_arg'] >= 10 and $card['type_arg'] <= 13 ): // 10 points
						self::trace("[bmc] 10,J,Q,K");
						$player_to_points [$player_id] += 10;
						break;
					case ($card['type_arg'] === 1 ): // 15 points	
						self::trace("[bmc] Ace");
						$player_to_points [$player_id] += 15;
						break;
				}
			} else { // It must be a joker, 20 points
				self::trace("[bmc] Joker");
				$player_to_points [$player_id] += 20;
			}
		}
//var_dump("[bmc] TALLY!");
//var_dump($player_to_points);
//var_dump($cards);

        // Apply scores to player
        foreach ( $player_to_points as $player_id => $points ) {
            if ($points != 0) {
                $sql = "UPDATE player SET player_score=player_score-$points  WHERE player_id='$player_id'";
                self::DbQuery($sql);
                $point_number = $player_to_points [$player_id];
                self::notifyAllPlayers("points", clienttranslate('${player_name} gets ${nbr} points'), array (
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
		
        // Next hand target
		self::incGameStateValue( 'currentHandType', 1 );
		$currentHandType = self::getGameStateValue( 'currentHandType' );
		$handTarget = $this->handTypes[$currentHandType]["Target"]; // Pull the description
		self::dump("[bmc] handTarget stEndHand2072:", $handTarget);
		
        $newScores = self::getCollectionFromDb("SELECT player_id, player_score FROM player", true );
        self::notifyAllPlayers( "newScores", '', array(
			'newScores' => $newScores,
			'handTarget' => $handTarget,
			'currentHandType' => $currentHandType
			));

		// Notify players to go to the next target hand
		
        ///// Test if this is the end of the game
		$currentHandType = $this->getGameStateValue( 'currentHandType' );
		if ($currentHandType >= count($this->handTypes) - 1) { // Last one is fake
			$this->gamestate->nextState("endGame");
		} else {
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

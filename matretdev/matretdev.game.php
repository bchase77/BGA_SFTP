<?php
 /**
  *------
  * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
  * MatRetDev implementation : © <Your name here> <Your email address here>
  * 
  * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
  * See http://en.boardgamearena.com/#!doc/Studio for more information.
  * -----
  * 
  * matretdev.game.php
  *
  * This is the main file for your game logic.
  *
  * In this PHP file, you are going to defines the rules of the game.
  *
  */


require_once( APP_GAMEMODULE_PATH.'module/table/table.game.php' );


class MatRetDev extends Table
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
			"period" => 10,
            "playerOnOffsense" => 11,
            "playerOnDefense" => 12,
            "playerOnTop" => 13,
            "playerOnBottom" => 14
        ) );        
	}
	
    protected function getGameName( )
    {
		// Used for translations and stuff. Please do not modify.
        return "matretdev";
    }	

    /*
        setupNewGame:
        
        This method is called only once, when a new game is launched.
        In this method, you must setup the game according to the game rules, so that
        the game is ready to be played.
    */
    protected function setupNewGame( $players, $options = array() )
    {
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
        
        /************ Start the game initialization *****/

        // Init global values with their initial values
        //self::setGameStateInitialValue( 'my_first_global_variable', 0 );
        
        // Init game statistics
        // (note: statistics used in this file must be defined in your stats.inc.php file)
        //self::initStat( 'table', 'table_teststat1', 0 );    // Init a table statistics
        //self::initStat( 'player', 'player_teststat1', 0 );  // Init a player statistics (for all players)

        // TODO: setup the initial game situation here
		
		self::setGameLength();

        self::setGameStateInitialValue( 'period', 1 );

        // Activate first player (which is in general a good idea :) )
        $this->activeNextPlayer();
		
		// Initial values are 0. Player with higher conditioning will choose Off or Def; Coin flip if tied.
		
		self::setGameStateInitialValue( 'playerOnOffsense', 0 );
		self::setGameStateInitialValue( 'playerOnDefense', 0 );
		self::setGameStateInitialValue( 'playerOnTop', 0 );
		self::setGameStateInitialValue( 'playerOnBottom', 0 );

        /************ End of the game initialization *****/
    }


	function stDeckSetup()
	{
		self::trace("[bmc] Enter setupNewDeck");
		
		// Set up Wrestler deck
		$cardsWrestler = array();
		
		foreach ( $this->wrestlerCards as $wrestler ) {
			$cardsWrestler[] = array ( 
				"Name"  => $wrestler["Name"],
				"ConR1" => $wrestler["conR1"],
				"ConR2" => $wrestler["conR2"],
				"ConR3" => $wrestler["ConR3"],
				"Off"   => $wrestler["Off"],
				"Def"   => $wrestler["Def"],
				"Top"   => $wrestler["Top"],
				"Bot"   => $wrestler["Bot"],
				"Token" => $wrestler["Token"],
				"Star"  => $wrestler["Star"],
				"TM"    => $wrestler["TM"]
			)
		}
		
		$this->cards->createCards( $cardsWrestler, 'deckWrestler' );

		$cardsDebug = $this->cards->getCardsInLocation( 'deckWrestler' );

		self::dump( "[bmc] deckWrestler: ", $cardsDebug );
		
		// $cardsScramble = array();
		
		// Set up Offense deck
		$cardsOffense = array();
		
		foreach ( $this->offenseCards as $card ) {
			$cardsOffense[] = array ( 
				"Name"         => $card["Name"],
				"MyCon"        => $card["MyCon"],
				"MyTokens"     => $card["MyTokens"],
				"RollDie"      => $card["RollDie"],
				"SplEff"       => $card["SplEff"],
				"BD_A"         => $card["BD_A"],
				"BD_B"         => $card["BD_B"],
				"BD_C"         => $card["BD_C"],
				"BD_D"         => $card["BD_D"],
				"BD_E"         => $card["BD_E"],
				"BD_F"         => $card["BD_F"],
				"BD_G"         => $card["BD_G"],
				"BD_H"         => $card["BD_H"],
				"BD_A"         => $card["RD_A"],
				"RD_B"         => $card["RD_B"],
				"RD_C"         => $card["RD_C"],
				"RD_D"         => $card["RD_D"],
				"RD_E"         => $card["RD_E"],
				"RD_F"         => $card["RD_F"],
				"RD_G"         => $card["RD_G"],
				"RD_H"         => $card["RD_H"],
				"OppAdjust"    => $card["OppAdjust"],
				"Scoring"      => $card["Scoring"],
				"DrawScramble" => $card["DrawScramble"]
			)
		}
		
		$this->cards->createCards( $cardsOffense, 'deckOffsense' );

		$cardsDebug = $this->cards->getCardsInLocation( 'deckOffsense' );

		self::dump( "[bmc] deckOffsense: ", $cardsDebug );

		// Set up Defense deck
		$cardsDefense = array();
		
		foreach ( $this->defenseCards as $card ) {
			$cardsDefense[] = array (
				"Name"         => $card["Name"],
				"MyCon"        => $card["MyCon"],
				"MyTokens"     => $card["MyTokens"],
				"RollDie"      => $card["RollDie"],
				"SplEff"       => $card["SplEff"],
				"BD_A"         => $card["BD_A"],
				"BD_B"         => $card["BD_B"],
				"BD_C"         => $card["BD_C"],
				"BD_D"         => $card["BD_D"],
				"BD_E"         => $card["BD_E"],
				"BD_F"         => $card["BD_F"],
				"BD_G"         => $card["BD_G"],
				"BD_H"         => $card["BD_H"],
				"BD_A"         => $card["RD_A"],
				"RD_B"         => $card["RD_B"],
				"RD_C"         => $card["RD_C"],
				"RD_D"         => $card["RD_D"],
				"RD_E"         => $card["RD_E"],
				"RD_F"         => $card["RD_F"],
				"RD_G"         => $card["RD_G"],
				"RD_H"         => $card["RD_H"],
				"OppAdjust"    => $card["OppAdjust"],
				"Scoring"      => $card["Scoring"],
				"DrawScramble" => $card["DrawScramble"]
			)
		}
		
		$this->cards->createCards( $cardsDefense, 'deckDefense' );

		$cardsDebug = $this->cards->getCardsInLocation( 'deckDefense' );

		self::dump( "[bmc] cardsDefense: ", $cardsDebug );

		// $cardsTop = array();
		// $cardsBottom = array();
		
		self::trace("[bmc] Exit setupNewDeck");
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
        $result = array();
    
        $current_player_id = self::getCurrentPlayerId();    // !! We must only return informations visible by this player !!
    
        // Get information about players
        // Note: you can retrieve some extra field you added for "player" table in "dbmodel.sql" if you need it.
        $sql = "SELECT player_id id, player_score score FROM player ";
        $result['players'] = self::getCollectionFromDb( $sql );

        // TODO: Gather all information about current game situation (visible by player $current_player_id).
  
		// Let the clients know game state so they know to have them choose wrestler or other actions
		$state = $this->gamestate->state();

		$result[ 'state' ] = $state;
		
		$result[ 'deckWrestler' ]  = $this->cards->getCardsInLocation( 'deckWrestler' );
		$result[ 'deckScramble' ]  = $this->cards->getCardsInLocation( 'deckScramble' );
		$result[ 'deckOffsense' ]  = $this->cards->getCardsInLocation( 'deckOffsense' );
		$result[ 'deckDefensee' ]  = $this->cards->getCardsInLocation( 'deckDefensee' );
		$result[ 'deckTop' ]       = $this->cards->getCardsInLocation( 'deckTop' );
		$result[ 'deckBottom' ]    = $this->cards->getCardsInLocation( 'deckBottom' );
		$result[ 'boardScramble' ] = $this->cards->getCardsInLocation( 'boardScramble' );

		$current_player_id = self::getCurrentPlayerId(); // !! Must only return informations visible by this player !!

		$players = self::loadPlayersBasicInfos();

		$playerOnOffsense = self::getGameStateValue( 'playerOnOffsense' );
		$playerOnDefense  = self::getGameStateValue( 'playerOnDefense' );
		$playerOnTop      = self::getGameStateValue( 'playerOnTop' );
		$playerOnBottom   = self::getGameStateValue( 'playerOnBottom' );

		$result[ 'playerOnOffsense' ] = $playerOnOffsense;
		$result[ 'playerOnDefense' ]  = $playerOnDefense;
		$result[ 'playerOnTop' ]      = $playerOnTop;
		$result[ 'playerOnBottom' ]   = $playerOnBottom;

		foreach ( $players as $player_id => $player ) {
			$result[ 'boardWrestler' ][ player_id ] = $this->cards->getCardsInLocation( 'boardWrestler' , $player_id );
			$result[ 'boardMove' ][ player_id ] = $this->cards->getCardsInLocation( 'boardMove' , $player_id );
		}
		
		$result[ 'deckWrestler' ] = $this->cards->getCardsInLocation( 'deckWrestler' );
		$result[ 'deckOffsense' ] = $this->cards->getCardsInLocation( 'deckOffsense' );
		$result[ 'deckDefense' ]  = $this->cards->getCardsInLocation( 'deckDefense' );
		$result[ 'deckTop' ]      = $this->cards->getCardsInLocation( 'deckTop' );
		$result[ 'deckBottom' ]   = $this->cards->getCardsInLocation( 'deckBottom' );
		
        return $result;
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

	function setGameLength() {
		$gameLengthOption = $this->getGameStateValue( 'gameLengthOption' );

		if ( $gameLengthOption == 1 ) {
			$this->gameType = $this->gameType1;
		} else {
			$this->gameType = $this->gameType2;
		}
	}

//////////////////////////////////////////////////////////////////////////////
//////////// Player actions
//////////// 

    /*
        Each time a player is doing some game action, one of the methods below is called.
        (note: each method below must match an input method in matretdev.action.php)
    */

    /*
    
    Example:

    function playCard( $card_id )
    {
        // Check that this is the player's turn and that it is a "possible action" at this game state (see states.inc.php)
        self::checkAction( 'playCard' ); 
        
        $player_id = self::getActivePlayerId();
        
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

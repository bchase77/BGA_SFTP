<?php
/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * TutorialRumOne implementation : © <Your name here> <Your email address here>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * tutorialrumone.view.php
 *
 * This is your "view" file.
 *
 * The method "build_page" below is called each time the game interface is displayed to a player, ie:
 * _ when the game starts
 * _ when a player refreshes the game page (F5)
 *
 * "build_page" method allows you to dynamically modify the HTML generated for the game interface. In
 * particular, you can set here the values of variables elements defined in tutorialrumone_tutorialrumone.tpl (elements
 * like {MY_VARIABLE_ELEMENT}), and insert HTML block elements (also defined in your HTML template file)
 *
 * Note: if the HTML of your game interface is always the same, you don't have to place anything here.
 *
 */
  
  require_once( APP_BASE_PATH."view/common/game.view.php" );
  
  class view_tutorialrumone_tutorialrumone extends game_view
  {
    function getGameName() {
        return "tutorialrumone";
    }    
  	function build_page( $viewArgs )
  	{		
        // Get players & players number
        $players = $this->game->loadPlayersBasicInfos();
        $players_nbr = count( $players );
		
        /*********** Place your code below:  ************/

//		This works
//        $number_to_display = 4;
//        $this->tpl['CURRENT_HAND_TYPE'] = $number_to_display;
		//$sevdebug = $this->game->gamestate_labels["currentHandType"];


// TODO: Why doesn't this get seen when it's in material.inc.php? It must not be in 2 places.
$this->handTypes = array(
  0 => "2 Sets",
  1 => "1 Set and 1 Run",
  2 => "2 Runs",
  3 => "3 Sets",
  4 => "2 Sets and 1 Run",
  5 => "1 Set and 2 Runs",
  6 => "3 Runs"
);


		$gameMethods = get_class_methods($this->game);

		$debug = $gameMethods;

		self::trace("[bmc] build_page: ");
		//self::trace($debug);
		
//var_dump($debug);
//die('okSev');
//        $to_display = $sevdebug;

		//$to_display = getGameStateValue( 'currentHandType' );
		$htNumber = $this->game->getGameStateValue( 'currentHandType' );
		
		$handTarget = $this->handTypes[$htNumber];
		
        $this->tpl['CURRENT_HAND_TYPE'] = " " . $handTarget;
			
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
		
        /*
        
        // Examples: set the value of some element defined in your tpl file like this: {MY_VARIABLE_ELEMENT}

        // Display a specific number / string
        $this->tpl['MY_VARIABLE_ELEMENT'] = $number_to_display;

        // Display a string to be translated in all languages: 
        $this->tpl['MY_VARIABLE_ELEMENT'] = self::_("A string to be translated");

        // Display some HTML content of your own:
        $this->tpl['MY_VARIABLE_ELEMENT'] = self::raw( $some_html_code );
        
        */
        
        /*
        
        // Example: display a specific HTML block for each player in this game.
        // (note: the block is defined in your .tpl file like this:
        //      <!-- BEGIN myblock --> 
        //          ... my HTML code ...
        //      <!-- END myblock --> 
        

        $this->page->begin_block( "tutorialrumone_tutorialrumone", "myblock" );
        foreach( $players as $player )
        {
            $this->page->insert_block( "myblock", array( 
                                                    "PLAYER_NAME" => $player['player_name'],
                                                    "SOME_VARIABLE" => $some_value
                                                    ...
                                                     ) );
        }
        
        */

        /*********** Do not change anything below this line  ************/
  	}
  }
  


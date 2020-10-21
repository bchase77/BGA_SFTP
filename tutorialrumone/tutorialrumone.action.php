<?php
/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * TutorialRumOne implementation : © Bryan Chase <bryanchase@yahoo.com>
 *
 * This code has been produced on the BGA studio platform for use on https://boardgamearena.com.
 * See http://en.doc.boardgamearena.com/Studio for more information.
 * -----
 * 
 * tutorialrumone.action.php
 *
 * TutorialRumOne main action entry point
 *
 *
 * In this file, you are describing all the methods that can be called from your
 * user interface logic (javascript).
 *       
 * If you define a method "myAction" here, then you can call it from your javascript code with:
 * this.ajaxcall( "/tutorialrumone/tutorialrumone/myAction.html", ...)
 *
 */
  
  
  class action_tutorialrumone extends APP_GameAction
  { 
    // Constructor: please do not modify
   	public function __default()
  	{
  	    if( self::isArg( 'notifwindow') )
  	    {
            $this->view = "common_notifwindow";
  	        $this->viewArgs['table'] = self::getArg( "table", AT_posint, true );
  	    }
  	    else
  	    {
            $this->view = "tutorialrumone_tutorialrumone";
            self::trace( "Complete reinitialization of board game" );
            }
  	}

    function parseNumberList($number_list_arg)
    {
        // Removing last ';' if exists
        if (substr( $number_list_arg, -1 ) == ';' ) {
            $number_list_arg = substr( $number_list_arg, 0, -1 );
        }

        if( $number_list_arg == '' ) {
            return array();
        } else {
            return explode( ';', $number_list_arg );
        }
    }
/*    function parseNumberList($number_list_arg)
    {
		self::dump("[bmc] number_list_arg", $number_list_arg);
		
        // Removing last ';' if exists
//        if (substr( $number_list_arg, -1 ) == ';' ) {
        if (substr( $number_list_arg, -1 ) == ',' ) {
            $number_list_arg = substr( $number_list_arg, 0, -1 );
        }

        if( $number_list_arg == '' ) {
			self::dump("[bmc] number_list_arg2", $number_list_arg);
            return array();
        } else {
			self::dump("[bmc] number_list_arg3", $number_list_arg);
//            return explode( ';', $number_list_arg );
            return explode( ',', $number_list_arg );
        }
    }
*/
  	// TODO: defines your action entry points there

		public function playerHasReviewedHand()
		{
			self::trace("[bmc] ajaxcall for playerHasReviewedHand");
            self::setAjaxMode();
//			$player_id = self::getArg("player_id", AT_posint, true);
            $this->game->playerHasReviewedHand();
            self::ajaxResponse();
		}
        public function discardCard()
        {
			self::trace("[bmc] ajaxcall for discardCard");
            self::setAjaxMode();
			$player_id = self::getArg("player_id", AT_posint, true);
            $card_id = self::getArg("id", AT_posint, true);
            $this->game->discardCard( $card_id, $player_id );
            self::ajaxResponse();
        }
/*
		public function playerGoDown()
		{
			self::trace("[bmc] ajaxcall for playerGoDown");
			self::setAjaxMode();
			$cardGroupA = self::parseNumberList(self::getArg('cardGroupA', AT_numberlist, true));
			$cardGroupB = self::parseNumberList(self::getArg('cardGroupB', AT_numberlist, true));
			$cardGroupC = self::parseNumberList(self::getArg('cardGroupC', AT_numberlist, true));
			self::dump("[bmc] A:", $cardGroupA);
			self::dump("[bmc] B:", $cardGroupB);
			self::dump("[bmc] C:", $cardGroupC);
			$this->game->playerGoDown($cardGroupA, $cardGroupB, $cardGroupC);
			self::ajaxResponse();
		}
*/
		public function playerGoDown()
		{
			self::trace("[bmc] ajaxcall for playerGoDown");
			self::setAjaxMode();
			$cardIDGroupA = self::parseNumberList(self::getArg('cardGroupA', AT_numberlist, true));
			$cardIDGroupB = self::parseNumberList(self::getArg('cardGroupB', AT_numberlist, true));
			$cardIDGroupC = self::parseNumberList(self::getArg('cardGroupC', AT_numberlist, true));
			$boardCardId = self::getArg('boardCardId', AT_alphanum, true);
			$boardArea = self::getArg('boardArea', AT_alphanum, true);
			$boardPlayer = self::getArg('boardPlayer', AT_alphanum, true);
			$handItems = self::parseNumberList(self::getArg('handItems', AT_numberlist, true));
			self::dump("[bmc] A:", $cardIDGroupA);
			self::dump("[bmc] B:", $cardIDGroupB);
			self::dump("[bmc] C:", $cardIDGroupC);
			self::dump("[bmc] BC:", $boardCardId);
			self::dump("[bmc] BA:", $boardArea);
			self::dump("[bmc] BP:", $boardPlayer);
			self::dump("[bmc] HI:", $handItems);

			$this->game->playerGoDown( $cardIDGroupA, $cardIDGroupB, $cardIDGroupC, $boardCardId, $boardArea, $boardPlayer, $handItems);
			self::ajaxResponse();
		}

		// public function playSeveralCards()
		// {
			// self::setAjaxMode();
			// $card_ids = self::parseNumberList(self::getArg('card_ids', AT_numberlist, true));
			// $this->game->playSeveralCards($card_ids);
			// self::ajaxResponse();
		// }

/*
        public function playSeveralCards() {
		    self::setAjaxMode();
			self::dump("[bmc] inPSC: ", self::getArg('card_ids', AT_numberlist, true));
		    $card_ids = self::parseNumberList(self::getArg('card_ids', AT_numberlist, true));
		    $this->game->playSeveralCards($card_ids);
		    self::ajaxResponse();
		}
*/

/*
        public function playCards()
        {
			self::trace("[bmc] ajaxcall for playCards");
            self::setAjaxMode();
			
			$card_ids_raw = self::getArg( "cardArray", AT_numberlist, true);

			// Removing last ';' if exists
			if( substr( $card_ids_raw, -1 ) == ';' )
				$card_ids_raw = substr( $card_ids_raw, 0, -1 );
			if( $card_ids_raw == '' )
				$card_ids = array();
			else
				$card_ids = explode( ';', $card_ids_raw );

			self::dump("[bmc] ajax playcards: ", $card_ids);

           $this->game->playCards($card_ids);
           self::ajaxResponse();
        }
*/
        public function buyRequest()
        {
            self::setAjaxMode();
			self::trace("[bmc] ajaxcall for buyRequest");
			$player_id = self::getArg( "player_id", AT_posint, true );
            $this->game->buyRequest( $player_id ); 
            self::ajaxResponse();
        }

        public function notBuyRequest()
        {
            self::setAjaxMode();
			self::trace("[bmc] ajaxcall for notBuyRequest");
			$player_id = self::getArg("player_id", AT_posint, true);
            $this->game->notBuyRequest( $player_id ); 
            self::ajaxResponse();
        }

        public function drawCard()
        {
            self::setAjaxMode();
			self::trace("[bmc] ajaxcall for drawCard");
            $card_id = self::getArg("id", AT_posint, true);
			$player_id = self::getArg("player_id", AT_posint, true);
//			$drawSource = self::getArg("drawSource", AT_posint, true); // 0 == 'deck', 1 == 'discardPile'
			$drawSource = self::getArg("drawSource", AT_alphanum, true);
            $this->game->drawCard( $card_id, $drawSource, $player_id );
            self::ajaxResponse();
        }

        public function playCard()
        {
            self::setAjaxMode();
			self::trace("[bmc] ajaxcall for playCard");
            $card_id = self::getArg("card_id", AT_posint, true);
			$player_id = self::getArg("player_id", AT_posint, true);
			$boardArea = self::getArg("boardArea", AT_alphanum, true);
			$boardPlayer = self::getArg("boardPlayer", AT_alphanum, true);
            $this->game->playCard($card_id, $player_id, $boardArea, $boardPlayer); 
            self::ajaxResponse();
        }

/*
        public function drawDiscard()
        {
            self::setAjaxMode();
			self::trace("[bmc] ajaxcall for drawDiscard");
//            $card_id = self::getArg("id", AT_posint, true);
            $this->game->drawDiscard();
            self::ajaxResponse();
        }
*/
    /*
    
    Example:
  	
    public function myAction()
    {
        self::setAjaxMode();     

        // Retrieve arguments
        // Note: these arguments correspond to what has been sent through the javascript "ajaxcall" method
        $arg1 = self::getArg( "myArgument1", AT_posint, true );
        $arg2 = self::getArg( "myArgument2", AT_posint, true );

        // Then, call the appropriate method in your game logic, like "playCard" or "myAction"
        $this->game->myAction( $arg1, $arg2 );

        self::ajaxResponse( );
    }
    
    */

  }

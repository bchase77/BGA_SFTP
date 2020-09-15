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

  	// TODO: defines your action entry points there

        public function discardCard()
        {
			self::trace("bmc: ajaxcall for discardCard");
            self::setAjaxMode();
            $card_id = self::getArg("id", AT_posint, true);
            $this->game->discardCard($card_id);
            self::ajaxResponse();
        }

        public function playCard()
        {
			self::trace("bmc: ajaxcall for playCard");
            self::setAjaxMode();
            $card_id = self::getArg("id", AT_posint, true);
            $this->game->playCard($card_id);
            self::ajaxResponse();
        }

        public function drawCard()
        {
            self::setAjaxMode();
			self::trace("bmc: ajaxcall for drawCard");
            $card_id = self::getArg("id", AT_posint, true);
            $this->game->drawCard($card_id);
            self::ajaxResponse();
        }
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

<?php
/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * LiverpoolRummy implementation : © Bryan Chase <bryanchase@yahoo.com>
 *
 * This code has been produced on the BGA studio platform for use on https://boardgamearena.com.
 * See http://en.doc.boardgamearena.com/Studio for more information.
 * -----
 * 
 * liverpoolrummy.action.php
 *
 * LiverpoolRummy main action entry point
 *
 *
 * In this file, you are describing all the methods that can be called from your
 * user interface logic (javascript).
 *       
 * If you define a method "myAction" here, then you can call it from your javascript code with:
 * this.ajaxcall( "/liverpoolrummy/liverpoolrummy/myAction.html", ...)
 *
 */
  
  
  class action_liverpoolrummy extends APP_GameAction
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
            $this->view = "liverpoolrummy_liverpoolrummy";
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
  }

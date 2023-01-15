/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * MatRetDev implementation : © Mike & Jack McKeever and Bryan Chase bryanchase@yahoo.com
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * matretdev.js
 *
 * MatRetDev user interface script
 * 
 * In this file, you are describing the logic of your user interface, in Javascript language.
 *
 */

// Task / Bug tracking:
//
//

define([
    "dojo","dojo/_base/declare",
    "ebg/core/gamegui",
    "ebg/counter",
	"ebg/stock"
],
function (dojo, declare) {
    return declare("bgagame.matretdev", ebg.core.gamegui, {
        constructor: function(){
            console.log('matretdev constructor');
              
            // Here, you can init the global variables of your user interface
            // Example:
            // this.myGlobalValue = 0;

            this.cardWidth = 500;
            this.cardHeight = 700;

        },
        
        /*
            setup:
            
            This method must set up the game user interface according to current game situation specified
            in parameters.
            
            The method is called each time the game interface is displayed to a player, ie:
            _ when the game starts
            _ when a player refreshes the game page (F5)
            
            "gamedatas" argument contains all datas retrieved by your "getAllDatas" PHP method.
        */
        
        setup: function( gamedatas )
        {
            console.log("[bmc] Starting game setup");
            
			console.log("[bmc] GAMEDATAS");
			console.log(this.gamedatas);

            // Setting up player boards
            // for( var player_id in gamedatas.players )
            // {
                // var player = gamedatas.players[player_id];
                         
                // TODO: Setting up players boards if needed
            // }

			// The players hand will alternate between TOP, BOTTOM, OFFENSE, DEFENSE and WRESTLER decks.
			// Which is shown depends upon the game state. Then just show that deck in the $('myHand') area.

            // Player hand
            this.playerHand = new ebg.stock(); // new stock object for hand
			this.playerHand.create( this, $('myHand'), this.cardWidth, this.cardHeight );

            // 13 images per row in the sprite file
			this.playerHand.image_items_per_row = 4;

			// Set the hand type per each player's position (offense, defense, top or bottom)
			
			this.playerHandType = 'NoType';
			
			if        ( this.gamedatas.playerOnOffense == this.player_id ) {
				this.playerHandType = "Offense";
			} else if ( this.gamedatas.playerOnDefense  == this.player_id ) {
				this.playerHandType = "Defense";
			} else if ( this.gamedatas.playerOnTop      == this.player_id ) {
				this.playerHandType = "Top";
			} else if ( this.gamedatas.playerOnBottom   == this.player_id ) {
				this.playerHandType = "Bottom";
			} else {

				console.log("[bmc] FATAL ERROR: Player not in any valid position.");
				exit (0); // Fatal Error! no player in any valid position
			}

			console.log("[bmc] this.playerHandType");
			console.log(this.playerHandType);

			var i = 1;
			
			console.log("[bmc] My Hand is " + this.playerHandType);

			switch ( this.playerHandType ) {
				case 'Offense' :
					for ( card of this.gamedatas.deckOffense ) {
						console.log("[bmc] Adding Cards");
						console.log(card);
						console.log(i);
						console.log(g_gamethemeurl + 'img/CardsOffense_v1.9.png', i);
						this.playerHand.addItemType( i, i, g_gamethemeurl + 'img/CardsOffense_v1.9.png', i);
						this.playerHand.addToStockWithId(i, i);
						i++;
					}
					break;
				case 'Defense' :
					for ( card of this.gamedatas.deckDefense ) {
						this.playerHand.addItemType( i, i, g_gamethemeurl + 'img/CardsDefense_v1.9.png', i);
						i++;
					}
					break;
				case 'Top' :
					break;
				case 'Bottom' :
					break;
			}



            // TODO: Set up your game interface here, according to "gamedatas"
 
            // Setup game notifications to handle (see "setupNotifications" method below)
            this.setupNotifications();
			
			$(MOVECARDOPPTRANSLATED).innerHTML = _('Move Card (Opponent)');
			$(WRESTLERCARDOPPTRANSLATED).innerHTML = _('Wrestler Card (Opponent)');
			$(SCRAMBLECARDTRANSLATED).innerHTML = _('Scramble Card');
			$(MOVECARDMINETRANSLATED).innerHTML = _('Move Card (Mine)');
			$(WRESTLERCARDMINETRANSLATED).innerHTML = _('Wrestler Card (Mine)');

            console.log( "Ending game setup" );
        },
       

        ///////////////////////////////////////////////////
        //// Game & client states
        
        // onEnteringState: this method is called each time we are entering into a new game state.
        //                  You can use this method to perform some user interface changes at this moment.
        //
        onEnteringState: function( stateName, args )
        {
            console.log( 'Entering state: '+stateName );
            
            switch( stateName )
            {
            
            /* Example:
            
            case 'myGameState':
            
                // Show some HTML block at this game state
                dojo.style( 'my_html_block_id', 'display', 'block' );
                
                break;
           */
           
           
            case 'dummmy':
                break;
            }
        },

        // onLeavingState: this method is called each time we are leaving a game state.
        //                 You can use this method to perform some user interface changes at this moment.
        //
        onLeavingState: function( stateName )
        {
            console.log( 'Leaving state: '+stateName );
            
            switch( stateName )
            {
            
            /* Example:
            
            case 'myGameState':
            
                // Hide the HTML block we are displaying only during this game state
                dojo.style( 'my_html_block_id', 'display', 'none' );
                
                break;
           */
           
           
            case 'dummmy':
                break;
            }               
        }, 

        // onUpdateActionButtons: in this method you can manage "action buttons" that are displayed in the
        //                        action status bar (ie: the HTML links in the status bar).
        //        
        onUpdateActionButtons: function( stateName, args )
        {
            console.log( 'onUpdateActionButtons: '+stateName );
                      
            if( this.isCurrentPlayerActive() )
            {            
                switch( stateName )
                {
/*               
                 Example:
 
                 case 'myGameState':
                    
                    // Add 3 action buttons in the action status bar:
                    
                    this.addActionButton( 'button_1_id', _('Button 1 label'), 'onMyMethodToCall1' ); 
                    this.addActionButton( 'button_2_id', _('Button 2 label'), 'onMyMethodToCall2' ); 
                    this.addActionButton( 'button_3_id', _('Button 3 label'), 'onMyMethodToCall3' ); 
                    break;
*/
                }
            }
        },        

        ///////////////////////////////////////////////////
        //// Utility methods
        
        /*
        
            Here, you can defines some utility methods that you can use everywhere in your javascript
            script.
        
        */


        ///////////////////////////////////////////////////
        //// Player's action
        
        /*
        
            Here, you are defining methods to handle player's action (ex: results of mouse click on 
            game objects).
            
            Most of the time, these methods:
            _ check the action is possible at this game state.
            _ make a call to the game server
        
        */
        
        /* Example:
        
        onMyMethodToCall1: function( evt )
        {
            console.log( 'onMyMethodToCall1' );
            
            // Preventing default browser reaction
            dojo.stopEvent( evt );

            // Check that this action is possible (see "possibleactions" in states.inc.php)
            if( ! this.checkAction( 'myAction' ) )
            {   return; }

            this.ajaxcall( "/matretdev/matretdev/myAction.html", { 
                                                                    lock: true, 
                                                                    myArgument1: arg1, 
                                                                    myArgument2: arg2,
                                                                    ...
                                                                 }, 
                         this, function( result ) {
                            
                            // What to do after the server call if it succeeded
                            // (most of the time: nothing)
                            
                         }, function( is_error) {

                            // What to do after the server call in anyway (success or failure)
                            // (most of the time: nothing)

                         } );        
        },        
        
        */

        
        ///////////////////////////////////////////////////
        //// Reaction to cometD notifications

        /*
            setupNotifications:
            
            In this method, you associate each of your game notifications with your local method to handle it.
            
            Note: game notification names correspond to "notifyAllPlayers" and "notifyPlayer" calls in
                  your matretdev.game.php file.
        
        */
        setupNotifications: function()
        {
            console.log( 'notifications subscriptions setup' );
            
            // TODO: here, associate your game notifications with local methods
            
            // Example 1: standard notification handling
            // dojo.subscribe( 'cardPlayed', this, "notif_cardPlayed" );
            
            // Example 2: standard notification handling + tell the user interface to wait
            //            during 3 seconds after calling the method in order to let the players
            //            see what is happening in the game.
            // dojo.subscribe( 'cardPlayed', this, "notif_cardPlayed" );
            // this.notifqueue.setSynchronous( 'cardPlayed', 3000 );
            // 
        },  
        
        // TODO: from this point and below, you can write your game notifications handling methods
        
        /*
        Example:
        
        notif_cardPlayed: function( notif )
        {
            console.log( 'notif_cardPlayed' );
            console.log( notif );
            
            // Note: notif.args contains the arguments specified during you "notifyAllPlayers" / "notifyPlayer" PHP call
            
            // TODO: play the card in the user interface.
        },    
        
        */
   });             
});

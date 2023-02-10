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



// TODO (Bryan):
// 2/4/2023: How to remove the text "You must choose a wrestler" after one player chose but not the other one?
// 2/5/2023: When changing hand types (e.g. wrester -> move, clear out the old cards from stock
// 2/5/2023: Wrester selection IDs are not right
// 2/5/2023: Limit the players choice in wrestlers to the number of wrestlers.
// 2/5/2023: I updated the move display so hopefully the move cards show now.
// 2/5/2023: Resolve error when double-click but nothing is selected.



function (dojo, declare) {
    return declare("bgagame.matretdev", ebg.core.gamegui, {
        constructor: function(){
            console.log('matretdev constructor');
              
            // Here, you can init the global variables of your user interface
            // Example:
            // this.myGlobalValue = 0;

            this.cardWidth = 250;
            this.cardHeight = 350;
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
            // TODO: Set up your game interface here, according to "gamedatas"
 
            console.log("[bmc] Starting game setup");
			console.log("[bmc] GAMEDATAS");
			console.log(gamedatas);
			console.log("this.gamedatas");
			console.log(this.gamedatas);

            // Setting up player boards
            for( var player_id in gamedatas.players )
            {
                var player = gamedatas.players[player_id];

				// Set all the stats and cards
				// OppoDefBase
				// OppoOffBase
				// OppoBotBase
				// OppoTopBase
				// OppoConBase
				// OppoDefTemp
				// OppoOffTemp
				// OppoBotTemp
				// OppoTopTemp
				// OppoConTemp
				// MineOffBase
				// MineDefBase
				// MineTopBase
				// MineBotBase
				// MineConBase
				// MineOffTemp
				// MineDefTemp
				// MineTopTemp
				// MineBotTemp
				// MineConTemp	
				if( this.gamedatas.playerOnOffense == this.player_id ){ 
					this.mineWID =   this.gamedatas.playerOnOffenseCard.WID;
				} else if ( this.gamedatas.playerOnOffense != 0 ) { // zero means no player is there. If not me then opponent.
					this.oppoWID =   this.gamedatas.playerOnOffenseCard.WID;
				}

				if( this.gamedatas.playerOnDefense == this.player_id ){ 
					this.mineWID =   this.gamedatas.playerOnDefenseCard.WID;
				} else if ( this.gamedatas.playerOnDefense != 0 ) { // zero means no player is there. If not me then opponent.
					this.oppoWID =   this.gamedatas.playerOnDefenseCard.WID;
				}

				if( this.gamedatas.playerOnTop == this.player_id ){ 
					this.mineWID =   this.gamedatas.playerOnTopCard.WID;
				} else if ( this.gamedatas.playerOnTop != 0 ) { // zero means no player is there. If not me then opponent.
					this.oppoWID =   this.gamedatas.playerOnTopCard.WID;
				}

				if( this.gamedatas.playerOnBottom == this.player_id ){ 
					this.mineWID =   this.gamedatas.playerOnBottomCard.WID;
				} else if ( this.gamedatas.playerOnBottom != 0 ) { // zero means no player is there. If not me then opponent.
					this.oppoWID =   this.gamedatas.playerOnBottomCard.WID;
				}
			}

            switch( gamedatas.state.name )
            {
			case 'chooseWrestler':
				this.setPlayerHand( 'Wrestler' );
				break;
			case 'chooseMove':
				this.setPlayerHand( 'Move' );
				break;
			default:
				exit(0); // Error should never get here
			}

/*
			// The players hand will alternate between TOP, BOTTOM, OFFENSE, DEFENSE and WRESTLER decks.
			// Which is shown depends upon the game state. Then just show that deck in the $('myHand') area.

            // Player hand
            this.playerHand = new ebg.stock(); // new stock object for hand
			this.playerHand.create( this, $('myHand'), this.cardWidth, this.cardHeight );
			this.playerHand.setSelectionMode(1); // 0=Cannot select; 1=Can select 1; 2=Can select multiple

            // 13 images per row in the sprite file
			this.playerHand.image_items_per_row = 4;

			// Set the hand type per each player's position (offense, defense, top or bottom)
			
			this.playerHandType = 'NoType';
			
			if ( this.gamedatas.state = 'chooseWrestler' ){
				this.playerHandType = "Wrestler";
			} else {

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
			}
			
			console.log("[bmc] this.playerHandType");
			console.log(this.playerHandType);

			var i = 0;
			
			console.log("[bmc] My Hand is " + this.playerHandType);

			switch ( this.playerHandType ) {
				case 'Offense' :
					console.log("[bmc] Adding Off Cards");
					for ( card of this.gamedatas.deckOffense ) {
						// console.log(card);
						// console.log(i);
						// console.log(g_gamethemeurl + 'img/CardsOffense_v1.9.png', i);
						this.playerHand.addItemType( i, i, g_gamethemeurl + 'img/CardsOffense_v1.9_50.png', i);
						this.playerHand.addToStockWithId(i, i);
						i++;
					}
					break;
				case 'Defense' :
					console.log("[bmc] Adding Def Cards");
					for ( card of this.gamedatas.deckDefense ) {
						this.playerHand.addItemType( i, i, g_gamethemeurl + 'img/CardsDefense_v1.9_50.png', i);
						this.playerHand.addToStockWithId(i, i);
						i++;
					}
					break;
				case 'Top' :
					console.log("[bmc] Adding Top Cards");
					for ( card of this.gamedatas.deckTop ) {
						this.playerHand.addItemType( i, i, g_gamethemeurl + 'img/CardsTop_v1.9_50.png', i);
						this.playerHand.addToStockWithId(i, i);
						i++;
					}
					break;
				case 'Bottom' :
					console.log("[bmc] Adding Bot Cards");
					for ( card of this.gamedatas.deckBottom ) {
						this.playerHand.addItemType( i, i, g_gamethemeurl + 'img/CardsBottom_v1.9_50.png', i);
						this.playerHand.addToStockWithId(i, i);
						i++;
					}
					break;
				case 'Scamble' :
					console.log("[bmc] Adding Scramble Cards");
					for ( card of this.gamedatas.deckScramble ) {
						this.playerHand.addItemType( i, i, g_gamethemeurl + 'img/CardsScramble_v1.9_50.png', i);
						this.playerHand.addToStockWithId(i, i);
						i++;
					}
					break;
				case 'Wrestler' :
					console.log("[bmc] Adding Wrestler Cards");
					for ( card of this.gamedatas.deckWrestler ) {
						this.playerHand.addItemType( i, i, g_gamethemeurl + 'img/CardsWrestler_v1.9_50.png', i);
						this.playerHand.addToStockWithId(i, i);
						i++;
					}
					break;
				default:
					console.log("[bmc] Fatal error. Player has no hand.");
					exit(0); // Fatal error, player has no hand
					break;
			}
*/

			// Setup game notifications to handle (see "setupNotifications" method below)
            this.setupNotifications();
			
			$(MOVECARDOPPOTRANSLATED).innerHTML = _('Move Card (Opponent)');
			$(WRESTLERCARDOPPOTRANSLATED).innerHTML = _('Wrestler Card (Opponent)');
			$(SCRAMBLECARDTRANSLATED).innerHTML = _('Scramble Card');
			$(MOVECARDMINETRANSLATED).innerHTML = _('Move Card (Mine)');
			$(WRESTLERCARDMINETRANSLATED).innerHTML = _('Wrestler Card (Mine)');

			dojo.connect( $('myhand'),      'ondblclick',        this, 'onMyHandDoubleClick' );
            //dojo.connect( this.playerHand,  'onChangeSelection', this, 'onPlayerHandSelectionChanged' );

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
			console.log( args );
			
			//GAMEDATAS is not set here, need to use a different variables
			
  			console.log(this.gamedatas);
          
            switch( stateName )
            {
            
            /* Example:
            
            case 'myGameState':
            
                // Show some HTML block at this game state
                dojo.style( 'my_html_block_id', 'display', 'block' );
                
                break;
           */
			case 'roundSetup':
				break;

			case 'chooseWrestler':
				this.setPlayerHand( 'Wrestler' );
				break;

			case 'chooseMove':
				// Show the wrestler cards on the board

				for( var player_id in this.gamedatas.players )
				{
					var player = this.gamedatas.players[player_id];
					if( this.gamedatas.playerOnOffense == this.player_id ){ 
						this.mineWID =   this.gamedatas.playerOnOffenseCard.WID - 1; // Index back 1 to get the right sprite
					} else if ( this.gamedatas.playerOnOffense != 0 ) { // zero means no player is there. If not me then opponent.
						this.oppoWID =   this.gamedatas.playerOnOffenseCard.WID - 1; // Index back 1 to get the right sprite
					}

					if( this.gamedatas.playerOnDefense == this.player_id ){ 
						this.mineWID =   this.gamedatas.playerOnDefenseCard.WID - 1; // Index back 1 to get the right sprite
					} else if ( this.gamedatas.playerOnDefense != 0 ) { // zero means no player is there. If not me then opponent.
						this.oppoWID =   this.gamedatas.playerOnDefenseCard.WID - 1; // Index back 1 to get the right sprite
					}

					if( this.gamedatas.playerOnTop == this.player_id ){ 
						this.mineWID =   this.gamedatas.playerOnTopCard.WID - 1; // Index back 1 to get the right sprite
					} else if ( this.gamedatas.playerOnTop != 0 ) { // zero means no player is there. If not me then opponent.
						this.oppoWID =   this.gamedatas.playerOnTopCard.WID - 1; // Index back 1 to get the right sprite
					}

					if( this.gamedatas.playerOnBottom == this.player_id ){ 
						this.mineWID =   this.gamedatas.playerOnBottomCard.WID - 1; // Index back 1 to get the right sprite
					} else if ( this.gamedatas.playerOnBottom != 0 ) { // zero means no player is there. If not me then opponent.
						this.oppoWID =   this.gamedatas.playerOnBottomCard.WID - 1; // Index back 1 to get the right sprite
					}
				}
				
				this.wrestlerCardMine = new ebg.stock(); // new stock object for hand
				this.wrestlerCardMine.create( this, $('wrestlerCardMine'), this.cardWidth, this.cardHeight );
				this.wrestlerCardMine.setSelectionMode(0); // 0=Cannot select; 1=Can select 1; 2=Can select multiple
				this.wrestlerCardMine.image_items_per_row = 4;
				this.wrestlerCardMine.addItemType( this.mineWID, this.mineWID, g_gamethemeurl + 'img/CardsWrestler_v1.9_50.png', this.mineWID );
				this.wrestlerCardMine.addToStockWithId( this.mineWID, this.mineWID );

				this.wrestlerCardOppo = new ebg.stock(); // new stock object for hand
				this.wrestlerCardOppo.create( this, $('wrestlerCardOppo'), this.cardWidth, this.cardHeight );
				this.wrestlerCardOppo.setSelectionMode(0); // 0=Cannot select; 1=Can select 1; 2=Can select multiple
				this.wrestlerCardOppo.image_items_per_row = 4;
				this.wrestlerCardOppo.addItemType( this.oppoWID, this.oppoWID, g_gamethemeurl + 'img/CardsWrestler_v1.9_50.png', this.oppoWID );
				this.wrestlerCardOppo.addToStockWithId( this.oppoWID, this.oppoWID );







				
				this.setPlayerHand( 'Move' );
				break;

            case 'dummmy':
                break;
            }
			
			console.log("this.gamedatas after onEnteringState");
			console.log(this.gamedatas);
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

// TODO: Figure out how the states work and updateactionbuttons and notifications....

// chooseWrestler
// Player 1 double-clicks to choose, sending cardid
// action ajaxcall choseWrestler(cardid)
// Set wrestler ids
// Notify both players that one has chosen
// Player 2 double-clicks to choose, sending cardid
// action ajaxcall choseWrestler(cardid)
// Set wrestler ids
// Notify both players that both have chosen




		setPlayerHand: function( handType )
		{
            console.log( 'ENTER setPlayerHand' );
            console.log( handType );
			// The players hand will alternate between TOP, BOTTOM, OFFENSE, DEFENSE and WRESTLER decks.
			// Which is shown depends upon the game state. Then just show that deck in the $('myHand') area.

			// Not sure if really need to create this new or can reuse
            // Player hand
            this.playerHand = new ebg.stock(); // new stock object for hand
			this.playerHand.create( this, $('myHand'), this.cardWidth, this.cardHeight );
			this.playerHand.setSelectionMode(1); // 0=Cannot select; 1=Can select 1; 2=Can select multiple

            // 4 images per row in the sprite file
			this.playerHand.image_items_per_row = 4;

			// Set the hand type per each player's position (offense, defense, top or bottom)

//			this.playerHandType = 'NoType';
//			if ( this.gamedatas.state = 'chooseWrestler' ){
			if ( handType == 'Wrestler' ) {
				this.playerHandType = "Wrestler";
			} else {

				if        ( this.gamedatas.playerOnOffense  == this.player_id ) {
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
			}
			
			console.log("[bmc] this.playerHandType");
			console.log(this.playerHandType);

			var i = 0;
			
			console.log("[bmc] My Hand is " + this.playerHandType);

			switch ( this.playerHandType ) {
				case 'Offense' :
					console.log("[bmc] Adding Off Cards");
					for ( card of this.gamedatas.deckOffense ) {
						// console.log(card);
						// console.log(i);
						// console.log(g_gamethemeurl + 'img/CardsOffense_v1.9.png', i);
						this.playerHand.addItemType( i, i, g_gamethemeurl + 'img/CardsOffense_v1.9_50.png', i);
						this.playerHand.addToStockWithId(i, i);
						i++;
					}
					break;
				case 'Defense' :
					console.log("[bmc] Adding Def Cards");
					for ( card of this.gamedatas.deckDefense ) {
						this.playerHand.addItemType( i, i, g_gamethemeurl + 'img/CardsDefense_v1.9_50.png', i);
						this.playerHand.addToStockWithId(i, i);
						i++;
					}
					break;
				case 'Top' :
					console.log("[bmc] Adding Top Cards");
					for ( card of this.gamedatas.deckTop ) {
						this.playerHand.addItemType( i, i, g_gamethemeurl + 'img/CardsTop_v1.9_50.png', i);
						this.playerHand.addToStockWithId(i, i);
						i++;
					}
					break;
				case 'Bottom' :
					console.log("[bmc] Adding Bot Cards");
					for ( card of this.gamedatas.deckBottom ) {
						this.playerHand.addItemType( i, i, g_gamethemeurl + 'img/CardsBottom_v1.9_50.png', i);
						this.playerHand.addToStockWithId(i, i);
						i++;
					}
					break;
				case 'Scamble' :
					console.log("[bmc] Adding Scramble Cards");
					for ( card of this.gamedatas.deckScramble ) {
						this.playerHand.addItemType( i, i, g_gamethemeurl + 'img/CardsScramble_v1.9_50.png', i);
						this.playerHand.addToStockWithId(i, i);
						i++;
					}
					break;
				case 'Wrestler' :
					console.log("[bmc] Adding Wrestler Cards");
					for ( card of this.gamedatas.deckWrestler ) {
						this.playerHand.addItemType( i, i, g_gamethemeurl + 'img/CardsWrestler_v1.9_50.png', i);
						this.playerHand.addToStockWithId(i, i);
						i++;
					}
					break;
				default:
					console.log("[bmc] Fatal error. Player has no hand.");
					exit(0); // Fatal error, player has no hand
					break;
			}
			
            console.log( 'EXIT setPlayerHand' );
		},


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
		onMyHandDoubleClick : function( control_name ) {
			console.log("[bmc] ENTER onMyHandDoubleClick");
			console.log(control_name);
            var cards = this.playerHand.getSelectedItems();
			console.log( cards );
			console.log( "state: ", this.gamedatas.state );
						
			// If state == choose wrester then call that function.
			// If state == choose move then call that function.		
						
			chosenCardID = cards[0].id + 1;
			
			console.log( chosenCardID );
			
			if ( cards ) {
				console.log( cards[0].id );
			
				switch( this.gamedatas.state.name ){
					case 'chooseWrestler':
						var action = 'choseWrestler';
						break;
					case 'chooseMove':
						var action = 'choseMove';
						break;
				}

				this.ajaxcall("/" + this.game_name + "/" + this.game_name + "/" + action + ".html", {
						chosenCardID: chosenCardID,
						lock : true
					}, this, function(result) {
					}, function(is_error) {
				});
				
			}
		console.log("[bmc] EXIT onMyHandDoubleClick");
		},
		
		onPlayerHandSelectionChanged : function( control_name, item_id ) {
			console.log("[bmc] ENTER onPlayerHandSelectionChanged");
			console.log(control_name);
			console.log(item_id);

			console.log("[bmc] EXIT onPlayerHandSelectionChanged");
		},
		
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
            console.log( '[bmc] ENTER notifications subscriptions setup' );
            
            // TODO: here, associate your game notifications with local methods
            
            // Example 1: standard notification handling
            // dojo.subscribe( 'cardPlayed', this, "notif_cardPlayed" );
            
            // Example 2: standard notification handling + tell the user interface to wait
            //            during 3 seconds after calling the method in order to let the players
            //            see what is happening in the game.
            // dojo.subscribe( 'cardPlayed', this, "notif_cardPlayed" );
            // this.notifqueue.setSynchronous( 'cardPlayed', 3000 );
            // 
			
			dojo.subscribe( 'setPositions', this, "notif_setPositions");
			dojo.subscribe( 'setStats',     this, "notif_setStats");
			dojo.subscribe( 'showMoves',    this, "notif_showMoves");
            console.log( '[bmc] EXIT notifications subscriptions setup' );
			
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
		
		notif_setPositions: function( notif )
		{
			console.log("[bmc] notif_setPositions", notif);
			
			this.gamedatas.playerOnOffense = notif.args.playerOnOffense;
			this.gamedatas.playerOnDefense = notif.args.playerOnDefense;
			this.gamedatas.playerOnTop     = notif.args.playerOnTop;
			this.gamedatas.playerOnBottom  = notif.args.playerOnBottom;

			// this.playerHandType = "Offense";
			// this.playerHandType = "Defense";
			// this.playerHandType = "Top";
			// this.playerHandType = "Bottom";

		},
		notif_setStats: function( notif )
		{
			console.log("[bmc] notif_setStats", notif);

		},
		notif_showMoves: function( notif )
		{
			console.log("[bmc] notif_showMoves", notif);
			
			this.moveCardMine = new ebg.stock(); // new stock object for hand
			this.moveCardMine.create( this, $('moveCardMine'), this.cardWidth, this.cardHeight );
			this.moveCardMine.setSelectionMode(0); // 0=Cannot select; 1=Can select 1; 2=Can select multiple
			this.moveCardMine.image_items_per_row = 4;

			this.moveCardOppo = new ebg.stock(); // new stock object for hand
			this.moveCardOppo.create( this, $('moveCardOppo'), this.cardWidth, this.cardHeight );
			this.moveCardOppo.setSelectionMode(0); // 0=Cannot select; 1=Can select 1; 2=Can select multiple
			this.moveCardOppo.image_items_per_row = 4;

			moveArray = notif.args.moves;
			positionArray = notif.arges.positions;
			
			moveArray.forEach( function( moveID, playerID ) {
				console.log( '%s: %s', playerID, moveID );
				if( this.player_id == playerID ) {
					this.moveCardMine.addItemType( moveID, moveID, g_gamethemeurl + 'img/Cards' + positionArray + '_v1.9_50.png', moveID );
					this.moveCardMine.addToStockWithId( moveID, moveID );
				} else {
					this.moveCardOppo.addItemType( moveID, moveID, g_gamethemeurl + 'img/Cards' + positionArray + '_v1.9_50.png', moveID );
					this.moveCardOppo.addToStockWithId( moveID, moveID );
				}
			});
			
			
			
			
			
			



			// this.gamedatas.playerOnOffense = notif.args.playerOnOffense;
			// this.gamedatas.playerOnDefense = notif.args.playerOnDefense;
			// this.gamedatas.playerOnTop     = notif.args.playerOnTop;
			// this.gamedatas.playerOnBottom  = notif.args.playerOnBottom;
			// this.gamedatas.playerOnOffenseCard = notif.args.playerOnOffenseCard;
			// this.gamedatas.playerOnDefenseCard = notif.args.playerOnDefenseCard;
			// this.gamedatas.playerOnTopCard     = notif.args.playerOnTopCard;
			// this.gamedatas.playerOnBottomCard  = notif.args.playerOnBottomCard;
		},

		
   });             
});

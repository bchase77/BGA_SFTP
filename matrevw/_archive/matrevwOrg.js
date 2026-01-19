/**
 *------
 * BGA framework: Gregory Isabelli & Emmanuel Colin & BoardGameArena
 * MatRevW implementation : © Mike & Jack McKeever and Bryan Chase bryanchase@yahoo.com
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * matrevw.js
 *
 * MatRevW user interface script
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

// TODO (Bryan's list, anyone feel free to fix them):
// 2/15/2023: It's stuck in roundsetup with players trying to choose move cards. REFRESH updates the state to be correct.
// 2/5/2023: Limit the players choice in wrestlers to the number of wrestlers.
// 2/5/2023: Resolve error when double-click but nothing is selected.
// 2/9/2023: Immediate wrester choice is wrong but refresh choice is correct.
// 2/9/2023: Make the red borders larger (can't tell which card is selected).
// 2/9/2023: Change position type to be TEXT in SQL model

// Completed:
// X2/4/2023: How to remove the text "You must choose a wrestler" after one player chose but not the other one?
// X2/5/2023: When changing hand types (e.g. wrester -> move, clear out the old cards from stock
// X2/5/2023: Wrester selection IDs are not right
// X2/5/2023: I updated the move display so hopefully the move cards show now.

function (dojo, declare) {
    return declare("bgagame.matrevw", ebg.core.gamegui, {
        constructor: function(){
            console.log('matrevw constructor');
              
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
            console.log( "Starting game setup" );

            // Example to add a div on the game area
            document.getElementById('game_play_area').insertAdjacentHTML('beforeend', `
                <div id="player-tables"></div>
            `);
            
            // Setting up player boards
            Object.values(gamedatas.players).forEach(player => {
                // example of setting up players boards
                this.getPlayerPanelElement(player.id).insertAdjacentHTML('beforeend', `
                    <div id="player-counter-${player.id}">A player counter</div>
                `);

                // example of adding a div for each player
                document.getElementById('player-tables').insertAdjacentHTML('beforeend', `
                    <div id="player-table-${player.id}">
                        <strong>${player.name}</strong>
                        <div>Player zone content goes here</div>
                    </div>
                `);
			}
			
			const wrestlers = gamedatas.wrestlerCards;


			if (this.isCurrentPlayerActive()) {
				for (var id in this.wrestlerCards) {
					var card = this.wrestlerCards[id];

					var node = dojo.create('div', {
						id: 'wrestler_card_' + id,
						innerHTML: '<b>' + card.name + '</b><br>' +
							'Trademark: ' + card.trademark,
						class: 'clickable wrestler_card_box',
					}, 'myHand');  // or your preferred div

					dojo.connect(node, 'onclick', this, dojo.hitch(this, 'onClickChooseWrestler', id));
				}
			}





















/*
// I think this goes here?
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
				
				let player_id = this.getActivePlayerId();
				
				if( '' + gamedatas.playerOnOffense == player_id ){ 
					this.mineWID =   gamedatas.playerOnOffenseCard.WID;
				} else if ( gamedatas.playerOnOffense != 0 ) { // zero means no player is there. If not me then opponent.
					this.oppoWID =   gamedatas.playerOnOffenseCard.WID;
				}

				if( '' + gamedatas.playerOnDefense == player_id ){ 
					this.mineWID =   gamedatas.playerOnDefenseCard.WID;
				} else if ( gamedatas.playerOnDefense != 0 ) { // zero means no player is there. If not me then opponent.
					this.oppoWID =   gamedatas.playerOnDefenseCard.WID;
				}

				if( '' + gamedatas.playerOnTop == player_id ){ 
					this.mineWID =   gamedatas.playerOnTopCard.WID;
				} else if ( gamedatas.playerOnTop != 0 ) { // zero means no player is there. If not me then opponent.
					this.oppoWID =   gamedatas.playerOnTopCard.WID;
				}

				if( '' + gamedatas.playerOnBottom == player_id ){ 
					this.mineWID =   gamedatas.playerOnBottomCard.WID;
				} else if ( this.gamedatas.playerOnBottom != 0 ) { // zero means no player is there. If not me then opponent.
					this.oppoWID =   gamedatas.playerOnBottomCard.WID;
				}
            });
            
            switch( gamedatas.state.name )
            {
			case 'chooseWrestler':
				this.setPlayerHand( 'Wrestler' );
				break;
			case 'chooseMove':
				this.setPlayerHand( 'Move' );
				break;
			default:
				console.error("Fatal error: Neither Wrestler nor Move.");
				break;
				// exit(0); // Error should never get here
			}

            // TODO: Set up your game interface here, according to "gamedatas"
            

            // Setup game notifications to handle (see "setupNotifications" method below)
            this.setupNotifications();
			$('MOVECARDOPPOTRANSLATED').innerHTML = _('Move Card (Opponent)');
			$('WRESTLERCARDOPPOTRANSLATED').innerHTML = _('Wrestler Card (Opponent)');
			$('SCRAMBLECARDTRANSLATED').innerHTML = _('Scramble Card');
			$('MOVECARDMINETRANSLATED').innerHTML = _('Move Card (Mine)');
			$('WRESTLERCARDMINETRANSLATED').innerHTML = _('Wrestler Card (Mine)');

			dojo.connect( $('myHand'),      'ondblclick',        this, 'onMyHandDoubleClick' );
            //dojo.connect( this.playerHand,  'onChangeSelection', this, 'onPlayerHandSelectionChanged' );
*/
			console.log("[bmc] Reloaded at", new Date().toLocaleString());
            console.log( "Ending game setup" );
        },
       
		onClickChooseWrestler: function(wrestlerId) {
			this.ajaxcall('/matrevw/matrevw/actChooseWrestler.html', {
				lock: true,
				wrestlerId: wrestlerId,
			}, this, function(result) {});
		},

		notif_wrestlerChosen: function(notif) {
			var msg = notif.args.player_name + " chose " + notif.args.wrestler;
			console.log(msg);
			// Optionally update UI
		},






        ///////////////////////////////////////////////////
        //// Game & client states
        
        // onEnteringState: this method is called each time we are entering into a new game state.
        //                  You can use this method to perform some user interface changes at this moment.
        //
        onEnteringState: function( stateName, args )
        {
            console.log( 'Entering state: '+stateName, args );
            
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

				for( var player_id in gamedatas.players )
				{
					var player = gamedatas.players[player_id];
					if( gamedatas.playerOnOffense == player_id ){ 
						this.mineWID =   gamedatas.playerOnOffenseCard.WID - 1; // Index back 1 to get the right sprite
					} else if ( gamedatas.playerOnOffense != 0 ) { // zero means no player is there. If not me then opponent.
						this.oppoWID =   gamedatas.playerOnOffenseCard.WID - 1; // Index back 1 to get the right sprite
					}

					if( gamedatas.playerOnDefense == player_id ){ 
						this.mineWID =   gamedatas.playerOnDefenseCard.WID - 1; // Index back 1 to get the right sprite
					} else if ( gamedatas.playerOnDefense != 0 ) { // zero means no player is there. If not me then opponent.
						this.oppoWID =   gamedatas.playerOnDefenseCard.WID - 1; // Index back 1 to get the right sprite
					}

					if( gamedatas.playerOnTop == player_id ){ 
						this.mineWID =   gamedatas.playerOnTopCard.WID - 1; // Index back 1 to get the right sprite
					} else if ( gamedatas.playerOnTop != 0 ) { // zero means no player is there. If not me then opponent.
						this.oppoWID =   gamedatas.playerOnTopCard.WID - 1; // Index back 1 to get the right sprite
					}

					if( gamedatas.playerOnBottom == player_id ){ 
						this.mineWID =   gamedatas.playerOnBottomCard.WID - 1; // Index back 1 to get the right sprite
					} else if ( gamedatas.playerOnBottom != 0 ) { // zero means no player is there. If not me then opponent.
						this.oppoWID =   gamedatas.playerOnBottomCard.WID - 1; // Index back 1 to get the right sprite
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
            case 'dummy':
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
           
           
            case 'dummy':
                break;
            }
        }, 

        // onUpdateActionButtons: in this method you can manage "action buttons" that are displayed in the
        //                        action status bar (ie: the HTML links in the status bar).
        //        
        onUpdateActionButtons: function( stateName, args )
        {
            console.log( 'onUpdateActionButtons: '+stateName, args );
                      
            if( this.isCurrentPlayerActive() )
            {            
                switch( stateName )
                {
                 case 'playerTurn':    
                    const playableCardsIds = args.playableCardsIds; // returned by the argPlayerTurn

                    // Add test action buttons in the action status bar, simulating a card click:
                    playableCardsIds.forEach(
                        cardId => this.statusBar.addActionButton(_('Play card with id ${card_id}').replace('${card_id}', cardId), () => this.onCardClick(cardId))
                    ); 

                    this.statusBar.addActionButton(_('Pass'), () => this.bgaPerformAction("actPass"), { color: 'secondary' }); 
                    break;
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
			if( typeof this.playerHand === 'undefined' ) {
				this.playerHand = new ebg.stock(); // new stock object for hand
				this.playerHand.create( this, $('myHand'), this.cardWidth, this.cardHeight );
				this.playerHand.setSelectionMode(1); // 0=Cannot select; 1=Can select 1; 2=Can select multiple

				// 4 images per row in the sprite file
				this.playerHand.image_items_per_row = 4;
			} else {
				this.playerHand.removeAll();
			}
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
					console.error("Fatal error: Player not in any valid position.");
					return;	
					// exit (0); // Fatal Error! no player in any valid position
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
					console.error("Fatal error: Player has no hand.");
					// exit(0); // Fatal error, player has no hand
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

		onMyHandDoubleClick : function( control_name ) {
			console.log("[bmc] ENTER onMyHandDoubleClick");
			console.log(control_name);
            var cards = this.playerHand.getSelectedItems();
			console.log( cards );
			console.log( "state: ", this.gamedatas.state );
			console.log( "state.name: ", this.gamedatas.state.name );
						
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
				
				console.log( 'action:', action );

				this.bgaPerformAction( action, { // 'actPlayerHasReviewedHand'
					chosenCardID: chosenCardID,

				});
			}
		console.log("[bmc] EXIT onMyHandDoubleClick");
		},

        // Example:
        
        // onCardClick: function( card_id )
        // {
            // console.log( 'onCardClick', card_id );

            // this.bgaPerformAction("actPlayCard", { 
                // card_id,
            // }).then(() =>  {                
                // // What to do after the server call if it succeeded
                // // (most of the time, nothing, as the game will react to notifs / change of state instead)
            // });        
        // },    

        
        ///////////////////////////////////////////////////
        //// Reaction to cometD notifications

        /*
            setupNotifications:
            
            In this method, you associate each of your game notifications with your local method to handle it.
            
            Note: game notification names correspond to "notifyAllPlayers" and "notifyPlayer" calls in
                  your matrevw.game.php file.
        
        */
        setupNotifications: function()
        {
            console.log( '[bmc] notifications subscriptions setup' );
            
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

			this.gamedatas.playerOnOffenseCard = notif.args.playerOnOffenseCard;
			this.gamedatas.playerOnDefenseCard = notif.args.playerOnDefenseCard;
			this.gamedatas.playerOnTopCard     = notif.args.playerOnTopCard;
			this.gamedatas.playerOnBottomCard  = notif.args.playerOnBottomCard;

			this.gamedatas.state = notif.args.state;
			
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

			const moveArray = notif.args.moves;
			const positionArray = notif.args.positions;
			
			console.log( 'moveArray', moveArray );
			console.log( 'moveArray.length' );
			console.log( moveArray.length );
			
			//The moveArray.length is undefined, not sure why because it has 2 objects:
			
//			moveArray Object { 2333742: "1", 2333747: "2" }
						
//			This movearray doesn't work:
//			moveArray.forEach( function( moveID, playerID ) {

			Object.entries( moveArray ).forEach(([ moveID, playerID ]) => {

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

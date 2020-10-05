/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * TutorialRumOne implementation : © Bryan Chase <bryanchase@yahoo.com>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * tutorialrumone.js
 *
 * TutorialRumOne user interface script
 * 
 * In this file, you are describing the logic of your user interface, in Javascript language.
 *
 */

define([
    "dojo","dojo/_base/declare",
    "ebg/core/gamegui",
    "ebg/counter",
    "ebg/stock"
],
function (dojo, declare) {
    return declare("bgagame.tutorialrumone", ebg.core.gamegui, {
        constructor: function(){
              
            // Here, you can init the global variables of your user interface
            // Example:
            // this.myGlobalValue = 0;

            this.cardwidth = 72;
            this.cardheight = 96;
			this.handlers = {};
			this.showingButtons = 'No';
			this.prepSetLoc = 0; // 1st spot in the array of Target Hands
			this.prepRunLoc = 3; // 4th spot in the array of Target Hands
			this.currentHandType = 'None';

			this.setsRuns = [ // Places in the downArea where the cards should go, per hand
				[ "Area_A", "Area_B", "None",   "None",   "None",   "None"],
				[ "Area_A", "None",   "None",   "Area_B", "None",   "None"],
				[ "None",   "None",   "None",   "Area_A", "Area_B", "None"],
				[ "Area_A", "Area_B", "Area_C", "None",   "None",   "None"],
				[ "Area_A", "Area_B", "None",   "Area_C", "None",   "None"],
				[ "Area_A", "None",   "None",   "Area_B", "Area_C", "None"],
				[ "None",   "None",   "None",   "Area_A", "Area_B", "Area_C"]
				];
				// So, accessing setsRuns[3][3] (shows as 'None') means in the 4th hand, no runs are needed.
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
////////
////////
////////
//
// TODO: Player can trade card for joker.
//		 Players can buy a card 1, 2 or 3 times.
//
////////
////////
////////
        setup: function( gamedatas ) {
//console.log( "[bmc] Starting game setup" );

            // Setting up player boards
//            for( var player_id in gamedatas.players )
//            {
//                var player = gamedatas.players[player_id];
//                         
//                // TODO: Set up each players down area
//            }
            
            // Player hand
			
            this.playerHand = new ebg.stock(); // new stock object for hand
//console.log(this.playerHand)
console.log("[bmc] myhand:");
console.log($('myhand'));

            this.playerHand.create( this, $('myhand'), this.cardwidth, this.cardheight );            
            this.playerHand.image_items_per_row = 13; // 13 images per row in the sprite file

            // Create 52 cards types:
            for (var color = 1; color <= 4; color++) {
                for (var value = 1; value <= 13; value++) {
                    // Build card type id. Only create 52 here, 2 jokers below
				
						let card_type_id = this.getCardUniqueId(color, value);
						this.playerHand.addItemType(card_type_id, card_type_id, g_gamethemeurl + 'img/4ColorCardsx5.png', card_type_id);
                }
            }

            // Add 2 jokers to the card types
            this.playerHand.addItemType( 52, 52, g_gamethemeurl + 'img/4ColorCardsx5.png', 52) // Color 5 Value 1
            this.playerHand.addItemType( 53, 53, g_gamethemeurl + 'img/4ColorCardsx5.png', 53) // Color 5 Value 2
            this.playerHand.setOverlap( 50, 0 );

//console.log("this.playerHand After Jokers");
//console.log(this.playerHand);

console.log("[bmc] Cards in gamedatas and in player's hand");

console.log(this.gamedatas);
console.log(this.playerHand);

// NEWHAND START MAYBE???

            //this.playerHand.addToStockWithId(this.getCardUniqueId(color, value), card.id);
console.log("[bmc] Add cards to hand");

            // Cards in player's hand
            for ( var i in this.gamedatas.hand) {
//console.log( "i: " + i);
                var card = this.gamedatas.hand[i];
                var color = card.type;
                var value = card.type_arg;
//console.log( "CCV: " + card.id + " / " + color + " / " + value );
//console.log(card);
//console.log("[bmc] getCardUnique and card.id:");
//console.log(this.getCardUniqueId(color, value));
//console.log(card.id);

                this.playerHand.addToStockWithId(this.getCardUniqueId(color, value), card.id);
            }

// NEWHAND MABYE NOT THIS START
			this.deck = new ebg.stock(); // New stock for the draw pile (the rest of the deck)
            this.deck.create( this, $('deck'), this.cardwidth, this.cardheight );            
			//this.deck.create( this, $('deck'), this.cardwidth, this.cardheight );
			this.deck.image_items_per_row = 13;
			this.deck.setOverlap( 0.1, 0 );
			//this.deck.setOverlap( 0, 0 );
			this.item_margin = 0;
			this.deck.addItemType( 1, 1, g_gamethemeurl + 'img/4ColorCardsx5.png', 54); // Color 5 Value 3 is red back of the card
//console.log("[bmc] deckIDs");
//console.log(this.gamedatas.deckIDs);
// NEWHAND MABYE NOT THIS END

			for ( let i = 0 ; i < this.gamedatas.deckIDs.length; i++) {
//console.log(i + " / " + this.gamedatas.deckIDs[i]);
				this.deck.addToStockWithId(1, this.gamedatas.deckIDs[i]);
			}
console.log("[bmc] this.deck");			
console.log(this.deck);
//exit(0);			
			
			// Create stock for the discard pile (could be any card)
            this.discardPile = new ebg.stock(); // new stock object for hand
            this.discardPile.create( this, $('discardPile'), this.cardwidth, this.cardheight );            
            this.discardPile.image_items_per_row = 13; // 13 images per row in the sprite file
            for (var color = 1; color <= 4; color++) {
                for (var value = 1; value <= 13; value++) {
                    // Build card type id. Only create 52 here, 2 jokers below
				
						let card_type_id = this.getCardUniqueId(color, value);
						this.discardPile.addItemType(card_type_id, card_type_id, g_gamethemeurl + 'img/4ColorCardsx5.png', card_type_id);
                }
            }
            this.discardPile.addItemType( 52, 52, g_gamethemeurl + 'img/4ColorCardsx5.png', 52) // Color 5 Value 1
            this.discardPile.addItemType( 53, 53, g_gamethemeurl + 'img/4ColorCardsx5.png', 53) // Color 5 Value 2
            this.discardPile.setOverlap( 50, 0 );

// NEWHAND MABYE NOT THIS START
			var discardPileWeights = new Array();
			
            // Show the cards actually in the discard pile
            for ( var i in this.gamedatas.discardPile) {
//console.log( "i: " + i);
                var card = this.gamedatas.discardPile[i];
                var color = card.type;
                var value = card.type_arg;
//console.log( "CCV: " + card.id + " / " + color + " / " + value );
//console.log(card);

                this.discardPile.addToStockWithId(this.getCardUniqueId(color, value), card.id);
//console.log(card.id);
				let location_arg = parseInt(this.gamedatas.discardPile[i]['location_arg']);
//console.log('[bmc] location_arg:');
//console.log(location_arg);

				discardPileWeights[this.getCardUniqueId(color, value)] = location_arg;
//console.log(discardPileWeights);
            }

// NEWHAND MABYE NOT THIS END

//console.log("[bmc] discardPile Before");
//console.log(this.discardPile);

//console.log("[bmc] discardPileWeights RAW");
//console.log(discardPileWeights);

			// Set the weights in the discard pile
			this.discardPile.changeItemsWeight(discardPileWeights);

// NEWHAND END MAYBE???
//console.log("[bmc] $(discardPile)");
//console.log($('discardPile'));

//console.log("[bmc] discardPile After");
//console.log(this.discardPile);

			// Create images for the Down Areas (1 stock for each)
			
			this.downArea_A_ = new Array();
			this.downArea_B_ = new Array();
			this.downArea_C_ = new Array();
			
            for ( var player in this.gamedatas.players) {
//console.log( "i: " + i);
				console.log(player);
				
				this.downArea_A_[player] = new ebg.stock(); // new stock object for the down cards

				// Create stock for Area A
				
				var containerName = 'playerDown_A_' + player;
				this.downArea_A_[player].create( this, $(containerName), this.cardwidth, this.cardheight );            
				this.downArea_A_[player].image_items_per_row = 13; // 13 images per row in the sprite file
				for (var color = 1; color <= 4; color++) {
					for (var value = 1; value <= 13; value++) {
						// Build card type id. Only create 52 here, 2 jokers below
					
						let card_type_id = this.getCardUniqueId(color, value);
						this.downArea_A_[player].addItemType(card_type_id, card_type_id, g_gamethemeurl + 'img/4ColorCardsx5.png', card_type_id);
					}
				}
				this.downArea_A_[player].addItemType( 52, 52, g_gamethemeurl + 'img/4ColorCardsx5.png', 52) // Color 5 Value 1
				this.downArea_A_[player].addItemType( 53, 53, g_gamethemeurl + 'img/4ColorCardsx5.png', 53) // Color 5 Value 2
				this.downArea_A_[player].setOverlap( 10, 0 );

				// Create stock for Area B
				this.downArea_B_[player] = new ebg.stock(); // new stock object for the down cards
				var containerName = 'playerDown_B_' + player;
				this.downArea_B_[player].create( this, $(containerName), this.cardwidth, this.cardheight );            
				this.downArea_B_[player].image_items_per_row = 13; // 13 images per row in the sprite file
				for (var color = 1; color <= 4; color++) {
					for (var value = 1; value <= 13; value++) {
						// Build card type id. Only create 52 here, 2 jokers below
					
						let card_type_id = this.getCardUniqueId(color, value);
						this.downArea_B_[player].addItemType(card_type_id, card_type_id, g_gamethemeurl + 'img/4ColorCardsx5.png', card_type_id);
					}
				}
				this.downArea_B_[player].addItemType( 52, 52, g_gamethemeurl + 'img/4ColorCardsx5.png', 52) // Color 5 Value 1
				this.downArea_B_[player].addItemType( 53, 53, g_gamethemeurl + 'img/4ColorCardsx5.png', 53) // Color 5 Value 2
				this.downArea_B_[player].setOverlap( 10, 0 );

				// Create stock for Area C
				this.downArea_C_[player] = new ebg.stock(); // new stock object for the down cards
				var containerName = 'playerDown_C_' + player;
				this.downArea_C_[player].create( this, $(containerName), this.cardwidth, this.cardheight );            
				this.downArea_C_[player].image_items_per_row = 13; // 13 images per row in the sprite file
				for (var color = 1; color <= 4; color++) {
					for (var value = 1; value <= 13; value++) {
						// Build card type id. Only create 52 here, 2 jokers below
					
						let card_type_id = this.getCardUniqueId(color, value);
						this.downArea_C_[player].addItemType(card_type_id, card_type_id, g_gamethemeurl + 'img/4ColorCardsx5.png', card_type_id);
					}
				}
				this.downArea_C_[player].addItemType( 52, 52, g_gamethemeurl + 'img/4ColorCardsx5.png', 52) // Color 5 Value 1
				this.downArea_C_[player].addItemType( 53, 53, g_gamethemeurl + 'img/4ColorCardsx5.png', 53) // Color 5 Value 2
				this.downArea_C_[player].setOverlap( 10, 0 );

				// Show the cards in the down areas
				console.log("[bmc] SHOW THE CARDS IN DOWN AREAS");
				console.log(this.gamedatas);
				
				// Populate Area A
				for ( var cardIndex in this.gamedatas.downArea_A_[player]) {
					console.log("[bmc] CARD IN DOWN AREA:");
					card = this.gamedatas.downArea_A_[player][cardIndex];
					console.log(card);
					var card_id = card.id;
					var color = card.type;
					var value = card.type_arg;
					console.log("[bmc] 3 VALUES:");
					console.log(card_id);
					console.log(color);
					console.log(value);
					this.downArea_A_[player].addToStockWithId(this.getCardUniqueId(color, value), card_id);
				}
				// Populate Area B
				for ( var cardIndex in this.gamedatas.downArea_B_[player]) {
					console.log("[bmc] CARD IN DOWN AREA:");
					card = this.gamedatas.downArea_B_[player][cardIndex];
					console.log(card);
					var card_id = card.id;
					var color = card.type;
					var value = card.type_arg;
					console.log("[bmc] 3 VALUES:");
					console.log(card_id);
					console.log(color);
					console.log(value);
					this.downArea_B_[player].addToStockWithId(this.getCardUniqueId(color, value), card_id);
				}
				// Populate Area C
				for ( var cardIndex in this.gamedatas.downArea_C_[player]) {
					console.log("[bmc] CARD IN DOWN AREA:");
					card = this.gamedatas.downArea_C_[player][cardIndex];
					console.log(card);
					var card_id = card.id;
					var color = card.type;
					var value = card.type_arg;
					console.log("[bmc] 3 VALUES:");
					console.log(card_id);
					console.log(color);
					console.log(value);
					this.downArea_C_[player].addToStockWithId(this.getCardUniqueId(color, value), card_id);
				}
			}
			
			// Add stock for a single card played by a player. Probably can delete this if slow
			this.play1card = new ebg.stock(); // New stock for the draw pile (the rest of the deck)
            this.play1card.create( this, $('play1card'), this.cardwidth, this.cardheight );            
			this.play1card.image_items_per_row = 13;
			this.play1card.setOverlap( 0.1, 0 );
			this.item_margin = 0;
			this.play1card.addItemType( 1, 1, g_gamethemeurl + 'img/4ColorCardsx5.png', 54); // Color 5 Value 3 is red back of the card


			this.goneDown = new Array();
//			console.log(this.gamedatas);
//			console.log(this.gamedatas.players);
			
			for (var player in this.gamedatas.players) {
				console.log(player);
				this.goneDown[player] = parseInt(this.gamedatas.goneDown[player]);
				console.log("[bmc] this.gonedown[]:");
				console.log(this.goneDown[player]);
			}

//			exit(0);
/*
                var card = this.gamedatas.discardPile[i];
                var color = card.type;
                var value = card.type_arg;
//console.log( "CCV: " + card.id + " / " + color + " / " + value );
//console.log(card);

                this.discardPile.addToStockWithId(this.getCardUniqueId(color, value), card.id);
//console.log(card.id);
				let location_arg = parseInt(this.gamedatas.discardPile[i]['location_arg']);
//console.log('[bmc] location_arg:');
//console.log(location_arg);

				discardPileWeights[this.getCardUniqueId(color, value)] = location_arg;
//console.log(discardPileWeights);
            }
*/

console.log(this.player_id);

			dojo.connect( $('myhand'), 'ondblclick', this, 'onPlayerHandDoubleClick' );

            dojo.connect( this.playerHand,  'onChangeSelection', this, 'onPlayerHandSelectionChanged' );
            dojo.connect( this.deck,        'onChangeSelection', this, 'onDeckSelectionChanged' );
            dojo.connect( this.discardPile, 'onChangeSelection', this, 'onDiscardPileSelectionChanged' );

			dojo.connect( $('deck'), 'onclick', this, 'onDeckSelectionChanged');

			// Set the cards in the down areas as clickable, so players can play on them and trade for jokers
			
			console.log("[bmc] DOWN CARD SELECT SETUP");
			
			for ( var player in this.gamedatas.players) {
				console.log( 'playerDown_A_, _B_, and _C_' + player);
				dojo.connect( this.downArea_A_[player], 'onChangeSelection', this, 'onDownAreaSelect' );
				dojo.connect( this.downArea_B_[player], 'onChangeSelection', this, 'onDownAreaSelect' );
				dojo.connect( this.downArea_C_[player], 'onChangeSelection', this, 'onDownAreaSelect' );
			}

// this.player_id is defined by BGA according to the docs:
// https://en.doc.boardgamearena.com/Game_interface_logic:_yourgamename.js

			// Set the down area for this player only, to pull cards back to hand before they go down
//			dojo.connect( $('playerDown_A_' + this.player_id), 'onclick', this, 'onDownAreaClick');
			dojo.connect( $('playerDown_A_' + this.player_id), 'onclick', this, 'onDownAreaClick');
			dojo.connect( $('playerDown_B_' + this.player_id), 'onclick', this, 'onDownAreaClick');
			dojo.connect( $('playerDown_C_' + this.player_id), 'onclick', this, 'onDownAreaClick');
			
            // Setup game notifications to handle (see "setupNotifications" method below)
            this.setupNotifications();

            console.log( "[bmc] Ending game setup" );
        },
       

        ///////////////////////////////////////////////////
        //// Game & client states
        
        // onEnteringState: this method is called each time we are entering into a new game state.
        //                  You can use this method to perform some user interface changes at this moment.
        //
        onEnteringState: function( stateName, args ) {
            console.log( 'ENTERING state: ' + stateName );
			console.log(this.gamedatas.gamestate.active_player);
			console.log(this.player_id);
			console.log("[bmc] STATENAME:");
			console.log(stateName);

            switch( stateName ) {
								
				case 'newHand':
					console.log("[bmc] FOUND newHand");
					break;
				case 'playerTurnPlay':
					console.log("[bmc] FOUND PTP");
					break;
				case 'nextPlayer':
					console.log("[bmc] FOUND nextPlayer");
					this.showHideButtons();
					break;
				case 'endHand':
					console.log("[bmc] FOUND endHand");
					break;
				case 'dummmy':
					break;
				default:
					console.log("[bmc] OES DEFAULT");
					break;
            }
            /* Example:
           
            case 'myGameState':
            
                // Show some HTML block at this game state
                dojo.style( 'my_html_block_id', 'display', 'block' );
                
                break;
           */
        },

        // onLeavingState: this method is called each time we are leaving a game state.
        //                 You can use this method to perform some user interface changes at this moment.
        //
        onLeavingState: function( stateName ) {
            console.log( 'Leaving state: ' + stateName );
            
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
        onUpdateActionButtons: function( stateName, args ) {
            console.log( '[bmc] onUpdateActionButtons: ' + stateName );
			var items = this.playerHand.getSelectedItems()
//			console.log(this.gamedatas);
//			console.log(this.player_id);
//			console.log(this.gamedatas.gamestate.active_player);
		},
/*
                 Example:
 
                 case 'myGameState':
                    
                    // Add 3 action buttons in the action status bar:
                    
                    this.addActionButton( 'button_1_id', _('Button 1 label'), 'onMyMethodToCall1' ); 
                    this.addActionButton( 'button_2_id', _('Button 2 label'), 'onMyMethodToCall2' ); 
                    this.addActionButton( 'button_3_id', _('Button 3 label'), 'onMyMethodToCall3' ); 
                    break;

                }
            }
        },        
*/
        ///////////////////////////////////////////////////
        //// Utility methods
        /*
            Here, you can defines some utility methods that you can use everywhere in your javascript
            script.
        */
        // Get card unique identifier based on its color and value
        getCardUniqueId : function(color, value) {
			var cui = (color - 1) * 13 + (value - 1);
//			console.log("return: " + cui)
            //return (color - 1) * 13 + (value - 2); // Offset depending upon the image sprite file
            return cui;
        },

        discardCard : function(player_id, color, value, card_id) {
			// Purpose is to show the played cards on the table, not really to play the card.
			// Playing of the card is done on the server side (PHP).
console.log("[bmc]discardCard");
console.log(player_id);
console.log(color);
console.log(value);
console.log(card_id);
			let cardUniqueId = this.getCardUniqueId(color, value);
			let weightChange = {};
			weightChange[cardUniqueId] = this.discardPile.items.length + 300; // might be > by 1
			this.discardPile.addToStockWithId(cardUniqueId, card_id, 'overall_player_board_' + player_id);

			// Get the id of the last card in the discard
			this.discardPile.changeItemsWeight(weightChange);

            if (player_id != this.player_id) {
console.log("[bmc] Card played not by me");
				
            } else {
console.log("[bmc] Card played by me");
                // You played a card. If it exists in your hand, move card from there and remove
                // corresponding item

                if ($('myhand_item_' + card_id)) {
console.log("[bmc]Was in hand");
                    this.placeOnObject('myhand_item_' + card_id, 'discardPile');
                    this.playerHand.removeFromStockById(card_id);
					// Slide to it's final destination
					this.slideToObject('myhand_item_' + card_id, 'discardPile', 1000).play();
                }
            }
        },
		
		arraymove : function (arr, fromIndex, toIndex) {
			var element = arr[fromIndex];
			arr.splice(fromIndex, 1);
			console.log("::"+arr);
			arr.splice(toIndex, 0, element);
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
		// The left-ist thing in the draw pile is called drawPile_item_1. The next is drawPile_item_2.
		// The left-ist thing in the player's hand is called myhand_item_1. The next is myhand_item_2.
        
        /* Example:
        
        onMyMethodToCall1: function( evt )
        {
            console.log( 'onMyMethodToCall1' );
            
            // Preventing default browser reaction
            dojo.stopEvent( evt );

            // Check that this action is possible (see "possibleactions" in states.inc.php)
            if( ! this.checkAction( 'myAction' ) )
            {   return; }

            this.ajaxcall( "/tutorialrumone/tutorialrumone/myAction.html", { 
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
/////////
/////////
/////////
		getSelectedDownAreaCards : function() {
			var selectedCards_A_ = new Array();
			var selectedCards_B_ = new Array();
			var selectedCards_C_ = new Array();
			
			// Find the selected card on the board, if any
			var boardCard = {}; // Empty object
			var boardArea = ''; // Empty string
			var boardPlayer = ''; // Empty string
			
			for ( var player in this.gamedatas.players) {
console.log( 'Cards in areas: playerDown_A_, _B_, and _C_' + player);
				
				selectedCards_A_[player] = this.downArea_A_[player].getSelectedItems();
console.log(selectedCards_A_[player]);
				
				if (selectedCards_A_[player].length === 1) {
console.log("[bmc] FOUND A");
					boardCard.id = selectedCards_A_[player][0]['id'];
					boardCard.type = selectedCards_A_[player][0]['type'];
					boardArea = 'playerDown_A';
					boardPlayer = player;
				}
				
				selectedCards_B_[player] = this.downArea_B_[player].getSelectedItems();
console.log(selectedCards_B_[player]);
				
				if (selectedCards_B_[player].length === 1) {
console.log("[bmc] FOUND B");
					boardCard.id = selectedCards_B_[player][0]['id'];
					boardCard.type = selectedCards_B_[player][0]['type'];
					boardArea = 'playerDown_B';
					boardPlayer = player;
				}
				
				selectedCards_C_[player] = this.downArea_C_[player].getSelectedItems();
console.log(selectedCards_C_[player]);
				
				
				if (selectedCards_C_[player].length === 1) {
console.log("[bmc] FOUND C");
					boardCard.id = selectedCards_C_[player][0]['id'];
					boardCard.type = selectedCards_C_[player][0]['type'];
					boardArea = 'playerDown_C';
					boardPlayer = player;
				}
			}
console.log("[bmc] Selected Board Card(s):");
console.log(selectedCards_A_);
console.log(selectedCards_B_);
console.log(selectedCards_C_);
			return [boardCard, boardArea, boardPlayer];
		},
/////////
/////////
/////////
		onDownAreaSelect : function() {
console.log("[bmc] ENTER onDownAreaSelect");
console.log(this.player_id);
			var handItems = this.playerHand.getSelectedItems();
console.log(handItems);
			
			if ( handItems.length === 1)  { // Then try to play the card	
		





		
			
/*			
				var selectedCards_A_ = new Array();
				var selectedCards_B_ = new Array();
				var selectedCards_C_ = new Array();
				
				// Find the selected card on the board, if any
				var boardCard = {}; // Empty object
				var boardArea = ''; // Empty string
				var boardPlayer = ''; // Empty string
				
				for ( var player in this.gamedatas.players) {
console.log( 'Cards in areas: playerDown_A_, _B_, and _C_' + player);
					
					selectedCards_A_[player] = this.downArea_A_[player].getSelectedItems();
console.log(selectedCards_A_[player]);
					
					if (selectedCards_A_[player].length === 1) {
console.log("[bmc] FOUND A");
						boardCard.id = selectedCards_A_[player][0]['id'];
						boardCard.type = selectedCards_A_[player][0]['type'];
						boardArea = 'playerDown_A';
						boardPlayer = player;
					}
					
					selectedCards_B_[player] = this.downArea_B_[player].getSelectedItems();
console.log(selectedCards_B_[player]);
					
					if (selectedCards_B_[player].length === 1) {
console.log("[bmc] FOUND B");
						boardCard.id = selectedCards_B_[player][0]['id'];
						boardCard.type = selectedCards_B_[player][0]['type'];
						boardArea = 'playerDown_B';
						boardPlayer = player;
					}
					
					selectedCards_C_[player] = this.downArea_C_[player].getSelectedItems();
console.log(selectedCards_C_[player]);
					
					
					if (selectedCards_C_[player].length === 1) {
console.log("[bmc] FOUND C");
						boardCard.id = selectedCards_C_[player][0]['id'];
						boardCard.type = selectedCards_C_[player][0]['type'];
						boardArea = 'playerDown_C';
						boardPlayer = player;
					}
				}
*/
				
				
				
				
				var [boardCard, boardArea, boardPlayer] = this.getSelectedDownAreaCards ();
				
				let playerCard = handItems[0];

				if  (( boardCard != {} ) &&
					(  boardArea != '' )) { // There is a card there on the board, so try to have player play
					let playerCard = handItems[0];
console.log("[bmc] Will try to play card here:");
console.log(playerCard);
console.log(boardCard);
console.log(boardArea);
console.log(boardPlayer);

					var action = 'playCard';

					// do the unselects before going to the server
					this.playerHand.unselectAll();
					for ( var player in this.gamedatas.players) {
						this.downArea_A_[player].unselectAll();
						this.downArea_B_[player].unselectAll();
						this.downArea_C_[player].unselectAll();
					}

					if (this.checkAction( action, true)) {
console.log("[bmc] PlayCard Action true AJAX");
console.log("/" + this.game_name + "/" + this.game_name + "/" + action + ".html");

						this.ajaxcall("/" + this.game_name + "/" + this.game_name + "/" + action + ".html", {
							card_id : playerCard['id'],
							player_id : this.player_id,
							boardArea : boardArea,
							boardPlayer : boardPlayer,
							lock : true
						}, this, function(result) {
						}, function(is_error) {
						});
					} else {
						console.log("[bmc] PlayCard Action false");
					}
				} else {
					console.log("[bmc] No card on board selected, do nothing");
				}
			} else {
				console.log("[bmc] No card in hand selected, do nothing");
			}
			console.log("[bmc] EXIT onDownAreaSelect");

		},
////////
////////
////////
		cardWasPlayed : function (card_id, player_id, color, value, boardArea, boardPlayer) {
console.log("[bmc] (from PHP) ENTER cardWasPlayed");
console.log(card_id);
console.log(player_id);
console.log(color);
console.log(value);
//			console.log(boardCard);
console.log(boardArea);
console.log(boardPlayer);
			
			cardUniqueId = this.getCardUniqueId(color, value);
console.log(cardUniqueId);

// add joker if there			this.playerHand.addToStockWithId(boardCard['type'], boardCard['id'], boardArea + boardPlayer);
			
			if (boardArea === 'playerDown_A') {
console.log(boardArea);
				this.downArea_A_[boardPlayer].addToStockWithId(
					cardUniqueId,
					card_id,
					'myhand');
//				this.downArea_A_[boardPlayer].addToStockWithId(
//					card_id['type'],
//					card_id['id'],
//					'myhand');
/*
				this.playerHand.addToStockWithId(
					boardCard['type'],
					boardCard['id'],
					boardArea + boardPlayer);
*/
//				this.downArea_A_[boardPlayer].removeFromStockById(boardCard['id']);
//				this.playerHand.removeFromStockById(card_id['id']);
console.log("[bmc] Added.");
				this.playerHand.removeFromStockById(card_id);
console.log("[bmc] Removed.");
			}
			
			if (boardArea === 'playerDown_B') {
console.log(boardArea);
				this.downArea_B_[boardPlayer].addToStockWithId(
					cardUniqueId,
					card_id,
					'myhand');
//				this.downArea_B_[boardPlayer].addToStockWithId(
//					card_id['type'],
//					card_id['id'],
//					'myhand');
/*
				this.playerHand.addToStockWithId(
					boardCard['type'],
					boardCard['id'],
					boardArea + boardPlayer);
*/
//				this.downArea_B_[boardPlayer].removeFromStockById(boardCard['id']);
//				this.playerHand.removeFromStockById(card_id['id']);
console.log("[bmc] Added.");
				this.playerHand.removeFromStockById(card_id);
console.log("[bmc] Removed.");
			}
			if (boardArea === 'playerDown_C') {
			console.log(boardArea);
				this.downArea_C_[boardPlayer].addToStockWithId(
					cardUniqueId,
					card_id,
					'myhand');
//				this.downArea_C_[boardPlayer].addToStockWithId(
//					card_id['type'],
//					card_id['id'],
//					'myhand');
/*
				this.playerHand.addToStockWithId(
					boardCard['type'],
					boardCard['id'],
					boardArea + boardPlayer);
*/
//				this.downArea_C_[boardPlayer].removeFromStockById(boardCard['id']);
//				this.playerHand.removeFromStockById(card_id['id']);
console.log("[bmc] Added.");
				this.playerHand.removeFromStockById(card_id);
console.log("[bmc] Removed.");
			}
			console.log("[bmc] (from PHP) EXIT cardWasPlayed");
		},
		
		onDownAreaClick : function() {
			console.log("[bmc] ENTER onDownAreaClick");
			player_id = this.gamedatas.currentPlayerId;
			console.log(player_id);
			console.log(this.goneDown[player_id]);
			
			// If the player has not gone down and clicks, pull all the cards back to their hand
			if ( this.goneDown[player_id] == 0 ) { //0 = Not gone down; 1 = Gone down.
				console.log("[bmc] PULL BACK");
				
				var cards = this.downArea_A_[player_id].getAllItems();
				console.log(cards);

				for (card of cards) {
					console.log(card);
					cardUniqueId = card.type;
					this.playerHand.addToStockWithId( cardUniqueId, card.id, 'myhand');
				}
				this.downArea_A_[player_id].removeAllTo('myhand');
				//
				var cards = this.downArea_B_[player_id].getAllItems();
				console.log(cards);

				for (card of cards) {
					console.log(card);
					cardUniqueId = card.type;
					this.playerHand.addToStockWithId( cardUniqueId, card.id, 'myhand');
				}
				this.downArea_B_[player_id].removeAllTo('myhand');
				//
				var cards = this.downArea_C_[player_id].getAllItems();
				console.log(cards);

				for (card of cards) {
					console.log(card);
					cardUniqueId = card.type;
					this.playerHand.addToStockWithId( cardUniqueId, card.id, 'myhand');
				}
				this.downArea_C_[player_id].removeAllTo('myhand');
				//
				this.prepSetLoc = 0; // Nothing is prepped, so clear the counters
				this.prepRunLoc = 3; 
			} // else do nothing, they've already gone down.
			console.log("[bmc] EXIT onDownAreaClick");
		},
/////////
/////////
/////////
		onDeckSelectionChanged : function() {
			var items = this.deck.getSelectedItems();
			console.log("bmc OnDeckSelectionChanged.");
			console.log(items[0]);
			console.log(this.gamedatas);
			console.log(this.player_id);
			this.drawCard2nd(items, 0 ); // 0 == 'deck', 1 == 'discardPile'
		},
/////////
/////////
/////////
		drawCard2nd : function (items, drawSource) {
			if (items.length > 0) {
				console.log("[bmc] >0; Sending the card.");
				
				var action = 'drawCard';
				if (this.checkAction( action, true)) {
					console.log("[bmc] Action true. AJAX next");
					console.log("/" + this.game_name + "/" + this.game_name + "/" + action + ".html");
					
					var card_id = items[0].id;
console.log(card_id);
					this.ajaxcall("/" + this.game_name + "/" + this.game_name + "/" + action + ".html", {
						id : card_id,
						drawSource : drawSource,
						lock : true
					}, this, function(result) {
					}, function(is_error) {
					});
				} else {
					console.log("[bmc] Action false");
				}
				this.discardPile.unselectAll();
				this.deck.unselectAll();
			} else {
				console.log("[bmc] no items; ignore");
			}
			console.log("[bmc] Leaving onDeckSelectionChanged.");
		},
/////////
/////////
/////////
		onPlayerPlayCardButton : function() {
		},
/////////
/////////
/////////

		clearButtons : function () {
		    this.removeActionButtons(); // Remove the button because they discarded
			this.showingButtons === 'No';
		},
/////////
/////////
/////////		
		onPlayerDiscardButton : function() {
			console.log("[bmc] BUTTON onPlayerDiscardButton!");
			console.log(this.player_id);
			this.clearButtons();
//		    this.removeActionButtons(); // Remove the button because they discarded
//			this.showingButtons === 'No';
			
			var card = this.playerHand.getSelectedItems()[0]; // It must be 1 card only
			console.log(card);
			
			if ( typeof card !== "undefined" ) {
				console.log("[bmc] destroy button!");
				dojo.destroy('currentPlayerPlayButton_id');

				var card_id = card.id;                    

	console.log("[bmc] Discarding card!");

				let action = 'discardCard';
				
				this.ajaxcall("/" + this.game_name + "/" + this.game_name + "/" + action + ".html", {
					id : card_id,
					lock : true
				}, this, function(result) {
				}, function(is_error) {
				});
	 console.log("[bmc] Did ajaxcall.");

				this.playerHand.unselectAll();
			}
		},
/////////
/////////
/////////
		onPlayerSortButton : function() {
			console.log("[bmc] BUTTON onPlayerSortButton!");
			console.log(this.player_id);
			
			var cards = this.playerHand.getSelectedItems(); // It can be >1 card
			console.log(cards);
			
			if (cards.length === 2) { // Sort only when 2 cards are selected
				var cardIds = this.getItemIds(cards);

	console.log("[bmc] cardIds: " + cardIds);

				this.clearButtons();
//				this.removeActionButtons(); // Remove the button because they clicked it
//				this.showingButtons === 'No';
				this.sortHand(cards);
			}
		},
/////////
/////////
/////////
		sortHand : function(items) {
			var thisPlayerHand = this.playerHand.getAllItems();
			console.log("[bmc] thisPlayerHand");
			console.log(thisPlayerHand);

			// Remove PLAY CARD button
			//this.removeActionButtons();
			this.clearButtons();

			// Swap the order if necessary to keep player's 1st selection 1st
			if (this.playerHand.firstSelected != items[0].type) {
				//console.log("[bmc] swap");
				let temp = items[0];
				items[0] = items[1];
				items[1] = temp;
			}
// console.log("[bmc] Move cards around");
		// If two cards have been selected, change the weights
		// Find the indices of the 1st and 2nd cards and move them around

			for ( const [i, card] of thisPlayerHand.entries()) {
				if (items[0].type === card.type) {
					var spotFrom = i;
				}
				if (items[1].type === card.type) {
					var spotTo = i;
				}
			}
			
			this.arraymove(thisPlayerHand, spotFrom, spotTo);

			// Make a change array from the result
			let weightChange = {};
			for (let i in thisPlayerHand) {
                weightChange[thisPlayerHand[i].type] = parseInt(i);
			}
// console.log("[bmc] WC");
// console.log(weightChange);
			this.playerHand.changeItemsWeight(weightChange);
			this.playerHand.unselectAll();
		},
/////////
/////////
/////////
		onPlayerPlaySetButton : function() {
			console.log("[bmc] BUTTON onPlayerPlaySetButton!");
			console.log(this.player_id);
			
			if ( this.goneDown[this.player_id] === 1 ) { // If player already went down, do nothing
				this.showMessage("You already went down");
				return;
			} else {
				this.clearButtons();

				var cards = this.playerHand.getSelectedItems(); // It can be >1 card
				console.log(cards);
				
				var cardIds = this.getItemIds(cards);

console.log("[bmc] cardIds: " + cardIds);

				// Move them into place on the board
				
				this.currentHandType = this.gamedatas.currentHandType;
console.log(this.currentHandType);

				console.log("[bmc] setsRuns: " + this.setsRuns[this.currentHandType][this.prepSetLoc]);
				console.log(this.prepSetLoc);
				
				if (this.setsRuns[this.currentHandType][this.prepSetLoc] == "None" ) {
console.log("[bmc] No more sets needed.");

					//None are needed so don't do anything
				} else {
					// A set is needed, so put it down (as preparation)
					this.putSetDown(cards);
					this.prepSetLoc++;
					console.log(this.prepSetLoc);
					console.log("[bmc] INCREMENTED prepSetLoc");
				}

				this.playerHand.unselectAll();
				this.showHideButtons();
			}
		},
/////////
/////////
/////////
		onPlayerGoDownButton : function() {
			console.log("[bmc] BUTTON onPlayerGoDownButton!");
			console.log(this.player_id)
			var handItems = this.playerHand.getSelectedItems(); // Get the card for joker swap, if any
			
		    this.removeActionButtons(); // Remove the button because they played

			var cardGroupA = this.downArea_A_[this.player_id].getAllItems();
			var cardGroupB = this.downArea_B_[this.player_id].getAllItems();
			var cardGroupC = this.downArea_C_[this.player_id].getAllItems();

			console.log(cardGroupA);
			console.log(cardGroupB);
			console.log(cardGroupC);
			console.log(handItems);
			
            var cardGroupAIds = this.getItemIds(cardGroupA);
            var cardGroupBIds = this.getItemIds(cardGroupB);
            var cardGroupCIds = this.getItemIds(cardGroupC);
			var handItemIds = this.getItemIds(handItems);

console.log("[bmc] cardIdsA: " + cardGroupAIds);
console.log("[bmc] cardIdsB: " + cardGroupBIds);
console.log("[bmc] cardIdsC: " + cardGroupCIds);
console.log("[bmc] handItemIds: " + handItemIds);

			var [boardCard, boardArea, boardPlayer] = this.getSelectedDownAreaCards ();

console.log( boardCard);
console.log( boardArea);
console.log( boardPlayer);
console.log( handItemIds);
			let boardCardId = ( boardCard['id'] === undefined) ? '' : boardCard['id'];
console.log( boardCardId);

			this.playerHand.unselectAll();
			this.action_playerGoDown(
				cardGroupAIds,
				cardGroupBIds,
				cardGroupCIds,
				boardCardId,
				boardArea,
				boardPlayer,
				handItemIds
			);
//            this.action_playSeveralCards(cardIds);
		},
/////////
/////////
/////////
		onPlayerPlayRunButton : function() {
			console.log("[bmc] BUTTON onPlayerPlayRunButton!");
			console.log(this.player_id)
			
			if ( this.goneDown[this.player_id] === 1 ) { // If player already went down, do nothing
				this.showMessage("You already went down");
				return;
			} else {
				this.clearButtons();

				var cards = this.playerHand.getSelectedItems(); // It can be >1 card
				console.log(cards);
			
				var cardIds = this.getItemIds(cards);

console.log("[bmc] cardIds: " + cardIds);

				// Move them into place on the board
				
				this.currentHandType = this.gamedatas.currentHandType;
console.log(this.currentHandType);

				console.log("[bmc] setsRuns: " + this.setsRuns[this.currentHandType][this.prepRunLoc]);
				console.log(this.prepRunLoc);
				
				if (this.setsRuns[this.currentHandType][this.prepRunLoc] == "None" ) {
console.log("[bmc] No more runs needed.");

					//None are needed so don't do anything
				} else {
					// A run is needed, so put it down (as preparation)
					this.putRunDown(cards);
					this.prepRunLoc++;
					console.log(this.prepRunLoc);
					console.log("[bmc] INCREMENTED prepRunLoc");
				}

				this.playerHand.unselectAll();
				this.showHideButtons();
			}
		},
//			this.playerHand.unselectAll();
//          this.action_playSeveralCards(cardIds);
/////////
/////////
/////////
        getItemIds: function (items) {
            var ids = [];
            for (var i in items) {
                var item = items[i];
                ids.push(item.id);
            }
            return ids;
        },
/////////
/////////
/////////
		action_playerGoDown: function(cardGroupA, cardGroupB, cardGroupC, boardCardId, boardArea, boardPlayer, handItems){
			console.log("[bmc] action_playerGoDown");
            this.sendAction('playerGoDown', {
                cardGroupA: this.toNumberList(cardGroupA),
                cardGroupB: this.toNumberList(cardGroupB),
                cardGroupC: this.toNumberList(cardGroupC),
				boardCardId: boardCardId,
				boardArea: boardArea,
				boardPlayer: boardPlayer,
				handItems: this.toNumberList(handItems)
            });
			
		},
/////////
/////////
/////////
//        action_playSeveralCards: function (cardIds) {
//           this.sendAction('playSeveralCards', {
//                card_ids: this.toNumberList(cardIds)
//            });
//        },
/////////
/////////
/////////
        toNumberList: function (ids) {
			numberedList = ids.join(';');
console.log("[bmc] numberedList: " + numberedList);
            return numberedList;
        },
/////////
/////////
/////////
        sendAction: function (action, args) {
console.log("[bmc] sendAction: " + action + " : " );
console.log(args);
            var params = {};
            if (args) {
                for (var key in args) {
                    params[key] = args[key];
                }
            }
            params.lock = true;
console.log("[bmc] params: ");
console.log(params);
            this.ajaxcall("/" + this.game_name + "/" + this.game_name + "/" +action+'.html', params, this, function (result) { });
        },
/////////
/////////
/////////
		drawCard : function ( player_id, card_id, color, value, drawSource, drawPlayer) {
console.log("[bmc] !!DrawCard!!");
console.log("[bmc] player_id: " + player_id);
console.log("[bmc] card_id, color, value, drawSource: ");
console.log(card_id);
console.log(color);
console.log(value);
console.log(drawSource);
console.log(drawPlayer);

			if (drawSource.match(/playerDown/g)) {
				var from = drawSource + '_' + drawPlayer;
			} else {
				var from = drawSource;
			}

console.log("[bmc] modified drawSource");
console.log(from);

			if (player_id != this.player_id) {
				console.log("[bmc] player_id is not me");
				if ( drawSource == 'deck' ) {
					this.deck.removeFromStockById(
						card_id,
						'overall_player_board_' + player_id
					);
				} else if ( drawSource == 'discardPile' ) {
					this.discardPile.removeFromStockById(
						card_id,
						'overall_player_board_' + player_id
					);
				} else if ( drawSource == 'playerDown_A') {
					this.downArea_A_[drawPlayer].removeFromStockById(
					card_id,
					'overall_player_board_' + player_id
					);
				} else if ( drawSource == 'playerDown_B') {
					this.downArea_B_[drawPlayer].removeFromStockById(
					card_id,
					'overall_player_board_' + player_id
					);
				} else if ( drawSource == 'playerDown_C') {
					this.downArea_C_[drawPlayer].removeFromStockById(
					card_id,
					'overall_player_board_' + player_id
					);
				}
			} else {
				console.log("[bmc] player_id is me");
				console.log(drawSource + '_item_' + card_id);
				console.log('myhand_item_' + card_id);
				// Only process if it's the detailed notification for this player
				if (( color !== undefined ) &&
				    ( color !== '' )) { 
					console.log("[bmc] 1 Player notify");
					let cardUniqueId = this.getCardUniqueId(color, value);
console.log(cardUniqueId);
//					this.playerHand.addToStockWithId( cardUniqueId, card_id, 'deck'); // Add the card to my hand
					this.playerHand.addToStockWithId( cardUniqueId, card_id, from ); // Add the card to my hand from the board
					if ( drawSource == 'deck' ) {
						this.deck.removeFromStockById(card_id, 'myhand');
					} else if ( drawSource == 'discardPile' ) {
						this.discardPile.removeFromStockById(card_id, 'myhand');
					} else if ( drawSource == 'playerDown_A' ) {
						this.downArea_A_[drawPlayer].removeFromStockById(card_id, 'myhand');
					} else if ( drawSource == 'playerDown_B' ) {
						this.downArea_B_[drawPlayer].removeFromStockById(card_id, 'myhand');
					} else if ( drawSource == 'playerDown_C' ) {
						this.downArea_C_[drawPlayer].removeFromStockById(card_id, 'myhand');
					}
				} else {
					console.log("[bmc] group notify");
				}
			}
		},
/////////
/////////
/////////
		onDiscardPileSelectionChanged: function() {
			var card = this.discardPile.getSelectedItems()[0]; // Only ajax if a card was selected
console.log("[bmc] onDiscardPileSelectionChanged.");
console.log(card);
console.log(this.gamedatas);
console.log(this.player_id);

			var items = new Array();
			items.push(card);
			this.drawCard2nd(items, 1 ); // 0 == 'deck', 1 == 'discardPile'
		},
/////////
/////////
/////////
		putSetDown: function (cards) {
			console.log('[bmc] putSetDown');
			console.log(cards);
			
			for (card of cards) {
				console.log("[bmc] card");
				console.log(card);

				cardUniqueId = card.type;
				cardId = card.id;
				
				console.log(cardUniqueId);
				console.log(cardId);
				console.log(this.player_id);
				
				var from = 'myhand_item_' + card.id;
				
				this.currentHandType = this.gamedatas.currentHandType;

				if (this.setsRuns[this.currentHandType][this.prepSetLoc] == "Area_A") {
					this.downArea_A_[ this.player_id ].addToStockWithId(cardUniqueId, cardId, 'myhand');
					console.log("[bmc] SLIDING!!!!!!");
					dojo.addClass('playerDown_A_' + this.player_id, "background-color: Blue");
				}
				if (this.setsRuns[this.currentHandType][this.prepSetLoc] == "Area_B") {
					this.downArea_B_[ this.player_id ].addToStockWithId(cardUniqueId, cardId, 'myhand');
					var to = 'playerDown_B_';
				}
				if (this.setsRuns[this.currentHandType][this.prepSetLoc] == "Area_C") {
					this.downArea_C_[ this.player_id ].addToStockWithId(cardUniqueId, cardId, 'myhand');
					var to = 'playerDown_C_';
					
				}
				
				this.cardDisplayClass = "downPrep";
				
				console.log(from);
				//console.log(to);
				
//				this.placeOnObject(from, to + this.player_id);
//				this.slideToObject(from, to + this.player_id, 1000).play();
//				this.slideToObjectPos(from, to + this.player_id, 1000).play();
//				this.slideToObjectPos(from, to, 0, -50, 1000, 0).play();
//				this.placeOnObject('myhand_item_' + card.id, 'playerDown_A_' + player_id);
				// Slide to it's final destination
//				this.slideToObject('myhand_item_' + card.id, 'playerDown_A_' + player_id, 1000).play();
				this.playerHand.removeFromStockById(card.id);
				this.play1card.removeFromStock(1);

				
/*
				//let card = {};
				//card.type = this.playerHand.getItemById(card_id).type; // This worked with 1 player
				card.id = cardIDs[card_id];
				card.type =  notif.args.card_type[card_id];
				card.type_arg =  notif.args.card_type_arg[card_id];
				console.log(card);
				color = card.type;
				value = card.type_arg;
				console.log("[bmc] color and value");
				console.log(color);
				console.log(value);
				
				cardUniqueId = this.getCardUniqueId(color, value);
				
				//TODO I think overall_player_board is wrong... probably downArea_A_...
				this.downArea_A_[player_id].addToStockWithId(cardUniqueId, card.id, 'overall_player_board_' + player_id);
				
				console.log("[bmc] PLAYERS");
				console.log(player_id);
				console.log(this.player_id);

				if (player_id === this.player_id) {
					var from = 'myhand_item_' + card.id;
				} else {
					this.play1card.addToStock(1); // Just a card back played by the other player
					var from = 'play1card';
				}

				this.placeOnObject(from, 'playerDown_B_' + player_id);
				this.slideToObject(from, 'playerDown_B_' + player_id, 1000).play();
//				this.placeOnObject('myhand_item_' + card.id, 'playerDown_A_' + player_id);
				// Slide to it's final destination
//				this.slideToObject('myhand_item_' + card.id, 'playerDown_A_' + player_id, 1000).play();
				this.playerHand.removeFromStockById(card.id);
				this.play1card.removeFromStock(1);
*/
			}
		},
////////
////////
////////
		putRunDown: function (cards) {
			console.log('[bmc] putRunDown');
			console.log(cards);
			
			for (card of cards) {
				console.log("[bmc] card");
				console.log(card);

				cardUniqueId = card.type;
				cardId = card.id;
				
				console.log(cardUniqueId);
				console.log(cardId);
				console.log(this.player_id);
				
//				var from = 'myhand_item_' + card.id;
				
				this.currentHandType = this.gamedatas.currentHandType;

				if (this.setsRuns[this.currentHandType][this.prepRunLoc] == "Area_A") {
					this.downArea_A_[ this.player_id ].addToStockWithId(cardUniqueId, cardId, 'myhand');
					dojo.addClass('playerDown_A_' + this.player_id, "background-color: Blue");
				}
				if (this.setsRuns[this.currentHandType][this.prepRunLoc] == "Area_B") {
					this.downArea_B_[ this.player_id ].addToStockWithId(cardUniqueId, cardId, 'myhand');
				}
				if (this.setsRuns[this.currentHandType][this.prepRunLoc] == "Area_C") {
					this.downArea_C_[ this.player_id ].addToStockWithId(cardUniqueId, cardId, 'myhand');
					
				}
				
				this.cardDisplayClass = "downPrep";
				
//				console.log(from);
				//console.log(to);
				
//				this.placeOnObject(from, to + this.player_id);
//				this.slideToObject(from, to + this.player_id, 1000).play();
//				this.slideToObjectPos(from, to + this.player_id, 1000).play();
//				this.slideToObjectPos(from, to, 0, -50, 1000, 0).play();
//				this.placeOnObject('myhand_item_' + card.id, 'playerDown_A_' + player_id);
				// Slide to it's final destination
//				this.slideToObject('myhand_item_' + card.id, 'playerDown_A_' + player_id, 1000).play();
				this.playerHand.removeFromStockById(card.id);
				this.play1card.removeFromStock(1);

				
/*
				//let card = {};
				//card.type = this.playerHand.getItemById(card_id).type; // This worked with 1 player
				card.id = cardIDs[card_id];
				card.type =  notif.args.card_type[card_id];
				card.type_arg =  notif.args.card_type_arg[card_id];
				console.log(card);
				color = card.type;
				value = card.type_arg;
				console.log("[bmc] color and value");
				console.log(color);
				console.log(value);
				
				cardUniqueId = this.getCardUniqueId(color, value);
				
				//TODO I think overall_player_board is wrong... probably downArea_A_...
				this.downArea_A_[player_id].addToStockWithId(cardUniqueId, card.id, 'overall_player_board_' + player_id);
				
				console.log("[bmc] PLAYERS");
				console.log(player_id);
				console.log(this.player_id);

				if (player_id === this.player_id) {
					var from = 'myhand_item_' + card.id;
				} else {
					this.play1card.addToStock(1); // Just a card back played by the other player
					var from = 'play1card';
				}

				this.placeOnObject(from, 'playerDown_B_' + player_id);
				this.slideToObject(from, 'playerDown_B_' + player_id, 1000).play();
//				this.placeOnObject('myhand_item_' + card.id, 'playerDown_A_' + player_id);
				// Slide to it's final destination
//				this.slideToObject('myhand_item_' + card.id, 'playerDown_A_' + player_id, 1000).play();
				this.playerHand.removeFromStockById(card.id);
				this.play1card.removeFromStock(1);
*/
			}
		},
/////////
/////////
/////////
		showHideButtons : function() {
			console.log("[bmc] ENTER ShowHideButtons");
            this.clearButtons();
			
			var showButtons = new Array();
			
			if (parseInt(this.gamedatas.currentPlayerId) === this.player_id ) { // If it's this player's turn
				showButtons['myturn'] = true;
			}

			console.log(this.player_id);
			console.log(this.goneDown[this.player_id]);

			showButtons['goneDown'] = (parseInt(this.goneDown[this.player_id]) === 1 ) ? true : false;
			
			//
			// Show SORT button if a player has a card selected
			//
			var items = this.playerHand.getSelectedItems();
			if ( items.length >= 1 ) {
				showButtons['handSelected'] = true;
				if ( items.length > 2 ) {
					showButtons['twoOrMore'] = true;
				}
				if ( items.length == 2 ) {
					showButtons['twoOrMore'] = true;
					this.addActionButton( 'buttonPlayerSort', _("Sort2"), 'onPlayerSortButton');
				}
			}
			
			////
			////
			////

			if ( this.prepRunLoc + this.prepSetLoc > 3) {
				showButtons['prepped'] = true;
			}

			////
			////
			////
			
			console.log("[bmc] showButtons:");
			console.log(showButtons);
			
			//
			// Show GO DOWN button if prepped, not gone down and my turn
			//
			
			if (  showButtons['prepped'] && 
				!(showButtons['goneDown']) &&
				  showButtons['myturn'] ) {
				this.addActionButton('buttonPlayerGoDown', _("Go Down2"), 'onPlayerGoDownButton');
				this.showingButtons === 'Yes';
			}
			//
			// Show PREP SET/RUN buttons if those are a target and player not down
			//
			if (!(showButtons['goneDown']) &&
				  showButtons['handSelected'] ) {
				
				// Determine if a Set and/or a Run is needed; Show buttons accordingly
				setNeeded = 0;
				runNeeded = 0;
				
				this.currentHandType = this.gamedatas.currentHandType;

				for (let i = 0; i < 3 ; i++) {
					if (this.setsRuns[this.currentHandType][i] != 'None') {
						setNeeded = true;
					}
					if (this.setsRuns[this.currentHandType][i+3] != 'None') {
						runNeeded = true;
					}
				}
				
				console.log("[bmc] Set and Run needed?");
				console.log(setNeeded);
				console.log(runNeeded);
				if (setNeeded) {
					this.addActionButton('buttonPlayerPlaySet', _("Prep Set2"), 'onPlayerPlaySetButton');
					this.showingButtons === 'Yes';
				}
				if (runNeeded) {
					this.addActionButton('buttonPlayerPlayRun', _("Prep Run2"), 'onPlayerPlayRunButton');
					this.showingButtons === 'Yes';
				}
			}				  
			//
			// Show DISCARD if card selected and it's my turn
			//
			if (  showButtons['handSelected'] && 
				  showButtons['myturn'] &&
				 !showButtons['twoOrMore'] ) {
				this.addActionButton('buttonPlayerDiscard', _("Discard2"), 'onPlayerDiscardButton');
				this.showingButtons === 'Yes';
			}
			//
			// Show PLAY if card selected and it's my turn and I've gone down
			//
/*
			if (  showButtons['handSelected'] && 
				  showButtons['goneDown'] &&
				  showButtons['myturn'] ) {
				this.addActionButton('buttonPlayerPlay', _("Play2"), 'onPlayerPlayCardButton');
				this.showingButtons === 'Yes';
			}
*/
		console.log("[bmc] EXIT ShowHideButtons");
		},
/////////
/////////
/////////
		onPlayerHandDoubleClick : function() {
console.log("[bmc] ENTER onPlayerHandDoubleClick");
            var items = this.playerHand.getSelectedItems();
console.log(items);
			if (items) {
				this.onPlayerDiscardButton();
			}
console.log("[bmc] EXIT onPlayerHandDoubleClick");
		},
/////////
/////////
/////////
        onPlayerHandSelectionChanged : function() {
			console.log("[bmc] ENTER onPlayerHandSelectionChanged");
            var items = this.playerHand.getSelectedItems();
//console.log("[bmc] on PlayerHand: items in OPHSC: ");
console.log(items);

console.log("[bmc] gamedatas:");
console.log(this.gamedatas);
console.log("[bmc] Players and this hand:");
console.log(this.gamedatas.gamestate.active_player);
console.log(this.player_id);
console.log(items.length);

/*
			if(items.length >= 1 ) {
				console.log("[bmc] All players see Set, Run and Sort buttons.");
				if (this.showingButtons === 'No' ) {
					this.showingButtons = 'Yes';
					this.addActionButton('buttonPlayerSort', _("Sort2"), 'onPlayerSortButton');
					this.addActionButton('buttonPlayerPlaySet', _("Prep Set2"), 'onPlayerPlaySetButton');
					this.addActionButton('buttonPlayerPlayRun', _("Prep Run2"), 'onPlayerPlayRunButton');
					
					 // if player can discard then they have already drawn. Then show buttons to discard or go down.
					if ((this.gamedatas.gamestate.active_player == this.player_id) && 
					    (this.gamedatas.gamestate.possibleactions.includes("discardCard"))) {
							this.addActionButton('buttonPlayerDiscard', _("Discard2"), 'onPlayerDiscardButton');
					}
				}
			}
*/
			if (items.length === 1) {
//console.log("[bmc] Store the first");
				this.playerHand.firstSelected = items[0].type;
			}

			this.showHideButtons();			
/*
			if (items.length === 0) {
// console.log("[bmc] unselectAll");
console.log(this.prepRunLoc + this.prepSetLoc);
console.log(this.showingButtons);
				if (( this.prepRunLoc + this.prepSetLoc > 3) && // If some cards are on the table
					( this.goneDown[this.player_id] === 0) ) { // And the player has not gone down
					this.removeActionButtons();
					this.addActionButton('buttonPlayerGoDown', _("Go Down2"), 'onPlayerGoDownButton');
					this.showingButtons = 'Yes';
				} else {
					this.clearButtons();
//					this.removeActionButtons();
//					this.showingButtons = 'No';
				}
			}
*/
//			this.removeActionButtons();
//			this.playerHand.unselectAll();
			console.log("[bmc] EXIT onPlayerHandSelectionChanged");
        },

// THIS onPlayerHandSelectionChanged USED TO WORK, NOW UPGRADING IT:
//
        // onPlayerHandSelectionChanged : function() {
			// console.log("[bmc] !!onPlayerHandSelectionChanged!!");
            // var items = this.playerHand.getSelectedItems();
//console.log("[bmc] on PlayerHand: items in OPHSC: ");
// console.log(items);

// console.log("[bmc] gamedatas:");
// console.log(this.gamedatas);
// console.log("[bmc] Players and this hand:");
// console.log(this.gamedatas.gamestate.active_player);
// console.log(this.player_id);
			// if (this.gamedatas.gamestate.active_player == this.player_id) {
				// if(items.length >= 1 ) {
					// console.log("[bmc] SHOW BUTTON TO ONLY 1");
					// if (this.showingButtons === 'No' ) {
						// this.showingButtons = 'Yes';
						// this.addActionButton('buttonPlayerPlaySet', _("Prep Set2"), 'onPlayerPlaySetButton');
						// this.addActionButton('buttonPlayerPlayRun', _("Prep Run2"), 'onPlayerPlayRunButton');
						// this.addActionButton('buttonPlayerSort', _("Sort2"), 'onPlayerSortButton');
						// this.addActionButton('buttonPlayerDiscard', _("Discard2"), 'onPlayerDiscardButton');
						// this.addActionButton('buttonPlayerGoDown', _("Go Down2"), 'onPlayerGoDownButton');
					// }
				// }
			// } else if (items.length === 2) {
				// this.addActionButton('buttonPlayerPlaySet', _("Prep Set2"), 'onPlayerPlaySetButton');
				// this.addActionButton('buttonPlayerPlayRun', _("Prep Run2"), 'onPlayerPlayRunButton');
				// this.sortHand(items);
			// }

			// if (items.length === 1) {
//console.log("[bmc] Store the first");
				// this.playerHand.firstSelected = items[0].type;
			// } else if (items.length === 0) {
//console.log("[bmc] unselectAll");
				// this.removeActionButtons();
				// this.showingButtons = 'No';
//				this.playerHand.unselectAll();
			// }
			// console.log("[bmc] leaving onPlayerHandSelectionChanged");
        // },
/////////
/////////
/////////
        ///////////////////////////////////////////////////
        //// Reaction to cometD notifications

        /*
            setupNotifications:
            
            In this method, you associate each of your game notifications with your local method to handle it.
            
            Note: game notification names correspond to "notifyAllPlayers" and "notifyPlayer" calls in
                  your tutorialrumone.game.php file.
        
        */
        setupNotifications: function()
        {
            console.log( 'notifications subscriptions setup' );
            
            dojo.subscribe( 'newHand'  , this, "notif_newHand");
			this.notifqueue.setSynchronous( 'newHand', 1000 );

            dojo.subscribe( 'discardCard' , this, "notif_discardCard");
			
            dojo.subscribe( 'drawCard' , this, "notif_drawCard");

            dojo.subscribe( 'newScores', this, "notif_newScores" );

            dojo.subscribe( 'playSeveralCards' , this, "notif_playSeveralCards");

			dojo.subscribe( 'playerGoDown' , this, "notif_playerGoDown");
			
            dojo.subscribe( 'cardPlayed' , this, "notif_cardPlayed");
			
            dojo.subscribe( 'deckShuffled' , this, "notif_deckShuffled");

			console.log("[bmc] finished cardPlayed dojo");

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
/////////
/////////
/////////
        notif_newScores : function(notif) {
			console.log("[bmc] notif_newScores", notif);

			this.currentHandType = notif.args.currentHandType;
			
            // Update players' scores
            for ( var player_id in notif.args.newScores) {
                this.scoreCtrl[player_id].toValue(notif.args.newScores[player_id]);
            }
			
        },
/////////
/////////
/////////
        notif_newHand : function(notif) {
			console.log("[bmc] notif_newHand");
			console.log(notif);

            // We received a new full hand of cards. Clear the table.
            this.playerHand.removeAll();
			this.discardPile.removeAll();
			this.deck.removeAll();
			
			for (var player in this.gamedatas.players) {
				this.downArea_A_[player].removeAll();
				this.downArea_B_[player].removeAll();
				this.downArea_C_[player].removeAll();

				this.goneDown[player] = 0;
			}
			
			this.prepSetLoc = 0; // Nothing is prepped, so clear the counters
			this.prepRunLoc = 3; 
			
			// Set up the new hand for the player
            for ( let i in notif.args.hand) {
                let card = notif.args.hand[i];
                let color = card.type;
                let value = card.type_arg;
                this.playerHand.addToStockWithId(this.getCardUniqueId(color, value), card.id);
			}
console.log("[bmc] this.playerHand");			
console.log(this.playerHand);

			// Set up the discard pile
			var discardPileWeights = new Array();

            for ( let i in notif.args.discardPile) {
				console.log("[bmc] NEW DISCARD PILE");
                let card = notif.args.discardPile[i];
                let color = card.type;
                let value = card.type_arg;
                this.discardPile.addToStockWithId(this.getCardUniqueId(color, value), card.id);
				let location_arg = parseInt(notif.args.discardPile[i]['location_arg']);
				discardPileWeights[this.getCardUniqueId(color, value)] = location_arg;
			}
			// Set the weights in the discard pile
			this.discardPile.changeItemsWeight(discardPileWeights);
console.log("[bmc] this.discardPile");			
console.log(this.discardPile);

			// Set up the draw deck
			for (let i = 0 ; i < notif.args.deck.length; i++) {
				this.deck.addToStockWithId(1, notif.args.deck[i]);
			}
console.log("[bmc] this.deck");			
console.log(this.deck);

        },
/////////
/////////
/////////
		notif_deckShuffled : function(notif) {
			// Set up the draw deck
			console.log("[bmc] Shuffle Cards:");
			console.log(notif);
			
			for (let i = 0 ; i < notif.args.deck.length; i++) {
				this.deck.addToStockWithId(1, notif.args.deck[i]);
			}
			
			this.discardPile.removeAll();

console.log("[bmc] Shuffled New Deck");			
console.log(this.deck);
		},
		
		notif_cardPlayed : function(notif) {
console.log("[bmc]notif_cardPlayed");
            this.cardWasPlayed(
				notif.args.card_id,
				notif.args.player_id,
				notif.args.color,
				notif.args.value,
				notif.args.boardArea,
				notif.args.boardPlayer
			);
console.log("[bmc] notif_cardPlayed Done.");
		},
		
        notif_discardCard : function(notif) {
console.log("[bmc]notif_discardCard");
            // Discard a card to the discard pile
            this.discardCard(notif.args.player_id, notif.args.color, notif.args.value, notif.args.card_id);
console.log("[bmc] leaving notif_discardCard");
        },
/////////
/////////
/////////
        notif_drawCard : function(notif) {
console.log("[bmc]notif_drawcard");
console.log(notif.args);
            // Draw a card from the deck
            this.drawCard(notif.args.player_id, notif.args.card_id, notif.args.color, notif.args.value, notif.args.drawSource, notif.args.drawPlayer);
        },
/////////
/////////
/////////
        notif_trickWin : function(notif) {
            // We do nothing here (just wait in order players can view the 4 cards played before they're gone.
        },
/////////
/////////
/////////
		notif_playerGoDown: function(notif) {
			console.log('Player had indeed gone down. Solidify the card positions.');
			console.log(notif.args.player_id);
			console.log(notif);
			this.goneDown[notif.args.player_id] = 1; //0 = Not gone down; 1 = Gone down.
			
			downPlayer = notif.args.player_id;
			downArea = notif.args.player_down;
			card_ids = notif.args.card_ids;
			console.log(card_ids);
//			card_types = notif.args.card_type;
//			card_type_args = notif.args.card_type_arg;

			joker = notif.args.joker;
			console.log("[bmc] joker:");
			console.log(joker);
			
			if (this.gamedatas.currentPlayerId === downPlayer ) {
				console.log("[bmc] I went down!");
				// TODO: change the color back to 'normal' if it was in 'prep' color
				
				// Move the joker, if any, to the down position, everything else is already in place because of the prep
				
				if (joker != undefined ) { // Per JS must check undefined before checking for a property of the variable
					if ( joker.id != 'None' ) { // Then there's a joker; Move it
				
						jokerUniqueID = this.getCardUniqueId(joker.type, joker.type_arg);
						
						targetArea = notif.args.targetArea;
						
						if (targetArea === 'playerDown_A') {
							console.log("[bmc] Adding Joker to AREA A");
							this.downArea_A_[downPlayer].addToStockWithId(jokerUniqueID, joker.id, 'myhand');
						}
						if (targetArea === 'playerDown_B') {
							console.log("[bmc] Adding Joker to AREA B");
							this.downArea_B_[downPlayer].addToStockWithId(jokerUniqueID, joker.id, 'myhand');
						}
						if (targetArea === 'playerDown_C') {
							console.log("[bmc] Adding Joker to AREA C");
							this.downArea_C_[downPlayer].addToStockWithId(jokerUniqueID, joker.id, 'myhand');
						}
						this.playerHand.removeFromStockById(joker.id);

					}
				}
				return;
				
			} else {
				console.log("[bmc] Someone else went down!");
				for (card_id in card_ids) {
					console.log(card_id);
					color = card_ids[card_id]['type'];
					value = card_ids[card_id]['type_arg'];
					
					console.log(color);
					console.log(value);
					
					cardUniqueId = this.getCardUniqueId(color, value);
					console.log(cardUniqueId);
					
					if (downArea === 'playerDown_A_') {
						console.log("[bmc] Adding to AREA A");
						this.downArea_A_[downPlayer].addToStockWithId(cardUniqueId, card_id, 'overall_player_board_' + downPlayer);
					}
					if (downArea === 'playerDown_B_') {
						console.log("[bmc] Adding to AREA B");
						this.downArea_B_[downPlayer].addToStockWithId(cardUniqueId, card_id, 'overall_player_board_' + downPlayer);
					}
					if (downArea === 'playerDown_C_') {
						console.log("[bmc] Adding to AREA C");
						this.downArea_C_[downPlayer].addToStockWithId(cardUniqueId, card_id, 'overall_player_board_' + downPlayer);
					}

/*TODO 9/28: 
Player was allowed to pull an old card from the discard pile.
When coming back from godownprep, all the buttons disappear. Instead, show button 'go down'.
When coming back from godownprep, selecting a card doesn't allow discard, only nothing or 'godown'.
When you go down, it should play a sound "Yes!" or something.
If player has gone down, when they click their hand do not show PREP buttons. (discard and sort)
Remove the DISCARD button if > 1 card is selected.
Get GO DOWN button to appear after 2nd PREP is placed.
Get the 2nd set of DOWN cards to appear on other players screens.

Add the automatic addition to a run, if possible.

Add double-click-discards card.

Add "Joker swap if it's there"

Stop the JS from calling playcard twice. It works, but then it calls it again and fails.




*/



/*
TODO: Moving 'overall_player_board' is wrong. Instead, 'mobile_obj' should be the set or run which was sent.

slideToObject
function( mobile_obj, target_obj, duration, delay )
Return an dojo.fx animation that is sliding a DOM object from its current position over another one
Animate a slide of the DOM object referred to by domNodeToSlide from its current position to the xpos, ypos relative to the object referred to by domNodeToSlideTo.
*/



//					this.slideToObject('overall_player_board_' + downPlayer, downArea + downPlayer, 1000).play();
					this.playerHand.removeFromStockById(card_id);

/*					
					if (player_id === this.player_id) {
						var from = 'myhand_item_' + card.id;
					} else {
						this.play1card.addToStock(1); // Just a card back played by the other player
						var from = 'play1card';
					}

					this.placeOnObject(from, 'playerDown_B_' + player_id);
					this.slideToObject(from, 'playerDown_B_' + player_id, 1000).play();
	//				this.placeOnObject('myhand_item_' + card.id, 'playerDown_A_' + player_id);
					// Slide to it's final destination
	//				this.slideToObject('myhand_item_' + card.id, 'playerDown_A_' + player_id, 1000).play();
					this.playerHand.removeFromStockById(card.id);
					this.play1card.removeFromStock(1);
*/
				}
			}
		},
/////////
///////// NOTE: playSeveralCards is an older function, no longer needed after 'godown' is finished
/////////
		notif_playSeveralCards: function (notif) {
			console.log('[All Players] Player played several cards');
			console.log(notif);
			player_id = notif.args.player_id;
			console.log(player_id);
			console.log(this.player_id);
			
			for (card_id in notif.args.card_ids) {
				console.log("[bmc] card_id");
				console.log(card_id);
				let card = {};
				//card.type = this.playerHand.getItemById(card_id).type; // This worked with 1 player
				card.id = notif.args.card_ids[card_id];
				card.type =  notif.args.card_type[card_id];
				card.type_arg =  notif.args.card_type_arg[card_id];
				console.log(card);
				color = card.type;
				value = card.type_arg;
				console.log("[bmc] color and value");
				console.log(color);
				console.log(value);
				
				cardUniqueId = this.getCardUniqueId(color, value);
				
				//TODO I think overall_player_board is wrong... probably downArea_A_...
				this.downArea_A_[player_id].addToStockWithId(cardUniqueId, card.id, 'overall_player_board_' + player_id);
				
				console.log("[bmc] PLAYERS");
				console.log(player_id);
				console.log(this.player_id);

				if (player_id === this.player_id) {
					var from = 'myhand_item_' + card.id;
				} else {
					this.play1card.addToStock(1); // Just a card back played by the other player
					var from = 'play1card';
				}

				this.placeOnObject(from, 'playerDown_B_' + player_id);
				this.slideToObject(from, 'playerDown_B_' + player_id, 1000).play();
//				this.placeOnObject('myhand_item_' + card.id, 'playerDown_A_' + player_id);
				// Slide to it's final destination
//				this.slideToObject('myhand_item_' + card.id, 'playerDown_A_' + player_id, 1000).play();
				this.playerHand.removeFromStockById(card.id);
				this.play1card.removeFromStock(1);
			}
		},
/////////
/////////
/////////
        // From this point and below, you can write your game notifications handling methods
        
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

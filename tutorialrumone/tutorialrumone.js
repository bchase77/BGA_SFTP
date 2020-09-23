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
			//playerDown_A_{PLAYER_ID}
			
			this.downArea_A = new Array();
			this.downArea_B = new Array();
			this.downArea_C = new Array();
			
            for ( var player in this.gamedatas.players) {
//console.log( "i: " + i);
				console.log(player);
				
				this.downArea_A[player] = new ebg.stock(); // new stock object for the down cards
				//this.downArea.create( this, $('playerDown_A_2333742'), this.cardwidth, this.cardheight );          

				// Create stock for Area A
				var containerName = 'playerDown_A_' + player;
				this.downArea_A[player].create( this, $(containerName), this.cardwidth, this.cardheight );            
				this.downArea_A[player].image_items_per_row = 13; // 13 images per row in the sprite file
				for (var color = 1; color <= 4; color++) {
					for (var value = 1; value <= 13; value++) {
						// Build card type id. Only create 52 here, 2 jokers below
					
							let card_type_id = this.getCardUniqueId(color, value);
							this.downArea_A[player].addItemType(card_type_id, card_type_id, g_gamethemeurl + 'img/4ColorCardsx5.png', card_type_id);
					}
				}
				this.downArea_A[player].addItemType( 52, 52, g_gamethemeurl + 'img/4ColorCardsx5.png', 52) // Color 5 Value 1
				this.downArea_A[player].addItemType( 53, 53, g_gamethemeurl + 'img/4ColorCardsx5.png', 53) // Color 5 Value 2
				this.downArea_A[player].setOverlap( 50, 0 );

				// Create stock for Area B
				this.downArea_B[player] = new ebg.stock(); // new stock object for the down cards
				var containerName = 'playerDown_B_' + player;
				this.downArea_B[player].create( this, $(containerName), this.cardwidth, this.cardheight );            
				this.downArea_B[player].image_items_per_row = 13; // 13 images per row in the sprite file
				for (var color = 1; color <= 4; color++) {
					for (var value = 1; value <= 13; value++) {
						// Build card type id. Only create 52 here, 2 jokers below
					
							let card_type_id = this.getCardUniqueId(color, value);
							this.downArea_B[player].addItemType(card_type_id, card_type_id, g_gamethemeurl + 'img/4ColorCardsx5.png', card_type_id);
					}
				}
				this.downArea_B[player].addItemType( 52, 52, g_gamethemeurl + 'img/4ColorCardsx5.png', 52) // Color 5 Value 1
				this.downArea_B[player].addItemType( 53, 53, g_gamethemeurl + 'img/4ColorCardsx5.png', 53) // Color 5 Value 2
				this.downArea_B[player].setOverlap( 50, 0 );

				// Create stock for Area C
				this.downArea_C[player] = new ebg.stock(); // new stock object for the down cards
				var containerName = 'playerDown_C_' + player;
				this.downArea_C[player].create( this, $(containerName), this.cardwidth, this.cardheight );            
				this.downArea_C[player].image_items_per_row = 13; // 13 images per row in the sprite file
				for (var color = 1; color <= 4; color++) {
					for (var value = 1; value <= 13; value++) {
						// Build card type id. Only create 52 here, 2 jokers below
					
							let card_type_id = this.getCardUniqueId(color, value);
							this.downArea_C[player].addItemType(card_type_id, card_type_id, g_gamethemeurl + 'img/4ColorCardsx5.png', card_type_id);
					}
				}
				this.downArea_C[player].addItemType( 52, 52, g_gamethemeurl + 'img/4ColorCardsx5.png', 52) // Color 5 Value 1
				this.downArea_C[player].addItemType( 53, 53, g_gamethemeurl + 'img/4ColorCardsx5.png', 53) // Color 5 Value 2
				this.downArea_C[player].setOverlap( 50, 0 );

				// Show the cards in the down areas
				console.log("[bmc] SHOW THE CARDS IN DOWN AREAS");
				console.log(this.gamedatas);
				
				// Populate Area A
				for ( var cardIndex in this.gamedatas.downArea_A[player]) {
					console.log("[bmc] CARD IN DOWN AREA:");
					card = this.gamedatas.downArea_A[player][cardIndex];
					console.log(card);
					var card_id = card.id;
					var color = card.type;
					var value = card.type_arg;
					console.log("[bmc] 3 VALUES:");
					console.log(card_id);
					console.log(color);
					console.log(value);
					this.downArea_A[player].addToStockWithId(this.getCardUniqueId(color, value), card_id);
				}
				// Populate Area B
				for ( var cardIndex in this.gamedatas.downArea_B[player]) {
					console.log("[bmc] CARD IN DOWN AREA:");
					card = this.gamedatas.downArea_B[player][cardIndex];
					console.log(card);
					var card_id = card.id;
					var color = card.type;
					var value = card.type_arg;
					console.log("[bmc] 3 VALUES:");
					console.log(card_id);
					console.log(color);
					console.log(value);
					this.downArea_B[player].addToStockWithId(this.getCardUniqueId(color, value), card_id);
				}
				// Populate Area C
				for ( var cardIndex in this.gamedatas.downArea_C[player]) {
					console.log("[bmc] CARD IN DOWN AREA:");
					card = this.gamedatas.downArea_C[player][cardIndex];
					console.log(card);
					var card_id = card.id;
					var color = card.type;
					var value = card.type_arg;
					console.log("[bmc] 3 VALUES:");
					console.log(card_id);
					console.log(color);
					console.log(value);
					this.downArea_C[player].addToStockWithId(this.getCardUniqueId(color, value), card_id);
				}
			}
			
			// Add stock for a single card played by a player. Probably can delete this if slow
			this.play1card = new ebg.stock(); // New stock for the draw pile (the rest of the deck)
            this.play1card.create( this, $('play1card'), this.cardwidth, this.cardheight );            
			this.play1card.image_items_per_row = 13;
			this.play1card.setOverlap( 0.1, 0 );
			this.item_margin = 0;
			this.play1card.addItemType( 1, 1, g_gamethemeurl + 'img/4ColorCardsx5.png', 54); // Color 5 Value 3 is red back of the card
			
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

console.log(this.gamedatas.activePlayerId);
console.log(this.player_id);

            dojo.connect( this.playerHand,  'onChangeSelection', this, 'onPlayerHandSelectionChanged' );
            dojo.connect( this.deck,        'onChangeSelection', this, 'onDeckSelectionChanged' );
            dojo.connect( this.discardPile, 'onChangeSelection', this, 'onDiscardPileSelectionChanged' );
			//dojo.connect($('currentPlayerPlayButton_id'), 'onChange', this, 'onPlayerPlayButton' );

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
		onDeckSelectionChanged : function() {
			var items = this.deck.getSelectedItems();
			console.log("bmc OnDeckSelectionChanged.");
			console.log(items[0]);
			console.log(this.gamedatas);
			console.log(this.player_id);
			this.drawCard2nd(items, 0 ); // 0 == 'deck', 1 == 'discardPile'
		},
		
		drawCard2nd : function (items, drawSource) {
			if (items.length > 0) {
				console.log("[bmc] >0; Sending the card.");
				
				var action = 'drawCard';
				if (this.checkAction( action, true)) {
					console.log("[bmc] Action true");
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
		onPlayerDiscardButton : function() {
			console.log("[bmc] BUTTON IN THE CALL!");
			console.log(this.player_id)
		    this.removeActionButtons(); // Remove the button because they discarded
			this.showingButtons === 'No';
			
			var card = this.playerHand.getSelectedItems()[0]; // It must be 1 card only
			console.log(card);
			
			if (Object.keys(card).length > 0) {
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
			console.log("[bmc] Player Sort button IN THE CALL!");
			console.log(this.player_id)
			
			var cards = this.playerHand.getSelectedItems(); // It can be >1 card
			console.log(cards);
			
			if (cards.length === 2) { // Sort only when 2 cards are selected
				var cardIds = this.getItemIds(cards);

	console.log("[bmc] cardIds: " + cardIds);

				this.removeActionButtons(); // Remove the button because they clicked it
				this.showingButtons === 'No';
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
			//this.removeActionButtons(); // TODO: ADD THIS BACK AFTER TESTING!

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
		onPlayerPlayButton : function() {
			console.log("[bmc] Player Play button IN THE CALL!");
			console.log(this.player_id)
		    this.removeActionButtons(); // Remove the button because they played

			var cards = this.playerHand.getSelectedItems(); // It can be >1 card
			console.log(cards);
			
            var cardIds = this.getItemIds(cards);

console.log("[bmc] cardIds: " + cardIds);

			this.playerHand.unselectAll();
            this.action_playSeveralCards(cardIds);
		},
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
        action_playSeveralCards: function (cardIds) {
            this.sendAction('playSeveralCards', {
                card_ids: this.toNumberList(cardIds)
            });
        },
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
console.log("[bmc] sendAction: " + action + " : " + args);
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

/*
TODO: Not sure why I'm getting this error: It almost works

ly_studio.js:2 Server syntax error: tutorialrumone/tutorialrumone/drawDiscard.html
 Array to string conversion in <b>/var/tournoi/release/tournoi-200916-0917-gs/www/game/module/common/deck.game.php</b>
 on line <b>248</b><br />
{"status":1,"data":{"valid":1,"data":null}}


THE AJAX CALL BELOW IS THE PROBLEM:

onDiscardPileSelectionChanged	@	tutorialrumone.js?1600757688128:836
(anonymous)	@	tutorialrumone?table…stuser=2333745:2047
(anonymous)	@	dojo.js:8
_269	@	dojo.js:8
onClickOnItem	@	ly_studio.js:2
(anonymous)	@	tutorialrumone?table…stuser=2333745:2047
(anonymous)	@	dojo.js:8
*/

		drawCard : function ( player_id, card_id, color, value, drawSourceBinary) {
console.log("[bmc] !!DrawCard!!");
console.log("[bmc] player_id: " + player_id);
console.log("[bmc] card_id, color, value, drawSource: ");
console.log(card_id);
console.log(color);
console.log(value);
			drawSource = (drawSourceBinary === 0 ) ? 'deck' : 'discardPile';
console.log(drawSource);

			if (player_id != this.player_id) {
				console.log("[bmc] player_id is not me");
			} else {
				console.log("[bmc] player_id is me");
				console.log(drawSource + '_item_' + card_id);
				console.log('myhand_item_' + card_id);
				// Only process if it's the detailed notification for this player
				if (color !== undefined) { 
					console.log("[bmc] 1 Player notify");
					let cardUniqueId = this.getCardUniqueId(color, value);
console.log(cardUniqueId);
//					this.playerHand.addToStockWithId( cardUniqueId, card_id, 'deck'); // Add the card to my hand
					this.playerHand.addToStockWithId( cardUniqueId, card_id, drawSource); // Add the card to my hand
				} else {
					console.log("[bmc] group notify");
				}
			}
			// Either way, move it from the deck
console.log("[bmc] SLIDING");
console.log(card_id);
console.log(player_id);
console.log(drawSource);
			if ( drawSource == 'deck' ) {
				this.deck.removeFromStockById(card_id, 'overall_player_board_' + player_id);
			} else {
				this.discardPile.removeFromStockById(card_id, 'myhand');
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
		
// Instead now use the 2nd half of the working drawcard function.
/*
			if (card !== undefined) {
				console.log("[bmc] card defined; Sending it.");

				var action = 'drawDiscard';
				if (this.checkAction( action, true)) {
					console.log("[bmc] Action true.");
					console.log("/" + this.game_name + "/" + this.game_name + "/" + action + ".html");
					
					var card_id = card.id;
console.log(card_id);
					this.ajaxcall("/" + this.game_name + "/" + this.game_name + "/" + action + ".html", {
						id : card_id,
						lock : true
					}, this, function(result) {
					}, function(is_error) {
					});				
					this.discardPile.unselectAll();
				} else {
					console.log("[bmc] Action false");
					this.discardPile.unselectAll();
				}
			} else {
				console.log("[bmc] card undefined; ignore");
			}
			console.log("[bmc] Leaving onDiscardPileSelectionChanged.");
		},
*/
/////////
/////////
/////////
        onPlayerHandSelectionChanged : function() {
			console.log("[bmc] !!onPlayerHandSelectionChanged!!");
            var items = this.playerHand.getSelectedItems();
//console.log("[bmc] on PlayerHand: items in OPHSC: ");
console.log(items);

console.log("[bmc] gamedatas:");
console.log(this.gamedatas);
console.log("[bmc] Players and this hand:");
console.log(this.gamedatas.gamestate.active_player);
console.log(this.player_id);
			if (this.gamedatas.gamestate.active_player == this.player_id) {
				if(items.length >= 1 ) {
					console.log("[bmc] SHOW BUTTON TO ONLY 1");
					if (this.showingButtons === 'No' ) {
						this.showingButtons = 'Yes';
						this.addActionButton('buttonPlayerDiscard', _("Discard"), 'onPlayerDiscardButton');
						this.addActionButton('buttonPlayerPlay', _("Play"), 'onPlayerPlayButton');
						this.addActionButton('buttonPlayerSort', _("Sort"), 'onPlayerSortButton');
					}
				}
			} else if (items.length === 2) {
				this.sortHand(items);
			}

			if (items.length === 1) {
//console.log("[bmc] Store the first");
				this.playerHand.firstSelected = items[0].type;
			} else if (items.length === 0) {
// console.log("[bmc] unselectAll");
				this.removeActionButtons();
				this.showingButtons = 'No';
				//this.playerHand.unselectAll();
			}
			console.log("[bmc] leaving onPlayerHandSelectionChanged");
        },
        
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
            // Update players' scores
            for ( var player_id in notif.args.newScores) {
                this.scoreCtrl[player_id].toValue(notif.args.newScores[player_id]);
            }
			
			// TODO: FIX THE TOOL TIP (or other) SO THAT THE HAND TARGET CAN UPDATE.
			console.log("[bmc] showing tooltip");
			this.addTooltip('handTarget', _('The target is bob'), '');
			//= notif.args.handTarget;
        },
/////////
/////////
/////////
        notif_newHand : function(notif) {
            // We received a new full hand of cards. Clear the table.
            this.playerHand.removeAll();
			this.discardPile.removeAll();
			this.deck.removeAll();
			
			for (var player in this.gamedatas.players) {
				this.downArea_A[player].removeAll();
				this.downArea_B[player].removeAll();
				this.downArea_C[player].removeAll();
			}
			
			console.log("[bmc] notif_newHand");
			console.log(notif);

			// Set up the new hand for the player
            for ( let i in notif.args.hand) {
				console.log("[bmc] NEW HAND OF CARDS");
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
            this.drawCard(notif.args.player_id, notif.args.card_id, notif.args.color, notif.args.value, notif.args.drawSource);
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
//		notif_activePlayerPlayedCards: function (notif) {
//			console.log('[1 player] You played cards');
//			console.log(notif);
//
//		},
/////////
/////////
/////////
		notif_playSeveralCards: function (notif) {
			console.log('[All Players] Player played cards');
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
				
				//TODO I think overall_player_board is wrong... probably downArea_A...
				this.downArea_A[player_id].addToStockWithId(cardUniqueId, card.id, 'overall_player_board_' + player_id);
				
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

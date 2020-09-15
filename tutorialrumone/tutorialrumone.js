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
//console.log( "bmc: Starting game setup" );

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
console.log("bmc: myhand:");
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

console.log("bmc: Cards in gamedatas and in player's hand");

console.log(this.gamedatas);
console.log(this.playerHand);


// NEWHAND START MAYBE???


            //this.playerHand.addToStockWithId(this.getCardUniqueId(color, value), card.id);
console.log("bmc: Add cards to hand");

            // Cards in player's hand
            for ( var i in this.gamedatas.hand) {
//console.log( "i: " + i);
                var card = this.gamedatas.hand[i];
                var color = card.type;
                var value = card.type_arg;
//console.log( "CCV: " + card.id + " / " + color + " / " + value );
//console.log(card);
//console.log("bmc: getCardUnique and card.id:");
//console.log(this.getCardUniqueId(color, value));
//console.log(card.id);

                this.playerHand.addToStockWithId(this.getCardUniqueId(color, value), card.id);
            }

// NEWHAND MABYE NOT THIS START
			this.deck = new ebg.stock(); // New stock for the draw pile (the rest of the deck)
            this.deck.create( this, $('deck'), this.cardwidth, this.cardheight );            
			//this.deck.create( this, $('deck'), this.cardwidth, this.cardheight );
			this.deck.image_items_per_row = 13;
			this.deck.setOverlap( 1, 0 );
			this.deck.addItemType( 1, 1, g_gamethemeurl + 'img/4ColorCardsx5.png', 54); // Color 5 Value 3 is red back of the card
//console.log("bmc: deckIDs");
//console.log(this.gamedatas.deckIDs);
// NEWHAND MABYE NOT THIS END

			for ( let i = 0 ; i < this.gamedatas.deckIDs.length; i++) {
//console.log(i + " / " + this.gamedatas.deckIDs[i]);
				this.deck.addToStockWithId(1, this.gamedatas.deckIDs[i]);
			}
console.log("bmc: this.deck");			
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
console.log( "i: " + i);
                var card = this.gamedatas.discardPile[i];
                var color = card.type;
                var value = card.type_arg;
console.log( "CCV: " + card.id + " / " + color + " / " + value );
//console.log(card);

                this.discardPile.addToStockWithId(this.getCardUniqueId(color, value), card.id);
//console.log(card.id);
				let location_arg = parseInt(this.gamedatas.discardPile[i]['location_arg']);
//console.log('bmc: location_arg:');
//console.log(location_arg);

				discardPileWeights[this.getCardUniqueId(color, value)] = location_arg;
console.log(discardPileWeights);
            }
			
// NEWHAND MABYE NOT THIS END

			
//console.log("bmc: discardPile Before");
//console.log(this.discardPile);

//console.log("bmc: discardPileWeights RAW");
//console.log(discardPileWeights);

			// Set the weights in the discard pile
			this.discardPile.changeItemsWeight(discardPileWeights);

// NEWHAND END MAYBE???

console.log("bmc: $(discardPile)");
console.log($('discardPile'));
			
console.log("bmc: discardPile After");
console.log(this.discardPile);

console.log(this.gamedatas.activePlayerId);
console.log(this.player_id);

//var up = document.documentElement.innerHTML;
//console.log(up);

            dojo.connect( this.playerHand,  'onChangeSelection', this, 'onPlayerHandSelectionChanged' );
            dojo.connect( this.deck,    'onChangeSelection', this, 'onDeckSelectionChanged' );
            dojo.connect( this.discardPile, 'onChangeSelection', this, 'onDiscardPileSelectionChanged' );
			//dojo.connect($('currentPlayerPlayButton_id'), 'onChange', this, 'onPlayerPlayButton' );

            // Setup game notifications to handle (see "setupNotifications" method below)
            this.setupNotifications();

            console.log( "bmc: Ending game setup" );
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
			console.log("bmc: SN:");
			console.log(stateName);

            switch( stateName ) {
								
				case 'newHand':
					console.log("bmc: FOUND newHand");
					break;
				case 'playerTurnPlay':
					console.log("bmc: FOUND PTP");
					break;
				case 'nextPlayer':
					console.log("bmc: FOUND nextPlayer");
					break;
				case 'endHand':
					console.log("bmc: FOUND endHand");
					break;
				case 'dummmy':
					break;
				default:
					console.log("bmc: OES DEFAULT");
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
            console.log( 'bmc: onUpdateActionButtons: ' + stateName );
			var items = this.playerHand.getSelectedItems()
//			console.log(this.gamedatas);
//			console.log(this.player_id);
//			console.log(this.gamedatas.gamestate.active_player);

			if (this.gamedatas.gamestate.active_player == this.player_id) {
				switch( stateName ) {
					case 'playerTurnPlay':
						if(items.length == 1 ) {

						console.log("bmc: SHOW BUTTON TO ONLY 1");
						this.addActionButton('buttonPlayerPlay', _("Play Card"), 'onPlayerPlayButton');
						}
				}
			}
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
//            console.log("getCardUniqueId color: " + color)
//            console.log("getCardUniqueId value: " + value)
			var bob = (color - 1) * 13 + (value - 1);
//			console.log("return: " + bob)
            //return (color - 1) * 13 + (value - 2); // Offset depending upon the image sprite file
            return bob;
        },

        playCardOnTable : function(player_id, color, value, card_id) {
			// Purpose is to show the played cards on the table, not really to play the card.
			// Playing of the card is done on the server side (PHP).
console.log("[bmc]playCardOnTable");

			let cardUniqueId = this.getCardUniqueId(color, value);
			let weightChange = {};
			weightChange[cardUniqueId] = this.discardPile.items.length + 300; // might be > by 1
			this.discardPile.addToStockWithId(cardUniqueId, card_id);

//console.log("bmc: Change weight of new card");
//console.log(this.getCardUniqueId(color, value));
// console.log(player_id);
// console.log(color);
// console.log(value);
// console.log(card_id);
// console.log(cardUniqueId);
// console.log(this.discardPile.items.length);
// console.log([weightChange]);
// console.log(this.discardPile);
			
			// Get the id of the last card in the discard
			this.discardPile.changeItemsWeight(weightChange);

            if (player_id != this.player_id) {
console.log("bmc: Card played not by me");
                // Some opponent played a card
                // Move card from player panel
				// placeOnObject( itemToBePlaced, locationToPutIt);
				//this.placeOnObject( mobile_obj, target_obj )
                //this.placeOnObject('cardontable_' + player_id, 'overall_player_board_' + player_id);
				//this.placeOnObject('overall_player_board_' + player_id, 'discardPile_item_' + card_id);
				//This puts the playerboard onto the discard pile (oops!):
				//this.placeOnObject('overall_player_board_' + player_id, 'discardPile');
				//this.placeOnObject('deck_item_43', 'discardPile');				
				
				//Slide the card in from the playing player's board
				// This moves the scorebox with the players's name in it!:
				//this.slideToObject('overall_player_board_' + player_id, 'discardPile').play();
				
            } else {
console.log("bmc: Card played by me");
                // You played a card. If it exists in your hand, move card from there and remove
                // corresponding item

                if ($('myhand_item_' + card_id)) {
console.log("bmc:Was in hand");
                    this.placeOnObject('myhand_item_' + card_id, 'discardPile');
                    this.playerHand.removeFromStockById(card_id);
					// Slide to it's final destination
					this.slideToObject('myhand_item_' + card_id, 'discardPile', 1000).play();
                }
            }
			// In any case: Remove the PLAY button
			//dojo.empty(this.format_block('jstpl_playerPlayButton', {}));
			//dojo.empty('jstpl_playerPlayButton'); // Died
			//dojo.destroy('jstpl_playerPlayButton'); // Didn't die, but didn't remove button
			//dojo.destroy('currentPlayerPlayButton_id'); // THIS ONE WORKS!

            // In any case: move it to its final destination
            //this.slideToObject('myhand_item_' + card_id, 'discardPile_item_' + card_id).play();
			//this.slideToObject('myhand_item_' + card_id, 'deck_item_' + card_id).play();
			//this.slideToObject('discardPile', 'myhand_item_' + card_id).play();
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
		onDeckSelectionChanged : function() {
			var items = this.deck.getSelectedItems();
			console.log("bmc ODPSC: ");
			console.log(items);
			if (items.length > 0) {
				var action = 'drawCard';
				if (this.checkAction( action, true)) {
					var card_id = items[0].id;
					console.log("bmc: Calling ajax with card_id: " + card_id);
					this.ajaxcall("/" + this.game_name + "/" + this.game_name + "/" + action + ".html", {
						id : card_id,
						lock : true
					}, this, function(result) {
					}, function(is_error) {
					});
					this.deck.unselectAll();
				} else {
					this.deck.unselectAll();
				}
			}
		},
		
		onPlayerPlayButton : function() {
			console.log("bmc: BUTTON IN THE CALL!");
			console.log(this.player_id)
		    this.removeActionButtons(); // Remove the button because they played

			var card = this.playerHand.getSelectedItems()[0]; // It must be just 1!
			console.log(card);
//			console.log(this.gamedatas.activePlayerId);
//			console.log(this.gamedatas.hand);
			
			if (Object.keys(card).length > 0) {
				console.log("bmc: destroy button!");
				dojo.destroy('currentPlayerPlayButton_id');

//				let color = this.gamedatas.hand[card.id].type; // Suit
//				let value = this.gamedatas.hand[card.id].type_arg; // Ace through King
				
				// MAYBE: No need to check if there is just 1 card selected because we
				// could not get here otherwise.
				// Also: If they got the button then they are the only player who could play.
				
	//console.log("bmc: checkAction1: " + action);
	//console.log("bmc: checkAction2: " + this.checkAction(action, true));
				var card_id = card.id;                    

	console.log("bmc: Playing card!");

				let action = 'playCard';
				
				this.ajaxcall("/" + this.game_name + "/" + this.game_name + "/" + action + ".html", {
					id : card_id,
					lock : true
				}, this, function(result) {
				}, function(is_error) {
				});
	 console.log("bmc: Did ajaxcall.");

				this.playerHand.unselectAll();
			}			
//			if (this.gamedatas.activePlayerId == this.player_id) {
//			this.playCardOnTable(this.player_id, color, value, card.type);
		},

		
		
		
		
		
		
		
		
		
		
		
		
/*		
		// This next function seems to work!
		onDrawPileSelectionChanged : function() {
			var items = this.drawPile.getSelectedItems();
			console.log("bmc ODPSC: ");
			console.log(items);
			if (items.length > 0) {
				var action = 'drawCard';
				if (this.checkAction( action, true)) {
					var card_id = items[0].id;
					console.log("bmc: Calling ajax with card_id: " + card_id);
					this.ajaxcall("/" + this.game_name + "/" + this.game_name + "/" + action + ".html", {
						id : card_id,
						lock : true
					}, this, function(result) {
					}, function(is_error) {
					});
					this.drawPile.unselectAll();
				} else {
					this.drawPile.unselectAll();
				}
			}
		},
*/
		// But how to get the handsize??? Get it by counting the number of cards in the array
		// on the PHP side and sending only that, not each card value.
		
		drawCard : function ( player_id, card_id) {
console.log("bmc: !!DrawCard(FromDeck)!!");
console.log("bmc: player_id: " + player_id);
console.log("bmc: card_id: " + card_id);
var items = this.deck.getSelectedItems();
console.log("bmc: items: " + items);
//			var phlen = this.playerHand.items.length;
//			var dplen = this.deck.items.length - 1;
//console.log(phlen);
//console.log(dplen);
//console.log(this.deck.items.id[dplen]);
//			if (player_id != this.player_id) {
//				console.log("bmc: player_id is not me");
//			} else {
//				console.log("bmc: forced path: player_id is me");
				console.log('deck_item_' + card_id);
				console.log('myhand_item_' + card_id);
//				this.playerHand.addToStockWithId('deck_item_' + card_id, 'myhand_item_' + card_id);
//				this.playerHand.addToStockWithId( card_id, 'myhand_item_' + card_id);
				//this.playerHand.addToStockWithId( 'deck_item_' + card_id);
//				this.placeOnObject('myhand_item_' + card_id, 'deck_item_' + card_id);
//				this.playerHand.addToStockWithId( items.type, items.id, card_id);
// TODO: Having both below are not correct!
				this.playerHand.addToStockWithId( card_id, 14); // Add the Spade Ace
				this.playerHand.addToStockWithId( 14, card_id); // Add the Spade Ace
				this.deck.removeFromStockById(card_id);
//				this.slideToObject(card_id, 'myhand').play();
//				//this.placeOnObject('cardontable_' + player_id, 'myhand_item_' + card_id);
//				this.placeOnObject(card_id, 'myhand_item_' + phl);
				//this.placeOnObject(card_id, 'myhand');
//				this.playerHand.addToStock(card_id);
//			}
			// In any case: move it to its final destination
		},
		
		/*
        onDrawPileSelectionChanged : function ( control_name, card_id ) {
console.log("bmc: !!onDrawPileSelectionChanged!!");
//console.log(player_id);
console.log(card_id);

//			if (player_id != this.player_id) {
//				console.log("bmc: player_id is not me");
//			} else {
				console.log("bmc: player_id is me");
				this.placeOnObject('myhand_item_' + card_id);
				this.playerHand.addToStock(card_id);
				this.slideToObject(card_id, 'myhand').play();
//			}
			// In any case: move it to its final destination
		},
*/


/*
            var items = this.drawPile.getSelectedItems();
console.log(items);

            if (items.length > 0) {
                var action = 'drawCard';
                if (this.checkAction(action, true)) {
                    // Can play a card
                    var card_id = items[0].id;                    

console.log("[bmc]Drawing card!");
console.log("[bmc]card_id: " + card_id);

					dojo.place(this.format_block('jstpl_cardontable', {
						x : this.cardwidth * (value - 1),
						y : this.cardheight * (color - 1),
						player_id : player_id
					}), 'playertablecard_' + player_id);



                    this.ajaxcall("/" + this.game_name + "/" + this.game_name + "/" + action + ".html", {
                        id : card_id,
                        lock : true
                    }, this, function(result) {
                    }, function(is_error) {
                    });
                    this.drawPile.unselectAll(); // TODO: Not sure if this is needed

//                } else if (this.checkAction('giveCards')) {
//                    // Can give cards => let the player select some cards
                } else {
                    this.drawPile.unselectAll();
                }
            }
		},
*/





		onDiscardPileSelectionChanged: function() {
console.log("bmc: !!onDiscardPileSelectionChanged!!");
            var items = this.discardPile.getSelectedItems();
console.log(items);
		},


        onPlayerHandSelectionChanged : function() {
			console.log("bmc: !!onPlayerHandSelectionChanged!!");
            var items = this.playerHand.getSelectedItems();
//console.log("bmc: on PlayerHand: items in OPHSC: ");
console.log(items);
//			let location_arg = parseInt(this.gamedatas.discardPile[i]['location_arg']);

console.log("bmc: gamedatas:");
console.log(this.gamedatas);
console.log("bmc: Players and this hand:");
console.log(this.gamedatas.gamestate.active_player);
console.log(this.player_id);
			if (this.gamedatas.gamestate.active_player == this.player_id) {
				if(items.length == 1 ) {
					console.log("bmc: SHOW BUTTON TO ONLY 1");
					this.addActionButton('buttonPlayerPlay', _("Play Card2"), 'onPlayerPlayButton');
				}
			}

			var thisPlayerHand = this.playerHand.getAllItems();
			console.log(thisPlayerHand);

            // if (items.length > 0) {
                // var action = 'playCard';
// console.log("bmc: checkAction1: " + action);
// console.log("bmc: checkAction2: " + this.checkAction(action, true));
                // if (this.checkAction(action, true)) {
                   // Can play a card
                    // var card_id = items[0].id;                    

// console.log("[bmc]Playing card!");
// console.log("[bmc]card_id: " + card_id);

                    // this.ajaxcall("/" + this.game_name + "/" + this.game_name + "/" + action + ".html", {
						// id : card_id,
						// lock : true
					// }, this, function(result) {
					// }, function(is_error) {
					// });
//console.log("[bmc]Did ajaxcall.");

                    // this.playerHand.unselectAll();

                // } else if (items.length === 2) {
				if (items.length === 2) {
					// Remove PLAY CARD button
					this.removeActionButtons();

					// Swap the order if necessary to keep player's 1st selection 1st
					if (this.playerHand.firstSelected != items[0].type) {
						//console.log("bmc: swap");
						let temp = items[0];
						items[0] = items[1];
						items[1] = temp;
					}
// console.log("bmc: Move cards around");
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
// console.log("bmc: WC");
// console.log(weightChange);
					this.playerHand.changeItemsWeight(weightChange);
					this.playerHand.unselectAll();
                } else if (items.length === 1) {
//console.log("bmc: Store the first");
					this.playerHand.firstSelected = items[0].type;
				} else {
// console.log("bmc: unselectAll");
				this.removeActionButtons();
			    this.playerHand.unselectAll();
				}
			//}
        },
        
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

            dojo.subscribe( 'playCard' , this, "notif_playCard");
            dojo.subscribe( 'drawCard' , this, "notif_drawCard");

            dojo.subscribe( 'newScores', this, "notif_newScores" );


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
        
        notif_newScores : function(notif) {
            // Update players' scores
            for ( var player_id in notif.args.newScores) {
                this.scoreCtrl[player_id].toValue(notif.args.newScores[player_id]);
            }
        },

        notif_newHand : function(notif) {
            // We received a new full hand of cards.
            this.playerHand.removeAll();
			this.discardPile.removeAll();
			this.deck.removeAll();
			
			console.log("bmc: notif_newHand");
			console.log(notif);

			// Set up the new hand for the player
            for ( let i in notif.args.hand) {
				console.log("bmc: NEW HAND OF CARDS");
                let card = notif.args.hand[i];
                let color = card.type;
                let value = card.type_arg;
                this.playerHand.addToStockWithId(this.getCardUniqueId(color, value), card.id);
			}
console.log("bmc: this.playerHand");			
console.log(this.playerHand);

			// Set up the discard pile
			var discardPileWeights = new Array();

            for ( let i in notif.args.discardPile) {
				console.log("bmc: NEW DISCARD PILE");
                let card = notif.args.discardPile[i];
                let color = card.type;
                let value = card.type_arg;
                this.discardPile.addToStockWithId(this.getCardUniqueId(color, value), card.id);
				let location_arg = parseInt(notif.args.discardPile[i]['location_arg']);
				discardPileWeights[this.getCardUniqueId(color, value)] = location_arg;
			}
			// Set the weights in the discard pile
			this.discardPile.changeItemsWeight(discardPileWeights);
console.log("bmc: this.discardPile");			
console.log(this.discardPile);

			// Set up the draw deck
			for (let i = 0 ; i < notif.args.deck.length; i++) {
				this.deck.addToStockWithId(1, notif.args.deck[i]);
			}
console.log("bmc: this.deck");			
console.log(this.deck);

        },
			
        notif_playCard : function(notif) {
console.log("[bmc]notif_playcard");
            // Play a card on the table
            this.playCardOnTable(notif.args.player_id, notif.args.color, notif.args.value, notif.args.card_id);
        },

        notif_drawCard : function(notif) {
console.log("[bmc]notif_drawcard");
console.log(notif.args.player_id);
console.log(notif.args.card_id);
            // Play a card on the table
            this.drawCard(notif.args.player_id, notif.args.card_id);
        },

        notif_trickWin : function(notif) {
            // We do nothing here (just wait in order players can view the 4 cards played before they're gone.
        },
		
        
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

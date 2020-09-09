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
console.log('bmc: tutorialrumone constructor');
              
            // Here, you can init the global variables of your user interface
            // Example:
            // this.myGlobalValue = 0;

            this.cardwidth = 72;
            this.cardheight = 96;

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
console.log( "bmc: Starting game setup" );

            // Setting up player boards
//            for( var player_id in gamedatas.players )
//            {
//                var player = gamedatas.players[player_id];
//                         
//                // TODO: Setting up each players boards if needed
//            }
            
            // Player hand
			
			// TODO: Change the card weight to change the order in the hand left to right

            this.playerHand = new ebg.stock(); // new stock object for hand
//console.log(this.playerHand)
console.log("bmc: myhand:");
console.log($('myhand'));

            this.playerHand.create( this, $('myhand'), this.cardwidth, this.cardheight );            
            this.playerHand.image_items_per_row = 13; // 13 images per row in the sprite file

console.log( "bmc: Creating cards" );

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
console.log("bmc: getCardUnique and card.id:");
console.log(this.getCardUniqueId(color, value));
console.log(card.id);

                this.playerHand.addToStockWithId(this.getCardUniqueId(color, value), card.id);
            }
			
			this.deck = new ebg.stock(); // New stock for the draw pile (the rest of the deck)
            this.deck.create( this, $('deck'), this.cardwidth, this.cardheight );            
			//this.deck.create( this, $('deck'), this.cardwidth, this.cardheight );
			this.deck.image_items_per_row = 13;
			this.deck.setOverlap( 1, 0 );
			this.deck.addItemType( 1, 1, g_gamethemeurl + 'img/4ColorCardsx5.png', 54); // Color 5 Value 3 is red back of the card
console.log("bmc: deckIDs");
console.log(this.gamedatas.deckIDs);

			for ( let i = 0 ; i < this.gamedatas.deckIDs.length; i++) {
console.log(i + " / " + this.gamedatas.deckIDs[i]);
				this.deck.addToStockWithId(1, this.gamedatas.deckIDs[i]);
			}
console.log("bmc: this.deck");			
console.log(this.deck);
//exit(0);			
			
			
/*
            // Cards for drawing
			// Find it in the data structure
console.log("bmc: !!playerHand");
console.log(this.playerHand);

            this.drawPile = new ebg.stock(); // new stock object for draw pile
//console.log(this.playerHand)
//console.log("myhand");
//console.log($('myhand'));

            this.drawPile.create( this, $('drawPile'), this.cardwidth, this.cardheight );            
            this.drawPile.image_items_per_row = 13; // 13 images per row in the sprite file
            this.drawPile.setOverlap( 1, 0 );

            this.drawPile.addItemType( 1, 1, g_gamethemeurl + 'img/4ColorCardsx5.png', 54); // Color 5 Value 3 is red back of the card

            // if need to add 10 cardbacks to the draw pile randomly colored then do this:
//            for ( var i=1 ; i < 10; i++) { 
//                this.drawPile.addToStock(Math.floor(Math.random() * 2 ) + 1);
//			}
            for ( var i = 0 ; i < this.gamedatas.deckCount; i++) { 
                this.drawPile.addToStock(1);
			}

console.log("bmc: drawPile after");
console.log(this.drawPile);
console.log("bmc: discardPile before");
console.log(this.discardPile);
//exit(0);
*/
// DRAWPILE is the rest of the 'deck'
// DISCARD PILE is the cards on table perhaps? So I need to merge the DISCARD pile and TABLE Concepts.

//[bmc] 8/31/2020 Adding Draw and Discard areas. Want to show N cards overlapped a little bit in each.

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

			var discardPileWeights = new Array();
			//var firstWeight = 200;
			
            // Show the cards actually in the discard pile
            for ( var i in this.gamedatas.discardPile) {
console.log( "i: " + i);
                var card = this.gamedatas.discardPile[i];
                var color = card.type;
                var value = card.type_arg;
console.log( "CCV: " + card.id + " / " + color + " / " + value );
//console.log(card);

                this.discardPile.addToStockWithId(this.getCardUniqueId(color, value), card.id);
console.log(card.id);
//console.log(firstWeight);
				let location_arg = this.gamedatas.discardPile[i]['location_arg'];
console.log('bmc: location_arg:');
console.log(location_arg);

//				discardPileWeights[card.id] = firstWeight;
				discardPileWeights[this.getCardUniqueId(color, value)] = location_arg;
//				discardPileWeights.splice(card.id, 0, firstWeight);
console.log(discardPileWeights);
				//firstWeight++;
            }
			
//			weightChange[cardUniqueId] = this.discardPile.items.length; // might be > by 1
//			this.discardPile.addToStockWithId(cardUniqueId, card_id);
			

console.log("bmc: $(discardPile)");
console.log($('discardPile'));
			
console.log("bmc: discardPile Before");
console.log(this.discardPile);

console.log("bmc: discardPileWeights RAW");
console.log(discardPileWeights);

//			discardPileWeights = ({12: 1, 17:0});
//console.log("bmc: discardPileWeights FORCED");
//console.log(discardPileWeights);

			// Set the weights in the discard pile
//			let weightChange = {};
//			weightChange[this.getCardUniqueId(color, value)] = 0;
//			this.discardPile.changeItemsWeight(weightChange);
			this.discardPile.changeItemsWeight(discardPileWeights);

//			this.discardPile.changeItemsWeight({ 20: 1, 15: 28, 6: 3});

console.log("bmc: discardPile After");
console.log(this.discardPile);



// Not sure what I need from below:
/*
            // Cards played on table
            for (i in this.gamedatas.cardsontable) {
                var card = this.gamedatas.cardsontable[i];
                var color = card.type;
                var value = card.type_arg;
                var player_id = card.location_arg;
                this.playCardOnTable(player_id, color, value, card.id);
            }
*/
            dojo.connect( this.playerHand,  'onChangeSelection', this, 'onPlayerHandSelectionChanged' );
            dojo.connect( this.deck,    'onChangeSelection', this, 'onDeckSelectionChanged' );
            //dojo.connect( this.drawPile,    'onChangeSelection', this, 'onDrawPileSelectionChanged' );
            dojo.connect( this.discardPile, 'onChangeSelection', this, 'onDiscardPileSelectionChanged' );


            // Setup game notifications to handle (see "setupNotifications" method below)
            this.setupNotifications();

            console.log( "bmc: Ending game setup" );
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
console.log("[bmc]playCardOnTable");

/*
console.log( player_id + " / " + color + " / " + value + " / " + card_id);
			let x = this.cardwidth * (value - 1);
			let y = this.cardheight * (color - 1);
console.log("bmc: x and y:" + x + ' / ' + y );

console.log(this.format_block('jstpl_cardontable', {
				x : this.cardwidth * (value - 1),
				y : this.cardheight * (color - 1),
				player_id : player_id
			}));
*/
/*			
			dojo.place(this.format_block('jstpl_cardontable', {
				x : this.cardwidth * (value - 1),
				y : this.cardheight * (color - 1),
				player_id : player_id
			}), 'playertablecard_' + player_id);
*/

			let cardUniqueId = this.getCardUniqueId(color, value);
			let weightChange = {};
			weightChange[cardUniqueId] = this.discardPile.items.length + 300; // might be > by 1
			//weightChange[card_id] = this.discardPile.items.length + 100; // might be > by 1
			this.discardPile.addToStockWithId(cardUniqueId, card_id);

console.log("bmc: Change weight of new card");
//console.log(this.getCardUniqueId(color, value));
console.log(card_id);
console.log(cardUniqueId);
console.log(this.discardPile.items.length);
console.log([weightChange]);
console.log(this.discardPile);

// 25, 45, 21, 29
//  6, 15, 20, 28
//			this.discardPile.changeItemsWeight({ 20: 1, 15: 28, 6: 3});
			
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
//                    this.placeOnObject('cardontable_' + player_id, 'myhand_item_' + card_id);
                    this.placeOnObject('myhand_item_' + card_id, 'discardPile');
                    this.playerHand.removeFromStockById(card_id);
					// Slide to it's final destination
					this.slideToObject('myhand_item_' + card_id, 'discardPile', 1000).play();
//					this.slideToObject('cardontable_' + player_id,'discardPile_item_' + card_id, 1000).play();
                }
				
            }

            // In any case: move it to its final destination
            //this.slideToObject('myhand_item_' + card_id, 'discardPile_item_' + card_id).play();
			//this.slideToObject('myhand_item_' + card_id, 'deck_item_' + card_id).play();
			//this.slideToObject('discardPile', 'myhand_item_' + card_id).play();
        },

/*
        playCardOnTable : function(player_id, color, value, card_id) {
console.log("[bmc]playCardOnTable");
console.log( player_id + " / " + color + " / " + value + " / " + card_id);
            // player_id => direction
			let x = this.cardwidth * (value - 1);
			let y = this.cardheight * (color - 1);
console.log("bmc: x and y:" + x + ' / ' + y );
			
			dojo.place(this.format_block('jstpl_discardPile', {
				x : this.cardwidth * (value - 1),
				y : this.cardheight * (color - 1)
			}), 'discardPile');
			
//            dojo.place(this.format_block('jstpl_discardPile', {
//                x : this.cardwidth * (value - 1),
//                y : this.cardheight * (color - 1)
//            }), 'discardPile');

            if (player_id != this.player_id) {
console.log("bmc: Card played not by me");
                // Some opponent played a card
                // Move card from player panel
                this.placeOnObject('discardPile_' + player_id, 'overall_player_board_' + player_id);
            } else {
console.log("bmc: Card played by  me");
                // You played a card. If it exists in your hand, move card from there and remove
                // corresponding item

                if ($('myhand_item_' + card_id)) {
                    this.placeOnObject('discardPile', 'myhand_item_' + card_id);
                    this.playerHand.removeFromStockById(card_id);
                }
            }

            // In any case: move it to its final destination
            this.slideToObject('myhand_item_' + card_id, 'discardPile').play();
        },
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
            var items = this.playerHand.getSelectedItems();
console.log("bmc: onDiscard: items in OPHSC: " + items);

		
		},


        onPlayerHandSelectionChanged : function() {
console.log("bmc: !!onPlayerHandSelectionChanged!!");
            var items = this.playerHand.getSelectedItems();
console.log("bmc: on PlayerHand: items in OPHSC: " + items);

			var debug =  Object.keys(this.playerHand);
//console.log("bmc playerHand1: " + debug );
			var debug =  Object.values(this.playerHand);
//console.log("bmc playerHand2: " + debug );
			var debug =  Object.getOwnPropertyNames(this.playerHand);
//console.log("bmc playerHand3: " + debug );

// TODO: Allow player to change the position of each card in their hand

            if (items.length > 0) {
                var action = 'playCard';
console.log("bmc: checkAction1: " + this.checkAction);
console.log("bmc: checkAction2: " + this.checkAction(action, true));
                if (this.checkAction(action, true)) {
                    // Can play a card
                    var card_id = items[0].id;                    

console.log("[bmc]Playing card!");
console.log("[bmc]card_id: " + card_id);

                    this.ajaxcall("/" + this.game_name + "/" + this.game_name + "/" + action + ".html", {
						id : card_id,
						lock : true
					}, this, function(result) {
					}, function(is_error) {
					});

console.log("[bmc]Did ajaxcall.");

                    this.playerHand.unselectAll();
console.log("[bmc]playerHand.unselectAll.");
                } else if (this.checkAction('giveCards')) {
                    // Can give cards => let the player select some cards
                } else {
                    this.playerHand.unselectAll();
                }
            }
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
            dojo.subscribe( 'playCard' , this, "notif_playCard");
            dojo.subscribe( 'drawCard' , this, "notif_drawCard");

            dojo.subscribe( 'trickWin' , this, "notif_trickWin" );
            this.notifqueue.setSynchronous( 'trickWin', 1000 );
//            dojo.subscribe( 'giveAllCardsToPlayer', this, "notif_giveAllCardsToPlayer" );

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

            for ( var i in notif.args.cards) {
                var card = notif.args.cards[i];
                var color = card.type;
                var value = card.type_arg;
                this.playerHand.addToStockWithId(this.getCardUniqueId(color, value), card.id);
			}
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
		
//        notif_giveAllCardsToPlayer : function(notif) {
//            // Move all cards on table to given table, then destroy them
//            var winner_id = notif.args.player_id;
//            for ( var player_id in this.gamedatas.players) {
//                var anim = this.slideToObject('cardontable_' + player_id, 'overall_player_board_' + winner_id);
//                dojo.connect(anim, 'onEnd', function(node) {
//                    dojo.destroy(node);
//                });
//                anim.play();
//            }
//        },  
        
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

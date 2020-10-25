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
var isDebug = window.location.host == 'studio.boardgamearena.com';
var debug = isDebug ? console.info.bind(window.console) : function () { };

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
			this.playerSortBy = 'Run';
			//this.buyCounted = 'No';
			this.buyCounterTimerShouldExist = 'No';
			this.buyCounterTimerExists = 'No';
			this.firstLoad = 'Yes';
			this.handReviewed = 'No';
			this.drawCounter = 0;
			this.buyTimeInSecondsDefault = 10;
			this.buyTimeInSeconds = this.buyTimeInSecondsDefault;

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
// TODO:
// X 10/13 Player can trade card for joker.
// X Players can buy a card 1, 2 or 3 times.
// X When a player discards, the buy-card timer starts but it stops right away.
// X Game progression didn't progress.
// X 10/17 Add player color to the ACTION bar (it's black/white now, b/c send only the text not the whole player object
// X 10/17 Add quantity of BUYS left to the player panel.
// X 10/13 New hand should sort by sets but doesn't
// X 10/13 Even with just 1 window open, the timer went through once then it went through twice then it ended.
// X Also 1 browser ending the timer kicked off another browser timer coutning down. Eventually they stopped
// X but it needs to be fixed.
// X TODO: Verify the RUN plays still work. I fixed SETS, after getting runs to work SETS was broken with jokers.
// X Make sure to go down with jokers and runs.
// X 10/14 players can play and discard whenever. That needs to be restricted.
// X 10/18 The jokers don't always move where they are supposed to. Sometimes show in 2 places: where they were and where they really went.
// X 10/18 The going down of a RUN, the NOTIFY OF OTHER PLAYERS gave an error: Cannot read property 'image' of undefined.
// X 10/19 Still having trouble with the drawnotify. Might want to go back to the previous code.
// X 10/12: The discard 1 card NOTIFY is not being noticed by the other tables.
// X 10/16: When playing a card for a joker, joker comes to hand and also stays on board. It should not stay on board.
// X 9/28 When coming back from godownprep, all the buttons disappear. Instead, show button 'go down'.
// X 9/28 When coming back from godownprep, selecting a card doesn't allow discard, only nothing or 'godown'.
// X 9/28 Remove the DISCARD button if > 1 card is selected.
// X 9/28 Get GO DOWN button to appear after 2nd PREP is placed.
// X 9/28 Get the 2nd set of DOWN cards to appear on other players screens.
// X Add the automatic addition to a run, if it is possible to be played.
// X Add double-click-discards card. NO: This causes inadvertent discards. Show a button instead.
// X 9/26 Add "Joker swap if it's there"
// X Stop the JS from calling playcard twice. It works, but then it calls it again and fails.
// X 10/14 Game does not allow a joker to be placed on a pile with another joker ("shouldn't happen")
// X 10/7: Each of the windows is using/seeing the same value for this.actionTimerId. Need to figure out how to separate them.  TODO: TRACE THROUGH THE TIMER CODE AND FIGUER OUT WHY IT'S STOPPING. ALSO SOME IDS ARE SAME SOME NOT.
// X 10/10: Change comparison player-whose-turn-it-is to be this.gamedatas.gamestate.active_player, throughout file.
// X 10/18: Add button for onPlayerReviewedHandButton
// X    and the functions wentout and notif.
// X 10/1 When prepping to go down, change the color back to 'normal' if it was in 'prep' color.
// X 10/20: Player 3 went down with a joker and the playerboards show 4 card when player really has 3. F5 didn't fix it.
// 9/28 Player was allowed to pull an old card from the discard pile. Leave AS-IS to reduce testing time.
// 9/28 When you go down, it should play a sound "Yes!" or something.
// 9/28 If player has gone down, when they click their hand do not show PREP buttons. (discard and sort)
// 10/20 Consider adding a database access to PHP (playerHasReviewedHand) to track when players hit the button ON TO THE NEXT.
// X 10/20 After a hand is over and a new hand, other players should be able to buy the discard.
// X 10/21 Played a card onto a run with a joker. The joker came to hand and also
// X       stayed on board (bad). The card did not move from hand to board. But the
// X       game took the action. The PHP worked OK, but the JS player doing the action
// X       did not get updated correctly.
// X 10/21 When the player draws the discard, stop the BUY timers.
// X 10/21 Use setSelectionAppearance to show the DOWN PREP. No, show a border.
// X 10/24: Update the HAND card count after buys, and after every draw and discard, and end of the hand.
// X 10/24: PREP RUN didn't light up the borders.
// X 10/24: X Sometimes the draw card doesn't go all the way to the right.
// 10/24: Something happened where player 2 tried to draw, but it didn't register, and still had only 1 card, then discarded.
// 10/24: Prep border lit up before anyone went down!
// X 10/24: Player buy count didn't update after buy (2 runs)
// X 10/24: Hand count doesn't show accurately after buys.
// 10/24: You cannot buy any more this hand shown to wrong player.
// 10/24: After a new deal, need 45 seconds to figure out if want to buy.
// 10/24: Drawing player IS ABLE to draw other cards from teh discard pile and should not be allowed to.
// 10/24: When a buyer exists, update the action bar to show everyone "player wants to buy."
// 10/24: In this.playerhand, somehow a null card item (id: null, type: -14) is getting into ITEMS under 'modified drawsource', along with other good ID/TYPEs.
////////
////////
////////
        setup: function( gamedatas ) {
console.log( "[bmc] ENTER game setup" );

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
//console.log("[bmc] myhand:");
//console.log($('myhand'));

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

console.log("[bmc] GAMEDATAS");
console.log(this.gamedatas);
//console.log(this.playerHand);

//console.log("[bmc] Add cards to hand");

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

			this.deck = new ebg.stock(); // New stock for the draw pile (the rest of the deck)
            this.deck.create( this, $('deck'), this.cardwidth, this.cardheight );            

			this.deck.image_items_per_row = 13;
			this.deck.setOverlap( 0.1, 0 );

			this.item_margin = 0;

			// Item 54, color 5, value 3 is red back of the card
			this.deck.addItemType( 1, 1, g_gamethemeurl + 'img/4ColorCardsx5.png', 54);
//console.log("[bmc] deckIDs");
//console.log(this.gamedatas.deckIDs);

			for ( let i = 0 ; i < this.gamedatas.deckIDs.length; i++) {
//console.log(i + " / " + this.gamedatas.deckIDs[i]);
				this.deck.addToStockWithId(1, this.gamedatas.deckIDs[i]);
			}
//console.log("[bmc] this.deck");			
//console.log(this.deck);
			
			// Create stock for the discard pile (could be any face-up card)
            this.discardPile = new ebg.stock(); // new stock object for hand
            this.discardPile.create( this, $('discardPile'), this.cardwidth, this.cardheight );            
			this.discardPile.order_items = false;

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
            this.discardPile.setOverlap( 0.1, 0 );
		
			// Create the variables which show how many cards in each pile (deck, hand, discard)
			this.drawDeckSize = new ebg.counter();
			this.drawDeckSize.create( 'drawDeckSize' );
			this.drawDeckSize.setValue( this.gamedatas.deckIDs.length );
			
			this.myHandSize = new ebg.counter();
			this.myHandSize.create( 'myHandSize' );
			this.myHandSize.setValue( this.gamedatas.allHands[ this.player_id ] );
			
			this.handCount = this.gamedatas.allHands[ this.player_id ];
			
			// NEW DISCARD PILE HANDLING
			this.discardSize = new ebg.counter();
			this.discardSize.create( 'discardSize' );
			this.discardSize.setValue( this.gamedatas.discardSize );

			thisDiscardPile = new Array();
			
			for (let i in this.gamedatas.discardPile ) {
				
//				let [ color, value ] = this.getColorValue( this.gamedatas.discardPile[ i ]['type'] );
				el = {
					'id' : this.gamedatas.discardPile[ i ][ 'id' ],
					'unique_id' : this.getCardUniqueId( color, value ),
					'type' : this.gamedatas.discardPile[ i ][ 'type' ],
					'type_arg' : this.gamedatas.discardPile[ i ][ 'type_arg' ],
					'location' : 'discardPile',
					'location_arg' : this.gamedatas.discardPile[ i ][ 'location_arg' ]
				}
				thisDiscardPile[ this.gamedatas.discardPile[ i ][ 'id' ]] = el;
			}
				
			thisDiscardPile.sort( this.compareLocationArg ); // Sort by location_arg, which is weight
console.log( "thisDiscardPile" );
console.log( thisDiscardPile );

// Keep the pile, just show 1 card

			if ( thisDiscardPile.length != 0 ) {
               var card = thisDiscardPile[ 0 ];
               var color = card.type;
               var value = card.type_arg;
console.log( "CCV: " + card.id + " / " + color + " / " + value );
console.log(card);
               this.discardPile.addToStockWithId( this.getCardUniqueId( color, value ), card.id,  );
console.log( discardPile );
			}






/*
			let topCard = thisDiscardPile[0];
console.log( topCard );

            this.discardTop = new ebg.stock(); // new stock object for hand

            for (var color = 1; color <= 4; color++) {
                for (var value = 1; value <= 13; value++) {
                    // Build card type id. Only create 52 here, 2 jokers below
				
						let card_type_id = this.getCardUniqueId(color, value);
						this.discardTop.addItemType(card_type_id, card_type_id, g_gamethemeurl + 'img/4ColorCardsx5.png', card_type_id);
                }
            }
			
            this.discardTop.addItemType( 52, 52, g_gamethemeurl + 'img/4ColorCardsx5.png', 52) // Color 5 Value 1
            this.discardTop.addItemType( 53, 53, g_gamethemeurl + 'img/4ColorCardsx5.png', 53) // Color 5 Value 2






			this.discardTop.addToStockWithId( this.getCardUniqueId( color, value ), topCard.id,  );
*/
// NEWHAND MABYE NOT THIS START
//			var discardPileWeights = new Array();
            // Show the cards actually in the discard pile
//            for ( var i in this.gamedatas.discardPile) {
//console.log( "i: " + i);
//                var card = this.gamedatas.discardPile[ i ];
//                var color = card.type;
//                var value = card.type_arg;
//console.log( "CCV: " + card.id + " / " + color + " / " + value );
//console.log(card);
//                this.discardPile.addToStockWithId( this.getCardUniqueId( color, value ), card.id,  );
//console.log(card.id);
//				let location_arg = parseInt( this.gamedatas.discardPile[ i ][ 'location_arg' ]);
//console.log('[bmc] location_arg:');
//console.log(location_arg);
//				discardPileWeights[ this.getCardUniqueId( color, value )] = location_arg;
//console.log(discardPileWeights);
 //           }

			// END NEW DISCARD PILE HANDLING


			this.buyCount = {};
			this.handCount = {};

			for ( var player_id in this.gamedatas.players ) {
				// console.log("[bmc] Making buy counters.");
				// console.log( player_id );
				
				var player_board_div = $('player_board_' + player_id );
				// console.log("[bmc] player_board_div:");
				// console.log( player_board_div );
				
				var playergomoku = this.gamedatas.players[ player_id ];
			
				dojo.place( this.format_block( 'jstpl_player_board', playergomoku ), player_board_div );
				
				// Track the # of buys per player
				this.buyCount[ player_id ] = new ebg.counter();
				this.buyCount[ player_id ].create( 'buycount_p' + player_id );
				this.buyCount[ player_id ].setValue( this.gamedatas.buyCount[ player_id ] );

				this.handCount[ player_id ] = new ebg.counter();
				this.handCount[ player_id ].create( 'handcount_p' + player_id );
				this.handCount[ player_id ].setValue( this.gamedatas.allHands[ player_id ] );
			}

// NEWHAND MAYBE NOT THIS END

//console.log("[bmc] discardPile Before");
//console.log(this.discardPile);

//console.log("[bmc] discardPileWeights RAW");
//console.log(discardPileWeights);

			// Set the weights in the discard pile to be the natural order in the sprite
//			this.discardPile.changeItemsWeight(discardPileWeights);

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
// console.log(player);
				
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
//console.log("[bmc] SHOW THE CARDS IN DOWN AREAS");
//console.log(this.gamedatas);
				
				// Populate Area A
				for ( var cardIndex in this.gamedatas.downArea_A_[ player ]) {
// console.log("[bmc] CARD IN DOWN AREA:");
					card = this.gamedatas.downArea_A_[ player ][ cardIndex ];
// console.log(card);
					var card_id = card.id;
					var color = card.type;
					var value = card.type_arg;
// console.log("[bmc] 3 VALUES:");
// console.log(card_id);
// console.log(color);
// console.log(value);
					this.downArea_A_[ player ].addToStockWithId( this.getCardUniqueId( color, value ), card_id );
				}
				
				// Populate Area B
				for ( var cardIndex in this.gamedatas.downArea_B_[ player ]) {
// console.log("[bmc] CARD IN DOWN AREA:");
					card = this.gamedatas.downArea_B_[ player ][ cardIndex ];
// console.log(card);
					var card_id = card.id;
					var color = card.type;
					var value = card.type_arg;
// console.log("[bmc] 3 VALUES:");
// console.log(card_id);
// console.log(color);
// console.log(value);
					this.downArea_B_[ player ].addToStockWithId( this.getCardUniqueId( color, value ), card_id );
				}
				// Populate Area C
				for ( var cardIndex in this.gamedatas.downArea_C_[ player ]) {
// console.log("[bmc] CARD IN DOWN AREA:");
					card = this.gamedatas.downArea_C_[ player ][ cardIndex ];
// console.log(card);
					var card_id = card.id;
					var color = card.type;
					var value = card.type_arg;
// console.log("[bmc] 3 VALUES:");
// console.log(card_id);
// console.log(color);
// console.log(value);
					this.downArea_C_[ player ].addToStockWithId( this.getCardUniqueId( color, value ), card_id );
				}
			}
/*			
			// Add stock for a single card played by a player. Probably can delete this if slow
			this.play1card = new ebg.stock(); // New stock for the draw pile (the rest of the deck)
            this.play1card.create( this, $('play1card'), this.cardwidth, this.cardheight );            
			this.play1card.image_items_per_row = 13;
			this.play1card.setOverlap( 0.1, 0 );
			this.item_margin = 0;
			this.play1card.addItemType( 1, 1, g_gamethemeurl + 'img/4ColorCardsx5.png', 54); // Color 5 Value 3 is red back of the card
*/

			this.goneDown = new Array();
//			console.log(this.gamedatas);
//			console.log(this.gamedatas.players);
			
			for (var player in this.gamedatas.players) {
// console.log(player);
				this.goneDown[ player ] = parseInt( this.gamedatas.goneDown[ player ]);
// console.log("[bmc] this.gonedown[]:");
// console.log(this.goneDown[player]);
			}

// console.log(this.player_id);

			dojo.connect( $('myhand'), 'ondblclick', this, 'onPlayerHandDoubleClick' );

            dojo.connect( this.playerHand,  'onChangeSelection', this, 'onPlayerHandSelectionChanged' );
            dojo.connect( this.deck,        'onChangeSelection', this, 'onDeckSelectionChanged' );
            dojo.connect( this.discardPile, 'onChangeSelection', this, 'onDiscardPileSelectionChanged' );

			//dojo.connect( $('deck'), 'onclick', this, 'onDeckSelectionChanged');

			// Set the cards in the down areas as clickable, so players can play on them and trade for jokers
			
			console.log("[bmc] DOWN CARD SELECT SETUP");
			
			for ( var player in this.gamedatas.players) {
//				console.log( 'playerDown_A_, _B_, and _C_' + player);
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

//	I cannot get keypresses to work. The forum shows 1 guy got them working (pass through chat)			
//			dojo.connect( this.playerHand, 'onkeypress', this, 'onPlayerHandKeypress' );
			
			dojo.connect( $('buttonPlayerSortBySet'), 'onclick', this, 'onPlayerSortByButtonSet' );
			dojo.connect( $('buttonPlayerSortByRun'), 'onclick', this, 'onPlayerSortByButtonRun' );

            // Setup game notifications to handle (see "setupNotifications" method below)
            this.setupNotifications();
			
			this.currentHandType = this.gamedatas.currentHandType;
			
			//this.showHideButtons(); // Show the buttons

            console.log( "[bmc] game setup: About to do onPlayerSortButton:" );
			
			this.onPlayerSortByButton(); // click it once because the default is runs
			
            console.log( "[bmc] game setup: About to do showBuyButton:" );
			console.log( this.player_id );
			console.log( this.gamedatas.buyers[ this.player_id ] );
			
			this.turnPlayer = this.gamedatas.activeTurnPlayer_id;
			
			// If this the first load and it's not our turn, then show the BUY buttons. Or,
			// if buy status is unknown and it's not our turn and not
			// the next player's turn, and the state is playerTurnDraw
			// then show BUY buttons.
			// (0==unknown, 1==Not buying 2==Buying) 

console.log("[bmc] Setup");
console.log(this.firstLoad);

			if ((( this.firstLoad == 'Yes' ) && 
			     ( this.player_id != this.gamedatas.activeTurnPlayer_id ) &&
				 
//				(( this.gamedatas.gamestate.action == 'playerTurnDraw' ) ||
				(( this.gamedatas.gamestate.name == 'playerTurnDraw' ) || // draw state
				 ( this.gamedatas.gamestate.action == 'stShowBuyButtons' ))) || // draw state
				 
			    (( this.gamedatas.buyers[ this.player_id ] == 0 ) && // buy status undefined
				 ( this.turnPlayer != this.player_id ) && // the current player
				 ( this.player_id != this.gamedatas.playerOrderTrue[ this.player_id ] ) && // the next player
				 ( this.gamedatas.gamestate.action == 'playerTurnDraw' ))) { // draw state
					
				console.log("[bmc] Decided yes, should show BUY button.");
				this.buyCounterTimerShouldExist = 'Yes'; // A timer and a button should exist
				this.showBuyButton2();
			}
//this.addActionButton( 'buttonPlayerSortBy', _("Sort By Sets"), 	'onPlayerSortByButton');
//this.addActionButton( 'buttonPlayerSortBy', _("Sort By Runs"), 'onPlayerSortByButton');

            console.log( "[bmc] EXIT game setup" );
        },
       

        ///////////////////////////////////////////////////
        //// Game & client states
        
        // onEnteringState: this method is called each time we are entering into a new game state.
        //                  You can use this method to perform some user interface changes at this moment.
        //
        onEnteringState: function( stateName, args ) {
            console.log( 'ENTER ENTERING state: ' + stateName );
			console.log( this.player_id);
			console.log( this.gamedatas.gamestate.active_player);
			console.log( this.gamedatas.activeTurnPlayer_id );

			console.log("[bmc] STATENAME:");
			console.log(stateName);

//			this.showHideButtons();
            
			switch( stateName ) {
								
				case 'newHand':
					console.log("[bmc] FOUND newHand");
					break;
				case 'playerTurnDraw':
					console.log("[bmc] FOUND PlayerTurnDraw");
					break;
				case 'checkEmptyDeck':
					console.log("[bmc] FOUND checkEmptyDeck");
					break;
				case 'drawDiscard':
					console.log("[bmc] FOUND drawDiscard");
					break;
				case 'playerTurnPlay':
					console.log("[bmc] FOUND PlayerTurnPlay");
					break;
				case 'nextPlayer':
					console.log("[bmc] FOUND nextPlayer");
					break;
				case 'endHand':
					console.log("[bmc] FOUND endHand");
					break;
				case 'playerWantsToBuy':
					console.log("[bmc] FOUND playerWantsToBuy");
					break;
				case 'playerDoesNotWantToBuy':
					console.log("[bmc] FOUND playerDoesNotWantToBuy");
					break;
				case 'turnPlayerDrawingResolveBuyers':
					console.log("[bmc] FOUND turnPlayerDrawingResolveBuyers");
					break;
				case 'turnPlayerDrawFromDeck':
					console.log("[bmc] FOUND turnPlayerDrawFromDeck");
					break;
				case 'playCard':
					console.log("[bmc] FOUND playCard");
					break;
				case 'playerGoDown':
					console.log("[bmc] FOUND playerGoDown");
					break;
				default:
					console.log("[bmc] OES DEFAULT");
//					this.showHideButtons();
					break;
            }
            /* Example:
           
            case 'myGameState':
            
                // Show some HTML block at this game state
                dojo.style( 'my_html_block_id', 'display', 'block' );
                
                break;
            */
			console.log( 'EXITING ENTERING state: ' + stateName );
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
/////////
/////////
/////////
        // onUpdateActionButtons: in this method you can manage "action buttons" that are displayed in the
        //                        action status bar (ie: the HTML links in the status bar).
        //        
		
		
		
		onUpdateActionButtons : function( stateName, args ) {
console.log( '[bmc] ENTER onUpdateActionButtons: ' + stateName );
console.log( args );
console.log( this.player_id );

			// If someone clicked their button 'On To The Next' just ignore it
			// and replace the button. The state machine will continue after ALL have clicked.
			if ( stateName == 'wentOut' ) {
				this.showReviewButton( args.player_id );
				return;
			}

			this.showBuyButton2();
			
			if (( this.buyCounterTimerShouldExist == 'Yes' ) && 
				( this.actionTimerId2 == null )) {
					
				var notBuyButtonID = 'buttonPlayerNotBuy' + this.player_id;
				this.startActionTimer2( notBuyButtonID );
			}
			this.showHideButtons();
		},

		
/*
		onUpdateActionButtons : function( stateName, args ) {
console.log( '[bmc] ENTER onUpdateActionButtons: ' + stateName );
console.log( args );
console.log( this.player_id );
console.log( this.gamedatas.gamestate.active_player );
console.log( this.gamedatas.gamestate.activeTurnPlayer_id );

			// Show the buy buttons if appropriate on every call
			// Every call here deletes the buttons, so they need to
			// Be rebuilt every time. There must be a better way...
			this.showBuyButton2();

			// if ( args == null ) {
// console.log( '[bmc] args == null' );
				// return;
			// }
			
			// if ( args.buyers == null ) {
// console.log( '[bmc] args.buyers == null' );
				// return;
			// }
			// if ( args.buyers[ this.player_id ] == 0 ) {
// console.log( '[bmc] args.buyers [ this.player_id ] == 0' );
				// this.showBuyButton();
			// }

console.log( '[bmc] EXIT onUpdateActionButtons: ' + stateName );
//exit(0);
		},
End older version of OnUPdateActionButtons*/
/*
        onUpdateActionButtonsORIGINAL : function( stateName, args ) {
console.log( '[bmc] ENTER onUpdateActionButtons: ' + stateName );
//			var items = this.playerHand.getSelectedItems()
//			console.log(this.gamedatas);
			console.log(this.player_id);
			console.log(this.gamedatas.gamestate.active_player);
			
			let notBuyButtonID = 'buttonPlayerNotBuy' + this.player_id;
console.log( "[bmc] BUTTONID 1:" );
console.log( notBuyButtonID );

			// Only show the buy buttons if they already don't exist
			
//			let notBuyButtonDOM = document.getElementById('buttonPlayerNotBuy');
//			let notBuyButtonDOM = document.getElementById( notBuyButtonID );
//console.log("[bmc] notBuyButtonDOM: ", notBuyButtonDOM);
			
			// If the buy button doesn't exist then fall through and check
			//    if it should be shown and timer created
//			if ( notBuyButtonDOM == null ) { // == null or undefined
console.log( "[bmc] this.buyCounterTimerExists:" );
console.log( this.buyCounterTimerExists );

			if (( this.buyCounterTimerExists == 'No' ) && 
			    ( this.player_id != this.gamedatas.gamestate.active_player ) &&
				( stateName == 'playerTurnDraw' )) {
console.log("[bmc] showing the buy button");
					this.buyCounted = 'No';
					this.showBuyButton();
//			}
			}
console.log( "[bmc] BUTTONID 2:" );
console.log( notBuyButtonID );
console.log( '[bmc] EXIT onUpdateActionButtons: ' + stateName );
		},
*/
/////////
/////////
/////////
		onPlayerReviewedHandButton : function() {
console.log("[bmc] ENTER onPlayerReviewedHandButton");
			this.clearButtons();
			this.stopActionTimer2();
			var action = 'playerHasReviewedHand';

			if (this.checkAction( action, true)) {
				this.ajaxcall("/" + this.game_name + "/" + this.game_name + "/" + action + ".html", {
						player_id : this.player_id,
						lock : true
					}, this, function(result) {
					}, function(is_error) {
				});
			}
		},
/////////
/////////
/////////
		onPlayerBuyButton : function() {
console.log("[bmc] ENTER onPlayerBuyButton");
			this.clearButtons();
			this.stopActionTimer2();
			var action = 'buyRequest';

			if (this.checkAction( action, true)) {
				this.ajaxcall("/" + this.game_name + "/" + this.game_name + "/" + action + ".html", {
						player_id : this.player_id,
						lock : true
					}, this, function(result) {
					}, function(is_error) {
				});
			}
		},
/////////
/////////
/////////
		onPlayerNotBuyButton : function() {
console.log("[bmc] ENTER onPlayerNotBuyButton");
			this.clearButtons();
			this.stopActionTimer2();
			
			var action = 'notBuyRequest';

			if (this.checkAction( action, true)) {
				this.ajaxcall("/" + this.game_name + "/" + this.game_name + "/" + action + ".html", {
						player_id : this.player_id,
						lock : true
					}, this, function(result) {
					}, function(is_error) {
				});
			}
		},
/////////
/////////
/////////
/*
		onPlayerReviewedHandButton : function() {
console.log("[bmc] ENTER onPlayerReviewedHandButton");
			var action = 'playerHasReviewedHand';
			
			if (this.checkAction( action, true)) {
				this.ajaxcall("/" + this.game_name + "/" + this.game_name + "/" + action + ".html", {
//						player_id : this.player_id,
						lock : true
					}, this, function(result) {
					}, function(is_error) {
				});
			}
		},
*/
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
		
		getColorValue : function( type ) {
			color = parseInt ( type / 13) + 1;
			value = type - (( color - 1 ) * 13 ) + 1;
			
			return [ color, value ];
		},
/////////
/////////
/////////
        discardCard : function( player_id, color, value, card_id, nextTurnPlayer, allHands, discardSize ) {
			// Purpose is to show the played cards on the table, not really to play the card.
			// Playing of the card is done on the server side (PHP).
console.log( "[bmc] ENTER discardCard" );
console.log( player_id );
console.log( this.player_id );
console.log( nextTurnPlayer );
console.log( color );
console.log( value );
console.log( card_id );
console.log( allHands );
console.log( discardSize );
console.log( "discardPile and playerhand:" );
console.log( this.discardPile );
console.log( this.playerHand );

			// Adjust all hand card-counts because of the discard
			for ( var p_id in allHands ) {
				this.handCount[ p_id ].setValue( allHands[ p_id ] );
			}

			// Set the discard pile size for players to see
			this.discardSize.setValue( discardSize );
			this.myHandSize.setValue( allHands[ this.player_id ] );

			// Add it to the pile and set the weight
			let cardUniqueId = this.getCardUniqueId( color, value );
			this.discardPile.addToStockWithId( cardUniqueId, card_id, 'overall_player_board_' + player_id );

//			let weightChange = {};
////			weightChange[ card_id ] = this.discardPile.items.length + 300; // might be > by 1
//			weightChange[ card_id ] = discardWeight;
//console.log( weightChange[ card_id ] );
//console.log( weightChange );
			
//			var thisDiscardPileIds = this.discardPile.getAllItems();
//console.log( thisDiscardPileIds );

			// var el = {};
			// var thisDiscardPile = new Array();
			// for ( let i in thisDiscardPileIds ) {
				// var [ color, value ] = this.getColorValue( thisDiscardPileIds[ i ]['type'] );
				// el = {
					// 'id' : thisDiscardPileIds[ i ][ 'id' ],
					// 'unique_id' : this.getCardUniqueId( color, value ),
					// 'type' : color,
					// 'type_arg' : value,
					// 'location' : 'discardPile',
					// 'location_arg' : this.player_id
				// }
				// thisDiscardPile[ thisDiscardPileIds[ i ][ 'id' ]] = el;
			// }
			// for ( let i in this.discardPile ) {
				// weightChange[ thisDiscardPile[ i ].unique_id ] = thisDiscardPileIds[ i ].discardWeight;
			// }
// console.log( "weightchange after process:" );
// console.log(weightChange);
			// this.discardPile.changeItemsWeight( weightChange );

			// Discarding a card means the turn shifts to the next player
			this.turnPlayer = player_id;

console.log( player_id );
console.log( this.player_id );

			if ( this.player_id == player_id ) {
console.log("[bmc] Card played by me");
                // You played a card. If it exists in your hand, move card from there and remove
                // corresponding item

                if ($('myhand_item_' + card_id)) {
console.log("[bmc] Was in hand");
                    this.placeOnObject('myhand_item_' + card_id, 'discardPile');
					// Slide to it's final destination
//TODO TRY REMOVING THIS:					this.slideToObject('myhand_item_' + card_id, 'discardPile', 1000).play();
                    this.playerHand.removeFromStockById(card_id);
                }
            } else if ( this.player_id != nextTurnPlayer ) {
				// If we are not the next player then show the buttons

console.log("[bmc] Card played not by me");
				this.buyCounterTimerShouldExist = 'Yes'; // A timer and a button should exit
				this.showBuyButton2();
            } else {
				// Then we are the next player, who gets to draw it for free; No need for BUY buttons
				console.log( "[bmc] I am the 'Next Player' who can draw the discard for free" );
			}
			
console.log("[bmc] EXIT discardCard");
        },
//function( mobile_obj, target_obj, duration, delay )
/////////
/////////
/////////		
		arraymove : function (arr, fromIndex, toIndex) {
			var element = arr[fromIndex];
			arr.splice(fromIndex, 1);
			console.log("::"+arr);
			arr.splice(toIndex, 0, element);
		},
/////////
/////////
/////////
		startActionTimer2: function ( buttonId ) {
console.log("this.firstLoad");
console.log(this.firstLoad);
			if ( this.firstLoad == 'Yes' ) { // Don't show the timers on the first loading of the game 
console.log("first load was YES");
				this.firstLoad = 'No';
				return;
			}

			// MUST CALL THIS ONLY AFTER THE BUTTONS ARE CREATED. IF BUTTONS ARE REMOVED
			//   (for example when onUpdateActionButtons is run, which is a lot) THEY 
			//   MUST BE RECREATED.
console.log( "[bmc] ENTER startActionTimer2" );
			if( !$(buttonId) ) {
console.log( "[bmc] buttonID is Null!" );
				return;
			}
			if( this.actionTimerId2 ) { // Don't create a new timer if one already exists.
console.log( "[bmc] Timer already exists, not need to create.");
				return;
			}
			var isReadOnly = this.isReadOnly();
//		  if (isDebug || isReadOnly || !this.bRealtime) {
			if ( isReadOnly ) { // Spectators are read only?
debug('Ignoring startActionTimer(' + buttonId + ')', 'debug=' + isDebug, 'readOnly=' + isReadOnly, 'realtime=' + this.bRealtime);
				return;
			}
			this.actionTimerLabel = $(buttonId).innerHTML;
			this.actionTimerSeconds = 11 ; // BUY COUNTDOWN
console.log( "[bmc] Starting Timer! " + this.actionTimerSeconds );


			this.actionTimerFunction2 = () => {
console.log("[bmc] ENTER actionTimerFunction2.");
console.log( buttonId );
console.log( $(buttonId) );
				var button = $(buttonId);
console.log( "[bmc] actionTimerFunction2 BUTTONID: ");
console.log( button );			
console.log( this.actionTimerId2 );	
				if ( button == null ) {
console.log( "[bmc] BUTTON IS GONE!" );
console.log( this.actionTimerId2 );
					this.stopActionTimer2();
console.log( "[bmc] YIKES! Not sure if this is fatal, but the button should exist.");
//					exit(0); // FATAL ERROR, the button should exist!
				} else if ( this.actionTimerSeconds-- > 1 ) {
debug('Timer ' + buttonId + ' has ' + this.actionTimerSeconds + ' seconds left');
					button.innerHTML = this.actionTimerLabel + ' (' + this.actionTimerSeconds + ')';
				} else {
debug('Timer ' + buttonId + ' execute');
					button.click();
console.log("[bmc] Just clicked the button automatically. Now remove timer, buttons and set that they should not exist and don't exist.");
					this.stopActionTimer2();
				}
console.log("[bmc] EXIT actionTimerFunction2.");
			};


console.log("[bmc] Between definition and call.");
			this.actionTimerFunction2();
			this.buyCounterTimerExists = 'Yes';			
			this.actionTimerId2 = window.setInterval( this.actionTimerFunction2, 1000 );
debug('Timer #' + this.actionTimerId2 + ' ' + buttonId + ' start');
console.log( "[bmc] EXIT startActionTimer2" );
		},			
/////////
/////////
/////////
		stopActionTimer2 : function () {
console.log( "[bmc] ENTER StopAction Timer2" );
console.log( this.actionTimerId2 );
			this.buyCounterTimerExists = 'No';
			this.buyCounterTimerShouldExist = 'No';
			this.clearButtons();
	
			if ( this.actionTimerId2 != null ) {
				debug('Timer #' + this.actionTimerId2 + ' stop');
				window.clearInterval( this.actionTimerId2 );
				delete this.actionTimerId2;
			}
console.log( "[bmc] EXIT StopAction Timer2" );
		},




















		/*
		 * Add a timer to an action and trigger action when timer is done (from Kingdom Builder)
		 */
		startActionTimer: function ( buttonId ) {
console.log( "[bmc] ENTER startActionTimer" );
console.log( buttonId );
			
			if( !$(buttonId) ) {
console.log( "[bmc] buttonID is Null!" );
				return;
			}

			if( this.actionTimerId ) { // Don't create a new timer if one already exists.
console.log( "[bmc] Timer already exists, not need to create.");
				return;
			}

			var isReadOnly = this.isReadOnly();
//		  if (isDebug || isReadOnly || !this.bRealtime) {
			if ( isReadOnly ) {
debug('Ignoring startActionTimer(' + buttonId + ')', 'debug=' + isDebug, 'readOnly=' + isReadOnly, 'realtime=' + this.bRealtime);
				return;
			}

			this.actionTimerLabel = $(buttonId).innerHTML;
			this.actionTimerSeconds = this.buyTimeInSeconds ; // BUY COUNTDOWN
		  
console.log( "[bmc] Starting Timer! " + this.actionTimerSeconds );




			this.actionTimerFunction = () => {
console.log("[bmc] ENTER actionTimerFunction. Here is S(buttonId):");
console.log(buttonId);
console.log($(buttonId));
				var button = $(buttonId);
console.log( "[bmc] actionTimerFunction BUTTONID: ");
console.log( button );			
console.log( this.actionTimerId );	

				if ( button == null ) {
console.log( "[bmc] NO LONGEER DOING: this.stopActionTimer" );
console.log( this.actionTimerId );	

					//this.stopActionTimer();
					
				} else if ( this.actionTimerSeconds-- > 1 ) {
debug('Timer ' + buttonId + ' has ' + this.actionTimerSeconds + ' seconds left');
					button.innerHTML = this.actionTimerLabel + ' (' + this.actionTimerSeconds + ')';
				} else {
debug('Timer ' + buttonId + ' execute');
//					window.clearInterval( this.actionTimerId );
//					delete this.actionTimerId; // NOT SURE IF THIS WILL FIX IT CONTINUING
					this.stopActionTimer();
					button.click();
				}
console.log("[bmc] EXIT actionTimerFunction. Here is S(buttonId):");
			};
			
			
			
			
console.log("[bmc] Between definition and call.");
			this.actionTimerFunction();
			this.buyCounterTimerExists = 'Yes';			
			
			this.actionTimerId = window.setInterval( this.actionTimerFunction, 1000 );
debug('Timer #' + this.actionTimerId + ' ' + buttonId + ' start');
console.log( "[bmc] EXIT startActionTimer" );
		},
/////////
/////////
/////////
		stopActionTimer: function () {
console.log( "[bmc] StopAction Timer! " );
console.log( this.actionTimerId );
			this.buyCounterTimerExists = 'No';
			this.buyCounterTimerShouldExist = 'No';
			this.clearButtons();
	
			if ( this.actionTimerId != null ) {
				debug('Timer #' + this.actionTimerId + ' stop');
				window.clearInterval( this.actionTimerId );
				delete this.actionTimerId;
			}
/*			if ( this.actionTimerId == null ) {
console.log( "this.actionTimerId is null or undefined" );
console.log( this.actionTimerId );	

			} else {
debug('Timer #' + this.actionTimerId + ' stop');
				window.clearInterval( this.actionTimerId );
				delete this.actionTimerId;
			}
*/
		},
/////////
/////////
/////////
		isReadOnly: function () {
		  return this.isSpectator || typeof g_replayFrom != "undefined" || g_archive_mode;
		},
/////////
/////////
/////////
		
		
		
		
		
		
		
		
		
		
		
		
		
		

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
		showBuyButton2 : function() {
console.log("[bmc] ENTER showBuyButton2");
console.log( this.buyCounterTimerShouldExist );
console.log( this.buyCounterTimerExists );

			if (( this.buyCounterTimerShouldExist == 'Yes' ) || 
				( this.buyCounterTimerExists == 'Yes' )) {

				var buyButtonID = 'buttonPlayerBuy' + this.player_id;
				var notBuyButtonID = 'buttonPlayerNotBuy' + this.player_id;
				this.addActionButton( buyButtonID, _("Buy!"), 'onPlayerBuyButton' );
				this.addActionButton( notBuyButtonID , _("Not Buy!"), 'onPlayerNotBuyButton' );
console.log("[bmc] Action buttons were just created.");

			}
console.log("[bmc] EXIT showBuyButton2");
		},
/////////
/////////
/////////
		showBuyButton : function() {
//			console.log("[bmc] this.buyCounted: ", this.buyCounted );
console.log("[bmc] ENTER showBuyButton");

			var buyButtonID = 'buttonPlayerBuy' + this.player_id;
			var notBuyButtonID = 'buttonPlayerNotBuy' + this.player_id;
console.log( "[bmc] BUTTONIDs:" );
console.log( buyButtonID );
console.log( notBuyButtonID );

			// Only show the buy buttons if they already don't exist
			
//			var notBuyButtonDOM = document.getElementById('buttonPlayerNotBuy');
			var notBuyButtonDOM = document.getElementById( notBuyButtonID );
console.log("[bmc] notBuyButtonDOM: ", notBuyButtonDOM);
console.log("[bmc] buyCounterTimerExists: ", this.buyCounterTimerExists );
console.log("[bmc] buyCounterTimerShouldExist: ", this.buyCounterTimerShouldExist );

			if (( this.buyCounterTimerShouldExist == 'Yes' ) && 
			    ( notBuyButtonDOM == null )) { // == null or undefined
//console.log("[bmc] notBuyButtonDOM is null");
//				if ( this.buyCounted == 'No' ) {
console.log("[bmc] Timer and buttons must exist, so create them.");
//					this.buyCounted = 'Yes';
					
//					this.addActionButton('buttonPlayerBuy', _("Buy2"), 'onPlayerBuyButton');
//					this.addActionButton('buttonPlayerNotBuy', _("Not Buy2"), 'onPlayerNotBuyButton');
//					this.startActionTimer( 'buttonPlayerNotBuy' );

					this.addActionButton( buyButtonID, _("Buy!"), 'onPlayerBuyButton' );
					this.addActionButton( notBuyButtonID , _("Not Buy!"), 'onPlayerNotBuyButton' );
console.log("[bmc] Action buttons were just created.");
console.log( document.getElementById( notBuyButtonID ));

					if ( this.buyCounterTimerExists != 'Yes' ) {
						this.startActionTimer( notBuyButtonID );
					}
//exit(0);
				} else {
					console.log( "[bmc] Not Showing Buy Buttons. Might consider removing the button here if it's not cleared in other ways." );
				}
//			} else { // Wait for the buy to count out
//console.log("[bmc] notBuyButtonDOM is not null");
//				return;
//			}
console.log("[bmc] EXIT showBuyButton");
		},
////////
////////
////////
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
				
				selectedCards_A_[ player ] = this.downArea_A_[player].getSelectedItems();
console.log(selectedCards_A_[player]);
				
				if ( selectedCards_A_[player].length === 1 ) {
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
			
			if ( handItems.length === 1 )  { // Then try to play the card	

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
				
				
				
				
				var [ boardCard, boardArea, boardPlayer ] = this.getSelectedDownAreaCards ();
				
				let playerCard = handItems[ 0 ];

				if  (( boardCard != {} ) &&
					(  boardArea != '' )) { // There is a card there on the board, so try to have player play
					let playerCard = handItems[ 0 ];
console.log("[bmc] Will try to play card here:");
console.log(playerCard);
console.log(boardCard);
console.log(boardArea);
console.log(boardPlayer);

					var action = 'playCard';

					// do the unselects before going to the server
					this.playerHand.unselectAll();
					for ( var player in this.gamedatas.players ) {
						this.downArea_A_[ player ].unselectAll();
						this.downArea_B_[ player ].unselectAll();
						this.downArea_C_[ player ].unselectAll();
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
		cardWasPlayed : function ( card_id, player_id, color, value, boardArea, boardPlayer, allHands ) {
console.log("[bmc] (from PHP) ENTER cardWasPlayed");
console.log( card_id );
console.log( player_id );
console.log( color );
console.log( value );
//			console.log(boardCard);
console.log( boardArea );
console.log( boardPlayer );
console.log( allHands );
			
			cardUniqueId = this.getCardUniqueId( color, value );
console.log( cardUniqueId );

			// Update card quantities in player hands
			for ( var p_id in allHands ) {
				this.handCount[ p_id ].setValue( allHands[ p_id ] );
			}

			if ( allHands != null ) {
				this.myHandSize.setValue( allHands[ this.player_id ] );
			}

// add joker if there
			
			if ( boardArea === 'playerDown_A' ) {
console.log(boardArea);
				this.downArea_A_[boardPlayer].addToStockWithId(
					cardUniqueId,
					card_id,
					'myhand' );
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
				this.playerHand.removeFromStockById( card_id );
console.log("[bmc] Removed.");
			}
			
			if ( boardArea === 'playerDown_B' ) {
console.log(boardArea);
				this.downArea_B_[boardPlayer].addToStockWithId(
					cardUniqueId,
					card_id,
					'myhand' );
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
				this.playerHand.removeFromStockById( card_id );
console.log("[bmc] Removed.");
			}
			if ( boardArea === 'playerDown_C' ) {
			console.log(boardArea);
				this.downArea_C_[boardPlayer].addToStockWithId(
					cardUniqueId,
					card_id,
					'myhand' );
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
/////////
/////////
/////////
		onDownAreaClick : function() {
			console.log("[bmc] ENTER onDownAreaClick");
			player_id = this.gamedatas.currentPlayerId;
			//player_id = this.gamedatas.playerorder[ 0 ];
			console.log( player_id );
			console.log( this.goneDown[player_id] );
			
			// If the player has not gone down and clicks, pull all the cards back to their hand
			if ( this.goneDown[ player_id ] == 0 ) { //0 = Not gone down; 1 = Gone down.
				console.log("[bmc] PULL BACK");
				
				var cards = this.downArea_A_[ player_id ].getAllItems();
				console.log(cards);

				for ( card of cards ) {
					console.log(card);
					cardUniqueId = card.type;
					this.playerHand.addToStockWithId( cardUniqueId, card.id, 'myhand' );
				}
				this.downArea_A_[ player_id ].removeAllTo( 'myhand' );
				//
				var cards = this.downArea_B_[ player_id ].getAllItems();
				console.log(cards);

				for ( card of cards ) {
					console.log(card);
					cardUniqueId = card.type;
					this.playerHand.addToStockWithId( cardUniqueId, card.id, 'myhand' );
				}
				this.downArea_B_[ player_id ].removeAllTo( 'myhand' );
				//
				var cards = this.downArea_C_[ player_id ].getAllItems();
				console.log(cards);

				for ( card of cards ) {
					console.log(card);
					cardUniqueId = card.type;
					this.playerHand.addToStockWithId( cardUniqueId, card.id, 'myhand' );
				}
				this.downArea_C_[ player_id ].removeAllTo( 'myhand' );
				
				dojo.removeClass('playerDown_A_' + this.player_id, "border1");
				dojo.removeClass('playerDown_B_' + this.player_id, "border1");
				dojo.removeClass('playerDown_C_' + this.player_id, "border1");

				this.prepSetLoc = 0; // Nothing is prepped, so clear the counters
				this.prepRunLoc = 3; 
			} // else do nothing, they've already gone down.
			console.log("[bmc] EXIT onDownAreaClick");
		},
/////////
/////////
/////////
		onDeckSelectionChanged : function() {
			console.log("[bmc] ENTER OnDeckSelectionChanged.");
			var items = this.deck.getSelectedItems();
			//console.log( items[0] );
			console.log("[bmc] GAMEDATAS and this.player_id.");
			console.log(this.gamedatas);
			console.log(this.player_id);
//			this.drawCard2nd(items, 0 ); // 0 == 'deck', 1 == 'discardPile'
			this.drawCard2nd(items, 'deck' );
			console.log("[bmc] EXIT OnDeckSelectionChanged.");
		},
/////////
/////////
/////////
		drawCard2nd : function ( items, drawSource ) {
			console.log("[bmc] ENTER drawCard2nd.");
			console.log( items );
			console.log( drawSource );
			if (items.length > 0) {
				console.log("[bmc] >0; Sending the card.");
				
				var action = 'drawCard';
				if (this.checkAction( action, true )) {
					console.log( "[bmc] Action true. AJAX next" );
					console.log( "/" + this.game_name + "/" + this.game_name + "/" + action + ".html");
					
					var card_id = items[0].id;
console.log(card_id);
					this.ajaxcall( "/" + this.game_name + "/" + this.game_name + "/" + action + ".html", {
						id : card_id,
						drawSource : drawSource,
						player_id : this.player_id,
						lock : true
					}, this, function(result) {
					}, function(is_error) {
					});
				} else {
					console.log("[bmc] Cannot Draw. Action false");
				}
				this.discardPile.unselectAll();
				this.deck.unselectAll();
			} else {
				console.log("[bmc] No items; ignoring click on deck.");
			}
			console.log( "[bmc] EXIT drawCard2nd." );
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
console.log( "[bmc] ENTER clearButtons" );
		    this.removeActionButtons(); // Remove the button because they discarded
			this.showingButtons === 'No';
		},
/////////
/////////
/////////
/*		clearButtonsNotBuy : function () {
console.log( "[bmc] ENTER clearButtonsNotBuy" );
		    this.removeActionButtons(); // Remove the button because they discarded
			this.showingButtons === 'No';
		},
*/
/////////
/////////
/////////		
		onPlayerDiscardButton : function() {
console.log( "[bmc] ENTER onPlayerDiscardButton" );
console.log( this.player_id );
			this.clearButtons();
//		    this.removeActionButtons(); // Remove the button because they discarded
//			this.showingButtons === 'No';
			
			var card = this.playerHand.getSelectedItems()[ 0 ]; // It must be 1 card only
			console.log(card);
			
			this.firstLoad = 'No'; // Since we're discarding, enable future timers

			
			if ( typeof card !== "undefined" ) {
				console.log("[bmc] destroy button!");
				dojo.destroy('currentPlayerPlayButton_id');

				var card_id = card.id;                    

console.log("[bmc] Discarding card!");

				let action = 'discardCard';
				
				this.ajaxcall("/" + this.game_name + "/" + this.game_name + "/" + action + ".html", {
					id : card_id,
					player_id: this.player_id,
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
		onPlayerSortByButtonSet : function() {
			this.playerSortBy = 'Run';
			this.onPlayerSortByButton();
		},
/////////
/////////
/////////
		onPlayerSortByButtonRun : function( thisPlayerHand ) {
			this.playerSortBy = 'Set';
			this.onPlayerSortByButton();
		},
/////////
/////////
/////////
		onPlayerSortByButton : function() {
console.log("[bmc] ENTER onPlayerSortByButton!");
console.log(this.player_id);
			
			var thisPlayerHandIds = this.playerHand.getAllItems();
			
//console.log(thisPlayerHandIds);

			var el = {};
			var thisPlayerHand = new Array();
			
			for ( let i in thisPlayerHandIds ) {
				//console.log(i);
				
				var [ color, value ] = this.getColorValue( thisPlayerHandIds[ i ]['type'] );

				el = {
					'id' : thisPlayerHandIds[i]['id'],
					'unique_id' : this.getCardUniqueId(color, value),
					'type' : color,
					'type_arg' : value,
					'location' : 'hand',
					'location_arg' : this.player_id
				}
				thisPlayerHand[thisPlayerHandIds[i]['id']] = el;
			}
//console.log( "thisPlayerHand:" );
//console.log( thisPlayerHand );
			
			if ( this.playerSortBy == 'Set' ) {
				this.playerSortBy = 'Run';
//				this.onPlayerSortByButtonRun( thisPlayerHand );
				thisPlayerHand.sort( this.compareId ) ;
//console.log( "thisPlayerHand after sort:");
//console.log( thisPlayerHand );

				let weightChange = {};
				for (let i in thisPlayerHand) {
					if ( thisPlayerHand[i].type == 5 ) {
						weightChange[ thisPlayerHand[ i ].unique_id ] = this.drawCounter + 1; // Keep jokers on the right
					} else {
						weightChange[ thisPlayerHand[ i ].unique_id ] = parseInt( thisPlayerHand[ i ].unique_id );
					}
				}
// console.log("weightChange RUN");
// console.log(weightChange);
				this.playerHand.changeItemsWeight(weightChange);
				
			} else {
				this.playerSortBy = 'Set';
//				this.onPlayerSortByButtonSet( thisPlayerHand );
				thisPlayerHand.sort( this.compareTypeArg ) ;

				let weightChange = {};
				for ( let i in thisPlayerHand ) {
					if ( thisPlayerHand[i].type == 5 ) {
						weightChange[ thisPlayerHand[ i ].unique_id ] = 100; // Keep jokers on the right
					} else {
						weightChange[ thisPlayerHand[ i ].unique_id ] = parseInt( thisPlayerHand[ i ].type_arg );
					}
				}
// console.log("weightChange SET");
// console.log(weightChange);
				this.playerHand.changeItemsWeight(weightChange);
			}
			this.showHideButtons();
			
console.log("[bmc] EXIT onPlayerSortByButton!");
		},
/////////
/////////
/////////
		compareId : function( a, b ) {
			if ( parseInt(a.id) < parseInt(b.id) ){
				return -1;
			}
			if ( parseInt(a.id) > parseInt(b.id) ){
				return 1;
			}
			return 0;
		},
/////////
/////////
/////////
		compareTypeArg : function( a, b ) {
			if ( parseInt(a.type_arg) < parseInt(b.type_arg) ){
				return -1;
			}
			if ( parseInt(a.type_arg) > parseInt(b.type_arg) ){
				return 1;
			}
			return 0;
		},
/////////
/////////
/////////
		compareLocationArg : function( b, a ) {
			if ( parseInt(a.location_arg) < parseInt(b.location_arg) ){
				return -1;
			}
			if ( parseInt(a.location_arg) > parseInt(b.location_arg) ){
				return 1;
			}
			return 0;
		},
/////////
/////////
/////////
		onPlayerSortButton : function( items ) {
			console.log("[bmc] BUTTON onPlayerSortButton!");
			console.log(this.player_id);
			
			var cards = this.playerHand.getSelectedItems(); // It can be >1 card
			this.onPlayerSortButton2( cards );
		},
/////////
/////////
/////////
		onPlayerSortButton2 : function( cards ) {
			console.log("[bmc] BUTTON onPlayerSortButton2!");
			console.log(this.player_id);
			
//			var cards = this.playerHand.getSelectedItems(); // It can be >1 card

			console.log( cards );
			
			if ( cards.length === 2 ) { // Sort only when 2 cards are selected
				var cardIds = this.getItemIds( cards );

console.log("[bmc] cardIds: " + cardIds );

				this.clearButtons();
//				this.removeActionButtons(); // Remove the button because they clicked it
//				this.showingButtons === 'No';
				this.sortHand( cards );
			}
		},
/////////
/////////
/////////
		sortHand : function( items ) {
			var thisPlayerHand = this.playerHand.getAllItems();
			console.log("[bmc] thisPlayerHand");
			console.log(thisPlayerHand);

			// Remove PLAY CARD button
			//this.removeActionButtons();
			this.clearButtons();

			// Swap the order if necessary to keep player's 1st selection 1st
			if ( this.playerHand.firstSelected != items[ 0 ].type ) {
				//console.log("[bmc] swap");
				let temp = items[0];
				items[0] = items[1];
				items[1] = temp;
			}
// console.log("[bmc] Move cards around");
		// If two cards have been selected, change the weights
		// Find the indices of the 1st and 2nd cards and move them around

			for ( const [i, card] of thisPlayerHand.entries() ) {
				if ( items[0].type === card.type ) {
					var spotFrom = i;
				}
				if ( items[1].type === card.type ) {
					var spotTo = i;
				}
			}
			
			this.arraymove(thisPlayerHand, spotFrom, spotTo);

			// Make a change array from the result
			let weightChange = {};
			for (let i in thisPlayerHand) {
                weightChange[ thisPlayerHand[ i ].type ] = parseInt(i);
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
			
			if ( this.goneDown[ this.player_id ] == 1 ) { // If player already went down, do nothing
				this.showMessage("You already went down");
				return;
			} else {
				this.clearButtons();

				var cards = this.playerHand.getSelectedItems(); // It can be >1 card
				console.log(cards);
				
				var cardIds = this.getItemIds(cards);

console.log("[bmc] cardIds: " + cardIds);

				// Move them into place on the board
				
//				this.currentHandType = this.gamedatas.currentHandType;
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
console.log("[bmc] ENTER onPlayerGoDownButton!");
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

console.log( boardCard );
console.log( boardArea );
console.log( boardPlayer );
console.log( handItemIds );
			let boardCardId = ( boardCard['id'] === undefined) ? '' : boardCard['id'];
console.log( boardCardId );

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
			
			if ( this.goneDown[ this.player_id ] == 1 ) { // If player already went down, do nothing
				this.showMessage("You already went down");
				return;
			} else {
				this.clearButtons();

				var cards = this.playerHand.getSelectedItems(); // It can be >1 card
				console.log(cards);
			
				var cardIds = this.getItemIds( cards );

console.log("[bmc] cardIds: " + cardIds);

				// Move them into place on the board
				
//				this.currentHandType = this.gamedatas.currentHandType;
console.log(this.currentHandType);

				console.log("[bmc] setsRuns: " + this.setsRuns[this.currentHandType][this.prepRunLoc]);
				console.log(this.prepRunLoc);
				
				if ( this.setsRuns[ this.currentHandType ][ this.prepRunLoc ] == "None" ) {
console.log("[bmc] No more runs needed.");

					//None are needed so don't do anything
				} else {
					// A run is needed, so put it down (as preparation)
					this.putRunDown( cards );
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
        getItemIds: function ( items ) {
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
		action_playerGoDown: function(
			cardGroupA,
			cardGroupB,
			cardGroupC,
			boardCardId,
			boardArea,
			boardPlayer,
			handItems
			) {
			console.log("[bmc] action_playerGoDown");
            this.sendAction('playerGoDown', {
                cardGroupA: this.toNumberList( cardGroupA ),
                cardGroupB: this.toNumberList( cardGroupB ),
                cardGroupC: this.toNumberList( cardGroupC ),
				boardCardId: boardCardId,
				boardArea: boardArea,
				boardPlayer: boardPlayer,
				handItems: this.toNumberList( handItems )
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
        toNumberList: function ( ids ) {
			numberedList = ids.join(';');
console.log("[bmc] numberedList: " + numberedList);
            return numberedList;
        },
/////////
/////////
/////////
        sendAction: function ( action, args ) {
console.log("[bmc] ENTER sendAction: " + action + " : " );
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
console.log("[bmc] EXIT sendAction: " + action + " : " );
        },
/////////
/////////
/////////
		drawCard : function (
			player_id,
			card_id,
			color,
			value,
			drawSource,
			drawPlayer,
			allHands,
			discardSize,
			drawDeckSize
			) {
console.log("[bmc] ENTER drawCard (from notif from PHP)");
console.log(this.player_id);
console.log(player_id);
console.log(card_id);
console.log(color);
console.log(value);
console.log(drawSource);
console.log(drawPlayer);
console.log(allHands);
console.log(discardSize);
console.log(drawDeckSize);

			for ( var p_id in allHands ) {
				this.handCount[ p_id ].setValue( allHands[ p_id ] );
			}

console.log(this.handCount);

			if ( drawSource.match(/playerDown/g) ) {
				var from = drawSource + '_' + drawPlayer;
				var drawingPlayer = player_id;
			} else {
				var from = drawSource;
				var drawingPlayer = drawPlayer;
			}

			this.discardSize.setValue( discardSize );
			this.drawDeckSize.setValue( drawDeckSize );
			this.myHandSize.setValue( allHands[ this.player_id ] );

console.log("[bmc] modified drawSource");
console.log(drawSource);
console.log(from);
console.log(this.playerHand)

			if (( color == null ) ||
				( value == null )) {
console.log("[bmc] Yikes!! Color or value is null! Need to fix this, this is fatal.");
				exit(0);
			}

//			if ( player_id == this.player_id ) {
			if ( drawingPlayer == this.player_id ) {
				console.log("[bmc] player_id is me");
				var addTo = 'myhand';

				let cardUniqueId = this.getCardUniqueId( color, value );
				
				this.playerHand.addToStockWithId( cardUniqueId, card_id ); // Add the card to my hand from the board
				
				let weightChange = {};
				weightChange[ cardUniqueId ] = this.playerHand.items.length + this.drawCounter;
console.log(weightChange);

				this.playerHand.changeItemsWeight( weightChange );

			} else {
				console.log("[bmc] player_id is NOT me");
				var addTo = 'overall_player_board_' + drawPlayer;
			}
				
console.log( '[bmc] addTo: ' + addTo );
				
				if ( drawSource == 'deck' ) {
console.log( '[bmc] Deck' );
					this.deck.removeFromStockById(card_id, addTo );
				}
				if ( drawSource == 'discardPile' ) {
console.log( '[bmc] DP' );
					this.discardPile.removeFromStockById( card_id, addTo );
				}
				if ( drawSource == 'playerDown_A' ) {
console.log( '[bmc] A' );
					this.downArea_A_[ drawPlayer ].removeFromStockById( card_id, addTo );
				}
				if ( drawSource == 'playerDown_B' ) {
console.log( '[bmc] B' );
// EXPERIMENT: Shouldn't remove both, but not sure how to distinquish joker swap while going down
//					this.downArea_B_[ player_id ].removeFromStockById( card_id, addTo );
					this.downArea_B_[ drawPlayer ].removeFromStockById( card_id, addTo );
				}
				if ( drawSource == 'playerDown_C' ) {
console.log( '[bmc] C' );
//						this.downArea_C_[ drawPlayerInContext ].removeFromStockById( card_id );
//					this.downArea_C_[ player_id ].removeFromStockById( card_id, addTo );
					this.downArea_C_[ drawPlayer ].removeFromStockById( card_id, addTo );
				}
		console.log("[bmc] EXIT drawCard");
		},
				
				
				
























/*


		drawCard : function (
			player_id,
			card_id,
			color,
			value,
			drawSource,
			drawPlayer,
			allHands
			) {
console.log("[bmc] ENTER drawCard (from notif from PHP)");
console.log(this.player_id);
console.log(player_id);
console.log(card_id);
console.log(color);
console.log(value);
console.log(drawSource);
console.log(drawPlayer);

			if ( drawSource.match(/playerDown/g) ) {
				var from = drawSource + '_' + drawPlayer;
			} else {
				var from = drawSource;
			}

console.log("[bmc] modified drawSource");
console.log(drawSource);
console.log(from);

			for ( var p_id in allHands ) {
				this.handCount[ p_id ].setValue( allHands[ p_id ] );
			}

			// If drawing a joker, use the player_id.
			// If drawing a card, use the drawPlayer.
			
			if ( color == 5 ) { // If a joker
				var drawPlayerInContext = player_id;
			} else {
				var drawPlayerInContext = drawPlayer;
			}


// EXPERIMENT:Trying to fix joker draw for all players. When should it be player_id????
//			var drawPlayerInContext = drawPlayer;

console.log( drawPlayerInContext );

			if ( drawPlayerInContext != this.player_id ) {
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
				} else if ( drawSource == 'playerDown_A' ) {
					this.downArea_A_[ drawPlayerInContext ].removeFromStockById(
						card_id,
						'overall_player_board_' + player_id
					);
				} else if ( drawSource == 'playerDown_B' ) {
					this.downArea_B_[ drawPlayerInContext ].removeFromStockById(
						card_id,
						'overall_player_board_' + player_id
					);
				} else if ( drawSource == 'playerDown_C' ) {
					this.downArea_C_[ drawPlayerInContext ].removeFromStockById(
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
console.log("[bmc] Single player notify");
					let cardUniqueId = this.getCardUniqueId( color, value );
console.log(cardUniqueId);

//TODO EXPERIMENTING cleaning up the card movements
					let cardInDOM = drawSource + '_' + drawPlayer + '_item_' + card_id;
console.log( cardInDOM );
console.log( $(cardInDOM) );

//					this.playerHand.addToStockWithId( cardUniqueId, $(cardInDOM), from ); // Add the card to my hand from the board
					this.playerHand.addToStockWithId( cardUniqueId, card_id, from ); // Add the card to my hand from the board
					
					let weightChange = {};
					weightChange[ cardUniqueId ] = this.playerHand.items.length + 300; // might be > by 1
console.log(weightChange);

					this.playerHand.changeItemsWeight( weightChange );

					if ( drawSource == 'discardPile' ) {
console.log( '[bmc] DP' );
						this.discardPile.removeFromStockById( card_id );
					}
					if ( drawSource == 'playerDown_A' ) {
console.log( '[bmc] A' );
//						this.downArea_A_[ drawPlayerInContext ].removeFromStockById( card_id );
						this.downArea_A_[ player_id ].removeFromStockById( card_id );
						this.downArea_A_[ drawPlayer ].removeFromStockById( card_id );
					}
					if ( drawSource == 'playerDown_B' ) {
console.log( '[bmc] B' );
// EXPERIMENT: Shouldn't remove both, but not sure how to distinquish joker swap while going down
						this.downArea_B_[ player_id ].removeFromStockById( card_id );
						this.downArea_B_[ drawPlayer ].removeFromStockById( card_id );
					}
					if ( drawSource == 'playerDown_C' ) {
console.log( '[bmc] C' );
//						this.downArea_C_[ drawPlayerInContext ].removeFromStockById( card_id );
						this.downArea_C_[ player_id ].removeFromStockById( card_id );
						this.downArea_C_[ drawPlayer ].removeFromStockById( card_id );
					}
/* EXPERIMENT: This might be the cause of the duplicate card images during draw
					if ( drawSource == 'deck' ) {
						this.deck.removeFromStockById(card_id, 'myhand');
					} else if ( drawSource == 'discardPile' ) {
						this.discardPile.removeFromStockById(card_id, 'myhand');
					} else if ( drawSource == 'playerDown_A' ) {
						this.downArea_A_[drawPlayerInContext].removeFromStockById(card_id, 'myhand');
					} else if ( drawSource == 'playerDown_B' ) {
						this.downArea_B_[drawPlayerInContext].removeFromStockById(card_id, 'myhand');
					} else if ( drawSource == 'playerDown_C' ) {
						this.downArea_C_[drawPlayerInContext].removeFromStockById(card_id, 'myhand');
					}
*/					
/*					
				} else {
					console.log("[bmc] It was a group notify");
				}
			}
		console.log("[bmc] EXIT drawCard");
		},
		
*/
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
/////////
/////////
/////////
		onDiscardPileSelectionChanged: function() {
			var card = this.discardPile.getSelectedItems()[0]; // Only ajax if a card was selected
console.log( "[bmc] ENTER onDiscardPileSelectionChanged." );
console.log( "[bmc] GAMEDATAS and this.player_id" );
//console.log(card);
console.log( this.gamedatas );
console.log( this.player_id );

			var items = new Array();
			items.push(card);
//			this.drawCard2nd( items, 1 ); // 0 == 'deck', 1 == 'discardPile'
			this.drawCard2nd( items, 'discardPile' );
		},
/////////
/////////
/////////
		putSetDown: function ( cards ) {
			console.log('[bmc] putSetDown');
			console.log(cards);
			
			for ( card of cards ) {
				console.log("[bmc] card");
				console.log(card);

				cardUniqueId = card.type;
				cardId = card.id;
				
				console.log(cardUniqueId);
				console.log(cardId);
				console.log(this.player_id);
				
				var from = 'myhand_item_' + card.id;
				
//				this.currentHandType = this.gamedatas.currentHandType;

				if ( this.setsRuns[ this.currentHandType ][ this.prepSetLoc ] == "Area_A" ) {
					 this.downArea_A_[ this.player_id ].addToStockWithId(cardUniqueId, cardId, 'myhand');
					 console.log("[bmc] SLIDING!!!!!!");
					 dojo.addClass('playerDown_A_' + this.player_id, "border1");
				}
				if ( this.setsRuns[ this.currentHandType ][ this.prepSetLoc ] == "Area_B" ) {
					 this.downArea_B_[ this.player_id ].addToStockWithId(cardUniqueId, cardId, 'myhand');
					 dojo.addClass('playerDown_B_' + this.player_id, "border1");
				}
				if ( this.setsRuns[ this.currentHandType ][ this.prepSetLoc ] == "Area_C" ) {
					 this.downArea_C_[ this.player_id ].addToStockWithId(cardUniqueId, cardId, 'myhand');
					 dojo.addClass('playerDown_C_' + this.player_id, "border1");
				}
				
				this.cardDisplayClass = "downPrep";
				
				console.log(from);

				this.playerHand.removeFromStockById (card.id );
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
				
//				this.currentHandType = this.gamedatas.currentHandType;

				if ( this.setsRuns[ this.currentHandType ][ this.prepRunLoc ] == "Area_A" ) {
					 this.downArea_A_[ this.player_id ].addToStockWithId(cardUniqueId, cardId, 'myhand');
					 dojo.addClass('playerDown_A_' + this.player_id, "border1");
				}
				if ( this.setsRuns[ this.currentHandType ][ this.prepRunLoc ] == "Area_B" ) {
					 this.downArea_B_[ this.player_id ].addToStockWithId(cardUniqueId, cardId, 'myhand');
					 dojo.addClass('playerDown_B_' + this.player_id, "border1");
				}
				if ( this.setsRuns[ this.currentHandType ][ this.prepRunLoc ] == "Area_C" ) {
					 this.downArea_C_[ this.player_id ].addToStockWithId(cardUniqueId, cardId, 'myhand');
					 dojo.addClass('playerDown_C_' + this.player_id, "border1");
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
/*				this.play1card.removeFromStock(1);
*/

				
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
//				this.slideToObject(from, 'playerDown_B_' + player_id, 1000).play();
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
			let buyButtonID = 'buttonPlayerBuy' + this.player_id;
			let notBuyButtonID = 'buttonPlayerNotBuy' + this.player_id;
console.log( "[bmc] BUTTONIDs:" );
console.log( buyButtonID );
console.log( notBuyButtonID );

			// Only show the buy buttons if they already don't exist
			
//			let notBuyButtonDOM = document.getElementById('buttonPlayerNotBuy');
			let notBuyButtonDOM = document.getElementById( notBuyButtonID );
console.log("[bmc] notBuyButtonDOM: ", notBuyButtonDOM);
			
			if ( notBuyButtonDOM == null ) { // == null or undefined
console.log("[bmc] Button Not Null");
				this.clearButtons();
			} else { // Wait for the buy to count out
console.log("[bmc] WAIT");
				return;
			}
			
			var showButtons = new Array();
			
console.log("this.playerSortBy");
console.log(this.playerSortBy);
			
			
			
//TODO: Remove these buttons and hook up the dedicated ones.

//TODO: The CSS changing the widths doesn't move the discardpile right, not sure why.

			// if ( this.playerSortBy == 'Run' ) {
				// this.addActionButton( 'buttonPlayerSortBy', _("Sort By Sets"), 'onPlayerSortByButton');
			// } else {
				// this.addActionButton( 'buttonPlayerSortBy', _("Sort By Runs"), 'onPlayerSortByButton');
			// }
console.log( "[bmc] Player:" );
console.log( this.player_id );
console.log( this.gamedatas.gamestate.active_player );
console.log( this.gamedatas.activeTurnPlayer_id );

			if ( this.gamedatas.gamestate.active_player == this.player_id ) {
				showButtons['myturn'] = true;
				console.log("[bmc] playerOrderTrue[0] == this.player_id (my turn)");
				
			} else {
				console.log("[bmc] not playerOrderTrue[0] == this.player_id (not my turn)");
			}

			console.log(this.player_id);
			console.log(this.goneDown[this.player_id]);

			showButtons['goneDown'] = (parseInt( this.goneDown[ this.player_id ]) === 1 ) ? true : false;
			
			// Show SORT button if a player has a card selected
			var items = this.playerHand.getSelectedItems();
			
			if ( items.length >= 1 ) {
				showButtons['handSelected'] = true;
				if ( items.length > 2 ) {
					showButtons['twoOrMore'] = true;
				}
				if ( items.length == 2 ) {
					showButtons['twoOrMore'] = true;
					this.addActionButton( 'buttonPlayerSort', _("Sort!"), 'onPlayerSortButton');
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
				this.addActionButton('buttonPlayerGoDown', _("Go Down!"), 'onPlayerGoDownButton');
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
				
//				this.currentHandType = this.gamedatas.currentHandType;

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
				
				if (( setNeeded ) &&
					( items.length >= 2 ) && // 1 joker allowed during godown
					( items.length <= 3 )) {  // A set is 3 cards
					this.addActionButton('buttonPlayerPlaySet', _("Prep Set!"), 'onPlayerPlaySetButton');
					this.showingButtons === 'Yes';
				}
				if (( runNeeded ) &&
					( items.length >= 3 ) && // 1 joker allowed during godown
					( items.length <= 4 )) {  // A run is 4 cards
					this.addActionButton('buttonPlayerPlayRun', _("Prep Run!"), 'onPlayerPlayRunButton');
					this.showingButtons === 'Yes';
				}
			}				  
			//
			// Show DISCARD if card selected, it's not state playerTurnDraw, and it's my turn
			//
			if (  showButtons['handSelected'] && 
				  showButtons['myturn'] &&
				 !showButtons['twoOrMore'] &&
			   (  this.gamedatas.gamestate.name != "playerTurnDraw" )) {
				   
				this.addActionButton('buttonPlayerDiscard', _("Discard!"), 'onPlayerDiscardButton');
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
            var cards = this.playerHand.getSelectedItems();
console.log( cards );
			if ( cards ) {
//				this.onPlayerDiscardButton();
				this.onPlayerSortButton2( cards );
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
console.log( items);

console.log( "[bmc] gamedatas:" );
console.log( this.gamedatas );
console.log( "[bmc] Players and this hand:" );
console.log( this.gamedatas.playerOrderTrue[ 0 ] ) ;
console.log( this.player_id );
console.log( items.length );

			if ( items.length === 1 ) {
//console.log("[bmc] Store the first");
				this.playerHand.firstSelected = items[ 0 ].type;
			}

			this.showHideButtons();			
			console.log("[bmc] EXIT onPlayerHandSelectionChanged");
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
console.log( '[bmc] ENTER notifications subscriptions setup' );
            
            dojo.subscribe( 'newHand'  ,         this, "notif_newHand");
			this.notifqueue.setSynchronous( 'newHand', 1000 );
            dojo.subscribe( 'discardCard' ,      this, "notif_discardCard");
            dojo.subscribe( 'drawCard' ,         this, "notif_drawCard");
            dojo.subscribe( 'newScores',         this, "notif_newScores" );
			dojo.subscribe( 'playerGoDown' ,     this, "notif_playerGoDown");
            dojo.subscribe( 'cardPlayed' ,       this, "notif_cardPlayed");
            dojo.subscribe( 'deckShuffled' ,     this, "notif_deckShuffled");
            dojo.subscribe( 'playerNotBuying' ,  this, "notif_playerNotBuying");
            dojo.subscribe( 'playerWantsToBuy' , this, "notif_playerWantsToBuy");
            dojo.subscribe( 'playerBought' ,     this, "notif_playerBought");
            dojo.subscribe( 'playerDidNotBuy' ,  this, "notif_playerDidNotBuy");
			dojo.subscribe( 'wentOut' , 		 this, "notif_playerWentOut");
//            dojo.subscribe( 'playerDidNotBuy' ,  this, "notif_NextPlayer");

console.log( "[bmc] EXIT notifications subscriptions setup" );

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
            for ( var player_id in notif.args.newScores ) {
                this.scoreCtrl[ player_id ].toValue( notif.args.newScores[ player_id ]);
            }
			
        },
/////////
/////////
/////////
		showReviewButton : function( player_id) {
			var reviewButtonID = 'buttonReview' + this.player_id;

			if ( player_id == this.player_id ) {
				this.addActionButton( reviewButtonID, _("AWESOME! WOOT! On To The Next!"), 'onPlayerReviewedHandButton' );
			} else {
				this.addActionButton( reviewButtonID, _("Bummer! on to the next."), 'onPlayerReviewedHandButton' );
			}
		},
/////////
/////////
/////////
		notif_playerWentOut : function( notif ) {
			console.log("[bmc] ENTER notif_playerWentOut")
			console.log( notif );
			
			// If someone went out, remove the BUY buttons, kill the timers and let them review.
			this.stopActionTimer2();
			this.clearButtons();
			
			// If someone clicked their button 'On To The Next' just ignore it
			// and replace the button. The state machine will continue after ALL have clicked.
			// if ( notif.type == 'wentOut' ) {
				// if (( notif.args.ackPlayer == this.player_id ) &&
				    // ( this.handReviewed == 'No' )) {
					
					// this.handReviewed = 'Yes';
				// }
				// return;
			// }

			this.showReviewButton( notif.args.player_id );
		},
/////////
/////////
/////////
        notif_newHand : function(notif) {
console.log("[bmc] ENTER notif_newHand");
console.log(notif);
			
			// At the start of each hand give everyone time to see the first discard
			// And clear the knowledge that they've reviewed the past hand.
			this.firstLoad = 'Yes';
			this.handReviewed = 'No';
			
            // We received a new full hand of cards. Clear the table.
            this.playerHand.removeAll();
			this.discardPile.removeAll();
			this.deck.removeAll();
			
			for (var player in this.gamedatas.players) {
				this.downArea_A_[ player ].removeAll();
				this.downArea_B_[ player ].removeAll();
				this.downArea_C_[ player ].removeAll();

				this.goneDown[ player ] = 0;
			}
			
			this.prepSetLoc = 0; // Nothing is prepped, so clear the counters
			this.prepRunLoc = 3; 
			
			// Set up the new hand for the player
            for ( let i in notif.args.hand) {
                let card = notif.args.hand[i];
                let color = card.type;
                let value = card.type_arg;
                this.playerHand.addToStockWithId( this.getCardUniqueId( color, value ), card.id );
			}

			if ( notif.args.allHands != null ) {
				this.myHandSize.setValue( notif.args.allHands[ this.player_id ] );
			}
console.log("[bmc] this.playerHand");			
console.log(this.playerHand);

			this.buyCount[ this.player_id ].setValue( this.gamedatas.buyCount[ this.player_id ] );

			// Set up the discard pile
			var discardPileWeights = new Array();

            for ( let i in notif.args.discardPile ) {
				console.log("[bmc] NEW DISCARD PILE");
                let card = notif.args.discardPile[i];
                let color = card.type;
                let value = card.type_arg;
                this.discardPile.addToStockWithId( this.getCardUniqueId( color, value ), card.id );
				let location_arg = parseInt( notif.args.discardPile[ i ][ 'location_arg' ]);
				discardPileWeights[ this.getCardUniqueId( color, value )] = location_arg;
			}
			// Set the weights in the discard pile
			this.discardPile.changeItemsWeight(discardPileWeights);
console.log("[bmc] this.discardPile");			
console.log(this.discardPile);

			// Set to show the count of cards in the discard pile
			this.discardSize.setValue( this.discardPile.items.length );

			// Set up the draw deck
			for ( let i = 0 ; i < notif.args.deck.length; i++ ) {
				this.deck.addToStockWithId( 1, notif.args.deck[i] );
			}
console.log("[bmc] this.deck");			
console.log(this.deck);

			// Set all players to buy, except for the player whose turn it is
			if ( this.player_id != notif.args.dealer ) {
				this.buyCounterTimerShouldExist = 'Yes'; // A timer and a button should exit
			}

			// Set the hand counts for all players
			for ( var p_id in notif.args.allHands ) {
				this.handCount[ p_id ].setValue( notif.args.allHands[ p_id ] );
			}
			
			this.buyTimeInSeconds = 40;

			console.log("[bmc] EXIT notif_newHand");
        },
/////////
/////////
/////////
		notif_deckShuffled : function(notif) {
			// Set up the draw deck
			console.log("[bmc] Shuffle Cards:");
			console.log(notif);
			
			for ( let i = 0 ; i < notif.args.deck.length; i++ ) {
				this.deck.addToStockWithId( 1, notif.args.deck[i] );
			}
			this.discardPile.removeAll();
console.log("[bmc] Shuffled New Deck:");			
console.log(this.deck);
		},
		
		notif_cardPlayed : function( notif ) {
console.log("[bmc]notif_cardPlayed");
            this.cardWasPlayed(
				notif.args.card_id,
				notif.args.player_id,
				notif.args.color,
				notif.args.value,
				notif.args.boardArea,
				notif.args.boardPlayer,
				notif.args.allHands
			);
console.log("[bmc] notif_cardPlayed Done.");
		},
/////////
/////////
/////////
        notif_discardCard : function( notif ) {
console.log("[bmc] ENTER notif_discardCard");
console.log( this.player_id );
console.log( notif );
			this.discardCard(
				notif.args.player_id,
				notif.args.color,
				notif.args.value,
				notif.args.card_id,
				notif.args.nextTurnPlayer,
				notif.args.allHands,
				notif.args.discardSize
			);
console.log("[bmc] EXIT notif_discardCard");
		},
/////////
/////////
/////////
        notif_drawCard : function( notif ) {
console.log("[bmc] ENTER notif_drawcard");
console.log( notif );

			this.buyTimeInSeconds = this.buyTimeInSecondsDefault;

			// If we drew or someone else drew the discard, then stop any timers.
			if (( this.gamedatas.gamestate.active_player == notif.player_id ) ||
				( notif.args.drawSource == 'discardPile' )) {
				this.clearButtons();
				this.stopActionTimer2();
			}
			
			// Steadily increment every time a card is drawn to set the weight properly
			this.drawCounter++;
			
            // Draw a card from the deck, discard pile or the board (i.e. joker replace)
            this.drawCard(
				notif.args.player_id,
				notif.args.card_id,
				notif.args.color,
				notif.args.value,
				notif.args.drawSource,
				notif.args.drawPlayer,
				notif.args.allHands,
				notif.args.discardSize,
				notif.args.drawDeckSize
			);
console.log("[bmc] EXIT notif_drawcard");
        },
/////////
/////////
/////////
		notif_playerNotBuying : function(notif) {
			console.log("[bmc]notif_playerIsNotBuying");
			console.log(notif);
//			if ( this.gamedatas.playerOrderTrue[ 0 ] == this.player_id ) {
//			if ( this.gamedatas.activeTurnPlayer_id == this.player_id ) {
			if ( this.gamedatas.gamestate.active_player == notif.player_id ) {
				this.stopActionTimer2();
			}
			this.showHideButtons();
		},
/////////
/////////
/////////
		notif_playerBought : function(notif) {
			console.log("[bmc]notif_playerBought");
			console.log(notif);
			this.buyCount[  notif.args.player_buying ].setValue( notif.args.buyCount[ notif.args.player_buying ] );
			this.handCount[ notif.args.player_buying ].setValue( notif.args.allHands[ notif.args.player_buying ] );
		},
/////////
/////////
/////////
		notif_playerDidNotBuy : function(notif) {
			console.log("[bmc]notif_playerDidNotBuy");
			console.log(notif);
		},
/////////
/////////
/////////
		notif_playerWantsToBuy : function(notif) {
			console.log("[bmc]notif_playerWantsToBuy");
			console.log(notif);
			this.stopActionTimer2();
			this.showHideButtons();
		},
/////////
/////////
/////////
		notif_playerGoDown: function( notif ) {
			console.log('ENTER notif_playerGoDown. Solidify the card positions.');
			console.log( notif );
			console.log( this.gamedatas.currentPlayerId );
			console.log( this.gamedatas.playerOrderTrue );

			// Update card-counts when someone goes down:

			for ( var p_id in notif.args.allHands ) {
				this.handCount[ p_id ].setValue( notif.args.allHands[ p_id ] );
			}

			if (notif.args.allHands != null ) {
				this.myHandSize.setValue( notif.args.allHands[ this.player_id ] );
			}
			
			this.goneDown[ notif.args.player_id ] = 1; //0 = Not gone down; 1 = Gone down.
			
			downPlayer = notif.args.player_id;
			downArea = notif.args.player_down;
			card_ids = notif.args.card_ids;
			card_type = notif.args.card_type;
			card_type_arg = notif.args.card_type_arg;
			console.log( card_ids );

			joker = notif.args.joker;
			console.log("[bmc] joker:");
			console.log(joker);

			// Remove the borders around the prep area
			dojo.removeClass('playerDown_A_' + this.player_id, "border1");
			dojo.removeClass('playerDown_B_' + this.player_id, "border1");
			dojo.removeClass('playerDown_C_' + this.player_id, "border1");

			
			if ( this.gamedatas.gamestate.active_player == this.player_id ) {
				console.log("[bmc] I went down!");
				
				this.goneDown[ this.player_id ] = 1;
				
				// Move the joker, if any, to the down position, everything else is already in place because of the prep
				
				if (joker != undefined ) { // Per JS must check undefined before checking for a property of the variable
					if ( joker.id != 'None' ) { // Then there's a joker; Move it
				
						jokerUniqueID = this.getCardUniqueId( joker.type, joker.type_arg );
						
						targetArea = notif.args.targetArea;
						
						if ( targetArea === 'playerDown_A' ) {
							console.log("[bmc] Adding Joker to AREA A");
							this.downArea_A_[ downPlayer ].addToStockWithId( jokerUniqueID, joker.id, 'myhand' );
						}
						if ( targetArea === 'playerDown_B' ) {
							console.log("[bmc] Adding Joker to AREA B");
							this.downArea_B_[ downPlayer ].addToStockWithId( jokerUniqueID, joker.id, 'myhand' );
						}
						if ( targetArea === 'playerDown_C' ) {
							console.log("[bmc] Adding Joker to AREA C");
							this.downArea_C_[ downPlayer ].addToStockWithId( jokerUniqueID, joker.id, 'myhand' );
						}
						this.playerHand.removeFromStockById(joker.id);

					}
				}
				this.showHideButtons();
//				return;
				
			} else {
				console.log("[bmc] Someone else went down!");
				for ( card_id in card_ids ) {
					console.log(card_id);
//					color = card_ids[card_id]['type'];
//					value = card_ids[card_id]['type_arg'];
					color = card_type[ card_id ];
					value = card_type_arg[ card_id ];

					console.log(color);
					console.log(value);
					
					cardUniqueId = this.getCardUniqueId( color, value );
					console.log(cardUniqueId);
					
					if ( downArea === 'playerDown_A_' ) {
						console.log("[bmc] Adding to AREA A");
// EXPERIMENT 10/20/2020 7:09pm Not sure how this could be wrong but it seems wrong. Changing it
//						this.downArea_A_[ downPlayer ].addToStockWithId( cardUniqueId, card_id, 'overall_player_board_' + downPlayer );
						this.downArea_A_[ downPlayer ].addToStockWithId( cardUniqueId, card_ids[ card_id ], 'overall_player_board_' + downPlayer );
						
						dojo.removeClass('playerDown_A_' + this.player_id, "border1");

					}
					if ( downArea === 'playerDown_B_' ) {
						console.log("[bmc] Adding to AREA B");
//						this.downArea_B_[ downPlayer ].addToStockWithId( cardUniqueId, card_id, 'overall_player_board_' + downPlayer );
						this.downArea_B_[ downPlayer ].addToStockWithId( cardUniqueId, card_ids[ card_id ], 'overall_player_board_' + downPlayer );
						dojo.removeClass('playerDown_B_' + this.player_id, "border1");
					}
					if ( downArea === 'playerDown_C_' ) {
						console.log("[bmc] Adding to AREA C");
//						this.downArea_C_[ downPlayer ].addToStockWithId( cardUniqueId, card_id, 'overall_player_board_' + downPlayer );
						this.downArea_C_[ downPlayer ].addToStockWithId( cardUniqueId, card_ids[ card_id ], 'overall_player_board_' + downPlayer );
						dojo.removeClass('playerDown_C_' + this.player_id, "border1");						
					}
/*
slideToObject
function( mobile_obj, target_obj, duration, delay )
Return an dojo.fx animation that is sliding a DOM object from its current position over another one
Animate a slide of the DOM object referred to by domNodeToSlide from its current position to the xpos, ypos relative to the object referred to by domNodeToSlideTo.
*/

//					this.slideToObject('overall_player_board_' + downPlayer, downArea + downPlayer, 1000).play();
// EXPERIMENT THIS TOO!					this.playerHand.removeFromStockById( card_id );
					this.playerHand.removeFromStockById( card_ids[ card_id ]);
				}
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

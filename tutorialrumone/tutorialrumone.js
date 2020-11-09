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
			this.drawCounter = 400; // Start with a number bigger than the # of cards
			this.buyTimeInSecondsDefault = 10;
			this.buyTimeInSeconds = this.buyTimeInSecondsDefault;
			this.prepAreas = 0; // No card are prepped on the board upon refresh
			// New variables for new timers on static buttons
			this.enableDBStatic = 'Yes'; // (except the player whose turn it is
			this.enableDBTimer = 'No';
			this.playedSoundWentOut = false;
			this.playedSoundWentDown = false;
			this.actionTimerLabelDefault = "Don't Buy";
			this.dealMeInClicked = false;

			this.setsRuns = [ // Places in the downArea where the cards should go, per hand (set, set, set, run, run, run)
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
//
// 11/8: Only notify of buy requests, not not-buys.
// 11/8: Make 6 across, and put prep area to the right of the discard pile.
// 11/8: JS code between 244 and 340 takes ~12 seconds (slow!)
// 11/7: Put BUYING and BUYTIMER in different tables so there is no deadlock. (PHP line 901)
// 11/7: Make the DRAW DECK be a single card, not covering most of the table.
// 11/7: Make the text of gray buttons also gray.
// 11/7: Remove "play" from the options if you cannot play.
// 11/7: Clear prepped coloring after the hand ends
// 11/7: Put everyone's prep area just below the DECK.
// 11/7: Review buttons reappear even though they were clicked
// 11/7: Konni had A890jqk but ace is still on the left
// 11/6: 2Runs: Going down with 568 spades and 456* hearts and ace of spaces swapping a joker. It did the swap but somehow the jokers have the same IDs! I think one of the functions took the wrong joker (in PHP).
// 11/7: 3 people are light blue
// 11/7: Make 2 side-by-side down areas (Spectator and Gary and Kristi cannot see whole board)
// 11/7: Dad chose the 7s but 2c was discarded
// 11/7: Some people cannot see coral BUY IT coloring.
// 11/7: Add a table with the players in an oval.
// 11/2: Ask group: Call Liverpool on another player?
// 10/29: Board Player names don't show up after new hand is dealt.
// 11/5: Cannot go down with 2356s and replacing a joker (can do it with 235s).
// 11/1: [forum] If 2 of same card (e.g. 2x 6 of hearts) is in hand cannot move just one of them
// 11/7: Limit the set size???
//
// X 11/1: One player has BUY buttons shown but in fact cannot buy, but should be allowed (#0).
// X 11/5: Buy buttons don't appear after new hand but should (but buy works).
// X 11/1: Add game option of buyer precedence or buyer click-speed.
// X 11/7: Game option for no timers. 10 seconds is too quick to. Allow buy up to the next player discard.
// X 11/7: Played card moves from my hand instead of the player board.
// X 11/7: Put the GO DOWN button next to Prep ABC buttons.
// X 11/7: Add confirmation screen after player prepped cards and hit DISCARD.
// X 11/7: Shuffling deck threw JS error (2385).
// X 11/5: After the hand, the SO FAR table shows only the current values, not the totals.
// X 11/5: 7810** both jokers went to the 9 spot
// X 11/5: Game ended after 2 runs and should have kept going
// X 11/5: After someone goes out, show a page with points per player
// X 11/5: 5 player table isn't big enough vertically.
// X 11/5: "It's your turn!" should not be shown after BUY REQUEST.
// X 11/5: Hard to know who wants to buy it. Make it more prominent, like a pop-up. Or color the board.
// X 11/5: Make it so that if > 1 card is selected and discard pile is selected it will discad but should not
// X 10/28: Change # of cards dealt each hand? and the rules for 3 runs???
// X 10/24: When a buyer exists, update the action bar to show everyone "player wants to buy."
// X 11/1: Runs on board, jokers always go to right when they sometimes should be elsewhere.
// X 11/1: Runs on board, Aces always go to left but sometimes should go to right.
// X 11/2: [B] Firefox doesn't show dollar sign and card images in the player board areas. Change to use only PNG.
// X 11/1: [B] Spectators should not see the buttons and the hand area. They should see TARGET.
// X 11/1: [B] Spectators should not see HAND and buttons.
// X 10/30: The GODOWN sound doesn't play, but you can hear the cards move.
// X 10/30: After a hand is over, draw deck shows 50 when it should be 66.
// X 10/28: MAY BE OK (because stuff was prepped): GO DOWN button appears when no cards are selected in hand, should not.
// X 10/29: ASK GROUP: Change allowed run to require sequential values. Now 5668 is an OK run.
// X 10/29: Play a 4 on a set of 4s in AREA A got RED RUN CARDS MUST ALL BE SAME SUIT. But tried it again and it worked!
// X 11/2: After the deal of 1Set 1 Run, all players show the same cards (dodyoaks1, not dealer)! F5 refresh clears.
// X 10/29: Change pictures to show game board.
// X 10/29: Add How To Play
// X 10/28: Keep the highlighting on after the turn moves around. Now it turns off, not sure why.
// X 10/29: At REVIEW time, not all the blue buttnos appear
// X 10/24: Prep border lit up before anyone went down!
// X 10/24: Something happened where player 2 tried to draw, but it didn't register, and still had only 1 card, then discarded.
// X 10/28: Sometimes after discard, clicking discard or somewhere quickly the player's draw is not registered and it goes to PLAY. Seems like if I click DRAW before the DISCARD completes then there is an issue (timing).
// X 10/29: move the sound to a later function, since it says "YEAH" too many times.
// X 9/28 When you go down, it should play a sound "Yes!" or something.
// X 9/28 After player has gone down, when they click their hand do not show PREP buttons. (discard and sort)
// X 10/28: Add options for fewer hands
// X 10/24: After a new deal, wait until click, or allow 45 seconds (?) to figure out if want to buy.
// X 10/26: Target on the board doesn't update when the hand goes to next hand and should.
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
// X 10/18 The jokers don't always move to right places. Sometimes show in 2 places: where they were and where really went.
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
// X 10/7: Each of the windows is using/seeing the same value for this.actionTimerId. Need to figure out how to separate them.
// X 10/7:  TODO: TRACE THROUGH THE TIMER CODE AND FIGUER OUT WHY IT'S STOPPING. ALSO SOME IDS ARE SAME SOME NOT.
// X 10/10: Change comparison player-whose-turn-it-is to be this.gamedatas.gamestate.active_player, throughout file.
// X 10/18: Add button for onPlayerReviewedHandButton
// X    and the functions wentout and notif.
// X 10/1 When prepping to go down, change the color back to 'normal' if it was in 'prep' color.
// X 10/20: Player 3 went down with a joker and the playerboards show 4 card when player really has 3. F5 didn't fix it.
// X 9/28 Player was allowed to pull an old card from the discard pile. Leave AS-IS to reduce testing time.
// X 10/20 Consider adding a database access to PHP (playerHasReviewedHand) to track when players hit the button ON TO THE NEXT.
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
// X 10/24: Player buy count didn't update after buy (2 runs)
// X 10/24: Hand count doesn't show accurately after buys.
// X 10/24: You cannot buy any more this hand shown to wrong player.
// X 10/24: Drawing player IS ABLE to draw other cards from teh discard pile and should not be allowed to.
// X 10/24: In this.playerhand, somehow a null card item (id: null, type: -14) got into ITEMS under 'modified drawsource'.
// X 10/26: The player names on the board disappeared! get them back.
// X 10/26: Still the names don't show up. VERIFY THE unplayed and played values stuff.
// X 10/28: Let me put >1 card onto the board at the same time as a PLAY
// X 10/28: Click next to hand to clear selections
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
            // 13 images per row in the sprite file
			this.playerHand.image_items_per_row = 13;
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
            //this.deck.create( this, $('deck'), this.cardwidth, this.cardheight );            

			this.deck.image_items_per_row = 13;
			this.deck.setOverlap( 0.1, 0 );
//			this.deck.setOverlap( 100, 0 );
			this.item_margin = 0;

			// Item 54, color 5, value 3 is red back of the card
			this.deck.addItemType( 1, 1, g_gamethemeurl + 'img/4ColorCardsx5.png', 54);
//console.log("[bmc] deckIDs");
//console.log(this.gamedatas.deckIDs);

//			if ( this.gamedatas.deckIDs.length != 0 ) {

			for ( let i = 0 ; i < this.gamedatas.deckIDs.length; i++) {

//console.log(i + " / " + this.gamedatas.deckIDs[i]);
				this.deck.addToStockWithId(1, this.gamedatas.deckIDs[i]);
			}
//console.log("[bmc] this.deck");			
//console.log(this.deck);
			
//EXP Start 11/8			
			// Create a single card to represent the card back
			this.deckOne = new ebg.stock(); // New stock for the draw pile (the rest of the deck)
            this.deckOne.create( this, $('deck'), this.cardwidth, this.cardheight );            
			this.deckOne.image_items_per_row = 13;

			// Item 54, color 5, value 3 is red back of the card
			this.deckOne.addItemType( 1, 1, g_gamethemeurl + 'img/4ColorCardsx5.png', 54);
			this.deckOne.addToStockWithId(1, this.gamedatas.deckIDs[i]);
//EXP End 11/8

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
            this.discardPile.setOverlap( 0.1 , 0 );

			this.discardPile.item_margin = 0; 
		
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
				
//console.log("DRAWING NAMES");
//console.log($(playerText_DodyOaks0));
				// Add playername text to down areas
//				$(playerText_DodyOaks0).innerHTML = "alice";//{PLAYER_NAME};
//console.log($(playerText_DodyOaks0));
//console.log("player");
//console.log("{player}");
//console.log({player});

//console.log( $("playerDown_A1_"+ player) );

			$("playerDown_A_"+ player).innerHTML = this.gamedatas.players[ player ][ 'name' ];
			$("playerDown_B_"+ player).innerHTML = this.gamedatas.players[ player ][ 'name' ];
			$("playerDown_C_"+ player).innerHTML = this.gamedatas.players[ player ][ 'name' ];

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
//            dojo.connect( this.deck,        'onChangeSelection', this, 'onDeckSelectionChanged' );
            dojo.connect( this.deckOne,        'onChangeSelection', this, 'onDeckSelectionChanged' );
            dojo.connect( this.discardPile, 'onChangeSelection', this, 'onDiscardPileSelectionChanged' );
			dojo.connect( $('discardPile' ), 'onclick', this, 'onDiscardPileSelectionChanged');
			dojo.connect( $('myhand' ), 'onclick', this, 'onMyHandAreaClick');

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

			dojo.connect( $('buttonPrepAreaA'), 'onclick', this, 'onPlayerPrepArea_A_Button' );
			dojo.connect( $('buttonPrepAreaB'), 'onclick', this, 'onPlayerPrepArea_B_Button' );
			dojo.connect( $('buttonPrepAreaC'), 'onclick', this, 'onPlayerPrepArea_C_Button' );
			dojo.connect( $('buttonGoDownStatic'), 'onclick', this, 'onPlayerGoDownButton' );

			dojo.connect( $('buttonBuy'), 'onclick', this, 'onPlayerBuyButton' );
			dojo.connect( $('buttonNotBuy'), 'onclick', this, 'onPlayerNotBuyButton' );

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

			this.buyTimeInSecondsDefault = this.gamedatas.options.buyTimeInSeconds;
			console.log( this.buyTimeInSecondsDefault );
			
console.log("[bmc] Buy setup");
console.log(this.firstLoad);
console.log(this.player_id);
// TODO: Remove this?
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

			if ((( this.firstLoad == 'Yes' ) && 
			     ( this.player_id != this.gamedatas.activeTurnPlayer_id ) && // It's not our turn
			     ( this.gamedatas.buyers[ this.player_id ] == 0 ) && // buy status undefined
				 //( this.player_id != this.gamedatas.playerOrderTrue[ this.gamedatas.activeTurnPlayer_id ] ) && // the next player
				 ( this.gamedatas.gamestate.name == 'playerTurnDraw' ))) { // draw state
				
console.log( "[bmc] Showing buttons to those who haven't registered buy." );
			// New variables for new timers on static buttons
				this.enableDBStatic = 'Yes';
				this.enableDBTimer = 'No'; // But let the timer run out if it's there
				this.enDisStaticBuyButtons();
			} else {
				this.enableDBStatic = 'No';
				this.enableDBTimer = 'No'; // But let the timer run out if it's there
				this.enDisStaticBuyButtons();
			}

			$(redTarget).innerHTML = this.gamedatas.handTarget;
			console.log( $(redTarget) );
			
			// Highlight the potential buyer, if any
			for ( let player_id in this.gamedatas.buyers ) {
				console.log(player_id);
				if ( this.gamedatas.buyers[ player_id ] == 2 ) {
					dojo.addClass( 'overall_player_board_' + player_id, 'pbInverse' );
				}
			}
			
//			let gda = this.gamedatas.playerorder.length * 114;
			let gda = ( Object.keys( this.gamedatas.playerOrderTrue ).length - 1 ) * 120;
            console.log( "height: " + gda + "px;" );
			dojo.setStyle( 'goDownArea_wrap', "height: " + gda + "px;" );

			// Move the board jokers, if any, to appropriate places
			this.sortBoard();
			
            console.log( "[bmc] EXIT game setup" );
        },
/////////
/////////
////////////////////////////////////////////////////////////
///////////// Game & client states
        // onEnteringState: this method is called each time we are entering into a new game state.
        //                  You can use this method to perform some user interface changes at this moment.
        //
        onEnteringState: function( stateName, args ) {
            console.log( 'ENTER ENTERING state: ' + stateName );
			console.log( args );
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
					this.playedSoundWentOut = false; // Reset to play the sound only once
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
/////////
/////////
/////////
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
				if ( this.playedSoundWentOut == false ) {
					this.playedSoundWentOut = true;
					playSound('tutorialrumone_wentOutYeah');
				}
				this.showReviewButton( args.player_id );
				return;
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
			this.dealMeInClicked = true;
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
			dojo.replaceClass( 'buttonBuy', "bgabutton_gray", "bgabutton_blue" ); // item, add, remove
			dojo.replaceClass( 'buttonNotBuy', "bgabutton_gray", "bgabutton_blue" ); // item, add, remove
			console.log(this.firstLoad);

			//if ( this.player_id == currentplayer then just ignore. But for now I'll throw a BGA error from PHP.
			if ( this.checkAction( action, true )  ||
			   ( this.firstLoad == 'Yes' )) {
				console.log("[bmc] ajax " + action );

				this.ajaxcall("/" + this.game_name + "/" + this.game_name + "/" + action + ".html", {
						player_id : this.player_id,
						lock : true
					}, this, function(result) {
					}, function(is_error) {
				});
			} else {
				console.log("[bmc] Buy not allowed now");
			}
		},
/////////
/////////
/////////
		onPlayerNotBuyButton : function() {
console.log("[bmc] ENTER onPlayerNotBuyButton");
			this.clearButtons();
			this.stopActionTimer2();
			console.log(this.gamedatas);
			
			var action = 'notBuyRequest';

			if (this.checkAction( action, true)) {
				console.log("[bmc] ajax " + action );
				this.ajaxcall("/" + this.game_name + "/" + this.game_name + "/" + action + ".html", {
						player_id : this.player_id,
						lock : true
					}, this, function(result) {
					}, function(is_error) {
				});
			} else {
				console.log( "[bmc] checkAction false");
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
        // Get card unique identifier based on its color and value (e.g. Ace of clubs is 0)
        getCardUniqueId : function(color, value) {
			var cui = (color - 1) * 13 + (value - 1); // Offset depending upon the image sprite file
//			console.log("return: " + cui)
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

//11/3 TODO:  Validate this can go through the board areas properly.
//Maybe just do it after a card is played onto a run and after someone goes down.

		sortBoard : function( ) {
console.log( "[bmc] ENTER sortBoard" );
			for ( var player in this.gamedatas.players ) {
console.log("SORTBOARD player");
console.log(player);
				cards = this.downArea_A_[ player ].getAllItems();
console.log(cards);
				if ( cards != null ) {
					weightChange = this.sortRun( cards, 'playerDown_A' );
console.log( weightChange );
					this.downArea_A_[ player ].changeItemsWeight( weightChange );
				}

				cards = this.downArea_B_[ player ].getAllItems();
console.log(cards);
				if ( cards != null ) {
					weightChange = this.sortRun( cards, 'playerDown_B' );
console.log( weightChange );
					this.downArea_B_[ player ].changeItemsWeight( weightChange );
				}
				
				cards = this.downArea_C_[ player ].getAllItems();
console.log(cards);
				if ( cards != null ) {
					weightChange = this.sortRun( cards, 'playerDown_C' );
console.log( weightChange );
					this.downArea_C_[ player ].changeItemsWeight( weightChange );
				}
			}
console.log( "[bmc] EXIT sortBoard" );
		},
/////////
/////////
/////////
		sortRun : function( cards, downArea ) {
console.log( "[bmc] ENTER sortRun" );
console.log( cards );
console.log( downArea );

			let weightChange = {};
					
			if ( cards.length != 0 ) {
				var el = {};
				var cardGroup = new Array();
				var cardGroupNonJokers = new Array();
				
				for ( let i in cards ) {
					//console.log(i);
					
					var [ color, value ] = this.getColorValue( cards[ i ]['type'] );

					el = {
						'id' : cards[i]['id'],
						'unique_id' : this.getCardUniqueId(color, value),
						'type' : color,
						'type_arg' : value,
						'location' : downArea,
						'location_arg' : value // value = weight
					}
					cardGroup[ cards[ i ][ 'id' ]] = el;
					
					// Also store the non-jokers
					if ( color != 5 ) {
						// Also store the ace, if present (assume there is only 1)
						cardGroupNonJokers[ cards[ i ][ 'id' ]] = el;
						if ( value == 1 ) {
							var ace = el ;
						}
console.log( ace );
					}
				}
console.log( "cardGroup:" );
console.log( cardGroup );
console.log( cardGroupNonJokers );

				// Count number of jokers and track their IDs to set weights later
				
				//type = 5; // Jokers are type 5
				var jokerCount = 0;
				var jokers = new Array();
				
				for ( let i in cardGroup ) {
console.log(i);
					if ( cardGroup[ i ][ 'type' ] == 5 ) {
						jokers[ jokerCount ] = {
							"uid" : cardGroup[ i ][ 'unique_id' ],
							"type_arg" : 0 };	 // Start jokers at value 0
						jokerCount++;
					}
				}
console.log("jokers");
console.log( jokerCount );
console.log( jokers );

				cardGroupNonJokers.sort( this.compareTypeArg ); // Sort by value, but it changes the indices
				
console.log( "cardGroupNonJokers aftersort 1" );
console.log( cardGroupNonJokers );
				// If there's an ace, move it to be high
				// TODO: Don't know if this is really going to work. This is kinda hokey.
				// The value of the ace to 14 so it goes next to the king.
				
				for ( card in cardGroupNonJokers ) {
console.log("card");
console.log(card);
console.log(cardGroupNonJokers[ card ]);
					if ( cardGroupNonJokers[ card ][ 'type_arg' ] == 1 ) {
console.log("FOUND ACE");
						var ace = cardGroupNonJokers[ card ];
						var aceid = card;
					}
				}
console.log( "ace" );
console.log( ace );
console.log( aceid );
				if ( ace ) { // If an ace and it's high then make it high
					if ( cardGroupNonJokers[ Object.keys(cardGroupNonJokers).length - 1 ][ 'type_arg' ] + jokerCount > 12 ) {
console.log("[bmc] Making Ace High");
						cardGroupNonJokers[ aceid ][ 'type_arg' ] = 54; // The highest type is 53. Go to right of it.
						cardGroup[ cardGroupNonJokers[ aceid ][ 'id' ]][ 'type_arg' ] = 54; // The highest type is 53. Go to right of it.
					}
				}
				
				cardGroupNonJokers.sort( this.compareTypeArg ); // Sort by value, but it changes the indices
				
console.log( "cardGroupNonJokers aftersort 2" );
console.log( cardGroupNonJokers );

				if ( jokerCount > 0 ) {
					lowestCard = cardGroupNonJokers.find(Boolean) ; // Store first non-joker value
	console.log( "lowestCard" );
	console.log( lowestCard );
					
					jokerIndex = 0;
					
					weightChange[ lowestCard.unique_id ] = 0; // Lowest card gets lowest weight

					cgLength = Object.keys(cardGroupNonJokers).length;
	console.log(cgLength);

					for ( let i = 1 ; i < cgLength ; i++ ) {
	console.log(i);
	console.log(cardGroupNonJokers[i]);
						let delta = cardGroupNonJokers[ i ][ 'type_arg' ] - lowestCard[ 'type_arg' ];
						
						for ( let j = 1; j < delta; j++ ) {
	console.log(jokerIndex);
	console.log(jokers[jokerIndex]);
	console.log(j);
							weightChange[ jokers[ jokerIndex ][ 'uid' ]] = i + jokerIndex;
							weightChange[ cardGroupNonJokers[ i ].unique_id ] = i + jokerIndex + 1;
							jokers[ jokerIndex ][ 'type_arg' ] = lowestCard[ 'type_arg' ] + 1;
							lowestCard = jokers[ jokerIndex ];
							jokerIndex++;
	//					} else {
						}
						weightChange[ cardGroupNonJokers[ i ].unique_id ] = i + jokerIndex;
						lowestCard = cardGroupNonJokers[ i ];
	//					}
	console.log("weightChange");
	console.log(weightChange);			
	console.log(lowestCard);
	console.log(jokers);
					}
	console.log("FINAL weightChange before ACE analysis");
	console.log(weightChange);
				}

				if ( ace != null ) {
	console.log("[bmc] There's an ace!");
					// Check if ace needs to be highest.  Assuming it's already a run, ace is high when:
					//   There is a King, or
					//   There is a Queen and 1 joker, or
					//   There is a Jack and 2 jokers, etc...
	console.log(Object.keys(cardGroupNonJokers).length);
					if ( cardGroupNonJokers[ Object.keys(cardGroupNonJokers).length - 1 ][ 'type_arg' ] + jokerCount > 12 ) {
	console.log("Making Ace High");
						weightChange[ ace.unique_id ] = 54; // Joker #2 is 53, so make it higher
					}
				}
console.log("FINAL FINAL weightChange after ACE analysis");
console.log(weightChange);
			return weightChange;
			}
console.log( "[bmc] EXIT sortRun" );
		},
/////////
/////////
/////////
        discardCard : function( player_id, color, value, card_id, nextTurnPlayer, allHands, discardSize ) {
			// (from PHP) Purpose is to show the played cards on the table, not really to play the card.
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
			
			if ( allHands[ this.player_id ] != undefined ) {
				this.myHandSize.setValue( allHands[ this.player_id ] );
			} else {
				this.myHandSize.setValue( 0 );
			}

			// Remove any existing discard pile card
			//if ( this.discardPile.items.length > 0 ) {
			//	this.discardPile.removeFromStockById( this.discardPile.items[ 0 ].id );
			//}
			
			// Add it to the pile and set the weight
			let cardUniqueId = this.getCardUniqueId( color, value );
			
			if ( player_id == this.player_id ) {
				this.discardPile.addToStockWithId( cardUniqueId, card_id, 'myhand' );
			} else {
				this.discardPile.addToStockWithId( cardUniqueId, card_id, 'overall_player_board_' + player_id );
			}
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
				
				// New variables for new timers on static buttons
				this.enableDBStatic = 'Yes';
				this.enableDBTimer = 'Yes';
				this.enDisStaticBuyButtons();
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
		startActionTimerStatic: function () {
console.log("[bmc] ENTER startActionTimerStatic");
			if( this.actionTimerIdStatic ) { // Don't create a new timer if one already exists.
console.log( "[bmc] Timer already exists, not need to create.");
				return;
			}
			var isReadOnly = this.isReadOnly();
			if ( isReadOnly ) { // Spectators are read only
console.log( "[bmc] Spectator, no timer.");
				return;
			}
			var notBuyButtonID = 'buttonNotBuy';
//			this.actionTimerLabelDefault = $(notBuyButtonID).innerHTML;
			this.actionTimerLabel = this.actionTimerLabelDefault;
			this.actionTimerSeconds = 11 ; // BUY COUNTDOWN TODO: Connect this to options (PHP)
console.log( "[bmc] Starting TimerStatic! " + this.actionTimerSeconds );

			this.actionTimerFunctionStatic = () => {
console.log("[bmc] ENTER actionTimerFunctionStatic.");
console.log( notBuyButtonID );
console.log( $(notBuyButtonID) );
				var button = $(notBuyButtonID);
console.log( button );			
console.log( this.actionTimerIdStatic );	
				if ( this.actionTimerSeconds-- > 1 ) {
debug('Timer ' + notBuyButtonID + ' has ' + this.actionTimerSeconds + ' seconds left');
					button.innerHTML = this.actionTimerLabel + ' (' + this.actionTimerSeconds + ')';
				} else {
debug('Timer ' + notBuyButtonID + ' execute');
					button.innerHTML = this.actionTimerLabelDefault;
					button.click();
console.log("[bmc] Just clicked the button automatically. Now remove timer.");
					this.stopActionTimerStatic();
				}
			}; // End timer function definition
			
			this.actionTimerFunctionStatic();
			this.actionTimerIdStatic = window.setInterval( this.actionTimerFunctionStatic, 1000 );
debug('Timer #' + this.actionTimerIdStatic + ' ' + notBuyButtonID + ' start');

console.log("[bmc] EXIT startActionTimerStatic");
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
					button.innerHTML = this.actionTimerLabelDefault;
					button.click();
					// dojo.replaceClass( 'buttonBuy', "bgabutton_gray", "bgabutton_blue" ); // item, add, remove
					// dojo.replaceClass( 'buttonNotBuy', "bgabutton_gray", "bgabutton_blue" ); // item, add, remove

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


/////////
/////////
/////////
		stopActionTimerStatic : function () {
console.log( "[bmc] ENTER StopActionTimerStatic" );
console.log( this.actionTimerIdStatic );
			this.enableDBStatic = 'No';
			this.enableDBTimer = 'No';
			this.enDisStaticBuyButtons();
			
			var notBuyButtonID = 'buttonNotBuy';
			var button = $(notBuyButtonID);
			this.actionTimerLabel = this.actionTimerLabelDefault;
			
console.log(this.actionTimerLabel);
			
			button.innerHTML = this.actionTimerLabel;
	
			if ( this.actionTimerIdStatic != null ) {
debug('Timer #' + this.actionTimerIdStatic + ' stop');
				window.clearInterval( this.actionTimerIdStatic );
				delete this.actionTimerIdStatic;
			}
console.log( "[bmc] EXIT stopActionTimerStatic" );
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
			if ( this.buyTimeInSeconds != 0 ) {
				this.actionTimerSeconds = this.buyTimeInSeconds ; // BUY COUNTDOWN
			} else {
				return; // DO NOT START THE TIMER, players didn't want not-buy timers.
			}
		  
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
					button.innerHTML = this.actionTimerLabel;
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
				var notBuyButStaticID = 'buttonNotBuy';
//EXP 10/26				this.addActionButton( buyButtonID, _("Buy!"), 'onPlayerBuyButton' );
//				this.addActionButton( notBuyButtonID , _("Not Buy!"), 'onPlayerNotBuyButton' );
				
				dojo.replaceClass( 'buttonBuy', "bgabutton_blue", "bgabutton_gray" );
				dojo.replaceClass( 'buttonNotBuy', "bgabutton_blue", "bgabutton_gray" );

console.log("[bmc] Action buttons were just created.");

			}
console.log("[bmc] EXIT showBuyButton2");
		},
/////////
/////////
/////////
		enDisStaticBuyButtons : function() {
console.log(this.enableDBStatic);
			if ( this.enableDBStatic == 'Yes' ) {
console.log("[bmc] YES enDisStaticBuyButtons");
				dojo.replaceClass( 'buttonBuy', "bgabutton_blue", "bgabutton_gray" ); // item, add, remove
				dojo.replaceClass( 'buttonNotBuy', "bgabutton_blue", "bgabutton_gray" ); // item, add, remove
				
				// Only start the timer if active during hand, not during game start nor hand start.
				if ( this.enableDBTimer == 'Yes' ) {
console.log("[bmc] YES enableDBTimer");
					this.startActionTimerStatic();
				}
			} else {
console.log("[bmc] NO enDisStaticBuyButtons");
				dojo.replaceClass( 'buttonBuy', "bgabutton_gray", "bgabutton_blue" ); // item, add, remove
				dojo.replaceClass( 'buttonNotBuy', "bgabutton_gray", "bgabutton_blue" ); // item, add, remove
			}
		},
		
		
		
		
//			var notBuyButtonID = 'buttonPlayerNotBuy' + this.player_id;
//			this.startActionTimer( notBuyButtonID );



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

//EXP 10/26					this.addActionButton( buyButtonID, _("Buy!"), 'onPlayerBuyButton' );
//					this.addActionButton( notBuyButtonID , _("Not Buy!"), 'onPlayerNotBuyButton' );
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
		onMyHandAreaClick : function() {
console.log("[bmc] ENTER onMyHandAreaClick");
			this.playerHand.unselectAll();
console.log("[bmc] EXIT onMyHandAreaClick");
		},
/////////
/////////
/////////
		onDownAreaSelect : function() {
console.log("[bmc] ENTER onDownAreaSelect");
console.log(this.player_id);

			var isReadOnly = this.isReadOnly();
			if ( isReadOnly ) { // Spectators are read only
				return;
			}

			var handItems = this.playerHand.getSelectedItems();
console.log(handItems);
			var area_A_Items = this.downArea_A_[ this.player_id ].getSelectedItems();
			var area_B_Items = this.downArea_B_[ this.player_id ].getSelectedItems();
			var area_C_Items = this.downArea_C_[ this.player_id ].getSelectedItems();
console.log(area_A_Items);
console.log(area_B_Items);
console.log(area_C_Items);
			
			// TODO: These if conditions overlap, could be simplified
			if (( this.goneDown[ this.player_id ] == 0 ) &&  //0 = Not gone down; 1 = Gone down.
				( handItems.length === 1 )) { // Just 1 card in hand
				// Then just highlight it in prep for GO DOWN button (i.e. do nothing)
				return;
				
			} else if ( handItems.length === 1 )  { // Then try to play the card
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
//					this.playerHand.unselectAll();
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
					console.log("[bmc] No card on board selected, do nothing (one card)");
				}
			// If the player has not gone down and clicks, pull all the cards back to their hand
			} else if ( this.goneDown[ this.player_id ] == 0 ) { //0 = Not gone down; 1 = Gone down.
				if ( area_A_Items.length === 1 ) {
console.log("[bmc] Card from prep A to hand");
					let card = area_A_Items[ 0 ];
					cardUniqueId = card.type;
					cardId = card.id;

					this.playerHand.addToStockWithId( cardUniqueId, cardId, 'myhand'); // Pull back to hand
					this.downArea_A_[ this.player_id ].removeFromStockById( card.id );
					this.downArea_A_[ this.player_id ].unselectAll();

				} else if ( area_B_Items.length === 1 ) {
console.log("[bmc] Card from prep B to hand");
					let card = area_B_Items[ 0 ];
					cardUniqueId = card.type;
					cardId = card.id;

					this.playerHand.addToStockWithId( cardUniqueId, cardId, 'myhand'); // Pull back to hand
					this.downArea_B_[ this.player_id ].removeFromStockById( card.id );
					this.downArea_B_[ this.player_id ].unselectAll();

				} else if ( area_C_Items.length === 1 ) {
console.log("[bmc] Card from prep C to hand");
					let card = area_C_Items[ 0 ];
					cardUniqueId = card.type;
					cardId = card.id;

					this.playerHand.addToStockWithId( cardUniqueId, cardId, 'myhand'); // Pull back to hand
					this.downArea_C_[ this.player_id ].removeFromStockById( card.id );
					this.downArea_C_[ this.player_id ].unselectAll();

				} else {
					console.log("[bmc] No card in hand selected, do nothing");
				}
			} else if ( handItems.length > 1 )  { // Then try to play multiple cards
				var [ boardCard, boardArea, boardPlayer ] = this.getSelectedDownAreaCards ();

				if  (( boardCard != {} ) &&
					(  boardArea != '' )) { // There is a card there on the board, so try to play cards there

					// do the unselects before going to the server
					for ( var player in this.gamedatas.players ) {
						this.downArea_A_[ player ].unselectAll();
						this.downArea_B_[ player ].unselectAll();
						this.downArea_C_[ player ].unselectAll();
					}
					
					// Make a list of the selected cards
					var handItemIds = this.getItemIds(handItems);
console.log("handItemIds");
console.log(handItemIds);
					
					var action = 'playCardMultiple';
					if (this.checkAction( action, true)) {
console.log("[bmc] PlayCard Action true AJAX");
console.log("/" + this.game_name + "/" + this.game_name + "/" + action + ".html");

						this.ajaxcall("/" + this.game_name + "/" + this.game_name + "/" + action + ".html", {
							card_ids : this.toNumberList( handItemIds ),
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
					console.log("[bmc] No card on board selected, do nothing (multiple cards)");
				}
			}
			
			var area_A_Items = this.downArea_A_[ this.player_id ].getAllItems();
			var area_B_Items = this.downArea_B_[ this.player_id ].getAllItems();
			var area_C_Items = this.downArea_C_[ this.player_id ].getAllItems();

			if ( area_A_Items.length == 0 ) {
				dojo.removeClass('playerDown_A_' + this.player_id, "border1");
			}
			if ( area_B_Items.length == 0 ) {
				dojo.removeClass('playerDown_B_' + this.player_id, "border1");
			}
			if ( area_C_Items.length == 0 ) {
				dojo.removeClass('playerDown_C_' + this.player_id, "border1");
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
			
			if ( player_id == this.player_id) {
				var from = 'myhand';
			} else {
				var from = 'overall_player_board_' + player_id;
			}
			
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
					from );
//					'myhand' );

console.log("[bmc] Added.");
				this.playerHand.removeFromStockById( card_id );
console.log("[bmc] Removed.");
			}
			
			if ( boardArea === 'playerDown_B' ) {
console.log(boardArea);
				this.downArea_B_[boardPlayer].addToStockWithId(
					cardUniqueId,
					card_id,
					from );
//					'myhand' );

console.log("[bmc] Added.");
				this.playerHand.removeFromStockById( card_id );
console.log("[bmc] Removed.");
			}
			if ( boardArea === 'playerDown_C' ) {
			console.log(boardArea);
				this.downArea_C_[boardPlayer].addToStockWithId(
					cardUniqueId,
					card_id,
					from );
//					'myhand' );
console.log("[bmc] Added.");
				this.playerHand.removeFromStockById(card_id);
console.log("[bmc] Removed.");
			}
			this.sortBoard();
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
					this.playerHand.addToStockWithId( cardUniqueId, card.id, 'myhand' ); // Pull back to hand
				}
				this.downArea_A_[ player_id ].removeAllTo( 'myhand' );
				//
				var cards = this.downArea_B_[ player_id ].getAllItems();
				console.log(cards);

				for ( card of cards ) {
					console.log(card);
					cardUniqueId = card.type;
					this.playerHand.addToStockWithId( cardUniqueId, card.id, 'myhand' ); // Pull back to hand
				}
				this.downArea_B_[ player_id ].removeAllTo( 'myhand' );
				//
				var cards = this.downArea_C_[ player_id ].getAllItems();
				console.log(cards);

				for ( card of cards ) {
					console.log(card);
					cardUniqueId = card.type;
					this.playerHand.addToStockWithId( cardUniqueId, card.id, 'myhand' ); // Pull back to hand
				}
				this.downArea_C_[ player_id ].removeAllTo( 'myhand' );
				
				dojo.removeClass('playerDown_A_' + this.player_id, "border1");
				dojo.removeClass('playerDown_B_' + this.player_id, "border1");
				dojo.removeClass('playerDown_C_' + this.player_id, "border1");

				this.prepSetLoc = 0; // Nothing is prepped, so clear the counters
				this.prepRunLoc = 3; 
				this.prepAreas = 0;

			} // else do nothing, they've already gone down.
			console.log("[bmc] EXIT onDownAreaClick");
		},
/////////
/////////
/////////
		onDeckSelectionChanged : function() {
			console.log("[bmc] ENTER OnDeckSelectionChanged.");
//			var items = this.deck.getSelectedItems();
			var items = this.deckOne.getSelectedItems();
			this.deckOne.unselectAll();

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
			if (( items.length > 0 ) || ( drawSource == 'discardPile' )) {
				console.log("[bmc] >0; Sending the card.");
				
				var action = 'drawCard';
				if (this.checkAction( action, true )) {
					console.log( "[bmc] Action true. AJAX next" );
					console.log( "/" + this.game_name + "/" + this.game_name + "/" + action + ".html");
					
//					var card_id = items[0].id;
					var card_id = 0; // TODOfake number probably should remove it
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
			// dojo.replaceClass( 'buttonBuy', "bgabutton_gray", "bgabutton_blue" ); // item, add, remove
			// dojo.replaceClass( 'buttonNotBuy', "bgabutton_gray", "bgabutton_blue" ); // item, add, remove

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
			let action = "reallyDiscard";
console.log( this.prepAreas );

			// If cards are in prep area, double check that the really want to discard and not go down.
			if (( this.prepAreas > 0 ) && ( this.goneDown[ this.player_id ] == 0 )) { //0 = Not gone down; 1 = Gone down.
console.log ("[bmc] CONFIRM");
				this.confirmationDialog( _('Are you sure you want to discard? You have cards prepped.'),
							 dojo.hitch( this, function() {
								 this.reallyDiscard();
							   // this.ajaxcall( '/' + this.game_name + "/" + this.game_name + "/" + action + ".html",
									// { id:'', lock:true }, this, function( result ) {} );
							}));
			} else { // nothing prepped so discard the card
console.log ("[bmc] Just discard");
				this.reallyDiscard();
			}
console.log( "[bmc] EXIT onPlayerDiscardButton" );
		},
/////////
/////////
/////////		
		reallyDiscard : function() {
console.log( "[bmc] ENTER reallyDiscard" );
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
			// Just practicing the sortRun function
			//this.sortRun( thisPlayerHandIds, 'playerDown_A' );
			
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
console.log( "thisPlayerHand:" );
console.log( thisPlayerHand );
			
			if ( this.playerSortBy == 'Set' ) {
				this.playerSortBy = 'Run';
//				this.onPlayerSortByButtonRun( thisPlayerHand );
				thisPlayerHand.sort( this.compareId ) ;
console.log( "thisPlayerHand after sort:");
console.log( thisPlayerHand );

				let weightChange = {};
				for (let i in thisPlayerHand) {
					if ( thisPlayerHand[i].type == 5 ) {
						weightChange[ thisPlayerHand[ i ].unique_id ] = this.drawCounter + 1; // Keep jokers on the right
					} else {
						weightChange[ thisPlayerHand[ i ].unique_id ] = parseInt( thisPlayerHand[ i ].unique_id );
					}
				}
console.log("weightChange RUN");
console.log(weightChange);
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
console.log("weightChange SET");
console.log(weightChange);
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
console.log("[bmc] WC");
console.log(weightChange);
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
//				this.showMessage("You already went down");
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

				console.log("[bmc] setsRuns: " + this.setsRuns[this.currentHandType][this.prepSetLoc]);
				console.log(this.prepSetLoc);
				
				if (this.setsRuns[this.currentHandType][this.prepSetLoc] == "None" ) {
console.log("[bmc] No more sets needed.");

					//None are needed so don't do anything
				} else {
					// A set is needed, so put it down (as preparation)
					this.putSetDown( cards );
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
// EXP 11/5
//				let cardUniqueId = this.getCardUniqueId( color, value );
				let cardUniqueId = this.getCardUniqueId( color, value );
				
				this.playerHand.addToStockWithId( cardUniqueId, card_id ); // Add the card to my hand from the board

console.log(this.drawCounter)
				
				let weightChange = {};
				weightChange[ cardUniqueId ] = this.drawCounter;
console.log(weightChange);

				this.playerHand.changeItemsWeight( weightChange );

			} else {
				console.log("[bmc] player_id is NOT me");
				var addTo = 'overall_player_board_' + drawPlayer;
			}
				
console.log( '[bmc] addTo: ' + addTo );
				
				if ( drawSource == 'deck' ) {
console.log( '[bmc] Deck' );
					this.deckOne.removeFromStockById(card_id, addTo );
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
					this.downArea_B_[ drawPlayer ].removeFromStockById( card_id, addTo );
				}
				if ( drawSource == 'playerDown_C' ) {
console.log( '[bmc] C' );
					this.downArea_C_[ drawPlayer ].removeFromStockById( card_id, addTo );
				}
		console.log("[bmc] EXIT drawCard");
		},
/////////
/////////
/////////
		drawCardSpect : function (
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
console.log("[bmc] ENTER drawCardSpect (from notif from PHP)");
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

			// This is only for spectators
			
			var isReadOnly = this.isReadOnly();
			console.log("isReadOnly");
			console.log(isReadOnly);
			
			if ( isReadOnly ) {
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

console.log("[bmc] modified drawSource");
console.log(drawSource);
console.log(from);
console.log(this.playerHand)

				if (( color == null ) ||
					( value == null )) {
	console.log("[bmc] Yikes!! Color or value is null! Need to fix this, this is fatal.");
					exit(0);
				}
				console.log("[bmc] player_id is NOT me");
				var addTo = 'overall_player_board_' + drawPlayer;
//			}
				
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
					this.downArea_B_[ drawPlayer ].removeFromStockById( card_id, addTo );
				}
				if ( drawSource == 'playerDown_C' ) {
console.log( '[bmc] C' );
					this.downArea_C_[ drawPlayer ].removeFromStockById( card_id, addTo );
				}
			}
		console.log("[bmc] EXIT drawCardSpect");
		},
/////////
/////////
/////////
		onDiscardPileSelectionChanged: function() {
console.log( "[bmc] ENTER onDiscardPileSelectionChanged." );
console.log( "[bmc] GAMEDATAS and this.player_id" );
//console.log(card);
console.log( this.gamedatas );
console.log( this.player_id );
			this.discardPile.unselectAll();

			// If the gamestate is play, then treat it as a discard.

            var handCards = this.playerHand.getSelectedItems();

			if (( this.gamedatas.gamestate.name == 'playerTurnPlay' ) &&
				( handCards.length == 1)) {
				this.onPlayerDiscardButton();
	
			// If the gamestate is draw, then draw the top of discard pile (chosen in php).
			} else if ( this.gamedatas.gamestate.name == 'playerTurnDraw' ) {
//console.log(card);

				var items = new Array();
				
				items[0] = {id: "0", type: 0 }; // "Fake" card just used for discarding (i.e. we need to send *something* but
				// when drawing from the discard it is ignored by the PHP and the top of the pile is chosen)
				this.drawCard2nd( items, 'discardPile' );
			}
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
				
				if ( this.setsRuns[ this.currentHandType ][ this.prepSetLoc ] == "Area_A" ) {
					 this.downArea_A_[ this.player_id ].addToStockWithId(cardUniqueId, cardId, 'myhand');
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

				this.playerHand.removeFromStockById( card.id );
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
				
				this.playerHand.removeFromStockById(card.id);

			}
		},
/////////
/////////
/////////
		onPlayerPrepArea_A_Button : function () {
			console.log("[bmc] BUTTON onPlayerPrepAreaAButton");
			console.log(this.player_id);
			
			if ( this.goneDown[ this.player_id ] == 1 ) { // If player already went down, do nothing
				this.showMessage("You already went down");
				return;
			} else {
				this.clearButtons();

				var cards = this.playerHand.getSelectedItems(); // It can be >1 card
				console.log(cards);
				
				var cardIds = this.getItemIds( cards );
				
console.log("[bmc] cardIds: " + cardIds);

				for ( card of cards ) {
					cardUniqueId = card.type;
					cardId = card.id;

					var from = 'myhand_item_' + card.id;
					this.downArea_A_[ this.player_id ].addToStockWithId(cardUniqueId, cardId, 'myhand');
					dojo.addClass('playerDown_A_' + this.player_id, "border1");
					this.playerHand.removeFromStockById (card.id );
				}
				this.prepAreas++;
				console.log(this.prepAreas);
				console.log("[bmc] INCREMENTED prepAreas");

				this.playerHand.unselectAll();
				this.showHideButtons();
			}
		},
/////////
/////////
/////////
		onPlayerPrepArea_B_Button : function () {
			console.log("[bmc] BUTTON onPlayerPrepArea_B_Button");
			console.log(this.player_id);
			if ( this.goneDown[ this.player_id ] == 1 ) { // If player already went down, do nothing
				this.showMessage("You already went down");
				return;
			} else {
				this.clearButtons();

				var cards = this.playerHand.getSelectedItems(); // It can be >1 card
				console.log(cards);
				
				var cardIds = this.getItemIds( cards );
console.log("[bmc] cardIds: " + cardIds);

				for ( card of cards ) {
					cardUniqueId = card.type;
					cardId = card.id;

					var from = 'myhand_item_' + card.id;
					this.downArea_B_[ this.player_id ].addToStockWithId(cardUniqueId, cardId, 'myhand');
					dojo.addClass('playerDown_B_' + this.player_id, "border1");
					this.playerHand.removeFromStockById (card.id );
				}
				this.prepAreas++;
				console.log(this.prepAreas);
				console.log("[bmc] INCREMENTED prepAreas");

				this.playerHand.unselectAll();
				this.showHideButtons();
			}
		},
/////////
/////////
/////////
		onPlayerPrepArea_C_Button : function () {
console.log("[bmc] BUTTON onPlayerPrepAreaAButton");
console.log(this.player_id);
			// If player already went down, do nothing
			if ( this.goneDown[ this.player_id ] == 1 ) {
				this.showMessage("You already went down");
				return;
			} else {
				this.clearButtons();

				var cards = this.playerHand.getSelectedItems(); // It can be >1 card
				console.log(cards);
				
				var cardIds = this.getItemIds( cards );
console.log("[bmc] cardIds: " + cardIds);

				for ( card of cards ) {
					cardUniqueId = card.type;
					cardId = card.id;

					var from = 'myhand_item_' + card.id;
					this.downArea_C_[ this.player_id ].addToStockWithId(cardUniqueId, cardId, 'myhand');
					dojo.addClass('playerDown_C_' + this.player_id, "border1");
					this.playerHand.removeFromStockById (card.id );
				}
				this.prepAreas++;
				console.log(this.prepAreas);
				console.log("[bmc] INCREMENTED prepAreas");

				this.playerHand.unselectAll();
				this.showHideButtons();
			}
		},
/////////
/////////
/////////
		showHideButtons : function() {
console.log("[bmc] ENTER ShowHideButtons");
			let buyButtonID = 'buttonBuy';
			let notBuyButtonID = 'buttonNotBuy';
console.log( "[bmc] BUTTONIDs:" );
console.log( notBuyButtonID );

			this.clearButtons();

			// Only show the buy buttons if they already don't exist
			
			var showButtons = new Array();
			
console.log("this.playerSortBy");
console.log(this.playerSortBy);
			
			
			if ( this.goneDown[ this.player_id ] == 0 ) {
				var items = this.playerHand.getSelectedItems();
				if ( items.length > 0 ) {
	console.log("[bmc] prepbuttons ON");
					dojo.replaceClass( 'buttonPrepAreaA', "bgabutton_blue", "bgabutton_gray" ); // item, add, remove
					dojo.replaceClass( 'buttonPrepAreaB', "bgabutton_blue", "bgabutton_gray" );
					dojo.replaceClass( 'buttonPrepAreaC', "bgabutton_blue", "bgabutton_gray" );
				} else {
	console.log("[bmc] prepbuttons OFF");
					dojo.replaceClass( 'buttonPrepAreaA', "bgabutton_gray", "bgabutton_blue" ); // item, add, remove
					dojo.replaceClass( 'buttonPrepAreaB', "bgabutton_gray", "bgabutton_blue" );
					dojo.replaceClass( 'buttonPrepAreaC', "bgabutton_gray", "bgabutton_blue" );
				}
			}
			
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
					//this.addActionButton( 'buttonPlayerSort', _("Sort!"), 'onPlayerSortButton');
				}
			}

			if ( this.prepRunLoc + this.prepSetLoc > 3) { // Starting positions are 0 and 3
				showButtons['prepped'] = true;
			}

			console.log("[bmc] showButtons:");
			console.log(showButtons);
			console.log(this.prepAreas);
			//
			// Show GO DOWN button if prepped, not gone down and my turn
			//
			var goDownDOM = document.getElementById( 'buttonPlayerGoDown' );

// TODO: Add condition that player has already drawn			
//			if (  showButtons['prepped'] && 
			if (( this.prepAreas > 0 ) &&
				 !showButtons['goneDown'] &&
				  showButtons['myturn'] && 
				( goDownDOM == null )) {

				dojo.replaceClass( 'buttonGoDownStatic', "bgabutton_blue", "bgabutton_gray" ); // item, add, remove
				this.addActionButton( 'buttonPlayerGoDown', _("Go Down!"), 'onPlayerGoDownButton' );
				this.showingButtons === 'Yes';
			} else {
				dojo.replaceClass( 'buttonGoDownStatic', "bgabutton_gray", "bgabutton_blue" ); // item, add, remove
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
					( items.length >= 2 ) &&  // 1 joker allowed during godown
					( items.length <= 3 )) {  // A set is 3 cards
//					this.addActionButton('buttonPlayerPlaySet', _("Prep Set!"), 'onPlayerPlaySetButton');
					this.showingButtons === 'Yes';
				}
				if (( runNeeded ) &&
					( items.length >= 3 ) && // 1 joker allowed during godown
					( items.length <= 4 )) {  // A run is 4 cards
//					this.addActionButton('buttonPlayerPlayRun', _("Prep Run!"), 'onPlayerPlayRunButton');
					this.showingButtons === 'Yes';
				}
			}
			//
			// Show DISCARD if card selected, it's not state playerTurnDraw, and it's my turn
			//
			if ( showButtons['handSelected'] && 
				 showButtons['myturn'] &&
			   ( items.length == 1 ) &&
			   ( this.gamedatas.gamestate.name != "playerTurnDraw" )) {
				   
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
console.log( items);

console.log( "[bmc] gamedatas:" );
console.log( this.gamedatas );
console.log( "[bmc] this.player_id:" );
//console.log( this.gamedatas.playerOrderTrue[ 0 ] ) ;
console.log( this.player_id );
console.log( items.length );

			if ( this.goneDown[ this.player_id ] == 0 ) {
				if ( items.length == 1 ) {
	console.log("[bmc] Store the first");
	console.log("[bmc] prepbuttons ON");
					this.playerHand.firstSelected = items[ 0 ].type;
					dojo.replaceClass( 'buttonPrepAreaA', "bgabutton_blue", "bgabutton_gray" ); // item, add, remove
					dojo.replaceClass( 'buttonPrepAreaB', "bgabutton_blue", "bgabutton_gray" );
					dojo.replaceClass( 'buttonPrepAreaC', "bgabutton_blue", "bgabutton_gray" );
				} else if ( items.length == 0 ) {
	console.log("[bmc] prepbuttons OFF");
					dojo.replaceClass( 'buttonPrepAreaA', "bgabutton_gray", "bgabutton_blue" );
					dojo.replaceClass( 'buttonPrepAreaB', "bgabutton_gray", "bgabutton_blue" );
					dojo.replaceClass( 'buttonPrepAreaC', "bgabutton_gray", "bgabutton_blue" );
				}
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
            dojo.subscribe( 'drawCardSpect' ,    this, "notif_drawCardSpect");
            dojo.subscribe( 'newScores',         this, "notif_newScores" );
			dojo.subscribe( 'playerGoDown' ,     this, "notif_playerGoDown");
            dojo.subscribe( 'cardPlayed' ,       this, "notif_cardPlayed");
            dojo.subscribe( 'deckShuffled' ,     this, "notif_deckShuffled");
            dojo.subscribe( 'playerNotBuying' ,  this, "notif_playerNotBuying");
            dojo.subscribe( 'playerWantsToBuy' , this, "notif_playerWantsToBuy");
            dojo.subscribe( 'playerBought' ,	 this, "notif_playerBought");
            dojo.subscribe( 'playerDidNotBuy' ,  this, "notif_playerDidNotBuy");
			dojo.subscribe( 'wentOut' , 		 this, "notif_playerWentOut");
            dojo.subscribe( 'clearBuyers' ,      this, "notif_clearBuyers");
			dojo.subscribe( 'close_btn' , 		 this, "onPlayerReviewedHandButton");
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
		showReviewButton : function( player_id ) {
			var reviewButtonID = 'buttonReview' + this.player_id;

			var isReadOnly = this.isReadOnly();
			if ( !isReadOnly ) { // Spectators are read only

//				if (( player_id == this.player_id )  && ( this.dealMeInClicked == false )) {
				if ( this.dealMeInClicked == false ) {
					this.addActionButton( reviewButtonID, _("Deal me in!"), 'onPlayerReviewedHandButton' );
				}// else {
					// this.addActionButton( reviewButtonID, _("Deal me in!"), 'onPlayerReviewedHandButton' );
				// }
			}
		},
/////////
/////////
/////////
		notif_playerWentOut : function( notif ) {
			console.log("[bmc] ENTER notif_playerWentOut")
			console.log( notif );
			
			// If someone went out, remove the BUY buttons, kill the timers and let them review.
			//this.stopActionTimer2();
			this.stopActionTimerStatic();
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
		clearTable : function() {
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
			this.prepAreas = 0;
		},
/////////
/////////
/////////
		setupDiscardPile : function(notif) {
			// Set up the discard pile
			var discardPileWeights = new Array();

			if ( notif.args.discardPile != undefined ) {
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
				this.discardSize.setValue( notif.args.discardSize );
			}
		},
/////////
/////////
/////////
		setupDeck : function(notif) {
			// Set up the draw deck
			if ( notif.args.deck != undefined ) {
				for ( let i = 0 ; i < notif.args.deck.length; i++ ) {
					this.deck.addToStockWithId( 1, notif.args.deck[i] );
				}
			}
			if ( notif.args.drawDeckSize != undefined ) {
				this.drawDeckSize.setValue( notif.args.drawDeckSize );
			}
console.log("[bmc] this.deck");			
console.log(this.deck);
		},
/////////
/////////
/////////
		clearPlayerBoards : function(notif) {
			dojo.removeClass('playerDown_A_' + this.player_id, "border1");
			dojo.removeClass('playerDown_B_' + this.player_id, "border1");
			dojo.removeClass('playerDown_C_' + this.player_id, "border1");

			if ( notif.args.buyCount != undefined ) {
				for ( var player_id in this.gamedatas.players ) {
console.log("[bmc] Updating buys and cards");
					this.buyCount[ player_id ].setValue( notif.args.buyCount[ player_id ] );
					this.handCount[ player_id ].setValue( notif.args.allHands[ player_id ] );
				}
			}
		},
/////////
/////////
/////////
        notif_newHand : function(notif) {
console.log("[bmc] ENTER notif_newHand");
console.log(notif);
			
			this.dealMeInClicked = false;
			if ( notif.args.hand == undefined ) {
console.log("Hand is undefined");
				this.clearTable();
				this.setupDiscardPile(notif);
				this.setupDeck(notif);
				this.clearPlayerBoards(notif);
				
				return;
				
			} else 	if ( notif.args.hand != undefined ) {
				if (notif.args.hand.length == 0 ) { // if it's just notify for the history log, do nothing
console.log("empty arg");
					return;
				}
			}
			this.clearTable();
			this.setupDiscardPile(notif);
			this.setupDeck(notif);
			this.clearPlayerBoards(notif);

			var isReadOnly = this.isReadOnly();
			if ( !isReadOnly ) { // Spectators are read only
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
			}

			// Set all players to buy, except for the player whose turn it is
			if ( this.player_id != this.gamedatas.playerOrderTrue[ notif.args.dealer_id ] ) {
				this.buyCounterTimerShouldExist = 'Yes'; // A timer and a button should exist
				this.showBuyButton2();
			}

			// Set the hand counts for all players
			for ( var p_id in notif.args.allHands ) {
				this.handCount[ p_id ].setValue( notif.args.allHands[ p_id ] );
			}
			
			this.buyTimeInSeconds = 40;
			
			// Draw the names on the board
			for ( var player in this.gamedatas.players) {
				$("playerDown_A_"+ player).innerHTML = this.gamedatas.players[ player ][ 'name' ];
				$("playerDown_B_"+ player).innerHTML = this.gamedatas.players[ player ][ 'name' ];
				$("playerDown_C_"+ player).innerHTML = this.gamedatas.players[ player ][ 'name' ];
			}

			// Update the webpage with the new target
			$(redTarget).innerHTML = notif.args.handTarget;

			console.log( $(redTarget) );
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
		notif_drawCardSpect : function(notif) {
console.log("[bmc] ENTER notif_drawcardSpect");
console.log(notif);
            this.drawCardSpect(
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
console.log("[bmc] EXIT notif_drawcardSpect");
		},
/////////
/////////
/////////
		notif_clearBuyers : function(notif) {
			console.log("[bmc]notif_clearBuyers");
			// No one is buying because the next-next player has discarded!
			console.log(notif);
			this.stopActionTimer2();
			this.showHideButtons();
			this.enableDBStatic = 'No';
			this.enableDBTimer = 'No'; // But let the timer run out if it's there
			this.enDisStaticBuyButtons();
			this.stopActionTimerStatic(); // Stop the timer, we are not buying			
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
			
//			if ( notif.player_id == this.player_id ) {
				
			
			if ( this.player_id == notif.player_id ) {
				// New variables for new timers on static buttons
				this.enableDBStatic = 'No';
				this.enableDBTimer = 'No'; // But let the timer run out if it's there
				this.enDisStaticBuyButtons();
				this.stopActionTimerStatic(); // Stop the timer, we are not buying
			}
		},
/////////
/////////
/////////
		notif_playerBought : function(notif) {
			console.log("[bmc]notif_playerBought");
			console.log(notif);
			dojo.removeClass( 'overall_player_board_' + notif.args.player_id, 'pbInverse' );
			this.buyCount[  notif.args.player_id ].setValue( notif.args.buyCount[ notif.args.player_id ] );
			this.handCount[ notif.args.player_id ].setValue( notif.args.allHands[ notif.args.player_id ] );
		},
/////////
/////////
/////////
		notif_playerDidNotBuy : function(notif) {
			console.log("[bmc]notif_playerDidNotBuy");
			console.log(notif);
			dojo.removeClass( 'overall_player_board_' + notif.args.player_id, 'pbInverse' );
		},
/////////
/////////
/////////
		notif_playerWantsToBuy : function(notif) {
			console.log("[bmc]notif_playerWantsToBuy");
			console.log(notif);
			this.stopActionTimer2();
			this.showHideButtons();
			console.log( this.gamedatas.players[ notif.args.player_id ].name  );
			
			console.log("[bmc] Adding class and showing bubble");
			
			dojo.addClass( 'overall_player_board_' + notif.args.player_id, 'pbInverse' );
			
			this.showMessage( this.gamedatas.players[ notif.args.player_id ].name + ' wants to buy ' +
				notif.args.value_displayed + notif.args.color_displayed,
				'error' ); // 'info' or 'error'

/*			anchor_id = 'overall_player_board_' + notif.args.player_id;
			text = "I'll buy it!";
			delay = 0;
			duration = 7;
			custom_class = '';
			
			console.log(anchor_id);
			
			this.showBubble(anchor_id, text, delay, duration, custom_class);
*/			
			// New variables for new timers on static buttons
			this.enableDBStatic = 'No';
			this.enableDBTimer = 'No'; // But let the timer run out if it's there
			this.enDisStaticBuyButtons();
			this.stopActionTimerStatic(); // Stop the timer, someone else is buying
		},
/////////
/////////
/////////
		notif_playerGoDown: function( notif ) {
			console.log('ENTER notif_playerGoDown. Solidify the card positions.');
			console.log( notif );
			console.log( this.gamedatas.currentPlayerId );
			console.log( this.gamedatas.playerOrderTrue );
			console.log( this.player_id );

			//this.disableNextMoveSound();
			playSound( 'tutorialrumone_GoingDown' );
			//this.disableNextMoveSound();
			
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
			
			// Check if we are a spectator. If so, then don't deal with CSS class borders.
			var isReadOnly = this.isReadOnly();
			if ( !isReadOnly ) {
				// Remove the borders around the prep area
				dojo.removeClass('playerDown_A_' + notif.args.player_id, "border1");
				dojo.removeClass('playerDown_B_' + notif.args.player_id, "border1");
				dojo.removeClass('playerDown_C_' + notif.args.player_id, "border1");
			}
			
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
						
//						dojo.removeClass('playerDown_A_' + this.player_id, "border1");

					}
					if ( downArea === 'playerDown_B_' ) {
						console.log("[bmc] Adding to AREA B");
//						this.downArea_B_[ downPlayer ].addToStockWithId( cardUniqueId, card_id, 'overall_player_board_' + downPlayer );
						this.downArea_B_[ downPlayer ].addToStockWithId( cardUniqueId, card_ids[ card_id ], 'overall_player_board_' + downPlayer );
//						dojo.removeClass('playerDown_B_' + this.player_id, "border1");
					}
					if ( downArea === 'playerDown_C_' ) {
						console.log("[bmc] Adding to AREA C");
//						this.downArea_C_[ downPlayer ].addToStockWithId( cardUniqueId, card_id, 'overall_player_board_' + downPlayer );
						this.downArea_C_[ downPlayer ].addToStockWithId( cardUniqueId, card_ids[ card_id ], 'overall_player_board_' + downPlayer );
//						dojo.removeClass('playerDown_C_' + this.player_id, "border1");						
					}
/*
slideToObject
function( mobile_obj, target_obj, duration, delay )
Return an dojo.fx animation that is sliding a DOM object from its current position over another one
Animate a slide of the DOM object referred to by domNodeToSlide from its current position to the xpos, ypos relative to the object referred to by domNodeToSlideTo.
*/
					this.playerHand.removeFromStockById( card_ids[ card_id ]);
				}
			}
			this.sortBoard();
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

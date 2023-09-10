<?php

/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * LiverpoolRummy implementation : © Bryan Chase <bryanchase@yahoo.com>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * gameoptions.inc.php
 *
 * LiverpoolRummy game options description
 * 
 * In this file, you can define your game options (= game variants).
 *   
 * Note: If your game has no variant, you don't have to modify this file.
 *
 * Note²: All options defined in this file should have a corresponding "game state labels"
 *        with the same ID (see "initGameStateLabels" in liverpoolrummy.game.php)
 *
 * !! It is not a good idea to modify this file when a game is running !!
 *
 */

$game_options = array(
    
	100 => array(
		'name' => totranslate( 'Number of decks' ),
		'values' => array(
			2 => array( 'name' => totranslate( '2 decks' ) ),
			3 => array( 'name' => totranslate( '3 decks' ) ),
			4 => array( 'name' => totranslate( '4 decks' ) ),
			5 => array( 'name' => totranslate( '5 decks' ) )
		),
		'default' => 2
	),
	/*
	101 => array(
		'name' => totranslate( 'Buy timer (seconds)' ),
		'values' => array(
			0  => array ( 'name' => totranslate( 'Disable buy timer' )	),
			10 => array ( 'name' => totranslate( '10 seconds' )	),
			20 => array ( 'name' => totranslate( '20 seconds' ) ),
			30 => array ( 'name' => totranslate( '30 seconds' ) ),
			40 => array ( 'name' => totranslate( '40 seconds' ) ),
			50 => array ( 'name' => totranslate( '50 seconds' ) ),
		),
		'default' => 10
	),
	*/
	102 => array(
		'name' => totranslate( 'Game Length (Set and Run targets needed for each hand)' ),
		'values' => array(
			1  => array ( 'name' => totranslate( '1 hand (2 Sets)' ) ),
			2  => array ( 'name' => totranslate( '1 hand (1 Set & 1 Run)' ) ),
			3  => array ( 'name' => totranslate( '1 hand (2 Runs)' ) ),
			4  => array ( 'name' => totranslate( '1 hand (3 Sets)' ) ),
			5  => array ( 'name' => totranslate( '1 hand (2 Sets & 1 Run)' ) ),
			6  => array ( 'name' => totranslate( '1 hand (1 Set & 2 Runs)' ) ),
			7  => array ( 'name' => totranslate( '1 hand (3 Runs)' ) ),
			8  => array ( 'name' => totranslate( '2 hand game (2Sets, 2Runs)' ) ),
			9  => array ( 'name' => totranslate( '3 hand game (2Sets, 1Set2Runs, 3Runs)' ) ),
			10 => array ( 'name' => totranslate( 'Short game (2Sets, 2Runs, 2Sets1Run, 3Runs)' ) ),
			11 => array ( 'name' => totranslate( 'Full game (2Sets, 1Set1Run, 2Runs, 3Sets, 2Sets1Run, 2Run1Set, 3Runs)' )	),
			12 => array ( 'name' => totranslate( 'May I variant (2S, 1S1R, 2R, 3S, 2S1R, 2R1S, 3R & deal contract + 1)' ) )
		),
		'default' => 9
	),
	103 => array(
		'name' => totranslate( 'Liverpool consequence' ),
		'values' => array(
			0  => array ( 'name' => totranslate( 'Bonus to caller' )	),
			1  => array ( 'name' => totranslate( 'Penalty to discarder' ) )
		),
		'default' => 0
	),
	104 => array(
		'name' => totranslate( 'Maximum number of Jokers (normally 2x per deck)' ),
		'values' => array(
			0  => array ( 'name' => totranslate( '0 no jokers' )	),
			1  => array ( 'name' => totranslate( '1 joker' ) ),
			2  => array ( 'name' => totranslate( '2 jokers' ) ),
			3  => array ( 'name' => totranslate( '3 jokers' ) ),
			4  => array ( 'name' => totranslate( '4 jokers' ) ),
			5  => array ( 'name' => totranslate( '5 jokers' ) ),
			6  => array ( 'name' => totranslate( '6 jokers' ) ),
			7  => array ( 'name' => totranslate( '7 jokers' ) ),
			8  => array ( 'name' => totranslate( '8 jokers' ) ),
			9  => array ( 'name' => totranslate( '9 jokers' ) ),
			10 => array ( 'name' => totranslate( 'All (2x number of decks)' ) ),
		),
		'default' => 10
	),
	105 => array(
		'name' => totranslate( 'Number of buys' ),
		'values' => array(
			0  => array ( 'name' => totranslate( '3 per hand' )	),
			1  => array ( 'name' => totranslate( 'Unlimited' ) )
		),
		'default' => 0
	),
	106 => array(
		'name' => totranslate( 'Always Deal 11 cards' ),
		'values' => array(
			0  => array ( 'name' => totranslate( 'No' )	),
			1  => array ( 'name' => totranslate( 'Yes' ) )
		),
		'default' => 0
	),
	108 => array(
		'name' => totranslate( 'Enable wish list' ),
		'values' => array(
			0  => array ( 'name' => totranslate( 'No' )	),
			1  => array ( 'name' => totranslate( 'Yes' ) )
		),
		'default' => 0
	),
	109 => array(
		'name' => totranslate( 'Allow joker swapping' ),
		'values' => array(
			0  => array ( 'name' => totranslate( 'No' )	),
			1  => array ( 'name' => totranslate( 'Yes' ) )
		),
		'default' => 1
	),
	// 200 => array(
		// 'name' => totranslate( 'Game Speed' ),
		// 'values' => array(
			// 1 => array ( 'name' => totranslate( 'Full length game (2Sets, 1Set1Run, 2Runs, 3Sets, 2Sets1Run, 2Run1Set, 3Runs)' )	),
			// 2 => array ( 'name' => totranslate( 'Short game (2Sets, 2Runs, 2Sets1Run, 3Runs)' ) )
		// ),
	//)
	// ,
	// 103 => array(
		// 'name' => totranslate( 'Buy-winner chosen' ),
		// 'values' => array(
			// 1 => array ( 'name' => totranslate( 'Fastest buyer' )	),
			// 2 => array ( 'name' => totranslate( 'Seat order' ) )
		// ),
		// 'default' => 10
	// )
/*

// note: game variant ID should start at 100 (ie: 100, 101, 102, ...). The maximum is 199.
100 => array(
			'name' => totranslate('my game option'),    
			'values' => array(

						// A simple value for this option:
						1 => array( 'name' => totranslate('option 1') )

						// A simple value for this option.
						// If this value is chosen, the value of "tmdisplay" is displayed in the game lobby
						2 => array( 'name' => totranslate('option 2'), 'tmdisplay' => totranslate('option 2') ),

						// Another value, with other options:
						//  description => this text will be displayed underneath the option when this value is selected to explain what it does
						//  beta=true => this option is in beta version right now.
						//  nobeginner=true  =>  this option is not recommended for beginners
						3 => array( 'name' => totranslate('option 3'), 'description' => totranslate('this option does X'), 'beta' => true, 'nobeginner' => true )
					),
			'default' => 1
		),
*/
);

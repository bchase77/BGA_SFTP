<?php

/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * TutorialRumOne implementation : © Bryan Chase <bryanchase@yahoo.com>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * gameoptions.inc.php
 *
 * TutorialRumOne game options description
 * 
 * In this file, you can define your game options (= game variants).
 *   
 * Note: If your game has no variant, you don't have to modify this file.
 *
 * Note²: All options defined in this file should have a corresponding "game state labels"
 *        with the same ID (see "initGameStateLabels" in tutorialrumone.game.php)
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
		'name' => totranslate( 'Game Length (Sets and Runs targets needed for each hand)' ),
		'values' => array(
			1 => array ( 'name' => totranslate( 'Full length game (2Sets, 1Set1Run, 2Runs, 3Sets, 2Sets1Run, 2Run1Set, 3Runs)' )	),
			2 => array ( 'name' => totranslate( 'Short game (2Sets, 2Runs, 2Sets1Run, 3Runs)' ) )
		),
		'default' => 10
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
	)
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

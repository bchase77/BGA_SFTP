<?php
/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * MatRetDev implementation : © <Your name here> <Your email address here>
 * 
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * material.inc.php
 *
 * MatRetDev game material description
 *
 * Here, you can describe the material of your game with PHP variables.
 *   
 * This file is loaded in your game logic class constructor, ie these variables
 * are available everywhere in your game logic code.
 *
 */


/*

Example:

$this->card_types = array(
    1 => array( "card_name" => ...,
                ...
              )
);

*/

$this->gameType1 = array(
  0 => array( "Length" => "9 Rounds!", "qtyRounds" => 9 ),
  1 => array( "Length" => "6 Rounds!", "qtyRounds" => 6 ),
  2 => array( "Length" => "6 Rounds!", "qtyRounds" => 6 )
);

$this->gameType2 = array(
  0 => array( "Length" => "6 Rounds!", "qtyRounds" => 6 ),
  1 => array( "Length" => "3 Rounds!", "qtyRounds" => 3 ),
  2 => array( "Length" => "3 Rounds!", "qtyRounds" => 3 )
);

$this->wrestlers = array(
  0 => array(
	"Name"  => "Goldie Meadows",
	"ConR1" => 22,
	"ConR2" => 14,
	"ConR3" => 15,
	"Off"   => 10,
	"Def"   => 10,
	"Top"   => 10,
	"Bot"   => 10,
	"Token" => 7,
	"Star"  => 0, 	// 0 == None are available
	"TM"    => 1 ), // 1 == When opponent plays Star Card you can choose to convert it to +6 stat in that position and -3 Conditioning instead
	
  1 => array(
	"Name"  => "Frankie Boulay",
	"ConR1" => 25,
	"ConR2" => 0,
	"ConR3" => 0,
	"Off"   => 10,
	"Def"   => 10,
	"Top"   => 10,
	"Bot"   => 10,
	"Token" => 0,
	"Star"  => 99, 	// 99 == All are available
	"TM"    => 2 ), // 2 == All Star Cards cost 0 conditioning
);

// Not sure how to keep track of the trademarks, they vary alot in effect
//$this->trademarks = array(
//);

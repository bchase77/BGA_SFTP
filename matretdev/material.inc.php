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


// Delete this values_labels after I get the cards working:
$this->values_label = array(
    1 => clienttranslate('Ace'),
    2 => '2',
    3 => '3',
    4 => '4',
    5 => '5',
    6 => '6',
    7 => '7',
    8 => '8',
    9 => '9',
    10 => '10',
    11 => clienttranslate('Jack'),
    12 => clienttranslate('Queen'),
    13 => clienttranslate('King')
);










$this->wrestlerCards = array(
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

$this->offenseCards = array(
  0 => array(
	"Name"  => "Hand Fight",
	"MyCon" => -2,
	"MyTokens" => 1,
	"RollDie" => 'Blue',
	"SplEff" => 1,
	"BD_A" => 0,
	"BD_B" => 0,
	"BD_C" => 0,
	"BD_D" => 0,
	"BD_E" => -1,
	"BD_F" => -1,
	"BD_G" => -2,
	"BD_H" => -2,
	"RD_A" => 0,
	"RD_B" => 0,
	"RD_C" => 0,
	"RD_D" => 0,
	"RD_E" => 0,
	"RD_F" => 0,
	"RD_G" => 0,
	"RD_H" => 0,
	"OppAdjust" => "Cond",
	"Scoring" => 'No',
	"DrawScramble" => 'No'
    ),
  1 => array(
	"Name"  => "Fake Shot",
	"MyCon" => -2,
	"MyTokens" => 1,
	"RollDie" => 'Red',
	"SplEff" => 1,
	"BD_A" => 0,
	"BD_B" => 0,
	"BD_C" => 0,
	"BD_D" => 0,
	"BD_E" => 0,
	"BD_F" => 0,
	"BD_G" => 0,
	"BD_H" => 0,
	"RD_A" => 0,
	"RD_B" => 0,
	"RD_C" => 0,
	"RD_D" => 0,
	"RD_E" => 1,
	"RD_F" => 0,
	"RD_G" => 0,
	"RD_H" => 0,
	"OppAdjust" => "Stall",
	"Scoring" => 'No',
	"DrawScramble" => 'No'
    )
);

$this->defenseCards = array(
  0 => array(
	"Name"  => "Down Block",
	"MyCon" => -2,
	"MyTokens" => 1,
	"RollDie" => 'Blue',
	"SpecialEffect" => 0,
	"BD_A" => 0,
	"BD_B" => 0,
	"BD_C" => 0,
	"BD_D" => 0,
	"BD_E" => -1,
	"BD_F" => -1,
	"BD_G" => -2,
	"BD_H" => -2,
	"RD_A" => 0,
	"RD_B" => 0,
	"RD_C" => 0,
	"RD_D" => 0,
	"RD_E" => 1,
	"RD_F" => 0,
	"RD_G" => 0,
	"RD_H" => 0,
	"OppAdjust" => "Stall",
	"Scoring" => 'No',
	"DrawScramble" => 'No'
    ),
	
  1 => array(
	"Name"  => "Sprawl",
	"MyCon" => -3,
	"MyTokens" => 2,
	"RollDie" => 'Red',
	"SpecialEffect" => 1,
	"BD_A" => 0,
	"BD_B" => 0,
	"BD_C" => 0,
	"BD_D" => 0,
	"BD_E" => 0,
	"BD_F" => 0,
	"BD_G" => 0,
	"BD_H" => 0,
	"RD_A" => 0,
	"RD_B" => 0,
	"RD_C" => 0,
	"RD_D" => 0,
	"RD_E" => 1,
	"RD_F" => 0,
	"RD_G" => 0,
	"RD_H" => 0,
	"OppAdjust" => "None",
	"Scoring" => 'No',
	"DrawScramble" => 'No'
    )
);

// Not sure how to keep track of the trademarks, they vary alot in effect
//$this->trademarks = array(
//);

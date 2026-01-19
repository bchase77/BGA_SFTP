<?php
/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * MatRev implementation : © <Your name here> <Your email address here>
 * 
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * material.inc.php
 *
 * MatRev game material description
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
	"Name"  => "None",
	"WID"   => 0,
	"ConR1" => 0,
	"ConR2" => 0,
	"ConR3" => 0,
	"Off"   => 0,
	"Def"   => 0,
	"Top"   => 0,
	"Bot"   => 0,
	"Token" => 0,
	"Star"  => 0,
	"TM"    => 0 ),

  1 => array(
	"Name"  => "Goldie Meadows",
	"WID"   => 1,
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
	
  2 => array(
	"Name"  => "Frankie Boulay",
	"WID"   => 2,
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

  3 => array(
	"Name"  => "Najat Abbas",
	"WID"   => 3,
	"ConR1" => 28,
	"ConR2" => 10,
	"ConR3" => 9,
	"Off"   => 9,
	"Def"   => 9,
	"Top"   => 9,
	"Bot"   => 9,
	"Token" => 4,
	"Star"  => array( "Headlock", "Spladle", "Splits", "Tilt", "Cradle", "Granby Roll", "Peterson Roll" ),
	"TM"    => 3 ), // 3 == All Cards cost 2 conditioning

  4 => array(
	"Name"  => "Raj Chandra",
	"WID"   => 4,
	"ConR1" => 328,
	"ConR2" => 20,
	"ConR3" => 12,
	"Off"   => 9,
	"Def"   => 3,
	"Top"   => 9,
	"Bot"   => 6,
	"Token" => 3,
	"Star"  => array( "Headlock", "Chin Whip", "Granby Roll", "Peterson Roll" ),
	"TM"    => 4 ), // 4 == Can collect up to 10 tokens
);

$this->offenseCards = array(
  0 => array(
	"Name"  => "Hand Fight",
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
	"SpecialEffect" => 0,
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
	"SpecialEffect" => 0,
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
	"ifOppPlays" => array( "Ankle Pick", "Single Leg", "High Crotch" ),
	"OppAdjust" => "None",
	"Scoring" => 'No',
	"DrawScramble" => 'No'
    )
);

$this->topCards = array(
  0 => array(
	"Name"  => "Break Down",
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
	"OppAdjust" => "Cond",
	"Scoring" => 'No',
	"DrawScramble" => 'No'
    ),
	
  1 => array(
	"Name"  => "Mat Return",
	"MyCon" => -3,
	"MyTokens" => 2,
	"RollDie" => 'Red',
	"SpecialEffect" => 0,
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
	"ifOppPlays" => array( "Stand Up", "Power Stand Up", "Sit Out" ),
	"OppAdjust" => "None",
	"Scoring" => 'No',
	"DrawScramble" => 'No'
    )
);

$this->bottomCards = array(
  0 => array(
	"Name"  => "Hold Your Base",
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
	"OppAdjust" => "Cond",
	"Scoring" => 'No',
	"DrawScramble" => 'No'
    ),
	
  1 => array(
	"Name"  => "Base Up",
	"MyCon" => -3,
	"MyTokens" => 2,
	"RollDie" => 'Red',
	"SpecialEffect" => 0,
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
	"ifOppPlays" => array( "Stand Up", "Power Stand Up", "Sit Out" ),
	"OppAdjust" => "None",
	"Scoring" => 'No',
	"DrawScramble" => 'No'
    )
);

$this->scrambleCards = array(
  1 => array(
	"Name"  => "One",
	"Players" => 2,
	"Repeat" => 5,
	"Die4" => 1,
	"Die6" => 1,
	"Die8" => 1,
	"Die10" => 1,
	"Die12" => 1,
	"Die20" => 0,
	"WinCond1" => "Win",
	"WinCond2" => "na",
	"BurstCond1" => "5+", // 5 or more
	"BurstCond2" => "na",
	"LoseCond1" => "Lose",
	"LoseCond2" => "na",
	"Tie" => "Opp"
  ),
  7 => array(
	"Name"  => "Seven",
	"Players" => 2,
	"Repeat" => 7,
	"Die4" => 0,
	"Die6" => 0,
	"Die8" => 0,
	"Die10" => 0,
	"Die12" => 0,
	"Die20" => 1,
	"WinCond1" => 4,
	"WinCond2" => "na",
	"BurstCond1" => 5, 
	"BurstCond2" => "PlayerGuess",
	"LoseCond1" => "3-",
	"LoseCond2" => "OppGuess",
	"Tie" => "Opp"
  ),
  8 => array(
	"Name"  => "Eight",
	"Players" => 1,
	"Repeat" => 5,
	"Die4" => 0,
	"Die6" => 0,
	"Die8" => 2,
	"Die10" => 0,
	"Die12" => 0,
	"Die20" => 0,
	"WinCond1" => "5-", // 5 or less
	"WinCond2" => "na",
	"BurstCond1" => "3-", // 3 or less
	"BurstCond2" => "na",
	"LoseCond1" => 0, // Fail to roll doubles in 5 rounds
	"LoseCond2" => "na",
	"Tie" => "na"
  ),
  9 => array(
	"Name"  => "Nine",
	"Players" => 1,
	"Repeat" => 5,
	"Die4" => 0,
	"Die6" => 0,
	"Die8" => 0,
	"Die10" => 0,
	"Die12" => 5,
	"Die20" => 0,
	"WinCond1" => 33, // 33, 34, 35 or 36
	"WinCond2" => 36,
	"Burst" => "37+", // 37 or more
	"Lose" => "32-", // 32 or less
	"Tie" => "na"
  ),
);

// Not sure how to keep track of the trademarks, they vary alot in effect
//$this->trademarks = array(
//);

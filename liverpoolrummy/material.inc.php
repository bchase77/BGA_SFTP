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
 * material.inc.php
 *
 * LiverpoolRummy game material description
 *
 * Here, you can describe the material of your game with PHP variables.
 *   
 * This file is loaded in your game logic class constructor, ie these variables
 * are available everywhere in your game logic code.
 *
 */

$this->colors = array(
    1 => array( 'name' => clienttranslate('club'),
                'nametr' => self::_('club') ),
    2 => array( 'name' => clienttranslate('spade'),
                'nametr' => self::_('spade') ),
    3 => array( 'name' => clienttranslate('heart'),
                'nametr' => self::_('heart') ),
    4 => array( 'name' => clienttranslate('diamond'),
                'nametr' => self::_('diamond') ),
	5 => array( 'name' => clienttranslate('joker'),
                'nametr' => self::_('wild') )
);

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

$target_translated = self::_("Target"); 
$hand0MI_translated = self::_("2 Sets; No more no less (Set is 3 any suit of same value)"); 
$hand1MI_translated = self::_("1 Set and 1 Run; No more no less (Set is 3 any suit of same value; Run is 4 in sequence of same suit. Ace can play as high or low.)");
$hand2MI_translated = self::_("2 Runs; No more no less (Run is 4 in sequence of same suit. Ace can play as high or low.)"); 
$hand3MI_translated = self::_("3 Sets; No more no less (Set is 3 any suit of same value)"); 
$hand4MI_translated = self::_("2 Sets and 1 Run; No more no less (Set is 3 any suit of same value; Run is 4 in sequence of same suit. Ace can play as high or low.)"); 
$hand5MI_translated = self::_("1 Set and 2 Runs; No more no less (Set is 3 any suit of same value; Run is 4 in sequence of same suit. Ace can play as high or low.)"); 
$hand6MI_translated = self::_("3 Runs; In a 2-player game you must buy your own discard! (Run is 4 in sequence of same suit. Ace is high or low.)"); 

$this->handTypesMayI = array( // Hand targets associate with the down areas:
  0 => array( $target_translated => $hand0MI_translated, "QtySets" => 2, "QtyRuns" => 0, "deal" => 7 ),
  1 => array( $target_translated => $hand1MI_translated, "QtySets" => 1, "QtyRuns" => 1, "deal" => 8 ),
  2 => array( $target_translated => $hand2MI_translated, "QtySets" => 0, "QtyRuns" => 2, "deal" => 9 ),
  3 => array( $target_translated => $hand3MI_translated, "QtySets" => 3, "QtyRuns" => 0, "deal" => 10 ),
  4 => array( $target_translated => $hand4MI_translated, "QtySets" => 2, "QtyRuns" => 1, "deal" => 11 ),
  5 => array( $target_translated => $hand5MI_translated, "QtySets" => 1, "QtyRuns" => 2, "deal" => 12 ),
  6 => array( $target_translated => $hand6MI_translated, "QtySets" => 0, "QtyRuns" => 3, "deal" => 13 )
);

/*
$this->handTypesMayI = array( // Hand targets associate with the down areas:
  0 => array( "Target" => "2 Sets; No more no less (Set is 3 any suit of same value)",
	"QtySets" => 2, "QtyRuns" => 0, "deal" => 7 ),
  1 => array( "Target" => "1 Set and 1 Run; No more no less (Set is 3 any suit of same value; Run is 4 in sequence of same suit. Ace can play as high or low.)",
	"QtySets" => 1, "QtyRuns" => 1, "deal" => 8 ),
  2 => array( "Target" => "2 Runs; No more no less (Run is 4 in sequence of same suit. Ace can play as high or low.)",
	"QtySets" => 0, "QtyRuns" => 2, "deal" => 9 ),
  3 => array( "Target" => "3 Sets; No more no less (Set is 3 any suit of same value)",
	"QtySets" => 3, "QtyRuns" => 0, "deal" => 10 ),
  4 => array( "Target" => "2 Sets and 1 Run; No more no less (Set is 3 any suit of same value; Run is 4 in sequence of same suit. Ace can play as high or low.)",
	"QtySets" => 2, "QtyRuns" => 1, "deal" => 11 ),
  5 => array( "Target" => "1 Set and 2 Runs; No more no less (Set is 3 any suit of same value; Run is 4 in sequence of same suit. Ace can play as high or low.)",
	"QtySets" => 1, "QtyRuns" => 2, "deal" => 12 ),
  6 => array( "Target" => "3 Runs; In a 2-player game you must buy your own discard! (Run is 4 in sequence of same suit. Ace is high or low.)",
	"QtySets" => 0, "QtyRuns" => 3, "deal" => 13 )
);
*/

$this->handTypesFull = array( // Hand targets associate with the down areas:
  0 => array( "Target" => "2 Sets; No more no less (Set is 3 any suit of same value)",
	"QtySets" => 2, "QtyRuns" => 0, "deal" => 10 ),
  1 => array( "Target" => "1 Set and 1 Run; No more no less (Set is 3 any suit of same value; Run is 4 in sequence of same suit. Ace can play as high or low.)",
	"QtySets" => 1, "QtyRuns" => 1, "deal" => 11 ),
  2 => array( "Target" => "2 Runs; No more no less (Run is 4 in sequence of same suit. Ace can play as high or low.)",
	"QtySets" => 0, "QtyRuns" => 2, "deal" => 11 ),
  3 => array( "Target" => "3 Sets; No more no less (Set is 3 any suit of same value)",
	"QtySets" => 3, "QtyRuns" => 0, "deal" => 12 ),
  4 => array( "Target" => "2 Sets and 1 Run; No more no less (Set is 3 any suit of same value; Run is 4 in sequence of same suit. Ace can play as high or low.)",
	"QtySets" => 2, "QtyRuns" => 1, "deal" => 12 ),
  5 => array( "Target" => "1 Set and 2 Runs; No more no less (Set is 3 any suit of same value; Run is 4 in sequence of same suit. Ace can play as high or low.)",
	"QtySets" => 1, "QtyRuns" => 2, "deal" => 12 ),
  6 => array( "Target" => "3 Runs; No more no less (Run is 4 in sequence of same suit. Ace can play as high or low.)",
	"QtySets" => 0, "QtyRuns" => 3, "deal" => 12 )
);

$this->handTypes2S0R = array( // Hand targets associate with the down areas:
  0 => $this->handTypesFull[0]
);

$this->handTypes1S1R = array( // Hand targets associate with the down areas:
  0 => $this->handTypesFull[1]
);

$this->handTypes0S2R = array( // Hand targets associate with the down areas:
  0 => $this->handTypesFull[2]
);

$this->handTypes3S0R = array( // Hand targets associate with the down areas:
  0 => $this->handTypesFull[3]
);

$this->handTypes2S1R = array( // Hand targets associate with the down areas:
  0 => $this->handTypesFull[4]
);

$this->handTypes1S2R = array( // Hand targets associate with the down areas:
  0 => $this->handTypesFull[5]
);

$this->handTypes0S3R = array( // Hand targets associate with the down areas:
  0 => $this->handTypesFull[6]
);

$this->handTypesTwo = array( // Hand targets associate with the down areas:
  0 => $this->handTypesFull[0],
  1 => $this->handTypesFull[2]
);
$this->handTypesThree = array( // Hand targets associate with the down areas:
  0 => $this->handTypesFull[0],
  1 => $this->handTypesFull[5],
  2 => $this->handTypesFull[6]
);

$this->handTypesShort = array( // Hand targets associate with the down areas:
  0 => $this->handTypesFull[0],
  1 => $this->handTypesFull[2],
  2 => $this->handTypesFull[4],
  3 => $this->handTypesFull[6]
);

// $this->handTypesShort = array( // Hand targets associate with the down areas:
  // 0 => array( "Target" => "2 Sets; No more no less (Set is 3 of same value)",
	// "QtySets" => 2, "QtyRuns" => 0, "deal" => 10 ),
  // 1 => array( "Target" => "2 Runs; No more no less (Run is 4 in sequence of same suit)",
	// "QtySets" => 0, "QtyRuns" => 2, "deal" => 11 ),
  // 2 => array( "Target" => "2 Sets and 1 Run; No more no less (Set is 3 of same value; Run is 4 in sequence of same suit)",
	// "QtySets" => 2, "QtyRuns" => 1, "deal" => 12 ),
  // 3 => array( "Target" => "3 Runs; No more no less (Run is 4 in sequence of same suit)",
	// "QtySets" => 0, "QtyRuns" => 3, "deal" => 12 )
// );
// $this->handTypesThree = array( // Hand targets associate with the down areas:
  // 0 => array( "Target" => "2 Sets; No more no less (Set is 3 of same value)",
	// "QtySets" => 2, "QtyRuns" => 0, "deal" => 10 ),
  // 1 => array( "Target" => "1 Set and 2 Runs; No more no less (Set is 3 of same value; Run is 4 in sequence of same suit)",
	// "QtySets" => 1, "QtyRuns" => 2, "deal" => 12 ),
  // 2 => array( "Target" => "3 Runs; No more no less (Run is 4 in sequence of same suit)",
	// "QtySets" => 0, "QtyRuns" => 3, "deal" => 12 )
// );
// $this->handTypesTwo = array( // Hand targets associate with the down areas:
  // 0 => array( "Target" => "2 Sets; No more no less (Set is 3 of same value)",
	// "QtySets" => 2, "QtyRuns" => 0, "deal" => 10 ),
  // 1 => array( "Target" => "2 Runs; No more no less (Run is 4 in sequence of same suit)",
	// "QtySets" => 0, "QtyRuns" => 2, "deal" => 11 )
// );

// $this->setsRunsShort = array ( // Places in the downArea where the cards should go, per hand
 // 0 => array( "Area_A", "Area_B", "None",   "None",   "None",   "None" ),
 // 2 => array( "None",   "None",   "None",   "Area_A", "Area_B", "None" ),
 // 4 => array( "Area_A", "Area_B", "None",   "Area_C", "None",   "None" ),
 // 6 => array( "None",   "None",   "None",   "Area_A", "Area_B", "Area_C" )
// );

// $this->setsRunsTwo = array ( // Places in the downArea where the cards should go, per hand
 // 0 => array( "Area_A", "Area_B", "None",   "None",   "None",   "None" ),
 // 1 => array( "None",   "None",   "None",   "Area_A", "Area_B", "None" )
// );

// $this->setsRunsOne = array ( // Places in the downArea where the cards should go, per hand
 // 0 => array( "Area_A", "Area_B", "None",   "None",   "None",   "None" )
// );

// $this->setsRunsFull = array ( // Places in the downArea where the cards should go, per hand
 // 0 => array( "Area_A", "Area_B", "None",   "None",   "None",   "None" ),
 // 1 => array( "Area_A", "None",   "None",   "Area_B", "None",   "None" ),
 // 2 => array( "None",   "None",   "None",   "Area_A", "Area_B", "None" ),
 // 3 => array( "Area_A", "Area_B", "Area_C", "None",   "None",   "None" ),
 // 4 => array( "Area_A", "Area_B", "None",   "Area_C", "None",   "None" ),
 // 5 => array( "Area_A", "None",   "None",   "Area_B", "Area_C", "None" ),
 // 6 => array( "None",   "None",   "None",   "Area_A", "Area_B", "Area_C" )
// );
/*
$this->handTypesShort = array( // Hand targets associate with the down areas:
  0 => array( "Target" => "2 Sets; No more no less (Set is 3 of same value; Run is 4 in sequence of same suit)",
	"QtySets" => 2, "QtyRuns" => 0, "deal" => 10, "Area_A" => "Set", "Area_B" => "Set", "Area_C" => "Empty" ),
  // 2 => array( "Target" => "2 Runs; No more no less (Set is 3 of same value; Run is 4 in sequence of same suit)",
	// "QtySets" => 0, "QtyRuns" => 2, "deal" => 10, "Area_A" => "Run", "Area_B" => "Run", "Area_C" => "Empty" ),
  // 4 => array( "Target" => "2 Sets and 1 Run; No more no less (Set is 3 of same value; Run is 4 in sequence of same suit)",
	// "QtySets" => 2, "QtyRuns" => 1, "deal" => 12, "Area_A" => "Set", "Area_B" => "Set", "Area_C" => "Run" ),
  // 6 => array( "Target" => "3 Runs; No more no less (Set is 3 of same value; Run is 4 in sequence of same suit)",
	// "QtySets" => 0, "QtyRuns" => 3, "deal" => 12, "Area_A" => "Run", "Area_B" => "Run", "Area_C" => "Run" )
);

$this->setsRunsShort = array ( // Places in the downArea where the cards should go, per hand
 0 => array( "Area_A", "Area_B", "None",   "None",   "None",   "None" ),
 // 2 => array( "None",   "None",   "None",   "Area_A", "Area_B", "None" ),
 // 4 => array( "Area_A", "Area_B", "None",   "Area_C", "None",   "None" ),
 // 6 => array( "None",   "None",   "None",   "Area_A", "Area_B", "Area_C" )
);
*/


/*
  0 => array( "m2 Sets", 2, 0),
  1 => array( "m1 Set and 1 Run", 1, 1),
  2 => array( "m2 Runs", 0, 2),
  3 => array( "m3 Sets", 3, 0),
  4 => array( "m2 Sets and 1 Run", 2, 1),
  5 => array( "m1 Set and 2 Runs", 1, 2),
  6 => array( "m3 Runs", 0, 3)

*/



/*

Example:

$this->card_types = array(
    1 => array( "card_name" => ...,
                ...
              )
);

*/


/*
$bob = array(2) {
	[0]=> array(7) {
		["Target"]=>string(83) "2 Sets; No more no less (Set is 3 of same value; Run is 4 in sequence of same suit)"
		["QtySets"]=>int(2)
		["QtyRuns"]=>int(0)
		["deal"]=>int(10)
		["Area_A"]=>string(3) "Set"
		["Area_B"]=>string(3) "Set"
		["Area_C"]=>string(5) "Empty"
	}
	[1]=>array(7) {
		["Target"]=>string(83) "2 Runs; No more no less (Set is 3 of same value; Run is 4 in sequence of same suit)"
		["QtySets"]=>int(0)
		["QtyRuns"]=>int(2)
		["deal"]=>int(10)
		["Area_A"]=>string(3) "Run"
		["Area_B"]=>string(3) "Run"
		["Area_C"]=>string(5) "Empty"
	}
}
*/
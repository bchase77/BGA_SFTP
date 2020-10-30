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
 * material.inc.php
 *
 * TutorialRumOne game material description
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

$this->handTypesFull = array( // Hand targets associate with the down areas:
  0 => array( "Target" => "2 Sets; No more no less",           "QtySets" => 2, "QtyRuns" => 0, "Area_A" => "Set", "Area_B" => "Set", "Area_C" => "Empty" ),
  1 => array( "Target" => "1 Set and 1 Run; No more no less",  "QtySets" => 1, "QtyRuns" => 1, "Area_A" => "Set", "Area_B" => "Run", "Area_C" => "Empty" ),
  2 => array( "Target" => "2 Runs; No more no less",           "QtySets" => 0, "QtyRuns" => 2, "Area_A" => "Run", "Area_B" => "Run", "Area_C" => "Empty" ),
  3 => array( "Target" => "3 Sets; No more no less",           "QtySets" => 3, "QtyRuns" => 0, "Area_A" => "Set", "Area_B" => "Set", "Area_C" => "Set" ),
  4 => array( "Target" => "2 Sets and 1 Run; No more no less", "QtySets" => 2, "QtyRuns" => 1, "Area_A" => "Set", "Area_B" => "Set", "Area_C" => "Run" ),
  5 => array( "Target" => "1 Set and 2 Runs; No more no less", "QtySets" => 1, "QtyRuns" => 2, "Area_A" => "Set", "Area_B" => "Run", "Area_C" => "Run" ),
  6 => array( "Target" => "3 Runs; No more no less",           "QtySets" => 0, "QtyRuns" => 3, "Area_A" => "Run", "Area_B" => "Run", "Area_C" => "Run" )
);

$this->setsRunsFull = array ( // Places in the downArea where the cards should go, per hand
 0 => array( "Area_A", "Area_B", "None",   "None",   "None",   "None" ),
 1 => array( "Area_A", "None",   "None",   "Area_B", "None",   "None" ),
 2 => array( "None",   "None",   "None",   "Area_A", "Area_B", "None" ),
 3 => array( "Area_A", "Area_B", "Area_C", "None",   "None",   "None" ),
 4 => array( "Area_A", "Area_B", "None",   "Area_C", "None",   "None" ),
 5 => array( "Area_A", "None",   "None",   "Area_B", "Area_C", "None" ),
 6 => array( "None",   "None",   "None",   "Area_A", "Area_B", "Area_C" )
);

$this->handTypesShort = array( // Hand targets associate with the down areas:
  0 => array( "Target" => "2 Sets; No more no less",           "QtySets" => 2, "QtyRuns" => 0, "Area_A" => "Set", "Area_B" => "Set", "Area_C" => "Empty" ),
  2 => array( "Target" => "2 Runs; No more no less",           "QtySets" => 0, "QtyRuns" => 2, "Area_A" => "Run", "Area_B" => "Run", "Area_C" => "Empty" ),
  4 => array( "Target" => "2 Sets and 1 Run; No more no less", "QtySets" => 2, "QtyRuns" => 1, "Area_A" => "Set", "Area_B" => "Set", "Area_C" => "Run" ),
  6 => array( "Target" => "3 Runs; No more no less",           "QtySets" => 0, "QtyRuns" => 3, "Area_A" => "Run", "Area_B" => "Run", "Area_C" => "Run" )
);

$this->setsRunsShort = array ( // Places in the downArea where the cards should go, per hand
 0 => array( "Area_A", "Area_B", "None",   "None",   "None",   "None" ),
 2 => array( "None",   "None",   "None",   "Area_A", "Area_B", "None" ),
 4 => array( "Area_A", "Area_B", "None",   "Area_C", "None",   "None" ),
 6 => array( "None",   "None",   "None",   "Area_A", "Area_B", "Area_C" )
);

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

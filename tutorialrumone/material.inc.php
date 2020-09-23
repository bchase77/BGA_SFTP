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

$this->handTypes = array(
  0 => "2 Sets",
  1 => "1 Set and 1 Run",
  2 => "2 Runs",
  3 => "3 Sets",
  4 => "2 Sets and 1 Run",
  5 => "1 Set and 2 Runs",
  6 => "3 Runs"
);


/*

Example:

$this->card_types = array(
    1 => array( "card_name" => ...,
                ...
              )
);

*/

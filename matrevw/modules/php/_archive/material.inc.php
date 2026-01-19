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

return [
	'wrestlerCards' => [
		1 => [
			'name' => 'Goldie Meadows',
			'trademark' => 'Opponent Star Play',
			'starting_stats' => [
				'conditioning' => [2, 2, 1],
				'offense' => 3,
				'defense' => 2,
				'top' => 1,
				'bottom' => 1,
				'tokens' => 0,
			],
			'special_moves' => [],
		],
		2 => [
			'name' => 'Frankie Boulay',
			'trademark' => 'Star Cards 0',
			'starting_stats' => [
				'conditioning' => [1, 2, 2],
				'offense' => 2,
				'defense' => 3,
				'top' => 1,
				'bottom' => 1,
				'tokens' => 0,
			],
			'special_moves' => [],
		],
	]
];

<?php
/**
 *------
 * BGA framework: Gregory Isabelli & Emmanuel Colin & BoardGameArena
 * MatRevW implementation :© Mike & Jack McKeever and Bryan Chase bryanchase@yahoo.com
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * states.inc.php
 *
 * MatRevW game states description
 */

$machinestates = [

    // The initial state. Please do not modify.
    1 => [
        "name" => "gameSetup",
        "description" => "",
        "type" => "manager",
        "action" => "stGameSetup",
        "transitions" => ["next" => 2]
    ],

    // Wrestler selection phase
    2 => [
        "name" => "deckSetup",
        "description" => clienttranslate('Setting up the game'),
        "descriptionmyturn" => clienttranslate('Setting up the game'),
        "type" => "game",
        "action" => "stDeckSetup",
        "transitions" => ["next" => 20]
    ],
    
    20 => [
        'name' => 'playerChooseWrestler',
        'description' => clienttranslate('Players must choose their wrestlers'),
        'descriptionmyturn' => clienttranslate('${you} must choose a wrestler'),
        'type' => 'multipleactiveplayer',
        'possibleactions' => ['chooseWrestler'],
        'transitions' => ['next' => 21, 'done' => 25],
    ],    
    
    21 => [
        'name' => 'nextPlayer',
        'description' => '',
        'type' => 'game',
        'action' => 'stNextPlayer',
        'transitions' => ['next' => 20],
    ],
    
    // Match initialization
    25 => [
        'name' => 'matchStart',
        'description' => clienttranslate('Starting the wrestling match'),
        'type' => 'game',
        'action' => 'stMatchStart',
        'transitions' => ['startMatch' => 30],
    ],

    // Main gameplay - Player turns
    30 => [
        'name' => 'playerTurn',
        'description' => clienttranslate('${actplayer} must play a card'),
        'descriptionmyturn' => clienttranslate('${you} must choose a move'),
        'type' => 'activeplayer',
        'args' => 'argPlayerTurn',
        'possibleactions' => ['playCard', 'stall'],
        'transitions' => [
            'cardPlayed' => 35,
            'stall' => 35,
            'zombiePass' => 35
        ],
    ],

    // Resolve round after both players play
    35 => [
        'name' => 'resolveRound',
        'description' => clienttranslate('Resolving the wrestling round'),
        'type' => 'game', 
        'action' => 'stResolveRound',
        'transitions' => [
            'nextRound' => 30,
            'scramble' => 40,
            'pinAttempt' => 50,
            'endPeriod' => 60,
            'endMatch' => 99
        ],
    ],

    // Scramble card resolution
    40 => [
        'name' => 'scrambleCard',
        'description' => clienttranslate('${actplayer} must resolve scramble card'),
        'descriptionmyturn' => clienttranslate('${you} must play the scramble game'),
        'type' => 'activeplayer',
        'args' => 'argScrambleCard',
        'possibleactions' => ['resolveScramble'],
        'transitions' => [
            'score' => 45,
            'noScore' => 35,
            'pin' => 50
        ],
    ],

    // Score and position change
    45 => [
        'name' => 'scorePoints',
        'description' => clienttranslate('Updating score and positions'),
        'type' => 'game',
        'action' => 'stScorePoints',
        'transitions' => [
            'continue' => 30,
            'techFall' => 99,
            'endPeriod' => 60,
            'endMatch' => 99
        ],
    ],

    // Pin attempt sequence
    50 => [
        'name' => 'pinAttempt',
        'description' => clienttranslate('${actplayer} is attempting a pin'),
        'descriptionmyturn' => clienttranslate('${you} are attempting a pin'),
        'type' => 'activeplayer',
        'args' => 'argPinAttempt',
        'possibleactions' => ['resolvePinRound'],
        'transitions' => [
            'pinSuccess' => 99,
            'pinContinue' => 50,
            'pinEscape' => 35
        ],
    ],

    // End of period
    60 => [
        'name' => 'endPeriod',
        'description' => clienttranslate('End of period ${period_number}'),
        'type' => 'game',
        'action' => 'stEndPeriod',
        'transitions' => [
            'nextPeriod' => 65,
            'overtime' => 70,
            'endMatch' => 99
        ],
    ],

    // Period break and position choice
    65 => [
        'name' => 'periodBreak',
        'description' => clienttranslate('${actplayer} chooses starting position for next period'),
        'descriptionmyturn' => clienttranslate('${you} choose starting position'),
        'type' => 'activeplayer',
        'args' => 'argPeriodBreak',
        'possibleactions' => ['choosePosition'],
        'transitions' => ['continue' => 30],
    ],

    // Overtime
    70 => [
        'name' => 'overtime',
        'description' => clienttranslate('Starting overtime - first to score wins!'),
        'type' => 'game',
        'action' => 'stOvertime',
        'transitions' => ['continue' => 30],
    ],
    
    // Final state.
    99 => [
        "name" => "gameEnd",
        "description" => clienttranslate("End of game"),
        "type" => "manager",
        "action" => "stGameEnd",
        "args" => "argGameEnd"
    ],
];
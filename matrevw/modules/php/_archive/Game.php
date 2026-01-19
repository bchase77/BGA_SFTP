<?php
/**
 *------
 * BGA framework: Gregory Isabelli & Emmanuel Colin & BoardGameArena
 * MatRevW implementation : © Mike & Jack McKeever and Bryan Chase bryanchase@yahoo.com
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * Game.php
 *
 * This is the main file for your game logic.
 */
declare(strict_types=1);

namespace Bga\Games\MatRevW;

require_once(APP_GAMEMODULE_PATH . "module/table/table.game.php");

class Game extends \Table
{
    private array $wrestlerCards;
    private array $basicMoveCards;
    private array $specialMoveCards;
    private array $scrambleCards;
    private array $gameConstants;

    public function __construct()
    {
        parent::__construct();

        // Load material data
        $material = include(__DIR__ . '/material.inc.php');
        $this->wrestlerCards = $material['wrestlerCards'];
        $this->basicMoveCards = $material['basicMoveCards'];
        $this->specialMoveCards = $material['specialMoveCards'];
        $this->scrambleCards = $material['scrambleCards'];
        $this->gameConstants = $material['gameConstants'];

        $this->initGameStateLabels([
            "current_period" => 10,
            "current_round" => 11,
            "gameLengthOption" => 19,
            "wrestlerSelectionDone" => 20,
            "active_player_position" => 21,
            "defending_player_position" => 22,
            "match_momentum_player" => 23,
        ]);        
    }

    /**
     * Player action for choosing a wrestler
     */
    public function actChooseWrestler(int $wrestlerId): void
    {
        self::checkAction('chooseWrestler');

        $playerId = (int)self::getActivePlayerId();

        $card = $this->wrestlerCards[$wrestlerId] ?? null;
        if ($card === null) {
            throw new \BgaUserException("Invalid wrestler ID");
        }

        // Check if already chosen
        $taken = self::getUniqueValueFromDB("SELECT COUNT(*) FROM player WHERE wrestler = '" . self::escapeStringForDB($card['name']) . "'");
        if ($taken > 0) {
            throw new \BgaUserException("That wrestler is already taken");
        }

        // Initialize player stats based on wrestler (using existing columns)
        $stats = $card['starting_stats'];
        self::DbQuery("UPDATE player SET 
            wrestler = '" . self::escapeStringForDB($card['name']) . "'
            WHERE player_id = $playerId");

        // Create basic deck for this player
        $this->createBasicDeck($playerId);
        
        // Add special moves if any
        if (!empty($card['special_moves'])) {
            $this->addSpecialMoves($playerId, $card['special_moves']);
        }

        // Notify player(s)
        self::notifyAllPlayers('wrestlerChosen', clienttranslate('${player_name} chose ${wrestler}'), [
            'player_id' => $playerId,
            'player_name' => self::getActivePlayerName(),
            'wrestler' => $card['name'],
            'stats' => $stats,
        ]);

        // Check if all players have chosen
        $playersCount = self::getUniqueValueFromDB("SELECT COUNT(*) FROM player");
        $chosenCount = self::getUniqueValueFromDB("SELECT COUNT(*) FROM player WHERE wrestler IS NOT NULL");
        
        if ($chosenCount >= $playersCount) {
            // All players have chosen, move to next phase
            $this->gamestate->nextState('done');
        } else {
            // More players need to choose
            $this->gamestate->nextState('next');
        }
    }

    /**
     * Player action for playing a card
     */
    public function actPlayCard(int $cardId): void
    {
        self::checkAction('playCard');
        
        $playerId = (int)self::getActivePlayerId();
        
        // Validate card can be played
        $card = self::getObjectFromDB("SELECT * FROM cards WHERE card_id = $cardId AND player_id = $playerId AND card_location = 'hand'");
        if (!$card) {
            throw new \BgaUserException("Invalid card");
        }

        $currentPosition = self::getGameStateValue('active_player_position');
        $cardData = $this->getCardData($card['card_type'], $card['card_type_arg']);
        
        // Check if card can be played in current position
        if (!$this->canPlayCardInPosition($cardData, $currentPosition)) {
            throw new \BgaUserException("Cannot play this card in current position");
        }

        // Check conditioning and token requirements
        $playerData = self::getObjectFromDB("SELECT * FROM player WHERE player_id = $playerId");
        if ($playerData['current_conditioning'] < $cardData['conditioning_cost']) {
            // Force stall
            $this->forceStall($playerId);
            return;
        }

        if (isset($cardData['special_tokens_cost']) && $playerData['special_tokens'] < $cardData['special_tokens_cost']) {
            throw new \BgaUserException("Not enough special tokens");
        }

        // Play the card
        $this->playCard($playerId, $cardId, $cardData);
        
        $this->gamestate->nextState('cardPlayed');
    }

    /**
     * Player action for passing/stalling
     */
    public function actStall(): void
    {
        self::checkAction('stall');
        
        $playerId = (int)self::getActivePlayerId();
        $this->forceStall($playerId);
        
        $this->gamestate->nextState('stall');
    }

    /**
     * Create basic deck for player (simplified for initial implementation)
     */
    private function createBasicDeck(int $playerId): void
    {
        // For now, just log that deck was created
        // We'll implement the full card system once the basic structure works
        self::notifyAllPlayers('deckCreated', clienttranslate('Deck created for ${player_name}'), [
            'player_id' => $playerId,
            'player_name' => self::getPlayerNameById($playerId),
        ]);
    }

    /**
     * Add special moves to player deck (simplified for initial implementation)
     */
    private function addSpecialMoves(int $playerId, array $specialMoves): void
    {
        // For now, just log special moves
        if (!empty($specialMoves)) {
            self::notifyAllPlayers('specialMovesAdded', clienttranslate('${player_name} gets special moves: ${moves}'), [
                'player_id' => $playerId,
                'player_name' => self::getPlayerNameById($playerId),
                'moves' => implode(', ', $specialMoves),
            ]);
        }
    }

    /**
     * Get card data from material
     */
    private function getCardData(string $type, $typeArg): array
    {
        if ($type === 'basic') {
            return $this->basicMoveCards[$typeArg] ?? [];
        } elseif ($type === 'special') {
            return $this->specialMoveCards[$typeArg] ?? [];
        }
        return [];
    }

    /**
     * Check if card can be played in current position
     */
    private function canPlayCardInPosition(array $cardData, string $position): bool
    {
        if (!isset($cardData['position'])) return false;
        
        return $cardData['position'] === 'any' || $cardData['position'] === $position;
    }

    /**
     * Force a player to stall
     */
    private function forceStall(int $playerId): void
    {
        // Increment stall count
        $stallCount = self::getUniqueValueFromDB("SELECT stall_count FROM player WHERE player_id = $playerId");
        $newStallCount = $stallCount + 1;
        
        self::DbQuery("UPDATE player SET stall_count = $newStallCount WHERE player_id = $playerId");
        
        // Apply stall effects
        $this->applyStallPenalty($playerId, $newStallCount);
        
        // Add conditioning if playing stall card
        self::DbQuery("UPDATE player SET current_conditioning = current_conditioning + 5 WHERE player_id = $playerId");
        
        self::notifyAllPlayers('playerStalled', clienttranslate('${player_name} takes a stall call'), [
            'player_id' => $playerId,
            'player_name' => self::getPlayerNameById($playerId),
            'stall_count' => $newStallCount,
        ]);
    }

    /**
     * Apply stall penalty based on count
     */
    private function applyStallPenalty(int $playerId, int $stallCount): void
    {
        $penalties = $this->gameConstants['stall_penalties'];
        
        if (isset($penalties[$stallCount])) {
            $penalty = $penalties[$stallCount];
            
            if ($stallCount >= 5) {
                // Disqualification
                $this->eliminatePlayer($playerId);
            } elseif ($stallCount >= 2) {
                // Award points to opponent
                $points = ($stallCount === 4) ? 2 : 1;
                $opponentId = $this->getOpponentId($playerId);
                $this->addScore($opponentId, $points);
            }
        }
    }

    /**
     * Get opponent player ID
     */
    private function getOpponentId(int $playerId): int
    {
        return (int)self::getUniqueValueFromDB("SELECT player_id FROM player WHERE player_id != $playerId LIMIT 1");
    }

    /**
     * Add score to player
     */
    private function addScore(int $playerId, int $points): void
    {
        self::DbQuery("UPDATE player SET player_score = player_score + $points WHERE player_id = $playerId");
        
        self::notifyAllPlayers('scoreUpdate', '', [
            'player_id' => $playerId,
            'points' => $points,
            'new_score' => self::getUniqueValueFromDB("SELECT player_score FROM player WHERE player_id = $playerId"),
        ]);
    }

    /**
     * Game state action for deck setup
     */
    public function stDeckSetup(): void
    {
        // Make all players active for wrestler selection
        $this->gamestate->setAllPlayersMultiactive();
        $this->gamestate->nextState('next');
    }

    /**
     * Game state action for match start
     */
    public function stMatchStart(): void
    {
        // Determine who starts based on conditioning
        $players = self::getCollectionFromDB("SELECT player_id, current_conditioning FROM player ORDER BY current_conditioning DESC");
        $playerIds = array_keys($players);
        
        if (count($playerIds) >= 2) {
            $player1 = $players[$playerIds[0]];
            $player2 = $players[$playerIds[1]];
            
            if ($player1['current_conditioning'] > $player2['current_conditioning']) {
                $startingPlayer = $playerIds[0];
            } elseif ($player2['current_conditioning'] > $player1['current_conditioning']) {
                $startingPlayer = $playerIds[1];
            } else {
                // Tie - flip coin (random)
                $startingPlayer = $playerIds[random_int(0, 1)];
            }
            
            // Set initial position (starting player chooses offense/defense)
            self::setGameStateValue('active_player_position', 'offense');
            self::setGameStateValue('defending_player_position', 'defense');
            self::setGameStateValue('match_momentum_player', $startingPlayer);
            
            // Initialize match
            self::setGameStateValue('current_period', 1);
            self::setGameStateValue('current_round', 1);
            
            $this->gamestate->changeActivePlayer($startingPlayer);
        }
        
        $this->gamestate->nextState('startMatch');
    }

    /**
     * Game state action for next player
     */
    public function stNextPlayer(): void
    {
        $player_id = (int)$this->getActivePlayerId();
        $this->giveExtraTime($player_id);
        
        $this->activeNextPlayer();
        $this->gamestate->nextState("next");
    }

    public function argPlayerTurn(): array
    {
        $playerId = (int)$this->getActivePlayerId();
        $playerData = self::getObjectFromDB("SELECT * FROM player WHERE player_id = $playerId");
        $currentPosition = self::getGameStateValue('active_player_position');
        
        // Get playable cards from hand
        $hand = $this->cards->getCardsInLocation('hand', $playerId);
        $playableCards = [];
        
        foreach ($hand as $card) {
            $cardData = $this->getCardData($card['type'], $card['type_arg']);
            if ($this->canPlayCardInPosition($cardData, $currentPosition)) {
                // Check if player has resources to play card
                $canPlay = true;
                
                if ($cardData['conditioning_cost'] > $playerData['current_conditioning']) {
                    $canPlay = false;
                }
                
                if (isset($cardData['special_tokens_cost']) && 
                    $cardData['special_tokens_cost'] > $playerData['special_tokens']) {
                    $canPlay = false;
                }
                
                if ($canPlay) {
                    $playableCards[] = $card['id'];
                }
            }
        }
        
        return [
            'playableCards' => $playableCards,
            'currentPosition' => $currentPosition,
            'playerStats' => [
                'conditioning' => $playerData['current_conditioning'],
                'tokens' => $playerData['special_tokens'],
                'offense' => $playerData['current_offense'],
                'defense' => $playerData['current_defense'],
                'top' => $playerData['current_top'],
                'bottom' => $playerData['current_bottom'],
            ],
        ];
    }

    /**
     * Play a card and resolve its effects
     */
    private function playCard(int $playerId, int $cardId, array $cardData): void
    {
        // Move card to played pile
        $this->cards->moveCard($cardId, 'played', $playerId);
        
        // Deduct conditioning
        if ($cardData['conditioning_cost'] > 0) {
            self::DbQuery("UPDATE player SET current_conditioning = current_conditioning - " . 
                $cardData['conditioning_cost'] . " WHERE player_id = $playerId");
        }
        
        // Deduct special tokens if required
        if (isset($cardData['special_tokens_cost']) && $cardData['special_tokens_cost'] > 0) {
            self::DbQuery("UPDATE player SET special_tokens = special_tokens - " . 
                $cardData['special_tokens_cost'] . " WHERE player_id = $playerId");
        }
        
        // Apply card action
        $this->resolveCardAction($playerId, $cardData);
        
        // Award special tokens at end of turn
        if (isset($cardData['special_tokens']) && $cardData['special_tokens'] > 0) {
            self::DbQuery("UPDATE player SET special_tokens = special_tokens + " . 
                $cardData['special_tokens'] . " WHERE player_id = $playerId");
        }
        
        // Apply card effects
        if (isset($cardData['effect'])) {
            $this->resolveCardEffect($playerId, $cardData['effect']);
        }
        
        self::notifyAllPlayers('cardPlayed', clienttranslate('${player_name} plays ${card_name}'), [
            'player_id' => $playerId,
            'player_name' => self::getActivePlayerName(),
            'card_name' => $cardData['name'],
            'card_id' => $cardId,
        ]);
    }

    /**
     * Resolve card action (dice rolling, etc.)
     */
    private function resolveCardAction(int $playerId, array $cardData): void
    {
        if (strpos($cardData['action'], 'Roll') !== false) {
            $diceType = (strpos($cardData['action'], 'Speed') !== false) ? 'speed' : 'strength';
            $statType = null;
            
            if (strpos($cardData['action'], 'Offense') !== false) $statType = 'offense';
            elseif (strpos($cardData['action'], 'Defense') !== false) $statType = 'defense';
            elseif (strpos($cardData['action'], 'Top') !== false) $statType = 'top';
            elseif (strpos($cardData['action'], 'Bottom') !== false) $statType = 'bottom';
            
            if ($statType) {
                $diceResult = $this->rollDice($diceType);
                $this->applyStatChange($playerId, $statType, $diceResult, true); // temporary change
                
                self::notifyAllPlayers('diceRolled', clienttranslate('${player_name} rolls ${dice_result}'), [
                    'player_id' => $playerId,
                    'player_name' => self::getActivePlayerName(),
                    'dice_type' => $diceType,
                    'dice_result' => $diceResult,
                    'stat_type' => $statType,
                ]);
            }
        } elseif (strpos($cardData['action'], 'Conditioning') !== false) {
            // Handle conditioning gain
            $amount = (int)filter_var($cardData['action'], FILTER_SANITIZE_NUMBER_INT);
            if ($amount > 0) {
                self::DbQuery("UPDATE player SET current_conditioning = current_conditioning + $amount WHERE player_id = $playerId");
            }
        } elseif (strpos($cardData['action'], 'Scramble') !== false) {
            // Special card - automatically draw scramble
            $this->drawScrambleCard($playerId);
        }
    }

    /**
     * Roll dice based on type
     */
    private function rollDice(string $type): int
    {
        $dice = $this->gameConstants['dice'][$type];
        $index = random_int(0, count($dice) - 1);
        return $dice[$index];
    }

    /**
     * Apply stat change to player
     */
    private function applyStatChange(int $playerId, string $stat, int $change, bool $temporary = false): void
    {
        $column = $temporary ? "current_$stat" : "base_$stat";
        
        if ($change != 0) {
            $operator = $change > 0 ? '+' : '-';
            $absChange = abs($change);
            self::DbQuery("UPDATE player SET $column = $column $operator $absChange WHERE player_id = $playerId");
        }
    }

    /**
     * Draw and resolve scramble card
     */
    private function drawScrambleCard(int $playerId): void
    {
        // For now, simulate scramble card result
        $result = random_int(1, 3); // 1=fail, 2=partial, 3=success
        
        self::notifyAllPlayers('scrambleCard', clienttranslate('${player_name} draws a scramble card'), [
            'player_id' => $playerId,
            'player_name' => self::getActivePlayerName(),
            'result' => $result,
        ]);
        
        // TODO: Implement actual scramble game mechanics
    }

    /**
     * Resolve card effects
     */
    private function resolveCardEffect(int $playerId, string $effect): void
    {
        // Parse and apply effects
        if (strpos($effect, 'Apply +') !== false) {
            // Stat boost effects
            preg_match('/Apply ([+-]\d+) to your (\w+)/', $effect, $matches);
            if (count($matches) >= 3) {
                $change = (int)$matches[1];
                $stat = strtolower($matches[2]);
                $this->applyStatChange($playerId, $stat, $change, true);
            }
        } elseif (strpos($effect, 'Apply -') !== false && strpos($effect, 'Opponent') !== false) {
            // Opponent stat reduction
            preg_match('/Apply ([+-]\d+) to Opponent\'s (\w+)/', $effect, $matches);
            if (count($matches) >= 3) {
                $change = (int)$matches[1];
                $stat = strtolower($matches[2]);
                $opponentId = $this->getOpponentId($playerId);
                $this->applyStatChange($opponentId, $stat, $change, true);
            }
        } elseif (strpos($effect, 'steal') !== false) {
            // Token stealing effects
            $opponentId = $this->getOpponentId($playerId);
            $opponentTokens = self::getUniqueValueFromDB("SELECT special_tokens FROM player WHERE player_id = $opponentId");
            
            if ($opponentTokens > 0) {
                self::DbQuery("UPDATE player SET special_tokens = special_tokens - 1 WHERE player_id = $opponentId");
                self::DbQuery("UPDATE player SET special_tokens = special_tokens + 1 WHERE player_id = $playerId");
                
                self::notifyAllPlayers('tokenStolen', clienttranslate('${player_name} steals a token'), [
                    'player_id' => $playerId,
                    'player_name' => self::getActivePlayerName(),
                ]);
            }
        }
    }

    /**
     * Check for low conditioning penalty
     */
    private function checkConditioningPenalty(int $playerId): void
    {
        $conditioning = self::getUniqueValueFromDB("SELECT current_conditioning FROM player WHERE player_id = $playerId");
        
        if ($conditioning < 10) {
            // Apply permanent stat reduction
            self::DbQuery("UPDATE player SET 
                base_offense = GREATEST(0, base_offense - 1),
                base_defense = GREATEST(0, base_defense - 1),
                base_top = GREATEST(0, base_top - 1),
                base_bottom = GREATEST(0, base_bottom - 1),
                current_offense = GREATEST(0, current_offense - 1),
                current_defense = GREATEST(0, current_defense - 1),
                current_top = GREATEST(0, current_top - 1),
                current_bottom = GREATEST(0, current_bottom - 1)
                WHERE player_id = $playerId");
                
            self::notifyAllPlayers('conditioningPenalty', 
                clienttranslate('${player_name} loses stats due to low conditioning'), [
                'player_id' => $playerId,
                'player_name' => self::getPlayerNameById($playerId),
            ]);
        }
    }

    /**
     * Compute and return the current game progression.
     */
    public function getGameProgression(): int
    {
        $currentPeriod = self::getGameStateValue('current_period');
        $currentRound = self::getGameStateValue('current_round');
        $totalRounds = 9 + 6 + 6; // Total rounds in all 3 periods
        
        $completedRounds = 0;
        if ($currentPeriod > 1) $completedRounds += 9; // Period 1
        if ($currentPeriod > 2) $completedRounds += 6; // Period 2
        if ($currentPeriod == 3) $completedRounds += $currentRound - 1;
        elseif ($currentPeriod == 2) $completedRounds += $currentRound - 1;
        elseif ($currentPeriod == 1) $completedRounds = $currentRound - 1;
        
        return (int)($completedRounds / $totalRounds * 100);
    }

    /**
     * Migrate database.
     */
    public function upgradeTableDb($from_version)
    {
        // Add database upgrade logic here when needed
    }

    /*
     * Gather all information about current game situation (visible by the current player).
     */
    protected function getAllDatas(): array
    {
        $result = [];
        $current_player_id = (int) $this->getCurrentPlayerId();

        // Game state
        $state = $this->gamestate->state();
        $result['state'] = $state;

        // Players with basic data (matching your current schema)
        $result["players"] = $this->getCollectionFromDb(
            "SELECT 
                `player_id` `id`, 
                `player_score` `score`, 
                `wrestler`,
                `move`,
                `position`
            FROM `player`"
        );

        // Game progress
        $result['currentPeriod'] = self::getGameStateValue('current_period');
        $result['currentRound'] = self::getGameStateValue('current_round');

        // Material data
        $result['wrestlerCards'] = $this->wrestlerCards;
        $result['gameConstants'] = $this->gameConstants;

        return $result;
    }

    /**
     * Returns the game name.
     */
    protected function getGameName(): string
    {
        return "matrevw";
    }

    /**
     * This method is called only once, when a new game is launched.
     */
    protected function setupNewGame($players, $options = [])
    {
        $gameinfos = $this->getGameinfos();
        $default_colors = $gameinfos['player_colors'];

        foreach ($players as $player_id => $player) {
            $query_values[] = vsprintf("('%s', '%s', '%s', '%s', '%s', NULL)", [
                $player_id,
                array_shift($default_colors),
                $player["player_canal"],
                addslashes($player["player_name"]),
                addslashes($player["player_avatar"]),
            ]);
        }

        // Create players with basic schema (matching your current dbmodel.sql)
        static::DbQuery(
            sprintf(
                "INSERT INTO player (player_id, player_color, player_canal, player_name, player_avatar, wrestler) VALUES %s",
                implode(",", $query_values)
            )
        );

        $this->reattributeColorsBasedOnPreferences($players, $gameinfos["player_colors"]);
        $this->reloadPlayersBasicInfos();

        // Initialize basic game values
        $this->setGameStateInitialValue("current_period", 1);
        $this->setGameStateInitialValue("current_round", 1);
        $this->setGameStateInitialValue('wrestlerSelectionDone', 0);

        // Move to wrestler selection
        $this->gamestate->nextState('next');
    }

    /**
     * Zombie turn handling
     */
    protected function zombieTurn(array $state, int $active_player): void
    {
        $state_name = $state["name"];

        if ($state["type"] === "activeplayer") {
            switch ($state_name) {
                case 'playerTurn':
                    // Force stall for zombie
                    $this->forceStall($active_player);
                    $this->gamestate->nextState("stall");
                    break;
                default:
                    $this->gamestate->nextState("zombiePass");
                    break;
            }
            return;
        }

        if ($state["type"] === "multipleactiveplayer") {
            if ($state_name === "playerChooseWrestler") {
                $availableWrestlers = [];
                foreach ($this->wrestlerCards as $id => $card) {
                    $taken = self::getUniqueValueFromDB("SELECT COUNT(*) FROM player WHERE wrestler = '" . self::escapeStringForDB($card['name']) . "'");
                    if ($taken == 0) {
                        $availableWrestlers[] = $id;
                    }
                }
                
                if (!empty($availableWrestlers)) {
                    $wrestlerId = $availableWrestlers[0];
                    $this->actChooseWrestler($wrestlerId);
                }
            }
            
            $this->gamestate->setPlayerNonMultiactive($active_player, 'next');
            return;
        }

        throw new \feException("Zombie mode not supported at this game state: \"{$state_name}\".");
    }
}
/**
 *------
 * BGA framework: Gregory Isabelli & Emmanuel Colin & BoardGameArena
 * MatRevW implementation : © Mike & Jack McKeever and Bryan Chase bryanchase@yahoo.com
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * matrevw.js
 *
 * MatRevW user interface script
 * 
 * In this file, you are describing the logic of your user interface, in Javascript language.
 *
 */

define([
    "dojo","dojo/_base/declare",
    "ebg/core/gamegui",
    "ebg/counter",
    "ebg/stock"
],

function (dojo, declare) {
    return declare("bgagame.matrevw", ebg.core.gamegui, {
        constructor: function(){
            console.log('matrevw constructor');
              
            // Here, you can init the global variables of your user interface
            this.cardWidth = 250;
            this.cardHeight = 350;
        },
        
        /*
            setup:
            
            This method must set up the game user interface according to current game situation specified
            in parameters.
        */
        
        setup: function( gamedatas )
        {
            console.log( "Starting game setup" );
            console.log( "Game data:", gamedatas );

            // Example to add a div on the game area
            document.getElementById('game_play_area').insertAdjacentHTML('beforeend', `
                <div id="player-tables"></div>
                <div id="wrestler-selection" style="display: none;">
                    <h3>Choose Your Wrestler</h3>
                    <div id="wrestler-cards"></div>
                </div>
            `);
            
            // Setting up player boards
            Object.values(gamedatas.players).forEach(player => {
                // example of setting up players boards
                this.getPlayerPanelElement(player.id).insertAdjacentHTML('beforeend', `
                    <div id="player-counter-${player.id}">
                        <div>Wrestler: <span id="player-wrestler-${player.id}">${player.wrestler || 'Not chosen'}</span></div>
                    </div>
                `);

                // example of adding a div for each player
                document.getElementById('player-tables').insertAdjacentHTML('beforeend', `
                    <div id="player-table-${player.id}">
                        <strong>${player.name}</strong>
                        <div>Wrestler: ${player.wrestler || 'Not chosen'}</div>
                    </div>
                `);
            });

            // Get wrestler cards from game data
            const wrestlers = gamedatas.wrestlerCards;
            console.log("Wrestler cards:", wrestlers);

            // Set up wrestler selection if in the right state
            if (gamedatas.state && gamedatas.state.name === 'playerChooseWrestler') {
                this.setupWrestlerSelection(wrestlers);
            }

            console.log("[bmc] Reloaded at", new Date().toLocaleString());
            console.log( "Ending game setup" );
        },

        setupWrestlerSelection: function(wrestlers) {
            console.log("Setting up wrestler selection");
            
            const wrestlerCardsDiv = document.getElementById('wrestler-cards');
            const selectionDiv = document.getElementById('wrestler-selection');
            
            if (!wrestlerCardsDiv || !selectionDiv) {
                console.error("Wrestler selection divs not found");
                return;
            }

            // Clear existing content
            wrestlerCardsDiv.innerHTML = '';

            // Show selection area if current player needs to choose
            if (this.isCurrentPlayerActive()) {
                selectionDiv.style.display = 'block';
                
                // Create wrestler card elements
                for (let id in wrestlers) {
                    const card = wrestlers[id];
                    console.log("Creating card for wrestler:", card);

                    const cardElement = document.createElement('div');
                    cardElement.id = 'wrestler_card_' + id;
                    cardElement.className = 'wrestler_card_box clickable';
                    cardElement.innerHTML = `
                        <div class="wrestler-card">
                            <h4>${card.name}</h4>
                            <p><strong>Trademark:</strong> ${card.trademark}</p>
                            <div class="stats">
                                <div>Conditioning: ${card.starting_stats.conditioning.join(', ')}</div>
                                <div>Offense: ${card.starting_stats.offense}</div>
                                <div>Defense: ${card.starting_stats.defense}</div>
                                <div>Top: ${card.starting_stats.top}</div>
                                <div>Bottom: ${card.starting_stats.bottom}</div>
                            </div>
                        </div>
                    `;
                    
                    wrestlerCardsDiv.appendChild(cardElement);
                    
                    // Add click handler
                    dojo.connect(cardElement, 'onclick', this, dojo.hitch(this, 'onClickChooseWrestler', id));
                }
            } else {
                selectionDiv.style.display = 'none';
            }
        },
       
        onClickChooseWrestler: function(wrestlerId) {
            console.log("Choosing wrestler:", wrestlerId);
            
            if (!this.isCurrentPlayerActive()) {
                console.log("Not current player's turn");
                return;
            }

            this.ajaxcall('/matrevw/matrevw/actChooseWrestler.html', {
                lock: true,
                wrestlerId: wrestlerId,
            }, this, function(result) {
                console.log("Wrestler choice result:", result);
            });
        },

        notif_wrestlerChosen: function(notif) {
            console.log("Wrestler chosen notification:", notif);
            
            const playerId = notif.args.player_id;
            const wrestler = notif.args.wrestler;
            const playerName = notif.args.player_name;
            
            // Update player panel
            const playerWrestlerSpan = document.getElementById('player-wrestler-' + playerId);
            if (playerWrestlerSpan) {
                playerWrestlerSpan.textContent = wrestler;
            }
            
            // Update player table
            const playerTable = document.getElementById('player-table-' + playerId);
            if (playerTable) {
                const wrestlerDiv = playerTable.querySelector('div:last-child');
                if (wrestlerDiv) {
                    wrestlerDiv.textContent = 'Wrestler: ' + wrestler;
                }
            }
            
            // Hide selection if this player chose
            if (playerId == this.player_id) {
                const selectionDiv = document.getElementById('wrestler-selection');
                if (selectionDiv) {
                    selectionDiv.style.display = 'none';
                }
            }
        },

        ///////////////////////////////////////////////////
        //// Game & client states
        
        onEnteringState: function( stateName, args )
        {
            console.log( 'Entering state: ' + stateName, args );
            
            switch( stateName )
            {
                case 'playerChooseWrestler':
                    console.log("Entering wrestler selection state");
                    if (this.gamedatas && this.gamedatas.wrestlerCards) {
                        this.setupWrestlerSelection(this.gamedatas.wrestlerCards);
                    }
                    break;
                    
                case 'dummy':
                    break;
            }
        },

        onLeavingState: function( stateName )
        {
            console.log( 'Leaving state: ' + stateName );
            
            switch( stateName )
            {
                case 'playerChooseWrestler':
                    // Hide wrestler selection
                    const selectionDiv = document.getElementById('wrestler-selection');
                    if (selectionDiv) {
                        selectionDiv.style.display = 'none';
                    }
                    break;
                    
                case 'dummy':
                    break;
            }
        }, 

        onUpdateActionButtons: function( stateName, args )
        {
            console.log( 'onUpdateActionButtons: ' + stateName, args );
                      
            if( this.isCurrentPlayerActive() )
            {            
                switch( stateName )
                {
                    case 'playerChooseWrestler':
                        // Action buttons are handled by clicking on wrestler cards
                        break;
                        
                    case 'playerTurn':    
                        const playableCardsIds = args.playableCardsIds;
                        
                        playableCardsIds.forEach(
                            cardId => this.statusBar.addActionButton(_('Play card with id ${card_id}').replace('${card_id}', cardId), () => this.onCardClick(cardId))
                        ); 

                        this.statusBar.addActionButton(_('Pass'), () => this.bgaPerformAction("actPass"), { color: 'secondary' }); 
                        break;
                }
            }
        },        

        ///////////////////////////////////////////////////
        //// Utility methods
        
        /*
            Here, you can defines some utility methods that you can use everywhere in your javascript
            script.
        */

        ///////////////////////////////////////////////////
        //// Player's action
        
        /*
            Here, you are defining methods to handle player's action (ex: results of mouse click on 
            game objects).
        */

        ///////////////////////////////////////////////////
        //// Reaction to cometD notifications

        setupNotifications: function()
        {
            console.log( '[bmc] notifications subscriptions setup' );
            
            // Subscribe to wrestler chosen notification
            dojo.subscribe( 'wrestlerChosen', this, "notif_wrestlerChosen" );
            
            // Subscribe to other notifications
            dojo.subscribe( 'setStats', this, "notif_setStats" );
            
            console.log( '[bmc] EXIT notifications subscriptions setup' );
        },  
        
        notif_setStats: function( notif )
        {
            console.log("[bmc] notif_setStats", notif);
        },
   });             
});
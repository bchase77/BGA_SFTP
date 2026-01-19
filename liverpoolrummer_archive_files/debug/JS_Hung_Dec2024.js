				var jokerCount = 0;
				var jokers = new Array();
				var thereIsAnAce = false;
				var aceCount = 0;
				
				for ( let i in cards ) {
					if ( cards[ i ][ 'type' ] == 0 ) {
							jokers[ jokerCount ] = {
								"id" : cards[ i ][ 'id' ],
								"type" : 0 };	 // Start jokers at value 0
							jokerCount++;
					}
					if (( cards[ i ][ 'type' ] == 1 ) ||
					    ( cards[ i ][ 'type' ] == 14 )) {
						thereIsAnAce = true;
						aceCount++;
					}
				}
// console.log("[bmc] jokers:");
// console.log(jokers);
// console.log(jokerCount);
// console.log(thereIsAnAce);
// console.log(aceCount);
				var cardValuesHard = new Array();
				
				for ( let i in cards ) {
					if (( cards[ i ][ 'type' ] != 0 ) &&  // Jokers here are type 0
					    ( cards[ i ][ 'type' ] != 1 )){   // Ignore aces for now
						cardValuesHard[ cards[ i ][ 'type' ]] = cards[ i ][ 'type' ];
					}
				}
// console.log("[bmc] cardValuesHard");
// console.log(cardValuesHard);
				var usedPositions = new Array(); // Temporary variable to track positions in the run while assigning jokers
				
				var jokerIndex = 0;
				var foundFirst = false;
				
				// Reindex cards with the IDs as the indices
				
				// Go through positions 1 through King and track 'real' cards if they exist
				for ( let i = 2; i < 14 ; i++) {
// console.log( i );
// console.log(cardValuesHard[ i ]);
// console.log(foundFirst);
					if ( cardValuesHard[ i ] != null ) {
// console.log("Location notNull:  (cards)");
// console.log( i );
// console.log( cards );
						foundFirst = true;
						index = cards.map( function(e) { return e.type; }).indexOf( i );
// console.log("FOUND THE FIRST HARD CARD (index, value)");
// console.log(index);
// console.log(i);
						cards[ index ][ 'boardLieIndex' ] = i;
						usedPositions.push(i);
					} else {
// console.log("card location is Null");
// console.log( i );
						if ( foundFirst ) {
// console.log("foundFirst");
// console.log(cardValuesHard.length);
// console.log("Nov2023cards");
// console.log(cards);



							// if this is the last of the hard cards then ignore
							// Deal with the aces later
							// This presumes the cards which are down are indeed a valid run
							
// console.log("[bmc] Assigning Joker!");
// console.log(i);
							// if ( i < cardValuesHard.length + 1 ) {
							if ( i < cardValuesHard.length ) {
// console.log(jokerIndex);
// console.log(jokerCount);
								if ( jokerIndex < jokerCount ) {

									index = cards.map( function(e) { return e.id; }).indexOf( jokers[ jokerIndex ][ 'id' ]);
// console.log("[bmc] Assigning joker index");
// console.log(index);
									jokerIndex++;

									cards[ index ][ 'boardLieIndex' ] = i;
//									cards[ jokerIndex ][ 'boardLieIndex' ] = i;
									usedPositions.push(i);
// console.log(usedPositions);
								} else {
// console.log("[bmc] ERROR Not enough Jokers!");
//  Presume the other function did it's job and allowed only true runs.
								}
							} else {
// console.log("[bmc] FINISHED HARD CARDS do not put high ace, yet");
							}
// console.log("[bmc] FINISHED HARD CARDS");
						}
					}
// console.log("[bmc] Spot near end of first loop");
				}

// All holes have been filled with jokers. Now figure out where to put the jokers (depends on ace and distance).

				leftOverJokers = jokerCount - jokerIndex;
				
// console.log("[bmc] Assess remaining jokers");
// console.log( jokerCount);
// console.log( jokerIndex );
// console.log( leftOverJokers );
// console.log( jokers );
// console.log( cards );
// console.log( usedPositions );
// console.log( aceCount );
				
// Put an ace as index 1 if any of these is true:
  // There are 2 aces
  // There are 14 cards
  // There are 13 cards and no joker
  // 12 cards and index 13 is empty
  // N cards and N+1 is empty
// else ace is index high
    
// If only 1 ace, determine if it's high or low, then assign remaining jokers

				var minUsed = Math.min.apply( Math, usedPositions );
				var maxUsed = Math.max.apply( Math, usedPositions );
// console.log( minUsed );
// console.log( maxUsed );

				switch( aceCount ) {
					case 0 : // No need to assign aces, just place the jokers properly
// console.log("[bmc] No aces.");

// 2024/12/27: This next if and for loop is causing the hung browser:

						if ( leftOverJokers > 0 ) { // Start by assigning some below the lowest hard number
							for ( let i = minUsed - 1; i > 0; i-- ){
								if ( jokerIndex < jokerCount ) {
									index = cards.map( function(e) { return e.id; }).indexOf( jokers[ jokerIndex ][ 'id' ]);
// console.log( index );
									cards[ index ][ 'boardLieIndex' ] = i;
									usedPositions.push(i);
									jokerIndex++;
								}
							}
							leftOverJokers = jokerCount - jokerIndex;
							
							if ( leftOverJokers > 0 ) { // If there are still some jokers then put them high
								// Put extra jokers on the right unless there is a High Ace
								for ( let i = jokerIndex; i < jokerCount; i++ ) {
									cards[ i ][ 'boardLieIndex' ] = 15;
									usedPositions.push(i);
								}
							}
						}
						break;
					case 1 : // There is 1 ace. Put the ace low if min is closer to 1 and high if max is closer to 14
// console.log("[bmc] One ace.");
						if (( minUsed - 1) < ( 14 - maxUsed )){
							// Put ace low
							index = cards.map( function(e) {return e.type; }).indexOf(1);
// console.log( index );
							if ( index > -1 ) {
								cards[ index ][ 'boardLieIndex' ] = 1;
								usedPositions.push( 1 );
							} else {
								console.log("[bmc] ASSERT DID NOT FIND LOW ACE");
							}
						 
							// Now assign jokers below the lowest hard number
						 
							if ( leftOverJokers > 0 ) {
	// console.log( minUsed );
// 2024/12/27: This next if and for loop should be checked to not go infinite:

								for ( let i = minUsed - 1; i > 1; i-- ){ // Don't assign to ace
									if ( jokerIndex < jokerCount ) {
										index = cards.map( function(e) { return e.id; }).indexOf( jokers[ jokerIndex ][ 'id' ]);
	// console.log( index );
										cards[ index ][ 'boardLieIndex' ] = i;
										usedPositions.push(i);
										jokerIndex++;
									}
								}
								
								leftOverJokers = jokerCount - jokerIndex;
								// If there are still some jokers then put them high
								 
								if ( leftOverJokers > 0 ) {
									// Put extra jokers on the right but less than the ace
									for ( let i = jokerIndex; i < jokerCount; i++ ) {
										cards[ i ][ 'boardLieIndex' ] = 13.5;
										usedPositions.push(i);
									}
								}
							}
						} else {
							// Put ace high
							index = cards.map( function(e) {return e.type; }).indexOf(1);
// console.log( index );
							if ( index > -1 ) {
								cards[ index ][ 'boardLieIndex' ] = 14;
								usedPositions.push( 14 );
							} else {
								console.log("[bmc] ASSERT DID NOT FIND HIGH ACE");
							}
							// Now assign jokers above the highest hard number
						 
							if ( leftOverJokers > 0 ) {
	// console.log( minUsed );
								for ( let i = maxUsed + 1; i < 14; i++ ){ // Don't assign to ace
									if ( jokerIndex < jokerCount ) {
										index = cards.map( function(e) { return e.id; }).indexOf( jokers[ jokerIndex ][ 'id' ]);
	// console.log( index );
										cards[ index ][ 'boardLieIndex' ] = i;
										usedPositions.push(i);
										jokerIndex++;
									}
								}
								
								leftOverJokers = jokerCount - jokerIndex;
								// If there are still some jokers then put them low
								 
								if ( leftOverJokers > 0 ) {
									// Put extra jokers on the left but above the ace
// Dec 2024: This for loop will certainly end:
									for ( let i = jokerIndex; i < jokerCount; i++ ) {
										cards[ i ][ 'boardLieIndex' ] = 1.5;
										usedPositions.push(i);
									}
								}
							}
						}

						break;
					case 2 : // There are 2 aces. Assign 1 low and 1 high
// console.log("[bmc] Two aces.");

						// Find the index of the ace (type == 1); Set to 1 if present
						index = cards.map( function(e) {return e.type; }).indexOf(1);
// console.log( index );
						if ( index > -1 ) {
							cards[ index ][ 'boardLieIndex' ] = 1;
							usedPositions.push( 1 );
						} else {
							console.log("[bmc] ASSERT DID NOT FIND LOW ACE");
						}

						// Set the 'other' ace to be lieIndex 14
						// Find the index of the ace (type == 14). It was set to 14 previously:
						index = cards.map( function(e) {return e.type; }).indexOf(14);

						if ( index > -1 ) {
							cards[ index ][ 'boardLieIndex' ] = 14;
							usedPositions.push( 14 );
						} else {
							console.log("[bmc] ASSERT DID NOT FIND HIGH ACE");
						}
						break;
				}

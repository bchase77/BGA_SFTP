{OVERALL_GAME_HEADER}

<!-- 
--------
-- BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
-- TutorialRumOne implementation : © Bryan Chase <bryanchase@yahoo.com>
-- 
-- This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
-- See http://en.boardgamearena.com/#!doc/Studio for more information.
-------

    tutorialrumone_tutorialrumone.tpl
    
    This is the HTML template of your game.
    
    Everything you are writing in this file will be displayed in the HTML page of your game user interface,
    in the "main game zone" of the screen.
    
    You can use in this template:
    _ variables, with the format {MY_VARIABLE_ELEMENT}.
    _ HTML block, with the BEGIN/END format
    
    See your "view" PHP file to check how to set variables and control blocks
    
    Please REMOVE this comment before publishing your game on BGA
-->
<div id="play1card" class="tiny"></div>
<!-- UNCOMMENT THIS TO SHOW TEXT AT THE TOP OF THE SCREEN
Dody Oaks Rummy First Implementation. This is your game interface. You can edit this HTML in the ".tpl" file.
-->

<div id="myhand_wrap" class="myhand whiteblock">
    <h3>My Hand</h3>
    <div id="myhand" class="">
    </div>
</div>

<div id="goDownArea_wrap" class="goDownWrap whiteblock">
<!--
	<div>Target hand: {CURRENT_HAND_TYPE}</div>
-->
	<div id="boxes">
		<div id = "leftbox" class="leftbox">
			<h3>Draw Deck</h3>
			<div id="deck"></div>
		</div>
		<div id = "rightbox" class="rightbox">
			<h3>Discard Pile: <span id="discardSize"></span> Card{DISCARDPLURAL}</span>
</h3>
			<div id="discardPile"></div>
		</div>
	</div>
	<div id="downArea">
		<!-- BEGIN goDownArea -->
			<div id="playerDown_A_{PLAYER_ID}" class="downWhite"></div>
			<div id="playerDown_B_{PLAYER_ID}" class="downWhite"></div>
			<div id="playerDown_C_{PLAYER_ID}" class="downWhite"></div>
		<!-- END goDownArea -->
	</div>
</div>

<script type="text/javascript">

	/* Javascript HTML templates */
/*
var jstpl_handTarget = '<div id="handTarget"></div>';
*/

/*
var jstpl_buys = '<div id="player-status-${player_id}" class="player-status"><span>Buys Left: ${buy_number}/3</span></div>';
*/

/* var jstpl_player_board = '\<div class="player_score">\ */
var jstpl_player_board = '\<div class="cp_board">\
    <span id="buycount_p${id}"></span> <img class="rum_buyicon"></img> <span id="handcount_p${id}"></span> <img class="rum_cardicon"></img></div>';

/* This works, but trying to show it in 1 line:
var jstpl_player_board = '\<div class="cp_board">\
    <div id="stoneicon_p${id}" class="gmk_stoneicon"></div><span id="buycount_p${id}">Buys Left</span></div>';
*/
/* This jstpl_playerPlayButton works but I'm doing the button in the white bar now.
var jstpl_playerPlayButton = '<a href="#" id="currentPlayerPlayButton_id" class="bgabutton bgabutton_blue">\
<span>Play Card</span></a>';
*/ 
/*
// Example:
var jstpl_some_game_item='<div class="my_game_item" id="my_game_item_${MY_ITEM_ID}"></div>';
var jstpl_cardontable = '<div class="cardontable" id="cardontable_${player_id}" style="background-position:-${x}px -${y}px">\
                        </div>';
*/
</script>  
{OVERALL_GAME_FOOTER}

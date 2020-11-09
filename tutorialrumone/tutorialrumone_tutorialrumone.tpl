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
<div id="myhand_wrap" class="myhand whiteblock">
-->

<h3>
	<span>Target: 
		<span id="redTarget" style="color:Blue"></span>
	</span>
</h3>
<div id="myhand_wrap" class="myHandWrap whiteblock">
	<div id="topBoxes">
		<div id="TLeftBox" class="leftboxHandArea">
				<span id="myHandArea" class="spectatorMode" style="font-weight: bold">     My Hand (<span id="myHandSize"></span>):
					<a href="#" id="buttonPlayerSortBySet" class="bgabutton bgabutton_blue spectatorMode">Sort Sets</a>
					<a href="#" id="buttonPlayerSortByRun" class="bgabutton bgabutton_blue spectatorMode">Sort Runs</a>
					<span style="margin-left:15px"></span>
					<a href="#" id="buttonBuy" class="bgabutton bgabutton_gray spectatorMode"> Buy it! </a>
					<a href="#" id="buttonNotBuy" class="bgabutton bgabutton_gray spectatorMode">Don't buy</a>
					<span style="margin-left:15px"></span>
					<a href="#" id="buttonPrepAreaA" class="bgabutton bgabutton_gray spectatorMode">Prep A</a>
					<a href="#" id="buttonPrepAreaB" class="bgabutton bgabutton_gray spectatorMode">Prep B</a>
					<a href="#" id="buttonPrepAreaC" class="bgabutton bgabutton_gray spectatorMode">Prep C</a>
					<a href="#" id="buttonGoDownStatic" class="bgabutton bgabutton_gray spectatorMode">Go Down</a>
					<div id="myhand" class="myhand"></div>
				</span>
		</div>
	</div>
</div>


<div id="goDownArea_wrap" class="goDownWrap whiteblock">
	<div id="boxes">
		<div id = "leftbox" class="leftbox">
			<h3>Draw Deck (<span id="drawDeckSize"></span>)</h3>
			<div id="deck"></div>
		</div>
		<div id = "rightbox" class="rightbox">
			<h3>Discard Pile (<span id="discardSize"></span>)</h3>
			<div id="discardPile">
Yelllow
			</div>
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

<audio id="audiosrc_tutorialrumone_wentOutYeah" src="{GAMETHEMEURL}img/tutorialrumone_wentOutYeah.mp3" preload="none" autobuffer></audio>
<audio id="audiosrc_o_tutorialrumone_wentOutYeah" src="{GAMETHEMEURL}img/tutorialrumone_wentOutYeah.ogg" preload="none" autobuffer></audio>
<audio id="audiosrc_tutorialrumone_GoingDown" src="{GAMETHEMEURL}img/tutorialrumone_GoingDown.mp3" preload="none" autobuffer></audio>
<audio id="audiosrc_o_tutorialrumone_GoingDown" src="{GAMETHEMEURL}img/tutorialrumone_GoingDown.ogg" preload="none" autobuffer></audio>

<script type="text/javascript">

	/* Javascript HTML templates */
/*			<div id="playerDown_A1_{PLAYER_ID}" class="downWhite">M:</div>
			<div id="playerDown_B1_{PLAYER_ID}" class="downWhite">N:</div>
			<div id="playerDown_C1_{PLAYER_ID}" class="downWhite">O:</div>
			<div id="playerDown_A2_{PLAYER_ID}" class="downWhite">P:</div>
			<div id="playerDown_B2_{PLAYER_ID}" class="downWhite">Q:</div>
			<div id="playerDown_C2_{PLAYER_ID}" class="downWhite">R:</div>
*/
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

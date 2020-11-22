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
					<span style="margin-left:15px"></span>
					<a href="#" id="buttonPrepAreaA" class="bgabutton prepButton bgabutton_gray spectatorMode">Meld A</a>
					<a href="#" id="buttonPrepAreaB" class="bgabutton prepButton bgabutton_gray spectatorMode">Meld B</a>
					<a href="#" id="buttonPrepAreaC" class="bgabutton prepButton bgabutton_gray spectatorMode">Meld C</a>
					<a href="#" id="buttonPrepJoker" class="bgabutton prepButton bgabutton_gray spectatorMode">Swap For Joker</a>
					<a href="#" id="buttonGoDownStatic" class="bgabutton prepButton bgabutton_gray spectatorMode">Go Down</a>
					<div id="myhand" class="myhand"></div>
				</span>
		</div>
	</div>
</div>


<div id="goDownArea_wrap" class="goDownWrap whiteblock">
	<div id="boxes">
		<div id = "deckbox" class="deckbox">
			<h3>Draw Deck (<span id="drawDeckSize"></span>)</h3>
			<div id="deck"></div>
		</div>
		<div id = "discardbox" class="discardbox">
			<h3>Discard Pile (<span id="discardSize"></span>)</h3>
			<div id="discardPile"></div>
		</div>
		<div id="myPrepA" class="prepbox">
			<h3>Meld A</h3>
			<div id="PrepA"></div>
		</div>
		<div id="myPrepB" class="prepbox">
			<h3>Meld B</h3>
			<div id="PrepB"></div>
		</div>
		<div id="myPrepC" class="prepbox">
			<h3>Meld C</h3>
			<div id="PrepC"></div>
		</div>
		<div id="myPrepJoker" class="prepjoker">
			<h3>Card For Joker</h3>
			<div id="prepjoker"></div>
		</div>
	</div>
	<div></div>
	<div id="downArea">
		<!-- BEGIN goDownArea -->
			<div>
			<span id="playerDown_A_{PLAYER_ID}" class="downWhite"></span>
			<span id="playerDown_B_{PLAYER_ID}" class="downWhite"></span>
			<span id="playerDown_C_{PLAYER_ID}" class="downWhite"></span>
			</div>
		<!-- END goDownArea -->
	</div>
</div>

<audio id="audiosrc_tutorialrumone_itsyourdraw" src="{GAMETHEMEURL}img/tutorialrumone_itsyourdraw.mp3" preload="none" autobuffer></audio>
<audio id="audiosrc_o_tutorialrumone_itsyourdraw" src="{GAMETHEMEURL}img/tutorialrumone_itsyourdraw.ogg" preload="none" autobuffer></audio>
<audio id="audiosrc_tutorialrumone_wentOutYeah" src="{GAMETHEMEURL}img/tutorialrumone_wentOutYeah.mp3" preload="none" autobuffer></audio>
<audio id="audiosrc_o_tutorialrumone_wentOutYeah" src="{GAMETHEMEURL}img/tutorialrumone_wentOutYeah.ogg" preload="none" autobuffer></audio>
<audio id="audiosrc_tutorialrumone_GoingDown" src="{GAMETHEMEURL}img/tutorialrumone_GoingDown.mp3" preload="none" autobuffer></audio>
<audio id="audiosrc_o_tutorialrumone_GoingDown" src="{GAMETHEMEURL}img/tutorialrumone_GoingDown.ogg" preload="none" autobuffer></audio>
<audio id="audiosrc_tutorialrumone_IllBuyIt" src="{GAMETHEMEURL}img/tutorialrumone_IllBuyIt.mp3" preload="none" autobuffer></audio>
<audio id="audiosrc_o_tutorialrumone_IllBuyIt" src="{GAMETHEMEURL}img/tutorialrumone_IllBuyIt.ogg" preload="none" autobuffer></audio>
<script type="text/javascript">

var jstpl_player_board = '\<div class="cp_board">\
    <span id="buycount_p${id}"></span> <img class="rum_buyicon"></img> <span id="handcount_p${id}"></span> <img class="rum_cardicon"></img></div>';

/*
// Example:
var jstpl_some_game_item='<div class="my_game_item" id="my_game_item_${MY_ITEM_ID}"></div>';
var jstpl_cardontable = '<div class="cardontable" id="cardontable_${player_id}" style="background-position:-${x}px -${y}px">\
                        </div>';
*/
</script>  
{OVERALL_GAME_FOOTER}

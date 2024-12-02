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
<div id="myhand_wrap" class="myhandClass whiteblock">
-->

<h3>
	<span> 
		<span id="handNumber" style="color:Black"></span>
		<span id="redTarget" style="color:Blue"></span>
	</span>
</h3>
<div id="myhand_wrap" class="myHandWrap whiteblock">
	<div id="topBoxes">
		<div id="TLeftBox" class="leftboxHandArea">
				<span id="myHandArea" style="font-weight: bold">
				<span id="MYHANDTRANSLATED"></span> (
				<span id="myHandSize"></span>):
					<a href="#" id="buttonPlayerSortBySet" class="bgabutton bgabutton_blue">
						<span id="SORTSETSTRANSLATED"></span></a>
					<a href="#" id="buttonPlayerSortByRun" class="bgabutton bgabutton_blue">
						<span id="SORTRUNSTRANSLATED"></span></a>
					<span style="margin-left:15px"></span>
					
					<a href="#" id="buttonBuy" class="bgabutton bgabutton_gray"> 
						<span id="BUYTRANSLATED"></span> </a>
					<a href="#" id="buttonNotBuy" class="bgabutton bgabutton_gray">
						<span id="NOTBUYTRANSLATED"></span> </a>
					<span style="margin-left:15px"></span>
					<a href="#" id="buttonLiverpool" class="bgabutton bgabutton_blue">
						<span id="LIVERPOOL"></span> </a>
						
					<a href="#" id="buttonGoDownStatic" class="bgabutton bgabutton_gray">
						<span id="GODOWNTRANSLATED"></span></a>
					<a href="#" id="buttonShowHideWishList" class="bgabutton bgabutton_blue wishListMode">
						<span id="SHOWHIDEWISHLIST"></span></a>
					<a href="#" id="buttonSavePrep"  class="bgabutton saveload bgabutton_blue">
						<span id="BUTSAVEPREPTRANSLATED"></span></a>
					<a href="#" id="buttonLoadPrep"  class="bgabutton saveload bgabutton_blue">
						<span id="BUTLOADPREPTRANSLATED"></span></a>
					<input type="checkbox" id="voice" name="scales" checked>
						<span id="VOICESTRANSLATED"></span></label>
					
					<div id="myhand" class="myhandClass"></div>

				</span>
		</div>
	</div>
</div>

<div id="wishListAreaWrap" class="wishListAreaWrapClass wishListMode">
	<div>
		<div id="TLeftBox2" class="leftboxHandArea">
			<div id="wishListAreaC" class="wishListAreaClass">
				<div id="myWishListClubs" ></div>
			</div>
			<div id="wishListAreaS" class="wishListAreaClass">
				<div id="myWishListSpades" ></div>
			</div>
			<div id="wishListAreaH" class="wishListAreaClass">
				<div id="myWishListHearts" ></div>
			</div>
			<div id="wishListAreaD" class="wishListAreaClass">
				<div id="myWishListDiamonds" ></div>
			</div>
			<a href="#" id="buttonSubmitWishList" class="bgabutton bgabutton_blue"> <span id="WISHLISTTRANSLATED"></span>! </a>
			<a href="#" id="buttonClearWishList" class="bgabutton bgabutton_gray"> <span id="CLEARWISHLISTTRANSLATED"></span>! </a>
		</div>
	</div>
</div>

<div>
</div>

<div id="goDownArea_wrap" class="goDownWrap whiteblock">
	<div id="boxes">
		<div id = "deckbox" class="deckbox">
			<h3><span id="DRAWDECKTRANSLATED"></span> (<span id="drawDeckSize"></span>)</h3>
			<div id="deckOne"></div>
		</div>
		<div id = "discardbox" class="discardbox">
			<h3><span id="DISCARDPILETRANSLATED"></span> (<span id="discardSize"></span>)</h3>
			<div id="discardPileOne"></div>
		</div>
		<div class="tim183spacer">
		</div>
		<div id="myPrepA" class="prepbox">
			<h3><span id="PREPATRANSLATED"></span></h3>
			<div id="PrepA"></div>
		</div>
		<div id="myPrepB" class="prepbox">
			<h3><span id="PREPBTRANSLATED"></span></h3>
			<div id="PrepB"></div>
		</div>
		<div id="myPrepC" class="prepbox">
			<h3><span id="PREPCTRANSLATED"></span></h3>
			<div id="PrepC"></div>
		</div>
		<div id="myPrepJoker" class="prepjoker">
			<h3><span id="CARDFORJOKERTRANSLATED2"></span></h3>
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
	<div></div>
</div>

<audio id="audiosrc_tutorialrumone_itsyourdraw"   src="{GAMETHEMEURL}img/tutorialrumone_itsyourdraw.mp3" preload="none" autobuffer></audio>
<audio id="audiosrc_o_tutorialrumone_itsyourdraw" src="{GAMETHEMEURL}img/tutorialrumone_itsyourdraw.ogg" preload="none" autobuffer></audio>
<audio id="audiosrc_tutorialrumone_wentOutYeah"   src="{GAMETHEMEURL}img/tutorialrumone_wentOutYeah.mp3" preload="none" autobuffer></audio>
<audio id="audiosrc_o_tutorialrumone_wentOutYeah" src="{GAMETHEMEURL}img/tutorialrumone_wentOutYeah.ogg" preload="none" autobuffer></audio>
<audio id="audiosrc_tutorialrumone_GoingDown"     src="{GAMETHEMEURL}img/tutorialrumone_GoingDown.mp3" preload="none" autobuffer></audio>
<audio id="audiosrc_o_tutorialrumone_GoingDown"   src="{GAMETHEMEURL}img/tutorialrumone_GoingDown.ogg" preload="none" autobuffer></audio>
<audio id="audiosrc_tutorialrumone_IllBuyIt"      src="{GAMETHEMEURL}img/tutorialrumone_IllBuyIt.mp3" preload="none" autobuffer></audio>
<audio id="audiosrc_o_tutorialrumone_IllBuyIt"    src="{GAMETHEMEURL}img/tutorialrumone_IllBuyIt.ogg" preload="none" autobuffer></audio>
<audio id="audiosrc_tutorialrumone_ItsYourTurn"   src="{GAMETHEMEURL}img/tutorialrumone_ItsYourTurn.mp3" preload="none" autobuffer></audio>
<audio id="audiosrc_o_tutorialrumone_ItsYourTurn" src="{GAMETHEMEURL}img/tutorialrumone_ItsYourTurn.ogg" preload="none" autobuffer></audio>
<audio id="audiosrc_Liverpool_audio"              src="{GAMETHEMEURL}img/Liverpool_audio.mp3" preload="none" autobuffer></audio>
<audio id="audiosrc_o_Liverpool_audio"            src="{GAMETHEMEURL}img/Liverpool_audio.ogg" preload="none" autobuffer></audio>
<audio id="audiosrc_MissedIt"                     src="{GAMETHEMEURL}img/MissedIt.mp3" preload="none" autobuffer></audio>
<audio id="audiosrc_o_MissedIt"                   src="{GAMETHEMEURL}img/MissedIt.ogg" preload="none" autobuffer></audio>
<audio id="audiosrc_TooSlow"                      src="{GAMETHEMEURL}img/TooSlow.mp3" preload="none" autobuffer></audio>
<audio id="audiosrc_o_TooSlow"                      src="{GAMETHEMEURL}img/TooSlow.ogg" preload="none" autobuffer></audio>

<script type="text/javascript">

var jstpl_player_board = '\<div class="cp_board">\
    <span id="buycount_p${id}"></span> <img class="rum_buyicon"></img> <span id="handcount_p${id}"></span> <img class="rum_cardicon"></img></div>';

/*
var jstpl_intersection='<div class="gmk_intersection ${stone_type}" id="intersection_${x}_${y}"></div>';
*/
/*
// Example:
var jstpl_some_game_item='<div class="my_game_item" id="my_game_item_${MY_ITEM_ID}"></div>';
var jstpl_cardontable = '<div class="cardontable" id="cardontable_${player_id}" style="background-position:-${x}px -${y}px">\
                        </div>';
*/
</script>  
{OVERALL_GAME_FOOTER}

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
			<h3>Discard Pile</h3>
			<div id="discardPile"></div>
		</div>
	</div>
	<div id="downArea">
		<!-- BEGIN goDownArea -->
			<div id="playerDown_A_{PLAYER_ID}" class="downWhite">Set 1</div>
			<div id="playerDown_B_{PLAYER_ID}" class="downWhite">Set 2</div>
			<div id="playerDown_C_{PLAYER_ID}" class="downWhite">Set 3</div>
		<!-- END goDownArea -->
	</div>
</div>

<div id="myhand_wrap" class="myhand whiteblock">
    <h3>My Hand</h3>
    <div id="myhand" class="">
    </div>
</div>





<script type="text/javascript">

/*
<div id="downArea_wrap">
<div id="downArea1" class="downLeft">
	<h3>Down Area1</h3>
	</div>
<div id="downArea2" class="downMiddle">
	<h3>Down Area2</h3>
	</div>
<div id="downArea3" class="downRight">
	<h3>Down Area3</h3>
	</div>

<div id="downArea1" class="downLeft">
	<h3>Down Area1</h3>
	</div>
<div id="downArea2" class="downMiddle">
	<h3>Down Area2</h3>
	</div>
<div id="downArea3" class="downRight">
	<h3>Down Area3</h3>
	</div>

</div>
*/
/*
<div id="boxes">
	<div id = "leftbox">
		<h3>Draw Deck</h3>
		<div id="deck">
		</div>
	</div>
	<div id = "rightbox">
		<h3>Discard Pile</h3>
		<div id="discardPile">
		</div>
	</div>
</div>


<div id="downArea_wrap" class="whiteblock downArea">
	<div id="downArea">
	</div>
</div>

<div id="downArea_wrap2" class="whiteblock downArea">
	<h3>Down Area2</h3>
	<div id="downArea2">
	</div>
</div>

<div id="myhand_wrap" class="whiteblock">
    <h3>My Hand</h3>
    <div id="myhand">
    </div>
</div>
*/

	/* Javascript HTML templates */

var jstpl_handTarget = '<div id="handTarget"></div>';


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

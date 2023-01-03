{OVERALL_GAME_HEADER}

<!-- 
--------
-- BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
-- MatRetDev implementation : © Mike & Jack McKeever and Bryan Chase bryanchase@yahoo.com
-- 
-- This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
-- See http://en.boardgamearena.com/#!doc/Studio for more information.
-------

    matretdev_matretdev.tpl
    
    This is the HTML template of your game.
    
    Everything you are writing in this file will be displayed in the HTML page of your game user interface,
    in the "main game zone" of the screen.
    
    You can use in this template:
    _ variables, with the format {MY_VARIABLE_ELEMENT}.
    _ HTML block, with the BEGIN/END format
    
    See your "view" PHP file to check how to set variables and control blocks
    
    Please REMOVE this comment before publishing your game on BGA
-->


This is your game interface4. It's January 2nd 2023. You can edit this HTML in your ".tpl" file.

<!-- Comment: Administrative Area -->

<h3>
	<span> 
		<span id="periodNumber" style="color:Black"></span>
	</span>
</h3>

<!-- Comment: My Hand Area -->

<div id="handsWrap" class="myHandWrapClass whiteblock">
	   <div id="boxMoveCardMine" class="moveCardClass">
	      <h3><span id="MOVECARDMINETRANSLATED"></span></h3>
		  <div id="moveCardMine"></div>
	   </div>
	   <div id="boxWrestlerCardMine" class="moveCardClass">
	      <h3><span id="WRESTLERCARDMINETRANSLATED"></span></h3>
		  <div id="wrestlerCardMine"></div>
	   </div>
	   <div id="boxMoveCardOpp" class="moveCardClass">
	      <h3><span id="MOVECARDOPPTRANSLATED" style="float: Left;"></span></h3>
		  <div id="moveCardOpp"></div>
	   </div>
	   <div id="boxWrestlerCardOpp" class="moveCardClass">
	      <h3><span id="WRESTLERCARDOPPTRANSLATED"></span></h3>
		  <div id="wrestlerCardOpp"></div>
	   </div>
</div>

<!-- Comment: Main Game Board Area -->

<div id="mainBoardWrap" class="mainBoardWrapClass whiteblock">
   <div id="boxes">
	   <div></div>
	   <div id="boxScrambleCard" class="moveCardClass">
	      <h3><span id="SCRAMBLECARDTRANSLATED"></span></h3>
		  <div id="scrambleCard"></div>
	   </div>
	   <div></div>
   </div>
</div>

<script type="text/javascript">

// Javascript HTML templates

/*
// Example:
var jstpl_some_game_item='<div class="my_game_item" id="my_game_item_${MY_ITEM_ID}"></div>';

*/

</script>  

{OVERALL_GAME_FOOTER}

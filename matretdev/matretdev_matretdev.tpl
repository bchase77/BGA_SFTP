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


This is your game interface2. You can edit this HTML in your ".tpl" file.

<!-- Comment: Administrative Area -->

<h3>
	<span> 
		<span id="periodNumber" style="color:Black"></span>
	</span>
</h3>

<!-- Comment: My Hand Area -->

<div id="myHandWrap" class="myHandWrapClass whiteblock">
<div id="myHand" class="myHandClass"></div>

</div>

<!-- Comment: Main Game Board Area -->

<div id="mainBoardWrap" class="mainBoardWrapClass whiteblock">
   <div id="cardMoveOpp" class="cardMoveClass">
   </div>
   <div id="cardwrestlerOpp" class="cardMoveClass">
   </div>
   <div id="cardScramble" class="cardMoveClass">
   </div>
   <div id="cardMoveMine" class="cardMoveClass">
   </div>
   <div id="cardWrestlerMine" class="cardMoveClass">
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

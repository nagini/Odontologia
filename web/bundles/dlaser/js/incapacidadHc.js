$(function() {        
	 
	//callback function to bring a hidden box back
	function callback() {
	  setTimeout(function() {
	    $( "#effectClip:visible" ).removeAttr( "style" ).fadeOut();
	  }, 20000 );
	};
    
	// set effect from select menu value
	$( "#medicamentoHc" ).click(function() {
	  $( "#effectClip" ).show( 'clip', 500, callback );// run the effect
	  return false;
	});
    
	$( "#effectClip" ).hide();
	
	
	/* escript para el dialog de los impresos*/	
    }); 

$(document).ready(function(){
  $('#cnst').button();
  $( "#cnst" ).click(function() {
	$( "#consentimientos" ).dialog( "open" );
  });
  
  $('#consentimientos').dialog({
	  autoOpen:false,
	  resizable: false,
	  width:800,
	  height:400,	
	  modal: true,
	  
	  buttons :{
	    Cancel: function() {
	      $( this ).dialog( "close" );
	    }
	  }
  });
  
});
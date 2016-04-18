// NOTICE!! DO NOT USE ANY OF THIS JAVASCRIPT
// IT'S ALL JUST JUNK FOR OUR DOCS!
// ++++++++++++++++++++++++++++++++++++++++++
!function ($) {
  $(function(){
   //  tooltip demo
//    $('body').tooltip({
//      selector: "a[data-toggle=tooltip]"
//    })
	$('a[data-toggle=tooltip]').mouseover(function() {
		$(this).tooltip('show');
	  })
  })
}(window.jQuery)

$("")
  .odd()
  .hide()
  .end()
  .even()
  .hover(function () {
    $(this).toggleClass("active").next().stop(true, true).slideToggle().odd().hide();
  });


  /*$("#cardbody")
    .odd()
    .hide()
    .end()
    .even()
    .hover(function () {
      $(this).toggleClass("active").next().stop(true, true).slideToggle();
    });*/


/*$("#cardbody").hover(function() {
  $("#cardbody").toggleClass("cardbody");
  $("#price").toggleClass("hidden");
});*/


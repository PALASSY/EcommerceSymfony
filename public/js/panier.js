

//Récupération le nombre menu dans ma commande
function nbrmenu() {
  var nbrmenu = +$(".panier").val();
  $(".paniertest2").append(nbrmenu);
}
 

function panier() {
  //Récupérer l'url de panier
  var url = $('.js-cartlink').data('url');
  $.ajax({
    url: url,
    success: function (data,statut) {
      $('.js-cartlink').html(data);
    },
    error: function (resultat,statut,erreur) {
      console.log(erreur);
    }
  })
}



//Quand c'est prêt => vas-y!!!!!
$(document).ready(function () {
  //Ici la function que je souhaite utiliser c'est => widget_cart()
  nbrmenu();
  //Appelle au panier
  panier();
});

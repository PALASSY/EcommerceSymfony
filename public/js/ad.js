// Instruction avec JS pour récupérer/modifier/ re-injecter la data-prototype  c'est la suite de factorisation dans templates/food/_collections.html.twig

//Quand on click sur le boutton <Ajouter une image >
$('#addcollection-image').click(function () {
  //On va récupérer la valeur de l'input type=hidden dans un constante
  const index = +$('#widget-count').val();

  //On va remplacer le __name__ de data-prototype par cette valeur grâce à Regex
  //On y entre à partir de l'ID du parent de data-prototype on vise ensuite le data-prototype
  //Le mettre dans un constante 
  const tmpl = $('#food_images').data('prototype').replace(/__name__/g, index);

  //Injecter dans la data-prototype cette valeur 
  $('#food_images').append(tmpl);

  //On va incrémenter ++ cette valeur 
  $('#widget-count').val(index + 1);

  //Pour éviter le bug quand il y aura de modification ou suppression de cette image crée, on va créer une function qui récupérer le data-prototype et l'injecter comme valeur(function à part)

  //On va supprimer la nouvelle image secindaire créee
  deleteButton();
  
});



//Pour éviter le bug
function updateCounter() {
  //Récupérer la longeur le data- en valeur dans un constante
  const count = +$('#food_images div.form-group').length;

  //Ré-injecter cette valeur dans la valeur de <input> type=hidden 
  $('#widget-count').val(count); 
}


//Suppression d'une nouvelle image secondaire
function deleteButton() {
  $('button[data-action = "delete"]').click(function() {
    //On va récupérer le data-target(le parent) et le supprimer
    const target = this.dataset.target;
    $(target).remove();
  })
}

//Appeller les 2 functions pour la ré-initialisation 
updateCounter();
deleteButton();
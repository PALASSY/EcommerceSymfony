/**********************function()***************************************/
(function ($) {
  //creation de function
  $.fn.slider = function (args) {
    //creation d'arguments par défaut
    var defaults = {
      speed: 600, //vitesse
      items: "slide",
      duration: 2000, //temps
      autoplay: true,
      //bullet:true,
    };
    //enroulé un autre div le .slider
    //Création de <div> pour ne pas être changé par les utilisateurs
    $(this.wrap('<div class="slider-container"></div>'));
    //si il y a des arguments le var defaults sera ecrasé
    //On offre la possibilité à l'utilisateur le choix de modifié
    //le plug-in est configurable
    var opts = $.extend(defaults, args);
    //console.log(opts);
    //Voir le NOMBRE de slide(qui peut être dynamique) dans .slider
    //mais il faut-être vigilent parce que ici on manipule un nombre
    //Pour ce faire on utilise un parseInt
    //Toutes les class slide
    //Prendre l'option est dans l'option on choisi l'item
    var itemCount = parseInt($("." + opts.items).length);
    //console.log(itemCount);
    //la création de var en jQuery necessite $ mais pas systematique
    //$this=>.slider
    var $this = $(this);
    //Le with 100% sera multiplier par le nombre de slide(6)
    $this.css("width", 100 * itemCount + "%");
    //Je vais définir le width de slide à 1140px(comme celui de .container)
    //dynamiquement
    //.slide
    var slide = "." + opts.items;
    $(slide).css("width", $(".slider-container").width());
    var withSlide = $(".slider-container").width();
    //Faire le slide en boucle à l'infini
    var play = function () {
      //l'animation en deduisant la longeur de .slide(slider-container)
      $this.animate(
        {
          //le margin-left de slider devient 0
          marginLeft: -withSlide,
        },
        opts.speed,
        function () {
          //Duplicata la dernière formation en première formation (en boucle)
          //On passe de 1140 à 0 puis dire la première image passe à la fin
          //cherche le dernier slide(dans slider dont le margin est à 0) sera suivi du premier slide(dans slider le margin est à 1140px)
          $this
            .css("margin-left", 0)
            .find(slide + ":last")
            .after($this.find(slide + ":first"));
        }
      );
    };

    if (opts.autoplay) {
      //Appelle la la function play
      setInterval(play, opts.duration);
    }

    //retourner la function créée
    return $(this);
  };
})(jQuery);




        $(document).ready(function () {
          //On appelle la function créée
          //En cas de changement de class .slide alors
          //On ajoute dans argument items => ({items:nom de class})
          //Si on met l'autoplay à false le play ne jouera plus
          $(".slider").slider({ duration: 5000, speed: 2000 });
        });
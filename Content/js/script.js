$(document).ready(function() {
  // Fonction pour basculer entre le mode sombre et le mode clair
  function toggleDarkMode() {
    // Sélectionne la checkbox
    var checkbox = $('.checkbox-wrapper-54.header input[type="checkbox"]');
    
    // Sélectionne le body ou tout autre élément que vous souhaitez appliquer le mode sombre
    var body = $('body');
    
    // Vérifie si la checkbox est cochée
    if (checkbox.prop('checked')) {
      // Active le mode sombre
      body.css({
        'background-color': '#303136',
        'color': '#fff'
      });
      $('header').css({
        'color': '#000'
      });
      $('.slider').css({
        'background-color': '#303136'
      });
      $('.slider:before').css({
        'background': 'linear-gradient(40deg,#ff0080,#ff8c00 70%)',
        'box-shadow': 'inset -3px -2px 5px -2px #8983f7, inset -10px -4px 0 0 #a3dafb'
      });
    } else {
      // Désactive le mode sombre
      body.css({
        'background-color': '',
        'color': ''
      });
      $('.slider').css({
        'background-color': ''
      });
      $('.slider:before').css({
        'background': '',
        'box-shadow': ''
      });
    }
  }

  // Ajoute un gestionnaire d'événement pour le changement de la checkbox
  $('.checkbox-wrapper-54.header input[type="checkbox"]').change(function() {
    // Appelle la fonction pour basculer entre le mode sombre et le mode clair
    toggleDarkMode();
  });
});


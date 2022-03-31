

let link = document.querySelector('.nav a');
link.addEventListener('click', monLien2);

function monLien2(e){
  let targ = e;
  console.log(`Le type d'événement est: ${targ.type}`);
  link.addClass('active');
}
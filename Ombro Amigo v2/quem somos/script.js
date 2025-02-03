
const gitpag = document.getElementById('git1');

gitpag.addEventListener('click', function() {
  window.open('https://github.com/GuilhermeDuarteB', '_blank');
});


const instapag = document.getElementById('insta');

instapag.addEventListener('click', function() {
  window.open('https://www.instagram.com/ombroamigo_24/', '_blank');
});

  

const gab = document.getElementById("gab");
let gabCount = 0; 

gab.addEventListener("click", function() {
  gabCount++; 

  if (gabCount == 3) {
    gab.addEventListener("click", function() {
      window.open("imgs/gabgood.png", "_blank");
      gabCount == 0;
    });
  }
});


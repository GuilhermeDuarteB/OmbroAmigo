document.addEventListener('DOMContentLoaded', function() {
    // Código existente do menu-open
    const checkbox = document.getElementById('check');
    
    checkbox.addEventListener('change', function() {
        if (this.checked) {
            document.body.classList.add('menu-open');
        } else {
            document.body.classList.remove('menu-open');
        }
    });

    // Novo código para o dropdown mobile
    const dropdownToggle = document.querySelector('.dropdown > a');
    const dropdown = document.querySelector('.dropdown');

    dropdownToggle.addEventListener('click', function(e) {
        if (window.innerWidth <= 858) {
            e.preventDefault(); // Previne a navegação
            dropdown.classList.toggle('active');
        }
    });
});

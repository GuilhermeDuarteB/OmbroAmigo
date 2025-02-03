document.addEventListener('DOMContentLoaded', function() {
    // Controle do menu mobile
    const checkbox = document.getElementById('check');
    
    checkbox.addEventListener('change', function() {
        if (this.checked) {
            document.body.classList.add('menu-open');
        } else {
            document.body.classList.remove('menu-open');
            // Fecha o dropdown quando o menu é fechado
            document.querySelector('.dropdown').classList.remove('active');
        }
    });

    // Controle do dropdown no mobile
    const dropdownToggle = document.querySelector('.dropdown > a');
    const dropdown = document.querySelector('.dropdown');

    dropdownToggle.addEventListener('click', function(e) {
        if (window.innerWidth <= 858) {
            e.preventDefault();
            dropdown.classList.toggle('active');
        }
    });

    // Fecha o dropdown quando clicar em um item dele
    const dropdownItems = document.querySelectorAll('.dropdown-menu a');
    dropdownItems.forEach(item => {
        item.addEventListener('click', function() {
            if (window.innerWidth <= 858) {
                dropdown.classList.remove('active');
                checkbox.checked = false;
                document.body.classList.remove('menu-open');
            }
        });
    });
}); 
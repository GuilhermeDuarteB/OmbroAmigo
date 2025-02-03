const isLeapYear = (year) => {
    return (
      (year % 4 === 0 && year % 100 !== 0 && year % 400 !== 0) ||
      (year % 100 === 0 && year % 400 === 0)
    );
  };
  const getFebDays = (year) => {
    return isLeapYear(year) ? 29 : 28;
  };
  let calendar = document.querySelector('.calendar');
  const month_names = [
    'Janeiro',
    'Fevereiro',
    'Março',
    'Abril',
    'Maio',
    'Junho',
    'Julho',
    'Agosto',
    'Setembro',
    'Outubro',
    'Novembro',
    'Dezembro',
  ];
  let month_picker = document.querySelector('#month-picker');
  const dayTextFormate = document.querySelector('.day-text-formate');
  const timeFormate = document.querySelector('.time-formate');
  const dateFormate = document.querySelector('.date-formate');
  
  month_picker.onclick = () => {
    month_list.classList.remove('hideonce');
    month_list.classList.remove('hide');
    month_list.classList.add('show');
    dayTextFormate.classList.remove('showtime');
    dayTextFormate.classList.add('hidetime');
    timeFormate.classList.remove('showtime');
    timeFormate.classList.add('hideTime');
    dateFormate.classList.remove('showtime');
    dateFormate.classList.add('hideTime');
  };
  
  const generateCalendar = async (month, year) => {
    let calendar_days = document.querySelector('.calendar-days');
    calendar_days.innerHTML = '';
    let calendar_header_year = document.querySelector('#year');
    let days_of_month = [
      31,
      getFebDays(year),
      31,
      30,
      31,
      30,
      31,
      31,
      30,
      31,
      30,
      31,
    ];
  
    let currentDate = new Date();
  
    month_picker.innerHTML = month_names[month];
    calendar_header_year.innerHTML = year;
  
    let first_day = new Date(year, month);
  
    // Obtém os horários do back-end
    const horarios = await fetchHorarios();
  
    for (let i = 0; i <= days_of_month[month] + first_day.getDay() - 1; i++) {
      let day = document.createElement('div');
  
      if (i >= first_day.getDay()) {
        let dayNumber = i - first_day.getDay() + 1;
        let date = new Date(year, month, dayNumber);
        day.innerHTML = dayNumber;
  
        // Adiciona a classe de destaque se a data estiver nos horários
        if (horarios.some(h => isDateInRange(date, h.dataInicio, h.dataFim))) {
          day.classList.add('highlight-date');
        }
  
        if (
          dayNumber === currentDate.getDate() &&
          year === currentDate.getFullYear() &&
          month === currentDate.getMonth()
        ) {
          day.classList.add('current-date');
        }
      }
      calendar_days.appendChild(day);
    }
  };
  
  const fetchHorarios = async () => {
    try {
      const response = await fetch('horario.php');
      const data = await response.json();
      
      if (data.error) {
        console.error('Erro ao buscar horários:', data.error);
        return [];
      }

      // Verifica se data.horarios existe e é um array
      if (data.horarios && Array.isArray(data.horarios)) {
        return data.horarios.map(horario => ({
          dataInicio: new Date(horario.dataInicio),
          dataFim: new Date(horario.dataFim)
        }));
      }
      
      return [];
    } catch (error) {
      console.error('Erro ao buscar horários e consultas:', error);
      return [];
    }
  };
  
  const isDateInRange = (date, startDate, endDate) => {
    const checkDate = new Date(date);
    const start = new Date(startDate);
    const end = new Date(endDate);
    
    // Remove o componente de tempo para comparação apenas de datas
    checkDate.setHours(0, 0, 0, 0);
    start.setHours(0, 0, 0, 0);
    end.setHours(0, 0, 0, 0);
    
    return checkDate >= start && checkDate <= end;
  };
  
  
  let month_list = calendar.querySelector('.month-list');
  month_names.forEach((e, index) => {
    let month = document.createElement('div');
    month.innerHTML = `<div>${e}</div>`;
  
    month_list.append(month);
    month.onclick = () => {
      currentMonth.value = index;
      generateCalendar(currentMonth.value, currentYear.value);
      month_list.classList.replace('show', 'hide');
      dayTextFormate.classList.remove('hideTime');
      dayTextFormate.classList.add('showtime');
      timeFormate.classList.remove('hideTime');
      timeFormate.classList.add('showtime');
      dateFormate.classList.remove('hideTime');
      dateFormate.classList.add('showtime');
    };
  });
  
  (function () {
    month_list.classList.add('hideonce');
  })();
  document.querySelector('#pre-year').onclick = () => {
    --currentYear.value;
    generateCalendar(currentMonth.value, currentYear.value);
  };
  document.querySelector('#next-year').onclick = () => {
    ++currentYear.value;
    generateCalendar(currentMonth.value, currentYear.value);
  };
  
  let currentDate = new Date();
  let currentMonth = { value: currentDate.getMonth() };
  let currentYear = { value: currentDate.getFullYear() };
  generateCalendar(currentMonth.value, currentYear.value);

  const todayShowTime = document.querySelector('.time-formate');
  const todayShowDate = document.querySelector('.date-formate');
  
  const currshowDate = new Date();
  const showCurrentDateOption = {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    weekday: 'long',
  };
  const currentDateFormate = new Intl.DateTimeFormat(
    'pt-PT',
    showCurrentDateOption
  ).format(currshowDate);
  todayShowDate.textContent = currentDateFormate;
  setInterval(() => {
    const timer = new Date();
    const option = {
      hour: 'numeric',
      minute: 'numeric',
      second: 'numeric',
    };
    const formateTimer = new Intl.DateTimeFormat('en-us', option).format(timer);
    let time = `${`${timer.getHours()}`.padStart(
      2,
      '0'
    )}:${`${timer.getMinutes()}`.padStart(
      2,
      '0'
    )}: ${`${timer.getSeconds()}`.padStart(2, '0')}`;
    todayShowTime.textContent = formateTimer;
  }, 1000);
  
  function initializeEventListeners() {
    const btnDefinirHorario = document.getElementById('definirHSemanal');
    const containerHorario = document.getElementById('definirHorarioContainer');
    const btnFechar = document.getElementById('closeMenu');

    console.log('Botão:', btnDefinirHorario); // Debug
    console.log('Container:', containerHorario); // Debug
    console.log('Botão Fechar:', btnFechar); // Debug

    if (btnDefinirHorario) {
        btnDefinirHorario.addEventListener('click', (e) => {
            e.preventDefault();
            console.log('Botão clicado'); // Debug
            if (containerHorario) {
                containerHorario.style.display = 'block';
                // Pequeno delay para garantir que o display:block seja aplicado antes da classe active
                setTimeout(() => {
                    containerHorario.classList.add('active');
                }, 10);
            }
        });
    }

    if (btnFechar) {
        btnFechar.addEventListener('click', (e) => {
            e.preventDefault();
            console.log('Botão fechar clicado'); // Debug
            if (containerHorario) {
                containerHorario.classList.remove('active');
                setTimeout(() => {
                    containerHorario.style.display = 'none';
                }, 500);
            }
        });
    }
  }

  // Remover qualquer outra inicialização de event listeners que possa existir
  document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM carregado'); // Debug
    initializeEventListeners();
  });

// Pega a data atual
const hoje = new Date();
const dia = String(hoje.getDate()).padStart(2, '0');
const mes = String(hoje.getMonth() + 1).padStart(2, '0'); // Mês começa de 0
const ano = hoje.getFullYear();

// Formata a data no formato AAAA-MM-DD
const dataMinima = `${ano}-${mes}-${dia}`;

// Define o valor mínimo do campo de data
document.getElementById("dataInicio").setAttribute("min", dataMinima);
document.getElementById("dataFim").setAttribute("min", dataMinima);




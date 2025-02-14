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
  let month_picker = document.querySelector('#month-picker');
  let month_list = calendar.querySelector('.month-list');

  const dayTextFormate = document.querySelector('.day-text-formate');
  const timeFormate = document.querySelector('.time-formate');
  const dateFormate = document.querySelector('.date-formate');


  // Array com os nomes dos meses
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

  // Configuração da data atual
  let currentDate = new Date();
  let currentMonth = { value: currentDate.getMonth() };
  let currentYear = { value: currentDate.getFullYear() };

  // Configuração do seletor de meses
  if (month_picker && month_list) {
    month_picker.onclick = () => {
        createMonthList();
        month_list.classList.remove('hideonce');
        month_list.classList.remove('hide');
        month_list.classList.add('show');
        
        // Esconder os elementos de data e hora
        dayTextFormate.classList.remove('showtime');
        dayTextFormate.classList.add('hidetime');
        timeFormate.classList.remove('showtime');
        timeFormate.classList.add('hideTime');
        dateFormate.classList.remove('showtime');
        dateFormate.classList.add('hideTime');
    };

    // Fechar a lista de meses quando clicar fora
    document.addEventListener('click', (e) => {
        if (!month_picker.contains(e.target) && !month_list.contains(e.target)) {
            month_list.classList.replace('show', 'hide');
        }
    });
  }

  let horarios = []; // Variável global para armazenar os horários

  const fetchHorarios = async () => {
    try {
        console.log('Buscando horários...');
        const response = await fetch('horario.php');
        const data = await response.json();
        console.log('Dados recebidos:', data);
        
        if (data.error) {
            console.error('Erro ao buscar horários:', data.error);
            return [];
        }

        // Converte as datas de string para objeto Date
        if (data.horarios && Array.isArray(data.horarios)) {
            return data.horarios.map(horario => ({
                dataInicio: new Date(horario.dataInicio),
                dataFim: new Date(horario.dataFim),
                diasSemana: horario.diasSemana || []
            }));
        }
        
        return [];
    } catch (error) {
        console.error('Erro ao buscar horários:', error);
        return [];
    }
  };
  
  const generateCalendar = async (month, year) => {
    console.log('Gerando calendário para:', month, year);
    
    // Buscar horários antes de gerar o calendário
    horarios = await fetchHorarios();
    console.log('Horários carregados:', horarios);

    const calendar_days = document.querySelector('.calendar-days');
    calendar_days.innerHTML = '';
    
    let first_day = new Date(year, month);
    console.log('Primeiro dia:', first_day);

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
  
    for (let i = 0; i <= days_of_month[month] + first_day.getDay() - 1; i++) {
        let day = document.createElement('div');

        if (i >= first_day.getDay()) {
            let dayNumber = i - first_day.getDay() + 1;
            let date = new Date(year, month, dayNumber);
            day.innerHTML = dayNumber;
            
            console.log('Verificando dia:', date);

            // Verifica se é um dia de trabalho
            const isDiaTrabalho = horarios.some(horario => {
                const dataAtual = new Date(date);
                const inicio = new Date(horario.dataInicio);
                const fim = new Date(horario.dataFim);
                
                const dentroDoIntervalo = dataAtual >= inicio && dataAtual <= fim;
                const diaSemana = dataAtual.getDay() + 1;
                const ehDiaTrabalho = horario.diasSemana.includes(diaSemana);
                
                console.log('Verificação:', {
                    data: dataAtual,
                    inicio,
                    fim,
                    dentroDoIntervalo,
                    diaSemana,
                    diasTrabalho: horario.diasSemana,
                    ehDiaTrabalho
                });

                return dentroDoIntervalo && ehDiaTrabalho;
            });

            if (isDiaTrabalho) {
                console.log('Dia de trabalho encontrado:', date);
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
  
  // Função para atualizar o mês selecionado visualmente
  const updateSelectedMonth = (selectedIndex) => {
    const monthDivs = month_list.querySelectorAll('div');
    monthDivs.forEach((div, index) => {
        if (index === selectedIndex) {
            div.classList.add('selected');
        } else {
            div.classList.remove('selected');
        }
    });
  };

  // Função para mostrar/esconder a lista de meses
  const toggleMonthList = (show) => {
    const monthList = document.querySelector('.month-list');
    const calendarBody = document.querySelector('.calendar-body');
    
    if (show) {
        monthList.classList.add('show');
        calendarBody.style.opacity = '0.3';
    } else {
        monthList.classList.remove('show');
        calendarBody.style.opacity = '1';
    }
  };

  // Função para criar a lista de meses
  const createMonthList = () => {
    month_list.innerHTML = '';
    
    month_names.forEach((month, index) => {
        const monthDiv = document.createElement('div');
        monthDiv.innerHTML = `<div>${month}</div>`;
        
        if (index === currentMonth.value) {
            monthDiv.querySelector('div').classList.add('selected');
        }
        
        monthDiv.onclick = () => {
            currentMonth.value = index;
            month_picker.textContent = month_names[index];
            generateCalendar(currentMonth.value, currentYear.value);
            month_list.classList.replace('show', 'hide');
        };
        
        month_list.appendChild(monthDiv);
    });
  };

  // Aguardar o DOM estar completamente carregado
  document.addEventListener('DOMContentLoaded', () => {
    const initCalendar = () => {
        // Verificar se todos os elementos necessários existem
        const yearElement = document.querySelector('#year');
        const preYearBtn = document.querySelector('#pre-year');
        const nextYearBtn = document.querySelector('#next-year');
        
        if (!calendar || !month_picker || !month_list || !yearElement) {
            console.error('Elementos necessários do calendário não encontrados');
            return;
        }

        // Atualizar o texto do ano
        yearElement.textContent = currentYear.value;
        
        // Atualizar o texto do mês
        month_picker.innerHTML = month_names[currentMonth.value];
        
        // Remover a criação inicial da lista de meses
        month_list.innerHTML = '';
        
        // Event listener para o seletor de mês
        month_picker.onclick = () => {
            createMonthList();
            month_list.classList.remove('hideonce');
            month_list.classList.remove('hide');
            month_list.classList.add('show');
        };

        // Event listeners para navegação do ano
        if (preYearBtn) {
            preYearBtn.onclick = () => {
                --currentYear.value;
                yearElement.textContent = currentYear.value;
                generateCalendar(currentMonth.value, currentYear.value);
            };
        }

        if (nextYearBtn) {
            nextYearBtn.onclick = () => {
                ++currentYear.value;
                yearElement.textContent = currentYear.value;
                generateCalendar(currentMonth.value, currentYear.value);
            };
        }

        // Fechar lista de meses ao clicar fora
        const handleClickOutside = (e) => {
            if (!month_picker.contains(e.target) && !month_list.contains(e.target)) {
                month_list.classList.replace('show', 'hide');
            }
        };

        document.addEventListener('click', handleClickOutside);

        // Gerar calendário inicial
        generateCalendar(currentMonth.value, currentYear.value);
    };

    // Inicializar o calendário após o DOM estar carregado
    initCalendar();
  });

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
    const container = document.getElementById('definirHorarioContainer');
    const overlay = document.getElementById('overlay');
    const btnDefinirHorario = document.getElementById('definirHSemanal');
    const btnFechar = document.getElementById('closeMenu');

    console.log('Botão:', btnDefinirHorario); // Debug
    console.log('Container:', container); // Debug
    console.log('Botão Fechar:', btnFechar); // Debug

    if (btnDefinirHorario) {
        btnDefinirHorario.addEventListener('click', () => {
            container.classList.add('show');
            overlay.classList.add('show');
        });
    }

    if (btnFechar) {
        btnFechar.addEventListener('click', () => {
            container.classList.remove('show');
            overlay.classList.remove('show');
        });
    }

    // Fechar ao clicar no overlay
    overlay.addEventListener('click', () => {
        container.classList.remove('show');
        overlay.classList.remove('show');
    });
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

document.getElementById('addPeriodo').addEventListener('click', () => {
    const periodos = document.getElementById('periodos');
    const novoPeriodo = document.createElement('div');
    novoPeriodo.className = 'periodo';
    const numPeriodo = periodos.children.length + 1;
    
    novoPeriodo.innerHTML = `
        <label>Período ${numPeriodo}</label>
        <div class="horarios">
            <input type="time" class="hora-inicio" required>
            <span>até</span>
            <input type="time" class="hora-fim" required>
            <button type="button" class="remove-periodo">×</button>
        </div>
    `;
    
    periodos.appendChild(novoPeriodo);
});

document.getElementById('formHorario').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const diasSelecionados = [...document.querySelectorAll('.dias-checks input:checked')]
        .map(input => parseInt(input.value));
    
    const periodos = [];
    diasSelecionados.forEach(dia => {
        document.querySelectorAll('.periodo').forEach(periodo => {
            periodos.push({
                diaSemana: dia,
                horaInicio: periodo.querySelector('.hora-inicio').value,
                horaFim: periodo.querySelector('.hora-fim').value
            });
        });
    });

    const formData = new FormData();
    formData.append('dataInicio', document.getElementById('dataInicio').value);
    formData.append('dataFim', document.getElementById('dataFim').value);
    formData.append('periodos', JSON.stringify(periodos));

    try {
        const response = await fetch('horario.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        
        if (data.success) {
            alert('Horário definido com sucesso!');
            location.reload();
        } else {
            alert('Erro ao definir horário: ' + data.error);
        }
    } catch (error) {
        alert('Erro ao salvar horário: ' + error.message);
    }
});



const renderCalendar = () => {
    // Verificar se os elementos existem
    const yearElement = document.getElementById('year');
    const monthPickerElement = document.querySelector('.month-picker');
    
    if (!yearElement || !monthPickerElement) {
        console.error('Elementos necessários não encontrados');
        return;
    }

    // Atualizar o texto do ano
    yearElement.textContent = currentYear.value || currentYear;
    
    // Atualizar o texto do mês
    monthPickerElement.textContent = month_names[currentMonth.value || currentMonth];
    
    // ... resto do código de renderização ...
};

// Atualizar os event listeners para navegação do ano
document.querySelector('.year-change.prev').addEventListener('click', () => {
    currentYear--;
    renderCalendar();
});

document.querySelector('.year-change.next').addEventListener('click', () => {
    currentYear++;
    renderCalendar();
});

// Atualizar o seletor de mês
document.querySelector('.month-picker').addEventListener('click', () => {
    document.querySelector('.month-list').classList.add('show');
});

// Atualizar a seleção de mês
month_names.forEach((month, index) => {
    const div = document.createElement('div');
    div.textContent = month;
    div.addEventListener('click', () => {
        currentMonth = index;
        renderCalendar();
        document.querySelector('.month-list').classList.remove('show');
    });
    document.querySelector('.month-list').appendChild(div);
});

// Inicializar o calendário
document.addEventListener('DOMContentLoaded', () => {
    renderCalendar();
});

// Atualizar a função navigateMonth
const navigateMonth = (direction) => {
    if (direction === 'prev') {
        if (currentMonth.value === 0) {
            currentMonth.value = 11;
            currentYear.value--;
        } else {
            currentMonth.value--;
        }
    } else {
        if (currentMonth.value === 11) {
            currentMonth.value = 0;
            currentYear.value++;
        } else {
            currentMonth.value++;
        }
    }
    
    // Atualizar o texto do mês e ano
    month_picker.textContent = month_names[currentMonth.value];
    document.getElementById('year').textContent = currentYear.value;
    
    // Gerar o calendário com os novos valores
    generateCalendar(currentMonth.value, currentYear.value);
};

// Adicionar os event listeners para as setas de navegação
document.addEventListener('DOMContentLoaded', () => {
    const prevMonthBtn = document.getElementById('pre-month');
    const nextMonthBtn = document.getElementById('next-month');
    
    if (prevMonthBtn) {
        prevMonthBtn.addEventListener('click', () => navigateMonth('prev'));
    }
    
    if (nextMonthBtn) {
        nextMonthBtn.addEventListener('click', () => navigateMonth('next'));
    }
});

// Atualizar o HTML do cabeçalho do calendário
const calendar_header = document.querySelector('.calendar-header');
if (calendar_header) {
    calendar_header.innerHTML = `
        <div class="month-picker-container">
            <span class="month-change" id="pre-month">
                <pre><</pre>
            </span>
            <span id="month-picker">${month_names[currentMonth.value]}</span>
            <span class="month-change" id="next-month">
                <pre>></pre>
            </span>
        </div>
        <div class="year-picker">
            <span class="year-change" id="pre-year">
                <pre><</pre>
            </span>
            <span id="year">${currentYear.value}</span>
            <span class="year-change" id="next-year">
                <pre>></pre>
            </span>
        </div>
    `;

    // Adicionar event listeners para as setas dos meses
    document.getElementById('pre-month').onclick = () => navigateMonth('prev');
    document.getElementById('next-month').onclick = () => navigateMonth('next');
}




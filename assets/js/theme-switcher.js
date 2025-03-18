/**
 * Простое переключение темы с использованием встроенных возможностей Tailwind CSS
 * Сохранить как assets/js/theme-switcher.js
 */


document.addEventListener('DOMContentLoaded', function() {
    // Проверяем сохраненную тему или системные настройки
    if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }

    // Находим кнопку переключения темы
    const themeToggle = document.getElementById('theme-toggle');
    if (themeToggle) {
        // Обновляем иконку в соответствии с текущей темой
        updateToggleButton();
        
        // Добавляем обработчик события
        themeToggle.addEventListener('click', function() {
            // Переключаем тему
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.theme = 'light';
            } else {
                document.documentElement.classList.add('dark');
                localStorage.theme = 'dark';
            }
            
            // Обновляем иконку
            updateToggleButton();
        });
    }
    
    // Функция обновления иконки кнопки
    function updateToggleButton() {
        if (!themeToggle) return;
        
        if (document.documentElement.classList.contains('dark')) {
            themeToggle.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd" />
                </svg>
                Темная тема
            `;
        } else {
            themeToggle.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
                </svg>
                Светлая тема
            `;
        }
    }
});

console.log("dev test");


// Устанавливаем время исчезновения (в миллисекундах)

const disappearTime = 5000; // 5000 мс = 5 секунд

// Функция для скрытия элемента с классом alert
function hideAlert() {
    const alertElement = document.querySelector('.alert');
    if (alertElement) {
        alertElement.style.transition = 'opacity 1s';
        alertElement.style.opacity = 0;
        setTimeout(() => {
            alertElement.style.display = 'none';
        }, 1000); // Время ожидания после начала анимации (1 секунда)
    }
}

// Запускаем таймер для скрытия элемента
setTimeout(hideAlert, disappearTime);

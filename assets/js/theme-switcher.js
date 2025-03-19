/**
 * Simple theme switcher using built-in Tailwind CSS features
 * Save as assets/js/theme-switcher.js
 */

document.addEventListener('DOMContentLoaded', function() {
    // Check the saved theme or system settings
    if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }

    // Find the theme toggle button
    const themeToggle = document.getElementById('theme-toggle');
    if (themeToggle) {
        // Update the icon based on the current theme
        updateToggleButton();
        
        // Add event listener
        themeToggle.addEventListener('click', function() {
            // Toggle the theme
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.theme = 'light';
            } else {
                document.documentElement.classList.add('dark');
                localStorage.theme = 'dark';
            }
            
            // Update the icon
            updateToggleButton();
        });
    }
    
    // Function to update the button icon
    function updateToggleButton() {
        if (!themeToggle) return;
        
        if (document.documentElement.classList.contains('dark')) {
            themeToggle.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd" />
                </svg>
                Dark Theme
            `;
        } else {
            themeToggle.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
                </svg>
                Light Theme
            `;
        }
    }
});

console.log("dev test");


// Set the disappear time (in milliseconds)

const disappearTime = 5000; // 5000 ms = 5 seconds

// Function to hide the alert element with the class alert
function hideAlert() {
    const alertElement = document.querySelector('.alert');
    if (alertElement) {
        alertElement.style.transition = 'opacity 1s';
        alertElement.style.opacity = 0;
        setTimeout(() => {
            alertElement.style.display = 'none';
        }, 1000); // Waiting time after the animation starts (1 second)
    }
}

// Start the timer to hide the element
setTimeout(hideAlert, disappearTime);

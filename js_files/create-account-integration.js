// This code would be added to your create account page (cacc.html)

function setupCreateAccountForm() {
    const form = document.getElementById('createAccountForm');
    
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        if (validateForm()) {
            // Get form data
            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            
            // Show loading state
            const button = document.getElementById('createAccountBtn');
            const originalText = button.textContent;
            button.textContent = 'Creating Account...';
            button.disabled = true;
            
            try {
                const response = await fetch('http://localhost:5000/api/users/register', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ name, email, password })
                });
                
                const data = await response.json();
                
                if (!response.ok) {
                    throw new Error(data.message || 'Registration failed');
                }
                
                // Save token and user info
                localStorage.setItem('hexOrbitToken', data.token);
                localStorage.setItem('hexOrbitUser', JSON.stringify(data.user));
                localStorage.setItem('hexOrbitLoggedIn', 'true');
                
                // Redirect to dashboard
                window.location.href = 'dashboard.html';
            } catch (error) {
                console.error('Registration error:', error);
                
                // Show error message
                const errorEl = document.getElementById('registrationError');
                errorEl.textContent = error.message || 'Registration failed';
                errorEl.style.display = 'block';
                
                // Shake the form to indicate error
                const formContainer = document.querySelector('.form-container');
                formContainer.style.animation = 'shake 0.5s';
                
                // Remove the animation after it completes
                setTimeout(() => {
                    formContainer.style.animation = '';
                }, 500);
            } finally {
                // Reset button state
                button.textContent = originalText;
                button.disabled = false;
            }
        }
    });
}

// Call this function when the DOM is loaded
document.addEventListener('DOMContentLoaded', setupCreateAccountForm);

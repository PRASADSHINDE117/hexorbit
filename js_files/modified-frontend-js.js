// Updated form submission function for the frontend
function setupFormSubmission() {
    const form = document.getElementById('loginForm');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (validateForm()) {
            // Get form data
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            
            // Send to backend API
            loginUser(email, password);
        }
    });
}

// Function to login user via API
async function loginUser(email, password) {
    // Show loading state on button
    const button = document.getElementById('signInBtn');
    const originalText = button.textContent;
    button.textContent = 'Signing In...';
    button.disabled = true;
    
    try {
        const response = await fetch('http://localhost:5000/api/users/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ email, password })
        });
        
        const data = await response.json();
        
        if (!response.ok) {
            throw new Error(data.message || 'Login failed');
        }
        
        // Save token and user info
        localStorage.setItem('hexOrbitToken', data.token);
        localStorage.setItem('hexOrbitUser', JSON.stringify(data.user));
        localStorage.setItem('hexOrbitLoggedIn', 'true');
        
        // Redirect to dashboard
        window.location.href = 'dashboard.html';
    } catch (error) {
        console.error('Login error:', error);
        
        // Show error message
        document.getElementById('loginError').textContent = error.message || 'Invalid email or password';
        document.getElementById('loginError').style.display = 'block';
        
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

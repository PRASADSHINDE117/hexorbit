// This script would be included in your dashboard.html page

// Check if user is logged in
document.addEventListener('DOMContentLoaded', function() {
    const token = localStorage.getItem('hexOrbitToken');
    
    if (!token) {
        // Redirect to login if no token exists
        window.location.href = 'signin.html';
        return;
    }
    
    // Load user data
    fetchUserData();
});

// Function to fetch user data from API
async function fetchUserData() {
    try {
        const token = localStorage.getItem('hexOrbitToken');
        
        const response = await fetch('http://localhost:5000/api/users/me', {
            method: 'GET',
            headers: {
                'x-auth-token': token
            }
        });
        
        if (!response.ok) {
            // Handle invalid token
            if (response.status === 401) {
                localStorage.removeItem('hexOrbitToken');
                localStorage.removeItem('hexOrbitUser');
                localStorage.removeItem('hexOrbitLoggedIn');
                window.location.href = 'signin.html';
                return;
            }
            
            throw new Error('Failed to fetch user data');
        }
        
        const userData = await response.json();
        
        // Update UI with user data
        updateDashboard(userData);
    } catch (error) {
        console.error('Error fetching user data:', error);
    }
}

// Function to update dashboard with user info
function updateDashboard(user) {
    // Update user name display
    const userNameElement = document.getElementById('userName');
    if (userNameElement) {
        userNameElement.textContent = user.name;
    }
    
    // Update email display
    const userEmailElement = document.getElementById('userEmail');
    if (userEmailElement) {
        userEmailElement.textContent = user.email;
    }
    
    // You can add more UI updates here
}

// Logout function
function logout() {
    // Clear local storage
    localStorage.removeItem('hexOrbitToken');
    localStorage.removeItem('hexOrbitUser');
    localStorage.removeItem('hexOrbitLoggedIn');
    
    // Redirect to login page
    window.location.href = 'signin.html';
}

// Update user profile
async function updateProfile(formData) {
    try {
        const token = localStorage.getItem('hexOrbitToken');
        
        const response = await fetch('http://localhost:5000/api/users/me', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'x-auth-token': token
            },
            body: JSON.stringify(formData)
        });
        
        if (!response.ok) {
            throw new Error('Failed to update profile');
        }
        
        const updatedUser = await response.json();
        
        // Update UI
        updateDashboard(updatedUser);
        
        // Update stored user data
        const currentUser = JSON.parse(localStorage.getItem('hexOrbitUser') || '{}');
        localStorage.setItem('hexOrbitUser', JSON.stringify({
            ...currentUser,
            name: updatedUser.name,
            email: updatedUser.email
        }));
        
        return true;
    } catch (error) {
        console.error('Error updating profile:', error);
        return false;
    }
}

// Change password
async function changePassword(currentPassword, newPassword) {
    try {
        const token = localStorage.getItem('hexOrbitToken');
        
        const response = await fetch('http://localhost:5000/api/users/change-password', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'x-auth-token': token
            },
            body: JSON.stringify({ currentPassword, newPassword })
        });
        
        if (!response.ok) {
            const data = await response.json();
            throw new Error(data.message || 'Failed to change password');
        }
        
        return { success: true };
    } catch (error) {
        console.error('Error changing password:', error);
        return { success: false, message: error.message };
    }
}

// Delete account
async function deleteAccount() {
    try {
        const token = localStorage.getItem('hexOrbitToken');
        
        const response = await fetch('http://localhost:5000/api/users/me', {
            method: 'DELETE',
            headers: {
                'x-auth-token': token
            }
        });
        
        if (!response.ok) {
            throw new Error('Failed to delete account');
        }
        
        // Clear local storage
        localStorage.removeItem('hexOrbitToken');
        localStorage.removeItem('hexOrbitUser');
        localStorage.removeItem('hexOrbitLoggedIn');
        
        // Redirect to login page
        window.location.href = 'signin.html';
        
        return true;
    } catch (error) {
        console.error('Error deleting account:', error);
        return false;
    }
}

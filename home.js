// Handle language selection
document.getElementById('languageSelect').addEventListener('change', function() {
    // alert('Language changed to: ' + this.value);
});

// Show popup form
document.getElementById("loginBtn").addEventListener("click", function() {
    document.getElementById("popupForm").style.display = "flex";
});

// Close popup form by clicking on the close button
document.getElementById("closePopup").addEventListener("click", function() {
    document.getElementById("popupForm").style.display = "none";
});

// Switch between login and registration form
document.getElementById("loginTab").addEventListener("click", function() {
    document.getElementById("loginForm").classList.remove("hidden");
    document.getElementById("registerForm").classList.add("hidden");
    this.classList.add("active");
    document.getElementById("registerTab").classList.remove("active");
});

document.getElementById("registerTab").addEventListener("click", function() {
    document.getElementById("registerForm").classList.remove("hidden");
    document.getElementById("loginForm").classList.add("hidden");
    this.classList.add("active");
    document.getElementById("loginTab").classList.remove("active");
});

// Show popup message function
function showPopupMessage(message) {
    const popupMessage = document.getElementById("popupMessage");
    const popupMessageText = document.getElementById("popupMessageText");
    popupMessageText.innerText = message;
    popupMessage.classList.add("visible");
}

// Close popup message function
function closePopupMessage() {
    const popupMessage = document.getElementById("popupMessage");
    popupMessage.classList.remove("visible");
}

// Close the login/registration popup message if the user clicks outside it
document.addEventListener("click", function(event) {
    const popupMessage = document.getElementById("popupMessage");
    if (event.target === popupMessage) {
        closePopupMessage();
    }
});

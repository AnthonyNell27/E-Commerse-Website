<?php
session_start();

// ✅ Database Connection
$servername = "localhost";
$username = "antots1";
$password = "Antots@123";
$dbname = "login_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// ✅ Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';

// Fetch User Info
if ($isLoggedIn) {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT email FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($email);
    $stmt->fetch();
    $stmt->close();

    // Extract default name from email
    $defaultName = ucfirst(explode("@", $email)[0]); 
} else {
    $defaultName = "Guest";
}

$profile_image = "assets/default-profile.png"; // Default profile image
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SlashDotPh</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css?v=<?php echo time(); ?>">
</head>
<body>

<nav class="navbar navbar-light bg-light fixed-top">
    <div class="container-fluid d-flex align-items-center justify-content-between">
        <a class="navbar-brand ms-3" href="index.php">
            <img src="assets/newlogo.png" height="40" alt="Logo">
        </a>
        <div class="position-absolute start-50 top-50 translate-middle">
            <ul class="nav">
                <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php#about-section">About</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php#pricing">Products & Services</a></li>
                <?php if ($isAdmin): ?>
                    <li class="nav-item"><a class="nav-link" href="net" target="_blank">PMS</a></li>
                <?php endif; ?>
            </ul>
        </div>
        <div class="d-flex align-items-center ms-auto">
            <a href="contact.php" class="btn btn-outline-primary me-3">Contact</a>
            <?php if ($isLoggedIn): ?>
                <!-- Profile Dropdown Menu -->
                <div class="dropdown">
                    <button class="btn btn-light border-0 dropdown-toggle" type="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="<?php echo $profile_image; ?>" alt="Profile" class="rounded-circle" width="40" height="40">
                        <span><?php echo htmlspecialchars($defaultName); ?></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#viewProfileModal">View Profile</a></li>
                    <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="#" id="logoutBtn">Log-out</a></li>
                    </ul>
                </div>
            <?php else: ?>
                <button class="btn btn-dark me-3" data-bs-toggle="modal" data-bs-target="#authModal">Log-in</button>
            <?php endif; ?>
        </div>
    </div>
</nav>

<!-- ✅ View Profile Modal -->
<div class="modal fade" id="viewProfileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="profileModalLabel">Your Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="profileImage" src="assets/default-profile.png" class="rounded-circle mb-3" width="80" height="80">
                <h5 id="profileFullName"><?php echo htmlspecialchars($defaultName); ?></h5>
                <p id="profileEmail"><?php echo htmlspecialchars($email); ?></p>
                <button class="btn btn-primary w-100 mt-2" data-bs-toggle="modal" data-bs-target="#updateProfileModal">Update Profile</button>
                <button class="btn btn-danger w-100 mt-2" data-bs-toggle="modal" data-bs-target="#resetPasswordModal">Reset Password</button>
            </div>
        </div>
    </div>
</div>

<!-- Update Profile Modal -->
<div class="modal fade" id="updateProfileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="updateProfileForm">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="updateFullName" required>
                        <label for="updateFullName">Full Name</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control" id="updateEmail" required>
                        <label for="updateEmail">Email</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="updatePhone" required>
                        <label for="updatePhone">Phone Number</label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Authentication Modal -->
<div class="modal fade" id="authModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="authModalLabel">Log in to Your Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="loginForm">
                    <form>
                        <div class="form-floating mb-3">
                            <input type="email" class="form-control" id="loginEmail" placeholder=" " required>
                            <label for="loginEmail">Email Address</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" id="loginPassword" placeholder=" " required>
                            <label for="loginPassword">Password</label>
                        </div>
                        <button type="submit" class="btn btn-custom w-100">Log In</button>
                    </form>
                    <p class="text-center mt-3">Don't have an account? <a href="#" id="showSignup">Sign up here</a></p>
                    <p class="text-center mt-3"> <a href="#" data-bs-toggle="modal" data-bs-target="#resetPasswordModal">Forgot Password?</a></p>

                </div>
                <div id="signupForm" style="display: none;">
                    <form>
                        <div class="form-floating mb-3">
                            <input type="email" class="form-control" id="signupEmail" placeholder=" " required>
                            <label for="signupEmail">Email Address</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" id="signupPassword" placeholder=" " required>
                            <label for="signupPassword">Password</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" id="signupConfirmPassword" placeholder=" " required>
                            <label for="signupConfirmPassword">Confirm Password</label>
                        </div>
                        <div class="privacy-checkbox">
                        <input type="checkbox" id="privacy-policy-signup" required>
                        <label for="privacy-policy-signup">I agree to the <a href="https://www.slash.ph/SlashDotPH_privacypolicy.pdf" target="_blank" download="Privacy-Policy.pdf">Privacy Policy</a> and <a href="#">Terms of Use</a>.</label>
                        </div>


                        <button type="submit" class="btn btn-custom w-100" id="signup-btn" disabled>Create Account</button>

                    </form>
                    <p class="text-center mt-3">Already have an account? <a href="#" id="showLogin">Go back to login</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.getElementById("privacy-policy-signup").addEventListener("change", function () {
    document.getElementById("signup-btn").disabled = !this.checked;
});

</script>
<!-- Reset Password Modal -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reset Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="resetPasswordForm">
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control" id="resetEmail" placeholder="Email Address" required>
                        <label for="resetEmail">Email Address</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" id="resetNewPassword" placeholder="New Password" required>
                        <label for="resetNewPassword">New Password</label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Reset Password</button>
                </form>
            </div>
        </div>
    </div>
</div>



<script>
document.getElementById("showSignup").addEventListener("click", function() {
    document.getElementById("loginForm").style.display = "none";
    document.getElementById("signupForm").style.display = "block";
});
document.getElementById("showLogin").addEventListener("click", function() {
    document.getElementById("signupForm").style.display = "none";
    document.getElementById("loginForm").style.display = "block";
});


// Login Functionality
document.querySelector("#loginForm").addEventListener("submit", function(event) {
    event.preventDefault();
    const email = document.querySelector("#loginEmail").value.trim();
    const password = document.querySelector("#loginPassword").value.trim();

    fetch("accounts.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `action=login&email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            window.location.reload();
        } else {
            alert(data.message || "Unknown error occurred.");
        }
    })
    .catch(error => {
        console.error("Fetch error:", error);
        alert("Error connecting to server.");
    });
});

// Sign-up Functionality
document.querySelector("#signupForm").addEventListener("submit", function(event) {
    event.preventDefault();
    const email = document.querySelector("#signupEmail").value;
    const password = document.querySelector("#signupPassword").value;
    const confirmPassword = document.querySelector("#signupConfirmPassword").value;

    if (password !== confirmPassword) {
        alert("Passwords do not match.");
        return;
    }

    fetch("accounts.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `action=signup&email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}`
})
.then(response => response.json())
.then(data => {
    alert(data.message);
    if (data.status === "success") {
        window.location.reload(); // Refresh the page
    }
});

});

document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("logoutBtn")?.addEventListener("click", function (event) {
        event.preventDefault();  // Prevent default link behavior

        fetch("accounts.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "action=logout"
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                window.location.href = "index.php";  // Redirect after logout
            } else {
                alert("Logout failed. Please try again.");
            }
        })
        .catch(error => console.error("Logout error:", error));
    });
});


document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("resetPasswordForm").addEventListener("submit", function(event) {
        event.preventDefault();
        
        const email = document.getElementById("resetEmail").value.trim();
        const newPassword = document.getElementById("resetNewPassword").value.trim();

        fetch("accounts.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `action=reset_password&email=${encodeURIComponent(email)}&new_password=${encodeURIComponent(newPassword)}`
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            if (data.status === "success") {
                document.getElementById("resetPasswordForm").reset();
                bootstrap.Modal.getInstance(document.getElementById("resetPasswordModal")).hide();
            }
        })
        .catch(error => {
            console.error("Error:", error);
            alert("Error resetting password.");
        });
    });
});


document.getElementById("resetPassword").addEventListener("click", function () {
    new bootstrap.Modal(document.getElementById("resetPasswordModal")).show();
});

// Handle Reset Password
document.getElementById("resetPasswordForm").addEventListener("submit", function(event) {
    event.preventDefault();
    const email = document.getElementById("resetEmail").value.trim();
    const newPassword = document.getElementById("resetNewPassword").value.trim();

    fetch("accounts.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `action=reset_password&email=${encodeURIComponent(email)}&new_password=${encodeURIComponent(newPassword)}`
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if (data.status === "success") {
            document.getElementById("resetPasswordForm").reset();
            bootstrap.Modal.getInstance(document.getElementById("resetPasswordModal")).hide();
        }
    })
    .catch(error => {
        console.error("Error:", error);
        alert("Error resetting password.");
    });
});

document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("viewProfile").addEventListener("click", function () {
        fetch("accounts.php?action=fetch_profile")
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    document.getElementById("profileFullName").textContent = data.full_name;
                    document.getElementById("profileEmail").textContent = data.email;
                    new bootstrap.Modal(document.getElementById("viewProfileModal")).show();
                } else {
                    alert("Failed to load profile: " + data.message);
                }
            })
            .catch(error => {
                console.error("Error fetching profile:", error);
                alert("Error fetching profile.");
            });
    });
});


// Handle Logout
document.getElementById("logoutBtn")?.addEventListener("click", function(event) {
    event.preventDefault();
    fetch("accounts.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "action=logout"
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            window.location.href = "index.php";
        }
    });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>



<!-- Floating Chatbot Icon -->
<div id="chatbot-icon">
    <img src="assets/chatbot1.png" alt="Chatbot">
    <span class="chat-label">Chat</span>
</div>

<!-- Chatbot Popup -->
<div id="chatbot-container" class="hidden">
    <div id="chatbot-header">
        <img src="assets/chatbot1.png" alt="Chatbot Avatar" class="chatbot-avatar">
        <span>Slash BOT</span>
        <button id="close-chatbot">&times;</button>
    </div>
    <!-- Chatbot Questions Box -->
<div class="chatbot-questions-container">
    <button id="toggleQuestions" class="question-toggle-btn">Questions ▾</button>
    <div class="chatbot-questions hidden">
        <button>How can I contact SLASH.PH?</button>
        <button>How does SLASH.PH help businesses reduce costs?</button>
        <button>What does SLASH.PH specialize in?</button>
        <button>What products and services does SLASH.PH offer?</button>
        <button>Where is SLASH.PH located?</button>
    </div>
    <div id="chatbot-privacy-overlay">
    <p>I agree to the <a href="https://www.slash.ph/SlashDotPH_privacypolicy.pdf" target="_blank" download="Privacy-Policy.pdf">Privacy Policy</a> and <a href="#">Terms of Use</a>.
    </p>
    <button id="agree-privacy-chatbot">I Agree</button>
</div>


</div>

    <div id="chatbox"></div>
    <div id="chat-input-container">
        <input type="text" id="user-input" placeholder="Type a message..." />
        <button id="send-btn">Send</button>
    </div>
    

</div>


<!-- JavaScript  -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        let heroIndex = 0;

        // Define text and images
        const heroData = [
            
            {
                title: "We can enhance your customer experience.",
                text: "Refine the way you manage your customer lifecycle interactions with our intuitive and scalable unified communications solutions.",
                image: "assets/abt2.jpg"
            },
            {
                title: "We can optimize your business gains.",
                text: "Realize your full potential for productivity and profitability with our proven and time-tested custom technology solutions.",
                image: "assets/abt3.jpg"
            }
        ];

        // Function to update hero content
        function updateHero() {
            heroIndex = (heroIndex + 1) % heroData.length; // Cycle through data

            document.getElementById("hero-title").textContent = heroData[heroIndex].title;
            document.getElementById("hero-text").textContent = heroData[heroIndex].text;
            document.getElementById("hero-image").src = heroData[heroIndex].image;
        }

        // Change content every 5 seconds
        setInterval(updateHero, 5000);
    });
</script>


<script>//ABOUT
document.addEventListener("DOMContentLoaded", function () {
    const subtitles = document.querySelectorAll(".subtitle");
    const image = document.getElementById("about-image");
    let index = 0;

    // Set a fixed height for the image container based on the first image
    function setFixedHeight() {
        image.style.height = `${image.clientHeight}px`; // Locks height based on the first image
    }

    function updateContent() {
        subtitles.forEach(sub => sub.classList.remove("active"));

        const currentSubtitle = subtitles[index];
        const newImage = currentSubtitle.getAttribute("data-img");

        currentSubtitle.classList.add("active");

        // Fade effect
        image.style.opacity = "0";
        setTimeout(() => {
            image.src = newImage;
            image.style.opacity = "1";
        }, 500);

        index = (index + 1) % subtitles.length;
    }

    setFixedHeight(); // Lock initial height
    setInterval(updateContent, 3000);
});
</script>

<script>//vision
    document.addEventListener("DOMContentLoaded", function() {
        // Hide all section content by default
        document.querySelectorAll(".section-content").forEach(content => {
            content.style.display = "none";
        });

        document.querySelectorAll(".toggle-section").forEach(header => {
            header.addEventListener("click", function() {
                let content = this.nextElementSibling;
                let arrow = this.querySelector(".arrow");

                // Toggle visibility
                if (content.style.display === "none") {
                    content.style.display = "block";
                    arrow.style.transform = "rotate(180deg)";
                } else {
                    content.style.display = "none";
                    arrow.style.transform = "rotate(0deg)";
                }
            });
        });
    });
</script>




<script>
  document.addEventListener("DOMContentLoaded", function () {
    const serviceBoxes = document.querySelectorAll(".service-box");
    const servicesSection = document.getElementById("services");
    const servicesTitle = document.querySelector("#services h2");

    function checkVisibility() {
        const rect = servicesSection.getBoundingClientRect();

        if (rect.top < window.innerHeight && rect.bottom > 0) {
            // If section is in viewport, trigger animation
            servicesSection.classList.add("show");
            serviceBoxes.forEach((box, index) => {
                setTimeout(() => {
                    box.classList.add("show");
                }, index * 150);
            });
        } else {
            // Reset animation when scrolling out
            servicesSection.classList.remove("show");
            serviceBoxes.forEach((box) => {
                box.classList.remove("show");
            });
        }
    }

    window.addEventListener("scroll", checkVisibility);
    checkVisibility(); // Check immediately on page load
});
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const buttons = document.querySelectorAll(".learn-more-btn");
    const popups = document.querySelectorAll(".service-popup-container");
    const closeButtons = document.querySelectorAll(".close-popup");

    // Open Popup on Click
    buttons.forEach(button => {
        button.addEventListener("click", function (event) {
            event.stopPropagation();
            const targetPopup = document.getElementById(this.dataset.popup);
            if (targetPopup) {
                targetPopup.classList.add("show");

                // Ensure overlay is added only once
                if (!document.querySelector(".popup-overlay")) {
                    document.body.insertAdjacentHTML("beforeend", '<div class="popup-overlay"></div>');
                }

                document.querySelector(".popup-overlay").style.display = "block";
            }
        });
    });

    // Close Popup when clicking the close button
    closeButtons.forEach(button => {
        button.addEventListener("click", function (event) {
            event.stopPropagation();
            this.closest(".service-popup-container").classList.remove("show");

            // Remove overlay
            const overlay = document.querySelector(".popup-overlay");
            if (overlay) overlay.remove();
        });
    });

    // Close Popup when clicking outside the modal
    document.addEventListener("click", function (e) {
        if (e.target.classList.contains("popup-overlay")) {
            document.querySelector(".popup-overlay").remove();
            popups.forEach(popup => popup.classList.remove("show"));
        }
    });
});

</script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
    const chatbotContainer = document.getElementById("chatbot-container");
    const chatbotIcon = document.getElementById("chatbot-icon");
    const closeChatbot = document.getElementById("close-chatbot");
    const sendBtn = document.getElementById("send-btn");
    const userInput = document.getElementById("user-input");
    const chatBox = document.getElementById("chatbox");
    const toggleBtn = document.getElementById("toggleQuestions");
    const questionsContainer = document.querySelector(".chatbot-questions");
    const chatbotPrivacyOverlay = document.getElementById("chatbot-privacy-overlay");

    
        // ✅ Click Outside Chatbot to Close
        document.addEventListener("click", function (event) {
        if (!chatbotContainer.contains(event.target) && !chatbotIcon.contains(event.target)) {
            chatbotContainer.style.display = "none";
        }
    });
    
    
    chatbotIcon.addEventListener("click", function () {
        if (!localStorage.getItem("chatbotPrivacyAgreed")) {
            chatbotPrivacyPopup.style.display = "block";
        } else {
            chatbotContainer.style.display = "flex";
        }
    });

    document.getElementById("agree-privacy-chatbot").addEventListener("click", function () {
    localStorage.setItem("chatbotPrivacyAgreed", "true"); // Store agreement

    // Hide the privacy agreement popup
    document.getElementById("chatbot-privacy-overlay").style.display = "none";

    // Enable the chatbot interaction
    document.getElementById("chatbot-container").style.display = "flex";
    document.getElementById("user-input").disabled = false;
    document.getElementById("send-btn").disabled = false;
});

    




    // ✅ Ensure Chatbox is Open on Refresh
    chatbotContainer.style.display = "flex";  // Forces chatbot to be visible on every page refresh

    // ✅ Restore Chatbot State on Refresh
    fetch("chatbot.php", {
        method: "POST",
        body: new URLSearchParams({ message: "restore_session" }),
        headers: { "Content-Type": "application/x-www-form-urlencoded" }
    })
    .then(response => response.json())
    .then(data => {
        console.log("Chat State:", data.chat_state);
        if (data.chat_state === "ask_name") {
            displayBotMessage("Hello! What is your name?");
        } else if (data.chat_state === "ask_contact") {
            displayBotMessage("Nice to meet you! Please provide your email or phone number.");
        } else if (data.chat_state === "conversation") {
            displayBotMessage("How can I assist you today?");
        }
    });

    // ✅ Toggle Questions Dropdown
    toggleBtn.addEventListener("click", function () {
        questionsContainer.classList.toggle("show");
        toggleBtn.textContent = questionsContainer.classList.contains("show") ? "Questions ▴" : "Questions ▾";
    });

    // ✅ Load questions dynamically from the database
    fetch("fetch_questions.php")
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                questionsContainer.innerHTML = ""; // Clear before adding new questions
                data.questions.forEach(question => {
                    const button = document.createElement("button");
                    button.classList.add("question-btn");
                    button.innerText = question;
                    button.addEventListener("click", function () {
                        handleUserQuestion(question);
                    });
                    questionsContainer.appendChild(button);
                });
            }
        })
        .catch(error => console.error("Error fetching questions:", error));

    // ✅ Handle User Input Submission
    sendBtn.addEventListener("click", function () {
        let message = userInput.value.trim();
        if (message !== "") {
            handleUserQuestion(message);
            userInput.value = "";
        }
    });

    userInput.addEventListener("keypress", function (event) {
        if (event.key === "Enter") {
            sendBtn.click();
        }
    });

    // ✅ Handle Clicked Questions and User Input
    function handleUserQuestion(message) {
        displayUserMessage(message);
        fetchBotResponse(message);
    }

    // ✅ Function to Send Message to Backend and Fetch Response
    function fetchBotResponse(message) {
        let typingIndicator = displayTypingEffect(); // Show typing effect

        setTimeout(() => {
            fetch("chatbot.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: new URLSearchParams({ message })
            })
            .then(response => response.json())
            .then(data => {
                typingIndicator.remove(); // Remove typing effect
                displayBotMessage(data.response);
            })
            .catch(error => {
                typingIndicator.remove();
                console.error("Fetch error:", error);
                displayBotMessage("⚠️ Error fetching response. Please try again.");
            });
        }, 800); // 800ms delay to simulate typing
    }

    // ✅ Function to Display User Message
    function displayUserMessage(message) {
        let userMessage = document.createElement("div");
        userMessage.className = "chat-message user-message";
        userMessage.textContent = message;
        chatBox.appendChild(userMessage);
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    // ✅ Function to Display Bot Message
    function displayBotMessage(message) {
        let botMessage = document.createElement("div");
        botMessage.className = "chat-message bot-message";
        botMessage.textContent = message;
        chatBox.appendChild(botMessage);
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    // ✅ Function to Show Typing Indicator
    function displayTypingEffect() {
        let typingIndicator = document.createElement("div");
        typingIndicator.className = "chat-message bot-message typing";
        typingIndicator.textContent = "Typing...";
        chatBox.appendChild(typingIndicator);
        chatBox.scrollTop = chatBox.scrollHeight;
        return typingIndicator;
    }

    // ✅ Chatbot Icon Click to Toggle
    chatbotIcon.addEventListener("click", function () {
        chatbotContainer.style.display = "flex";
    });

    // ✅ Close Button Click to Hide Chatbot
    closeChatbot.addEventListener("click", function () {
        chatbotContainer.style.display = "none";
    });

});


</script>



</body>
</html>

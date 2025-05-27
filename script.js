// Get the modal
var modal = document.getElementById('id01');

// Close modal when clicking outside
window.onclick = function(event) {
  if (event.target == modal) {
    modal.style.display = "none";
  }
}

function togglePasswordVisibility() {
    var passwordInput = document.getElementById("password");
    var eyeIcon = document.getElementById("eye-icon");

    if (passwordInput.type === "password") {
        passwordInput.type = "text";
        eyeIcon.src = "http://localhost/election/bg-image/open-eye.png";
    } else {
        passwordInput.type = "password";
        eyeIcon.src = "http://localhost/election/bg-image//close-eye.png";
    }
}

document.addEventListener('DOMContentLoaded', function () {
    var emailInput = document.getElementById('email');
    var passwordInput = document.getElementById("password");
    var rememberMeCheckbox = document.getElementById('rememberMe');

    // Input border color changes on focus and blur
    emailInput.addEventListener('click', function () {
        this.style.borderColor = '#ff0000';
    });

    emailInput.addEventListener('blur', function () {
        if (this.value.trim() !== '') {
            this.style.borderColor = '';
        }
    });

    passwordInput.addEventListener('click', function () {
        this.style.borderColor = '#ff0000';
    });

    passwordInput.addEventListener('blur', function () {
        if (this.value.trim() !== '') {
            this.style.borderColor = '';
        }
    });

    // Change password text color to gray when user starts typing
    passwordInput.addEventListener('input', function () {
        if (this.value.trim() !== "") {
            this.classList.add("filled");
        } else {
            this.classList.remove("filled");
        }
    });

    // Load saved email if it exists
    if (localStorage.getItem('rememberMe') === 'true') {
        emailInput.value = localStorage.getItem('email');
        rememberMeCheckbox.checked = true;
    }

    document.getElementById('loginForm').addEventListener('submit', function (event) {
        if (!validateForm()) {
            event.preventDefault(); // prevent submit if validation fails
            return;
        }

        if (rememberMeCheckbox.checked) {
            localStorage.setItem('email', emailInput.value);
            localStorage.setItem('rememberMe', 'true');
        } else {
            localStorage.removeItem('email');
            localStorage.removeItem('rememberMe');
        }
        // form submits normally
    });
});

function checkInputValidity(inputId) {
    var input = document.getElementsByName(inputId)[0];
    var errorDiv = document.getElementById(inputId + '-error');

    if (input.value === "" || (errorDiv && errorDiv.innerHTML !== "")) {
        input.classList.add("red-placeholder");
    } else {
        input.classList.remove("red-placeholder");
    }
}

const sliderWrapper = document.getElementById('slider-wrapper');
const slides = document.querySelectorAll('.slide');
let index = 0;

setInterval(() => {
  index = (index + 1) % slides.length;
  sliderWrapper.style.transform = `translateX(-${index * 100}%)`;
}, 5000); // Change every 5 seconds


function validateForm() {
    checkInputValidity('email');
    checkInputValidity('password');

    return (!document.getElementsByName('email')[0].classList.contains("red-placeholder") &&
        !document.getElementsByName('password')[0].classList.contains("red-placeholder"));
}
// Left Scrolling Slideshow
let scrollIndex = 0;

function startScrollingSlideshow() {
  const container = document.querySelector(".slideshow-container");
  const slides = document.querySelectorAll(".slide-img");
  const totalSlides = slides.length;
  const slideWidth = slides[0].clientWidth;

  setInterval(() => {
    scrollIndex = (scrollIndex + 1) % totalSlides;
    container.scrollTo({
      left: scrollIndex * slideWidth,
      behavior: "smooth"
    });
  }, 3000);
}

document.addEventListener("DOMContentLoaded", startScrollingSlideshow);
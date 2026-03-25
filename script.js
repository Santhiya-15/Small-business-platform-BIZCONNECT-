const testimonials = [
  "Outstanding service and support!",
  "Highly professional team.",
  "Helped our startup scale quickly."
];

let tIndex = 0;

function showTestimonial(){
  document.getElementById("testimonialText").innerText = testimonials[tIndex];
}

function nextTestimonial(){
  tIndex = (tIndex + 1) % testimonials.length;
  showTestimonial();
}

function prevTestimonial(){
  tIndex = (tIndex - 1 + testimonials.length) % testimonials.length;
  showTestimonial();
}

setInterval(nextTestimonial, 4000);

document.getElementById("contactForm").addEventListener("submit", async e=>{
  e.preventDefault();
  
  const nameInput = document.getElementById("name");
  const emailInput = document.getElementById("email");
  const messageInput = document.getElementById("message");
  const formMessage = document.getElementById("formMessage");
  const submitBtn = e.target.querySelector("button[type='submit']");
  
  // Clear previous message
  formMessage.style.display = "none";
  
  // Validate inputs
  if(!nameInput.value.trim() || !emailInput.value.trim() || !messageInput.value.trim()){
    showMessage("Please fill all fields", "error");
    return;
  }
  
  // Disable submit button
  submitBtn.disabled = true;
  submitBtn.textContent = "Sending...";
  
  try {
    // Create FormData for the request
    const formData = new FormData();
    formData.append("name", nameInput.value.trim());
    formData.append("email", emailInput.value.trim());
    formData.append("message", messageInput.value.trim());
    
    // Send to PHP backend
    const response = await fetch("handle_contact.php", {
      method: "POST",
      body: formData
    });
    
    const data = await response.json();
    
    if (data.success) {
      showMessage(data.message, "success");
      // Reset form
      document.getElementById("contactForm").reset();
    } else {
      const errorMsg = data.errors ? data.errors.join(", ") : data.message;
      showMessage(errorMsg, "error");
    }
  } catch (error) {
    showMessage("Error sending message. Please try again.", "error");
    console.error("Error:", error);
  } finally {
    // Re-enable submit button
    submitBtn.disabled = false;
    submitBtn.textContent = "Send Message";
  }
});

function showMessage(message, type){
  const formMessage = document.getElementById("formMessage");
  formMessage.textContent = message;
  formMessage.style.display = "block";
  
  if(type === "success"){
    formMessage.style.background = "rgba(34, 197, 94, 0.2)";
    formMessage.style.color = "#86efac";
    formMessage.style.border = "1px solid rgba(34, 197, 94, 0.4)";
  } else {
    formMessage.style.background = "rgba(239, 68, 68, 0.2)";
    formMessage.style.color = "#fca5a5";
    formMessage.style.border = "1px solid rgba(239, 68, 68, 0.4)";
  }
}

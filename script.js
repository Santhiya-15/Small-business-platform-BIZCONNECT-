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

document.getElementById("contactForm").addEventListener("submit", e=>{
  e.preventDefault();
  if(!name.value.trim() || !email.value.trim() || !message.value.trim()){
    alert("Please fill all fields");
  } else {
    alert("Message Sent Successfully!");
  }
});

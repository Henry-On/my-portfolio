
document.addEventListener("DOMContentLoaded", init_App);
function init_App() { 

  const body = document.querySelector('body');
  const header = document.querySelector(".header");
  let modeSwitch = document.querySelector("#mode-switch");
  const menuHamburger = document.getElementById("menu-hamburger");

    // set current year for footer
  document.getElementById("current-year").innerText = new Date().getFullYear();

  let lightmode = localStorage.getItem("lightmode");

  const enableLightmode = function () {
    document.documentElement.setAttribute("data-theme", "light");
    localStorage.setItem("lightmode", "active");
  }
  
  const disableLightmode = function () {
    document.documentElement.removeAttribute("data-theme");
    localStorage.setItem("lightmode", null);
  }

  if(lightmode === "active") {
    enableLightmode();
    modeSwitch.classList.replace("fa-sun", "fa-moon");
  }

  modeSwitch.addEventListener("click", (event) => {
    lightmode = localStorage.getItem("lightmode");
    lightmode !== "active" ? enableLightmode() : disableLightmode();

    toggleModeIcon(event.target); //change the mode icon

  });

  function toggleModeIcon (btn) {
    if(btn.classList.contains("fa-sun")) {
      btn.classList.replace("fa-sun", "fa-moon");
    }
    else if(btn.classList.contains("fa-moon")) {
      btn.classList.replace("fa-moon", "fa-sun");
    }
    else {
      return;
    }
  }
  
  // prevent hiding of header when mouse is over it
  let isMouseOnHeader = false;
  header.addEventListener("mouseenter", () => {
    isMouseOnHeader = true;
  })
  header.addEventListener("mouseleave", () => {
    isMouseOnHeader = false;
  })

  hideHeaderAfterScroll();
  function hideHeaderAfterScroll() {

    let scrollTimeout;

    window.addEventListener("scroll", () => {

      clearTimeout(scrollTimeout); 
      body.classList.add("show-header");

      scrollTimeout = setTimeout(() => {
        if(!body.classList.contains("menu-hidden")) return;
        if(isMouseOnHeader) return;

        body.classList.remove("show-header");
      }, 5000);

    });
  }

  menuHamburger.addEventListener("click", function() {
    this.classList.toggle("animate");
    body.classList.toggle("menu-hidden");
  });

  document.getElementById("user-message").addEventListener("submit", (e) => {

    e.preventDefault();
    let messageForm = e.target;
    let submitBtn = messageForm.querySelector("#btn-send-message");
    let formData = new FormData(messageForm);

    const responseFields = Array.from(document.querySelectorAll(".form-response"));
    
    let preSubmitText = submitBtn.textContent;
    submitBtn.textContent = "Processing";
    submitBtn.classList.add("processing");

    fetch("/send-mail.php", {
      method: "POST",
      body: formData
    })
    .then(response => response.text())
    .then(text => {
      if(!text) throw new Error(`Empty response`);
      return JSON.parse(text);
    })
    .then(data => {
      console.log(data);
      if(data.errors) showResponse(data.errors);

      if(data.success){

        responseFields.forEach((e) => e.innerHTML = ""); // clear all old form errors

        // print successful message
        const msgbody = messageForm.querySelector(".response-body");
        msgbody.classList.add("success");
        msgbody.innerHTML = "I have received your message, I will reply soon!";

        messageForm.reset(); // reset the form field values
      }
      
    })
    .catch(error => console.log(error))
    .finally(()=>{
      submitBtn.textContent = preSubmitText;
      submitBtn.classList.remove("processing");
    });

    function showResponse(objErrors) {

      // check if errors exist
      if (objErrors === undefined)  return;

      responseFields.forEach((e) => {
        const responseClass = Array.from(e.classList).find(c => c.startsWith('response-'));
        if (responseClass) {
          const name = responseClass.replace('response-', '');
          e.innerHTML = name in objErrors ? objErrors[name] : "";
        }
      })  
    }


  });

  Array.from(document.querySelectorAll("[data-scrollTo]")).forEach(function(e) {
    e.addEventListener("click", (current) => {
      current.preventDefault();
      let section = e.getAttribute("data-scrollTo");
      document.getElementById(section).scrollIntoView({ behavior: 'smooth' });
    })
  });

  // auto increment textarea height on user ipnut
  document.getElementsByTagName("textarea")[0].addEventListener('input', function () {
    this.style.height = 'auto';
    this.style.height = this.scrollHeight + 'px';
  });

  const sections = document.querySelectorAll(".section");
  const links = document.querySelectorAll(".menu-link");

  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if(entry.isIntersecting) {
        links.forEach((link)=>{
          link.classList.remove('active');
          if(link.getAttribute("data-scrollTo") === entry.target.id) link.classList.add("active");
        });
      }
    });
  }, {
    threshold: 0.3
  });

  sections.forEach(section => {
    observer.observe(section);
  });

  // Register ScrollTrigger
  gsap.registerPlugin(ScrollTrigger);

  const hTL = gsap.timeline();
  hTL.from("#hero-section .wrapper-texts", {y:-50, opacity:0, scale:0.9, duration:1})
  .call(()=>document.querySelector("body").classList.add('show-header'));

  gsap.utils.toArray(".fade-in").forEach(element => {
    gsap.from(element, {
      y: 50,
      opacity: 0,
      duration: 1,
      ease: "power3",
      scrollTrigger: {
        trigger: element,
        start: "top 80%",
        toggleActions: "play none none reset",
      }
    });
  });

  gsap.utils.toArray(".slide-in").forEach(element => {
    gsap.from(element, {
      y: 50,
      duration: 1,          
      ease: "power3",   
      scrollTrigger: {
        trigger: element,
        start: "top 80%",
        toggleActions: "play none none reset",
      }
    });
  });

  gsap.from(".tech-skill", {
    duration: .5, 
    scale: 0.8,
    opacity: 0, 
    stagger: 0.1, 
    ease: "power3.inOut",
    scrollTrigger: {
      trigger: ".tech-skill",
      start: "top 90%",   // trigger when element is 50% into viewport
      toggleActions: "play none none reset",
    }
  
  });

  gsap.from(".project-card", {
    duration: .75, 
    scale: 0.8,
    opacity: 0, 
    stagger: 0.1, 
    ease: "power3.inOut",
    scrollTrigger: {
      trigger: ".project-card",
      start: "top 70%",
      toggleActions: "play none none reset",
    }
  
  });

  gsap.from(".form-field", {
    y: 50,
    duration: 0.5,
    stagger: 0.07, 
    ease: "power3.inOut",
    scrollTrigger: {
      trigger: ".form-field",
      start: "top 90%", 
      toggleActions: "play none none reset",
    }
  
  });

}




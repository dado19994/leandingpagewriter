/* HAMBURGER */
const hamburger = document.getElementById("hamburger");
const navMenu = document.getElementById("navMenu");

hamburger.addEventListener("click", () => {
  hamburger.classList.toggle("active");
  navMenu.classList.toggle("active");
});

/* DROPDOWN */
const dropdown = document.querySelector(".dropdown");
dropdown.querySelector("button").addEventListener("click", e => {
  e.stopPropagation();
  dropdown.classList.toggle("open");
});
document.addEventListener("click", () => dropdown.classList.remove("open"));

/* ABOUT */
const aboutBox = document.getElementById("aboutBox");
const aboutSection = document.getElementById("about");
window.addEventListener("scroll", () => {
  const r = aboutSection.getBoundingClientRect();
  if (r.top < window.innerHeight * 0.6) aboutBox.classList.add("open");
});

/* GALLERY */
document.querySelectorAll(".gallery-grid img").forEach(img => {
  img.addEventListener("click", () => {
    document.getElementById("lightbox-img").src = img.src;
    document.getElementById("lightbox").classList.add("active");
  });
});
document.querySelector(".lightbox-close").onclick = () =>
  document.getElementById("lightbox").classList.remove("active");

/* RECENSIONI AUTO */
const reviews = [
  {
    name: "Giulia R.",
    stars: "★★★★★",
    text: "Un libro che mi ha profondamente emozionata.",
    img: "https://randomuser.me/api/portraits/women/45.jpg"
  },
  {
    name: "Luca M.",
    stars: "★★★★☆",
    text: "Scrittura intensa e coinvolgente. Lo consiglio.",
    img: "https://randomuser.me/api/portraits/men/32.jpg"
  },
  {
    name: "Sara P.",
    stars: "★★★★★",
    text: "Parole che restano dentro.",
    img: "https://randomuser.me/api/portraits/women/68.jpg"
  },
  {
    name: "Marco D.",
    stars: "★★★★★",
    text: "Una voce autentica e potente.",
    img: "https://randomuser.me/api/portraits/men/76.jpg"
  }
];

const wheel = document.getElementById("reviewsWheel");
let index = 0;

function renderWheel() {
  wheel.innerHTML = "";

  for (let i = 0; i < 3; i++) {
    const review = reviews[(index + i) % reviews.length];
    const div = document.createElement("div");

    div.className = `review-item review-pos-${i}`;
    div.innerHTML = `
      <div class="review-header">
        <img src="${review.img}">
        <div>
          <strong>${review.name}</strong>
          <div class="stars">${review.stars}</div>
        </div>
      </div>
      <p class="review-text">${review.text}</p>
    `;

    wheel.appendChild(div);
  }
}

renderWheel();

setInterval(() => {
  index = (index + 1) % reviews.length;
  renderWheel();
}, 4000);


/* NEWSLETTER */
document.getElementById("newsletterForm").onsubmit = e => {
  e.preventDefault();
  document.getElementById("newsletterResult").textContent = "Iscrizione completata 💌";
  e.target.reset();
};

/* CONTATTI */
document.getElementById("contactForm").onsubmit = e => {
  e.preventDefault();
  document.getElementById("contactResult").textContent = "Messaggio inviato 💌";
  e.target.reset();
};




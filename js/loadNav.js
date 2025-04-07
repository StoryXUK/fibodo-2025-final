// Function to load content dynamically, with optional callback
function loadContent(elementId, file, callback) {
  const element = document.getElementById(elementId);

  fetch(file)
    .then((response) => {
      if (!response.ok) {
        throw new Error(`Error loading ${file}: ${response.statusText}`);
      }
      return response.text();
    })
    .then((html) => {
      element.innerHTML = html;
      if (typeof callback === "function") {
        callback(); // Run the callback after content is injected
      }
    })
    .catch((error) => {
      console.error(`Error fetching ${file}:`, error);
    });
}

// Function to set 'active' class on the current page's nav item
function highlightActiveNavItem() {
  const path = window.location.pathname.split("/").pop();
  const currentPage = path === "" ? "index.html" : path;

  const navLinks = document.querySelectorAll(".nav-link");

  navLinks.forEach(link => {
    const href = link.getAttribute("href");
    if (href === currentPage) {
      const navItem = link.closest(".nav-item");
      if (navItem) {
        navItem.classList.add("active");
      }
    }
  });
}
  
  // Load navigation and footer when the DOM is fully loaded
  document.addEventListener("DOMContentLoaded", function () {
    loadContent("navbar", "nav.html"); // Load the navigation
    loadContent("footer", "footer.html"); // Load the footer
    loadContent("pricing", "pricing.html"); // Load the pricing
    loadContent("core-features", "core-features.html"); // Load the features
    loadContent("additional-features", "additional-features.html"); // Load the features
  });
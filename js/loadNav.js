// Function to load content dynamically
function loadContent(elementId, file) {
    const element = document.getElementById(elementId);
  
    // Fetch the content from the specified file
    fetch(file)
      .then((response) => {
        if (!response.ok) {
          throw new Error(`Error loading ${file}: ${response.statusText}`);
        }
        return response.text();
      })
      .then((html) => {
        element.innerHTML = html;
      })
      .catch((error) => {
        console.error(`Error fetching ${file}:`, error);
      });
  }
  
  // Load navigation and footer when the DOM is fully loaded
  document.addEventListener("DOMContentLoaded", function () {
    loadContent("navbar", "nav.html"); // Load the navigation
    loadContent("footer", "footer.html"); // Load the footer
  });
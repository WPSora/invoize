const invoize_admin_js = {
  updateDatabase: async (dom) => {
    // Disable the button and update its text
    dom.setAttribute("disabled", true);
    const oldText = dom.innerHTML;
    dom.innerHTML = "Updating...";
    console.log(invoize_admin.nonce_for_invoize);
    try {
      // Make the API request
      const response = await fetch(
        `${invoize_admin.site_url}/wp-json/invoize/api/misc/update-database`,
        {
          method: "GET",
          "X-WP-Nonce": invoize_admin.nonce_for_invoize,
        }
      );

      // Parse the JSON response
      const result = await response.json();

      // Update the message element
      const domMessage = document.getElementById(
        "invoize-update-database-message"
      );

      domMessage.style.display = "block"; // Correctly set the display style
      domMessage.textContent = result.message; // Use textContent instead of text

      if (domMessage && response.status != 200) {
        domMessage.style.color = "#c0392b"; // Correctly set the display style
      } else {
        domMessage.style.color = "#27ae60";
        dom.remove();
      }
    } catch (error) {
      console.error("Error during database update:", error);

      // Optionally, display an error message in the UI
      const domMessage = document.getElementById(
        "invoize-update-database-message"
      );
      if (domMessage) {
        domMessage.style.display = "block";
        domMessage.style.color = "red"; // Correctly set the display style
        domMessage.textContent =
          "An error occurred while updating the database.";
      }
    } finally {
      // Re-enable the button and restore its original text
      dom.removeAttribute("disabled");
      dom.innerHTML = oldText;
    }
  },
};

// Plugin code may 1 2024
// Bill
(function($) {
    $.fn.showToast = function(message, duration, status) {
      if (!duration) {
        duration = 5000; // Default duration in milliseconds
      }
  
      var backgroundColor;
      switch (status) {
        case 'ok':
          backgroundColor = '#008000'; // Green
          break;
        case 'nok':
          backgroundColor = '#FF0000'; // Red
          break;
        case 'info':
          backgroundColor = '#007bff'; // Blue (suggestion for info)
          break;
        default:
          backgroundColor = '#333'; // Default for unknown status
      }
  
      // Create the toast container
      var toastContainer = $('<div class="toast"></div>');
      toastContainer.css({
        'position': 'fixed',
        'top': '40px', // Fixed position at the top
        'right': '20px',
        'z-index': 1000,
        'width': '200px',
        'padding': '10px',
        'background-color': backgroundColor,
        'color': '#fff',
        'border-radius': '5px',
        'opacity': 0,
        'transition': 'opacity 0.5s ease-in-out'
      });
  
      // Add the message content
      var toastMessage = $('<span>' + message + '</span>');
      toastMessage.css({
        'display': 'block',
        'margin': '0 auto'
      });
  
      // Append the message to the container
      toastContainer.append(toastMessage);
  
      // Add the toast container to the body
      $('body').append(toastContainer);
  
      // Animate the toast in (fade in)
      toastContainer.animate({
        opacity: 1
      }, 500);
  
      // Set a timeout to animate the toast out (fade out) after the specified duration
      setTimeout(function() {
        toastContainer.animate({
          opacity: 0
        }, 500, function() {
          toastContainer.remove(); // Remove the toast container from the DOM
        });
      }, duration);
    };
  })(jQuery);
  
  /*
  // Usage example with status parameter
  $(document).ready(function() {
    // Show a toast with the message "Success" for 3 seconds, with status "ok" (green background)
    $('body').showToast('Success', 3000, 'ok');
  
    // Show a toast with the message "Error" for 2 seconds, with status "nok" (red background)
    $('body').showToast('Error', 2000, 'nok');
  
    // Show a toast with the message "Information" for 5 seconds, with status "info" (blue background)
    $('body').showToast('Information', 5000, 'info');
  });
  */
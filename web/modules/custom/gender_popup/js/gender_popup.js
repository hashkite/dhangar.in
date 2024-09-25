(function ($, Drupal) {
    Drupal.behaviors.genderPopup = {
      attach: function (context, settings) {
        // Only show the popup once when the page loads.
        if (!$('#gender-selection-popup').length) {
          // Create the popup modal container.
          var popupHtml = '<div id="gender-selection-popup" class="gender-popup">' +
                          '<div class="popup-content">' +
                          '<div id="popup-form"></div>' +
                          '</div></div>';
  
          // Append the popup container to the body.
          $('body').append(popupHtml);
  
          // Load the gender selection form via AJAX into the popup.
          $('#popup-form').load('/gender-selection-form', function () {
            // After form loads, open the popup.
            $('#gender-selection-popup').fadeIn();
          });
  
          // Prevent the popup from being closed until gender is selected.
          $(document).on('click', function (e) {
            if ($(e.target).closest('#gender-selection-popup').length === 0) {
              e.stopPropagation();
              e.preventDefault();
            }
          });
  
          // AJAX form submission handling.
          $(document).on('submit', '#gender-selection-form', function (e) {
            e.preventDefault();  // Prevent default form submission.
  
            // Submit the form using AJAX.
            $.ajax({
              type: 'POST',
              url: $(this).attr('action'),
              data: $(this).serialize(),
              success: function (response) {
                // Handle success, close the popup.
                $('#gender-selection-popup').fadeOut();
                $('#gender-selection-popup').remove();
                alert('Gender saved successfully!');
              },
              error: function () {
                // Handle error.
                alert('There was an error saving your gender. Please try again.');
              }
            });
          });
        }
      }
    };
  })(jQuery, Drupal);
  
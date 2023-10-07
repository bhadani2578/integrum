
$(document).ready(function() {
  $('#dropdown_name').on('change', function() {
      var selectedValue = $(this).val();

      // Make an AJAX request
      $.ajax({
          url: "{{ route('dashboard', ['id' => base64_encode("+selectedValue+")]) }}",
          method: 'GET',
          
          success: function(response) {
              // Handle the response from the server
              console.log(response);
          },
          error: function(xhr, status, error) {
              // Handle any errors that occur during the AJAX request
              console.error(error);
          }
      });
  });
});
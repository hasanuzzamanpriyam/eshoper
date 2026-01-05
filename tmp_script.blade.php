        <script>
        // Handle empty input - show sort buttons
        jQuery(document).on('input', '#search-input', function() {
            const query = jQuery(this).val().trim();
            
            if (query.length < 1) {
                jQuery('#search-results-ajax').html('');
                jQuery('.sort-buttons-wrapper').removeClass('d-none');
            }
        });
        </script>

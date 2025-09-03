/**
 * Theme Scripts
 *
 * @package BST
 */

(function($) {
    'use strict';
    
    $(document).on('change', '.bst-casinos-table .bst-middle-select', function() {
        var $container = $(this).closest('.bst-casinos-table');
        var selectedField = $(this).val();
        var $tableBody = $container.find('tbody');
        
        var loadingHtml = '';
        for (var i = 0; i < 3; i++) {
            loadingHtml += '<tr>' +
                '<td><div class="bst-skeleton" style="width:180px"></div></td>' +
                '<td class="d-none d-sm-table-cell"><div class="bst-skeleton"></div></td>' +
                '<td><div class="bst-skeleton" style="width:80px"></div></td>' +
                '</tr>';
        }
        $tableBody.html(loadingHtml);
        
        $.ajax({
            url: bstAjax.ajax_url,
            method: 'POST',
            data: {
                action: 'bst_template2_change',
                nonce: bstAjax.nonce,
                field: selectedField
            },
            success: function(response) {
                if (response && response.success) {
                    $tableBody.html(response.data.rows);
                }
            },
            error: function() {
                $tableBody.html('<tr><td colspan="3">Error loading data</td></tr>');
            }
        });
    });
    
})(jQuery);

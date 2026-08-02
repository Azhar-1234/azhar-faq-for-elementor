(function ($) {
  'use strict';

  var settings = window.azhafafoFaqAdmin || {};

  $(function () {
    var $panel = $('#azhafafo_faq_product_data');
    if (!$panel.length) return;

    var $rows = $panel.find('.azhafafo-faq-rows');
    var $empty = $panel.find('.azhafafo-faq-empty');
    var template = $('#tmpl-azhafafo-faq-row').html() || '';
    var nextIndex = $rows.find('.azhafafo-faq-row').length;

    function toggleEmptyNotice() {
      $empty.toggle($rows.find('.azhafafo-faq-row').length === 0);
    }

    // Add a row
    $panel.on('click', '.azhafafo-add-faq', function (e) {
      e.preventDefault();

      var markup = template.split('__INDEX__').join(nextIndex);
      nextIndex++;

      var $row = $(markup).appendTo($rows);
      $row.addClass('is-open').find('.azhafafo-question-input').trigger('focus');
      toggleEmptyNotice();
    });

    // Remove a row
    $panel.on('click', '.azhafafo-remove-faq', function (e) {
      e.preventDefault();

      if (settings.confirmRemove && !window.confirm(settings.confirmRemove)) return;

      $(this).closest('.azhafafo-faq-row').remove();
      toggleEmptyNotice();
    });

    // Collapse / expand a row
    $panel.on('click', '.azhafafo-toggle-faq, .azhafafo-faq-row-title', function (e) {
      e.preventDefault();
      $(this).closest('.azhafafo-faq-row').toggleClass('is-open');
    });

    // Keep the row header in sync with the question
    $panel.on('input', '.azhafafo-question-input', function () {
      var value = $.trim($(this).val());
      $(this)
        .closest('.azhafafo-faq-row')
        .find('.azhafafo-faq-row-title')
        .text(value !== '' ? value : settings.newItemLabel || 'New FAQ item');
    });

    // Reorder. Input names keep their original index; PHP re-indexes on save,
    // so only the submitted order matters.
    if ($.fn.sortable) {
      $rows.sortable({
        handle: '.azhafafo-faq-handle',
        axis: 'y',
        placeholder: 'azhafafo-faq-placeholder',
        forcePlaceholderSize: true
      });
    }

    toggleEmptyNotice();
  });
})(jQuery);

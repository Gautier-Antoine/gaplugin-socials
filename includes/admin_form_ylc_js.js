jQuery(document).ready(function () {
  jQuery(".form-table tbody").sortable({
    // axis: "y",
    items: 'tr:not(:first)'
  });
});

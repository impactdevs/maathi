// Call the dataTables jQuery plugin
$(document).ready(function() {
  $('#dataTable').DataTable(
    //add export buttons
    {
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ],
    }
  );
});

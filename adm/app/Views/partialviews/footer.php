<script src="<?php echo base_url("external/admlte/plugins/jquery/jquery.min.js")?>"></script>
<script src="<?php echo base_url("external/admlte/plugins/bootstrap/js/bootstrap.bundle.min.js")?>"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js" integrity="sha512-Tn2m0TIpgVyTzzvmxLNuqbSJH3JP8jm+Cy3hvHrW7ndTDcJ1w5mBiksqDBb8GpE2ksktFvDB/ykZ0mDpsZj20w==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script src="<?php echo base_url("external/admlte/plugins/datatables/jquery.dataTables.js")?>"></script>
<script src="<?php echo base_url("external/admlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js")?>"></script>
<script src="<?php echo base_url("external/admlte/plugins/datatables-responsive/js/dataTables.responsive.min.js")?>"></script>
<script src="<?php echo base_url("external/admlte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js")?>"></script>
<script src="<?php echo base_url("external/admlte/plugins/datatables-buttons/js/dataTables.buttons.min.js")?>"></script>
<script src="<?php echo base_url("external/admlte/plugins/datatables-buttons/js/buttons.bootstrap4.min.js")?>"></script>
<script src="<?php echo base_url("external/admlte/plugins/jszip/jszip.min.js")?>"></script>
<script src="<?php echo base_url("external/admlte/plugins/pdfmake/pdfmake.min.js")?>"></script>
<script src="<?php echo base_url("external/admlte/plugins/pdfmake/vfs_fonts.js")?>"></script>
<script src="<?php echo base_url("external/admlte/plugins/datatables-buttons/js/buttons.html5.min.js")?>"></script>
<script src="<?php echo base_url("external/admlte/plugins/datatables-buttons/js/buttons.print.min.js")?>"></script>
<script src="<?php echo base_url("external/admlte/plugins/datatables-buttons/js/buttons.colVis.min.js")?>"></script>

<script src="<?php echo base_url("external/admlte/dist/js/adminlte.min.js?v=3.2.0")?>"></script>

<script src="<?php echo base_url("external/admlte/plugins/inputmask/jquery.inputmask.js")?>"></script>
<script src="<?php echo base_url("external/js/includes.js")?>"></script>
<script src="<?php echo base_url("external/js/jquery.maskedinput.js")?>"></script>

<script src="<?php echo base_url("external/js/formularios.js")?>"></script>

<script>
  $(function () {
    $(".minhaDataTable").DataTable({
      "order":[],
      "responsive": true, "lengthChange": false, "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    $('#example2').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
    });
  });
</script>

</body>
</html>

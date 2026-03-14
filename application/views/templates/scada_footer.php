    </div><!-- /.content-wrapper -->
    <footer class="main-footer text-sm">
        <div class="float-right d-none d-sm-block"><span class="text-muted">SCADA IoT Platform v1.0</span></div>
        <strong>iBreathe</strong> &mdash; Home Air Quality Monitoring System
    </footer>
</div><!-- /.wrapper -->

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap 4 (required by AdminLTE 3) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE 3.2 -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.bundle.min.js"></script>
<!-- Moment.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<!-- DateRangePicker -->
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<!-- Toastr -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
    var BASE_URL = '<?= base_url() ?>';
    toastr.options = { closeButton: true, progressBar: true, positionClass: "toast-top-right", timeOut: 5000 };
</script>

<?php if (isset($page_js)): ?>
<script src="<?= base_url('assets_scada/js/' . $page_js) ?>"></script>
<?php endif; ?>
</body>
</html>

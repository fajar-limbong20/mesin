    </div>
</div>
</div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    if ($('.datatable').length) {
        $('.datatable').DataTable({
            responsive: true,
            pageLength: 10,
            language: {
                search: "Cari:",
                lengthMenu: "Tampil _MENU_ data",
                info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                infoEmpty: "Tidak ada data",
                zeroRecords: "Data tidak ditemukan",
                paginate: { first: "Awal", last: "Akhir", next: "→", previous: "←" }
            }
        });
    }
});
</script>
</body>
</html>
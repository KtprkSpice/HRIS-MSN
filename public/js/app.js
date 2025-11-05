//  Data table 
 $(document).ready(function() {
            var table = $('#tugasTable').DataTable({
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                    paginate: {
                        previous: "Sebelumnya",
                        next: "Berikutnya"
                    }
                }
            });

            // Filter dropdown
            $('#filterKaryawan, #filterStatus').on('change', function() {
                var karyawan = $('#filterKaryawan').val();
                var status = $('#filterStatus').val();
                table.rows().every(function() {
                    var rowKaryawan = this.data()[0];
                    var rowStatus = $(this.node()).find('td:eq(4)').text();
                    if (
                        (karyawan === "" || rowKaryawan.includes(karyawan)) &&
                        (status === "" || rowStatus.includes(status))
                    ) {
                        $(this.node()).show();
                    } else {
                        $(this.node()).hide();
                    }
                });
            });
        });
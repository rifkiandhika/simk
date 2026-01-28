<script>
    $(document).ready(function() {
        // Initialize DataTables for all tables
        const tableIds = [
            'internalPoTable',
            'internalGrTable', 
            'internalSuccessTable',
            'externalPoTable',
            'externalGrTable',
            'externalInvoiceTable'
        ];

        tableIds.forEach(function(tableId) {
            $(`#${tableId}`).DataTable({
                ordering: false,
                searching: false,
                lengthChange: false,
                info: false,
                paging: false,
                language: {
                    emptyTable: `
                        <div class="text-center py-5">
                            <i class="ri-inbox-line ri-3x text-muted d-block mb-3"></i>
                            <p class="text-muted mb-0">Tidak ada data</p>
                        </div>
                    `
                }
            });
        });

        // Auto dismiss alerts
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    });
</script>

<script>
    let submitModalInstance, deleteModalInstance;

    document.addEventListener('DOMContentLoaded', function() {
        submitModalInstance = new bootstrap.Modal(document.getElementById('submitModal'));
        deleteModalInstance = new bootstrap.Modal(document.getElementById('deleteModal'));
    });

    function submitPO(poId) {
        document.getElementById('poIdSubmit').value = poId;
        document.getElementById('pinSubmit').value = '';
        submitModalInstance.show();
    }

    function confirmSubmit() {
        const pin = document.getElementById('pinSubmit').value;
        const poId = document.getElementById('poIdSubmit').value;

        if (!pin || pin.length !== 6) {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: 'PIN harus 6 digit'
            });
            return;
        }

        fetch(`/po/${poId}/submit`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ pin: pin })
        })
        .then(response => response.json())
        .then(data => {
            if (data.message) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: data.message
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: data.error || 'Terjadi kesalahan'
                });
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan sistem'
            });
        });

        submitModalInstance.hide();
    }

    function deletePO(poId) {
        document.getElementById('poIdDelete').value = poId;
        document.getElementById('pinDelete').value = '';
        deleteModalInstance.show();
    }

    function confirmDelete() {
        const pin = document.getElementById('pinDelete').value;
        const poId = document.getElementById('poIdDelete').value;

        if (!pin || pin.length !== 6) {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: 'PIN harus 6 digit'
            });
            return;
        }

        fetch(`/po/${poId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ pin: pin })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: data.message
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: data.message || 'Terjadi kesalahan'
                });
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan sistem'
            });
        });

        deleteModalInstance.hide();
    }
</script>
<?= $this->include('template/header'); ?>

<div id="main">
    <div class="tabel-heading-gemoy">
        <h3>Data Artikel (AJAX)</h3>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 id="formTitle">Tambah Artikel Baru</h5>
        </div>
        <div class="card-body">
            <form id="artikelForm">
                <input type="hidden" id="artikelId" name="id">
                <div class="mb-3">
                    <label for="judul" class="form-label">Judul</label>
                    <input type="text" id="judul" name="judul" class="form-control" required>
                    <div class="invalid-feedback" id="judulError"></div>
                </div>
                <div class="mb-3">
                    <label for="isi" class="form-label">Isi Artikel</label>
                    <textarea id="isi" name="isi" rows="5" class="form-control" required></textarea>
                    <div class="invalid-feedback" id="isiError"></div>
                </div>
                <div style="text-align: right;">
                    <button type="submit" class="btn-edit-girly" style="padding: 10px 20px; font-weight: bold;" id="submitBtn">Simpan</button>
                    <button type="button" class="btn-edit-girly d-none" id="cancelBtn">Batal</button>
                </div>
            </form>
        </div>
    </div>

    <table class="table-data" id="artikelTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Judul</th>
                <th>Isi</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<script src="<?= base_url('assets/js/jquery-3.7.1.min.js') ?>"></script>
<script>
    $(document).ready(function() {

        function showLoadingMessage() {
            $('#artikelTable tbody').html('<tr><td colspan="5">Loading data...</td></tr>');
        }

        function clearForm() {
            $('#artikelForm')[0].reset();
            $('#artikelId').val('');
            $('#formTitle').text('Tambah Artikel Baru');
            $('#submitBtn').text('Simpan').removeClass('btn-warning').addClass('btn-edit-girly');
            $('#cancelBtn').addClass('d-none');
            $('#judul').removeClass('is-invalid');
            $('#judulError').text('');
            $('#isi').removeClass('is-invalid');
            $('#isiError').text('');
        }

        function loadData() {
            showLoadingMessage();
            $.ajax({
                url: "<?= base_url('ajax/data') ?>",
                method: "GET",
                dataType: "json",
                success: function(data) {
                    var tableBody = "";
                    if (data.length > 0) {
                        for (var i = 0; i < data.length; i++) {
                            var row = data[i];
                            tableBody += '<tr>';
                            tableBody += '<td>' + row.id + '</td>';
                            tableBody += '<td>' + row.judul + '</td>';
                            tableBody += '<td>' + row.isi.substring(0, 100) + '...</td>';
                            tableBody += '<td><span class="status">' + row.status + '</span></td>';
                            tableBody += '<td>';
                            tableBody += '<button class="btn-edit-girly btn-edit" data-id="' + row.id + '" data-judul="' + row.judul + '" data-isi="' + row.isi + '" data-status="' + row.status + '">Edit</button>';
                            tableBody += '<button class="btn-delete-girly btn-delete" data-id="' + row.id + '">Delete</button>';
                            tableBody += '</td>';
                            tableBody += '</tr>';
                        }
                    } else {
                        tableBody = '<tr><td colspan="5" style="text-align:center;">Tidak ada data artikel.</td></tr>';
                    }
                    $('#artikelTable tbody').html(tableBody);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    $('#artikelTable tbody').html('<tr><td colspan="5" class="text-danger">Error memuat data: ' + textStatus + ' - ' + errorThrown + '</td></tr>');
                }
            });
        }

        loadData();

        $(document).on('click', '.btn-delete', function(e) {
            e.preventDefault();
            var id = $(this).data('id');

            if (confirm('Apakah Anda yakin ingin menghapus artikel ini?')) {
                $.ajax({
                    url: "<?= base_url('ajax/delete/') ?>" + id,
                    method: "DELETE",
                    success: function(response) {
                        if (response.status === 'OK') {
                            alert('Artikel berhasil dihapus!');
                            loadData();
                        } else {
                            alert('Gagal menghapus artikel: ' + response.message);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        alert('Error deleting article: ' + textStatus + ' - ' + errorThrown);
                    }
                });
            }
        });

        $('#artikelForm').on('submit', function(e) {
            e.preventDefault();

            var id = $('#artikelId').val();
            var url = id ? "<?= base_url('ajax/update/') ?>" + id : "<?= base_url('ajax/create') ?>";
            var method = "POST";

            $('.form-control').removeClass('is-invalid');
            $('.invalid-feedback').text('');

            $.ajax({
                url: url,
                method: method,
                data: $(this).serialize(),
                dataType: "json",
                success: function(response) {
                    if (response.status === 'OK') {
                        alert(response.message);
                        clearForm();
                        loadData();
                    } else if (response.status === 'Validation Error') {
                        for (var key in response.errors) {
                            $('#' + key).addClass('is-invalid');
                            $('#' + key + 'Error').text(response.errors[key]);
                        }
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert('Terjadi kesalahan AJAX: ' + textStatus + ' - ' + errorThrown);
                }
            });
        });

        $(document).on('click', '.btn-edit', function() {
            var id = $(this).data('id');
            var judul = $(this).data('judul');
            var isi = $(this).data('isi');
            var status = $(this).data('status');

            $('#artikelId').val(id);
            $('#judul').val(judul);
            $('#isi').val(isi);

            $('#formTitle').text('Ubah Artikel (ID: ' + id + ')');
            $('#submitBtn').text('Update').removeClass('btn-edit-girly').addClass('btn-warning');
            $('#cancelBtn').removeClass('d-none');
        });

        $('#cancelBtn').on('click', function() {
            clearForm();
        });
    });
</script>

<?= $this->include('template/footer'); ?>
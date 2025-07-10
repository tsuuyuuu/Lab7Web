<?= $this->include('template/header_admin'); ?>

<div class="tabel-heading-gemoy">
    <h3><?= $title; ?></h3>
</div>

<div class="row mb-3">
    <div class="col-md-8">
        <form id="search-form">
            <input type="text" name="q" id="search-box" value="<?= esc($q); ?>"
                placeholder="Cari judul artikel...">

            <select name="kategori_id" id="filter-kategori">
                <option value="">Semua Kategori</option>
                <?php foreach ($kategori as $k): ?>
                    <option value="<?= esc($k['id_kategori']); ?>" <?= ($kategori_id == $k['id_kategori']) ? 'selected' : ''; ?>>
                        <?= esc($k['nama_kategori']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit">🔍 Cari</button>
        </form>

    </div>
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
        <!-- Heading Girly -->
        <div class="tabel-heading-gemoy flex-grow-1">
            <h3>📚 Daftar Artikel Kesayangan 💖</h3>
        </div>

        <!-- Tombol Tambah Artikel -->
        <div>
            <a href="<?= base_url('admin/artikel/add'); ?>" class="btn btn-add-girly">➕ Tambah Artikel Baru</a>
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Judul</th>
                <th>Kategori</th>
                <th>Gambar</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($artikel) > 0): ?>
                <?php foreach ($artikel as $row): ?>
                    <tr>
                        <td><?= $row['id']; ?></td>
                        <td>
                            <b><?= $row['judul']; ?></b>
                            <p><small><?= substr($row['isi'], 0, 50); ?></small></p>
                        </td>
                        <td><?= $row['nama_kategori']; ?></td>
                        <td>
                            <img src="<?= base_url('/gambar/' . $row['gambar']); ?>" alt="<?= esc($row['judul']); ?>" width="100">
                        </td>
                        <td><?= $row['status']; ?></td>
                        <td>
                            <a href="<?= base_url('/admin/artikel/edit/' . $row['id']); ?>" class="btn btn-edit-girly">✏️ Edit</a>
                            <a href="<?= base_url('/admin/artikel/delete/' . $row['id']); ?>" class="btn btn-delete-girly" onclick="return confirm('Yakin ingin menghapus?');">🗑️ Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center">Belum ada data.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <?= $pager->only(['q', 'kategori_id'])->links(); ?>

    <?= $this->include('template/footer_admin'); ?>
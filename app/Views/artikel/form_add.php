<?= $this->include('template/header_admin'); ?>

<h2 style="text-align: center; margin-top: 30px;"><?= $title; ?></h2>

<div style="display: flex; justify-content: center; margin-top: 20px;">

    <?php if (isset($validation)): ?>
        <div class="alert alert-danger">
            <?= $validation->listErrors(); ?>
        </div>
    <?php endif; ?>

    <form action="<?= base_url('admin/artikel/add') ?>" method="post" enctype="multipart/form-data" style="width: 100%; max-width: 600px;">

        <div style="margin-bottom: 15px;">
            <label for="judul" style="display: block; font-weight: bold; margin-bottom: 5px;">Judul Artikel</label>
            <input type="text" name="judul" id="judul" class="form-control" placeholder="Masukkan judul artikel" required style="width: 100%; padding: 10px;">
        </div>

        <div style="margin-bottom: 15px;">
            <label for="isi" style="display: block; font-weight: bold; margin-bottom: 5px;">Isi Artikel</label>
            <textarea name="isi" id="isi" cols="50" rows="10" class="form-control" placeholder="Tulis isi artikel..." required style="width: 100%; padding: 10px;"></textarea>
        </div>

        <div style="margin-bottom: 15px;">
            <label for="id_kategori" style="display: block; font-weight: bold; margin-bottom: 5px;">Kategori</label>
            <select name="id_kategori" id="id_kategori" class="form-control" required style="width: 100%; padding: 10px;">
                <?php foreach ($kategori as $k): ?>
                    <option value="<?= $k['id_kategori']; ?>"><?= $k['nama_kategori']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="margin-bottom: 15px;">
            <label for="gambar" style="display: block; font-weight: bold; margin-bottom: 5px;">Upload Gambar</label>
            <input type="file" name="gambar" id="gambar" class="form-control" style="width: 100%;">
        </div>

        <div style="text-align: right;">
            <input type="submit" value="Kirim" class="btn btn-primary" style="padding: 10px 20px; font-weight: bold;">
        </div>
    </form>
</div>


<?= $this->include('template/footer_admin'); ?>
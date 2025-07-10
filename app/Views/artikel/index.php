<?= $this->include('template/header'); ?>

<section>
    <h2><?= esc($title); ?></h2>

    <form method="get" action="<?= base_url('/artikel'); ?>" class="filter-kategori">
        <label for="kategori"><strong>Pilih Kategori:</strong></label>
        <select name="kategori" id="kategori" onchange="this.form.submit()" class="kategori-dropdown">
            <option value="">-- Semua Kategori --</option>
            <?php foreach ($kategoriList as $kategori): ?>
                <option value="<?= esc($kategori['slug_kategori']); ?>"
                    <?= ($kategoriDipilih === $kategori['slug_kategori']) ? 'selected' : ''; ?>>
                    <?= esc($kategori['nama_kategori']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <hr>

    <?php if ($artikel): ?>
        <?php foreach ($artikel as $row): ?>
            <article class="entry">
                <h2>
                    <a href="<?= base_url('/artikel/' . esc($row['slug'])); ?>">
                        <?= esc($row['judul']); ?>
                    </a>
                </h2>
                <img src="<?= base_url('/gambar/' . esc($row['gambar'])); ?>" alt="<?= esc($row['judul']); ?>" width="300">
                <p><?= esc(substr(strip_tags($row['isi']), 0, 200)); ?>...</p>
                <small><em>Kategori: <?= esc($row['nama_kategori']); ?></em></small>
            </article>
            <hr class="divider" />
        <?php endforeach; ?>
    <?php else: ?>
        <article class="entry">
            <h3>Tidak ada artikel untuk kategori ini.</h3>
        </article>
    <?php endif; ?>
</section>


<?= $this->include('template/footer'); ?>
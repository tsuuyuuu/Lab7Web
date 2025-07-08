<?= $this->include('template/header'); ?>

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
            </article>
            <hr class="divider" />
        <?php endforeach; ?>
    <?php else: ?>
        <article class="entry">
            <h3>Belum ada data.</h3>
        </article>
    <?php endif; ?> 
<?= $this->include('template/footer'); ?>

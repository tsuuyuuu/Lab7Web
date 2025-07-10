<h3 class="mb-3">Artikel Terkini</h3>
<ul class="list-group">
    <?php foreach ($artikel as $row): ?>
        <li class="list-group-item">
            <a href="<?= base_url('/artikel/' . esc($row['slug'])) ?>" class="text-decoration-none">
                <?= esc($row['judul']) ?>
            </a>
        </li>
    <?php endforeach; ?>
</ul>
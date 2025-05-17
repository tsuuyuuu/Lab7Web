<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title; ?></title>
    <link rel="stylesheet" href="<?= base_url('style.css'); ?>" >
</head>
<body>
    <div id="container">
        <header>
            <h1>Admin Portal Berita</h1>
        </header>
        <nav>
            <a href="<?= base_url('/artikel'); ?>" class="active">Home</a>
            <a href="<?= base_url('/admin/artikel'); ?>" >Artikel</a>
            <a href="<?= base_url('/admin/artikel/add/'); ?>" >Tambah Artikel</a>
            <a href="<?= base_url('/user/login/'); ?>" >Logout</a>
        </nav>
        <section id="wrapper">
            <section id="main">
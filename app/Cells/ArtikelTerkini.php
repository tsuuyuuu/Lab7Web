<?php

namespace App\Cells;

use CodeIgniter\View\Cell;
use App\Models\ArtikelModel;

class ArtikelTerkini extends Cell
{
    public function render() : string
    {
        $model = new ArtikelModel();
        $artikel = $model->orderBy('created_at', 'DESC')->set_time_limit(5)-findAll();
        
        return view('components/artikel_terkini', ['artikel' => $artikel]);
        
    }
}




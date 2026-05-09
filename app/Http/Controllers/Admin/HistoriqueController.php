<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HistoriqueOperation;

class HistoriqueController extends Controller
{
    public function index()
    {
        $historiques = HistoriqueOperation::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.historique.index', compact('historiques'));
    }
}

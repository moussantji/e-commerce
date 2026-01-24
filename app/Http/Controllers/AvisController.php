<?php

namespace App\Http\Controllers;

use App\Models\AvisClient;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class AvisController extends Controller
{
    public function index()
    {
        $avis = AvisClient::with(['user', 'product'])->latest()->paginate(10);
        return view('avis.index', compact('avis'));
    }

    public function show(AvisClient $avis)
    {
        $avis->load(['user', 'product']);
        return view('avis.show', compact('avis'));
    }

    public function export(AvisClient $avis)
    {
        // PDF ou Excel
        $avis->load(['user', 'product']);

        // Exemple PDF
        $pdf = PDF::loadView('avis.export-pdf', compact('avis'));
        return $pdf->download('avis-' . $avis->id . '.pdf');
    }

    public function destroy(AvisClient $avis)
    {
        $avis->delete();
        return redirect()->route('dashboard')
            ->with('success', 'Avis supprimé avec succès');
    }
}

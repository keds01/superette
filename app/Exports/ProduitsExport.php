<?php

namespace App\Exports;

use App\Models\Produit;
use App\Models\Categorie;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProduitsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $query;

    public function __construct($query = null)
    {
        $this->query = $query;
    }

    public function collection()
    {
        if ($this->query) {
            return $this->query;
        }
        return Produit::with(['categorie', 'uniteVente'])->get();
    }

    public function headings(): array
    {
        return [
            'Référence',
            'Nom',
            'Catégorie',
            'Stock',
            'Unité',
            'Prix d\'achat HT',
            'Prix de vente TTC',
            'Marge',
            'TVA',
            'Seuil d\'alerte',
            'Emplacement',
            'Date de péremption'
        ];
    }

    public function map($produit): array
    {
        return [
            $produit->reference,
            $produit->nom,
            $produit->categorie->nom,
            number_format($produit->stock, 2),
            $produit->uniteVente->symbole,
            number_format($produit->prix_achat_ht, 0, ',', ' ') . ' FCFA',
            number_format($produit->prix_vente_ttc, 0, ',', ' ') . ' FCFA',
            $produit->marge . '%',
            $produit->tva . '%',
            number_format($produit->seuil_alerte, 2),
            $produit->emplacement_rayon . ' - ' . $produit->emplacement_etagere,
            $produit->date_peremption ? $produit->date_peremption->format('d/m/Y') : '-'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
} 
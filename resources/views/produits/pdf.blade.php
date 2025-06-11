<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Liste des Produits</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20mm;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            font-size: 24px;
            margin: 0;
            color: #4f46e5; /* Couleur Indigo */
        }
        .header p {
            margin: 5px 0;
            color: #6b7280; /* Couleur Gray */
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            border-radius: 1rem; /* rounded-2xl */
            overflow: hidden; /* S'assure que les coins arrondis sont appliqués */
        }
        thead {
            background: linear-gradient(to top right, #eef2ff, #f3e8ff); /* bg-gradient-to-tr from-indigo-100 to-purple-100 */
        }
        th {
            padding: 12px 10px; /* py-3.5 px-3 */
            text-align: left;
            font-size: 10px; /* text-xs */
            font-weight: bold; /* font-bold */
            color: #374151; /* text-indigo-900 */
            border-bottom: 1px solid #e0e7ff; /* divide-indigo-100 */
        }
        th:first-child {
            padding-left: 24px; /* pl-6 */
        }
        th:last-child {
             padding-right: 24px; /* pr-6 */
        }
        tbody {
            background-color: rgba(255, 255, 255, 0.8); /* bg-white/80 */
            divide-y: 1px solid #e0e7ff; /* divide-indigo-50 */
        }
        td {
            padding: 16px 10px; /* py-4 px-3 */
            font-size: 12px; /* text-sm */
            color: #4b5563; /* text-indigo-700 ou 900 selon la colonne */
            border-bottom: 1px solid #e0e7ff; /* divide-indigo-50 */
        }
         td:first-child {
            padding-left: 24px; /* pl-6 */
        }
        td:last-child {
             padding-right: 24px; /* pr-6 */
        }
        .text-indigo-900 { color: #374151; }
        .text-indigo-700 { color: #4b5563; }
        .text-indigo-800 { color: #3b0764; }
        .text-red-600 { color: #dc2626; }
        .text-green-600 { color: #16a34a; }
        .text-gray-500 { color: #6b7280; }
        .font-medium { font-weight: 500; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .whitespace-nowrap { white-space: nowrap; }
        .status-badge {
            display: inline-block;
            padding: 2.5px 10px; /* px-2.5 py-0.5 */
            font-size: 10px; /* text-xs */
            font-weight: 500; /* font-medium */
            border-radius: 9999px; /* rounded-full */
        }
        .bg-red-100 { background-color: #fee2e2; }
        .text-red-800 { color: #991b1b; }
        .bg-green-100 { background-color: #d1fae5; }
        .text-green-800 { color: #065f46; }
        .object-cover { object-fit: cover; }
        .h-10 { height: 40px; }
        .w-10 { width: 40px; }
        .rounded-full { border-radius: 9999px; }
        .border-2 { border-width: 2px; }
        .border-indigo-200 { border-color: #e0e7ff; }
        .shadow { box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); }
         .image-placeholder {
            height: 40px;
            width: 40px;
            border-radius: 9999px;
            background-color: #e0e7ff; /* bg-indigo-100 */
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #e0e7ff; /* border-2 border-indigo-200 */
        }
        .image-placeholder svg {
             height: 24px; /* h-6 */
            width: 24px; /* w-6 */
            color: #a5b4fc; /* text-indigo-400 */
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Liste des Produits</h1>
        <p>Date d'édition : {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Image</th>
                <th>Nom</th>
                <th>Catégorie</th>
                <th class="text-center">Prix d'achat</th>
                <th class="text-center">Prix de vente</th>
                <th class="text-center">Stock</th>
                <th class="text-center">Emplacement</th>
                <th class="text-center">Péremption</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
            <tr>
                <td>
                    @if($product->image)
                        <img src="{{ Storage::url($product->image) }}" alt="{{ $product->nom }}" class="h-10 w-10 rounded-full object-cover border-2 border-indigo-200 shadow">
                    @else
                        <div class="image-placeholder">
                             <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    @endif
                </td>
                <td class="whitespace-nowrap text-indigo-900 font-medium">{{ $product->nom }}</td>
                <td class="whitespace-nowrap text-indigo-700">{{ $product->categorie->nom }}</td>
                <td class="whitespace-nowrap text-center text-indigo-800">{{ number_format($product->prix_achat_ht, 0, ',', ' ') }} FCFA</td>
                <td class="whitespace-nowrap text-center">
                     @if($product->promotion_active)
                        <div style="display: flex; flex-direction: column; align-items: center;">
                            <span style="text-decoration: line-through; color: #6b7280;">{{ number_format($product->prix_vente_ttc, 0, ',', ' ') }} FCFA</span>
                            <span style="color: #dc2626; font-weight: 500;">{{ number_format($product->prix_promo, 0, ',', ' ') }} FCFA</span>
                            <span style="font-size: 10px; color: #ef4444;">
                                @if($product->promotion_active->type === 'pourcentage')
                                    -{{ $product->promotion_active->valeur }}%
                                @else
                                    -{{ number_format($product->promotion_active->valeur, 0, ',', ' ') }} FCFA
                                @endif
                            </span>
                        </div>
                    @else
                        {{ number_format($product->prix_vente_ttc, 0, ',', ' ') }} FCFA
                    @endif
                </td>
                <td class="whitespace-nowrap text-center">
                    @php
                        $stockFaible = $product->stock <= $product->seuil_alerte;
                    @endphp
                    <span class="status-badge {{ $stockFaible ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                        {{ number_format($product->stock, 2) }} {{ $product->uniteVente->symbole }}
                    </span>
                </td>
                <td class="whitespace-nowrap text-center text-indigo-700">
                    {{ $product->emplacement_rayon }} - {{ $product->emplacement_etagere }}
                </td>
                <td class="whitespace-nowrap text-center">
                    @if($product->date_peremption)
                        <span style="{{ $product->estProchePeremption() ? 'color: #dc2626; font-weight: 500;' : 'color: #4b5563;' }}">
                            {{ $product->date_peremption->format('d/m/Y') }}
                        </span>
                    @else
                        -
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Document généré automatiquement par le système de gestion de superette</p>
    </div>
</body>
</html> 
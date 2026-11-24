<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>{{ $titre }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; }
        h1 { font-size: 14px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #999; padding: 4px; text-align: left; }
        th { background: #eee; }
    </style>
</head>
<body>
    <h1>{{ $titre }}</h1>
    <p>Édité le {{ now()->format('d/m/Y H:i') }}</p>
    <table>
        <thead><tr>@foreach($entetes as $e)<th>{{ $e }}</th>@endforeach</tr></thead>
        <tbody>
        @foreach($lignes as $ligne)
            <tr>@foreach($ligne as $cellule)<td>{{ $cellule }}</td>@endforeach</tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>

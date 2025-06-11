@extends('layouts.app')

@section('content')
    @php
        $types = [
            'cv' => 'CV',
            'contrat' => 'Contrat',
            'diplome' => 'Diplôme',
            'certificat' => 'Certificat',
            'autre' => 'Autre'
        ];
    @endphp

    @include('documents-employes.form', [
        'employe' => $employe,
        'types' => $types
    ])
@endsection 
@extends('front.desktop.layouts.store')

@section('title', $servicePageTitle ?? 'Usluge')
@section('main_class', 'w-full px-0 py-0')

@section('content')
    @include('front.desktop.partials.service-pillars-showcase', [
        'sectionId' => 'ac-services-index',
        'headingLevel' => 1,
        'titleLead' => 'Naše usluge',
        'titleAccent' => '',
        'intro' => '',
        'variant' => 'image',
        'outro' => [
            'Kroz integrirani pristup reviziji, računovodstvu i financijskom savjetovanju stvaramo dodatnu vrijednost pomažući klijentima da posluju sigurnije, transparentnije i učinkovitije.',
            'Naša podrška omogućuje bolje upravljanje financijama, kvalitetnije strateško planiranje i donošenje odluka temeljenih na relevantnim i pravovremenim informacijama, uz smanjenje poslovnih i financijskih rizika.',
        ],
        'cards' => $primaryServicePillars ?? [],
    ])
@endsection

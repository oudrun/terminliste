@extends('layouts.app')

@section('content')
    @if (!$trials)
        <section class="card">
            <h2>Ingen prøver registrert</h2>
            <p>Gå til administrasjon for å legge inn første prøve.</p>
        </section>
    @endif

    @foreach ($trials as $trial)
        @php($classes = $classesByTrial[(int) $trial['id']] ?? [])
        <section class="card">
            <div class="actions" style="justify-content: space-between; align-items: start;">
                <div>
                    <h2>{{ $trial['title'] }}</h2>
                    <p>{!! nl2br(e($trial['description'])) !!}</p>
                </div>
                <span class="badge">Påmeldingsfrist {{ formatDate($trial['registration_deadline']) }}</span>
            </div>

            <div class="meta">
                <span>Prøvenummer: {{ $trial['trial_number'] ?? '' }}</span>
                <span>Arrangør: {{ $trial['organizer'] }}</span>
                <span>Ansvarlig klubb: {{ $trial['responsible_club'] ?? '' }}</span>
                <span>Sted: {{ $trial['location'] }}</span>
                <span>Prøveplass: {{ $trial['trial_place'] ?? '' }}</span>
                <span>Kontakt: {{ $trial['contact_person'] ?? '' }}@if(!empty($trial['contact_phone'])) / {{ $trial['contact_phone'] }} @endif @if(!empty($trial['contact_email'])) / {{ $trial['contact_email'] }} @endif</span>
                <span>Dato: {{ formatDate($trial['date_start']) }} - {{ formatDate($trial['date_end']) }}</span>
            </div>

            <h3>Klasser</h3>
            @if (!$classes)
                <p>Ingen klasser er registrert ennå.</p>
            @else
                <table class="table">
                    <thead>
                    <tr>
                        <th>Klasse</th>
                        <th>Start</th>
                        <th>Dommer</th>
                        <th>Pris</th>
                        <th>Ledige plasser</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($classes as $class)
                        @php($available = max(0, (int) $class['max_participants'] - (int) $class['registration_count']))
                        <tr>
                            <td>
                                <strong>{{ $class['name'] }}</strong><br>
                                <small class="muted">{{ $class['notes'] ?? '' }}</small>
                            </td>
                            <td>{{ formatTime($class['start_time']) }}</td>
                            <td>{{ $class['judge'] }}</td>
                            <td>{{ number_format((float) $class['price'], 0, ',', ' ') }} kr</td>
                            <td>{{ $available }} / {{ (int) $class['max_participants'] }}</td>
                            <td>
                                @if ($available > 0)
                                    <a class="button primary" href="{{ route('apply', ['class_id' => (int) $class['id']]) }}">Meld på</a>
                                @else
                                    <span class="badge">Fullt</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </section>
    @endforeach
@endsection

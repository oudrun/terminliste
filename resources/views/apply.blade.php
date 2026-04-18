@extends('layouts.app')

@section('content')
    <div class="grid">
        <section class="card">
            <h2>Meld på hundeprøve</h2>
            <div class="meta">
                <span>Prøvenummer: {{ $class['trial_number'] }}</span>
                <span>Sted: {{ $class['location'] }}</span>
                <span>Prøveplass: {{ $class['trial_place'] }}</span>
                <span>Dato: {{ formatDate($class['date_start']) }} - {{ formatDate($class['date_end']) }}</span>
                <span>Dommer: {{ $class['judge'] }}</span>
            </div>
            <p><strong>Ansvarlig klubb:</strong> {{ $class['responsible_club'] }}</p>
            <p><strong>Kontakt:</strong> {{ $class['contact_person'] }} / {{ $class['contact_phone'] }} / {{ $class['contact_email'] }}</p>
            <p><strong>Pris:</strong> {{ number_format((float) $class['price'], 0, ',', ' ') }} kr</p>
            <p><strong>Ledige plasser:</strong> {{ $available }} / {{ (int) $class['max_participants'] }}</p>
            <p><strong>Påmeldingsfrist:</strong> {{ formatDate($class['registration_deadline']) }}</p>
            <p>{!! nl2br(e($class['notes'] ?? '')) !!}</p>
        </section>

        <section class="card">
            <h2>Registrer påmelding</h2>

            @if ($errors)
                <div class="notice error">
                    @foreach ($errors as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="post" action="{{ route('apply', ['class_id' => (int) $class['id']]) }}">
                @csrf
                <div>
                    <label for="owner_name">Fører / eier</label>
                    <input id="owner_name" name="owner_name" value="{{ $formData['owner_name'] ?? '' }}" required>
                </div>
                <div>
                    <label for="email">E-post</label>
                    <input id="email" type="email" name="email" value="{{ $formData['email'] ?? '' }}" required>
                </div>
                <div>
                    <label for="phone">Telefon</label>
                    <input id="phone" name="phone" value="{{ $formData['phone'] ?? '' }}">
                </div>
                <div>
                    <label for="dog_name">Hundens navn</label>
                    <input id="dog_name" name="dog_name" value="{{ $formData['dog_name'] ?? '' }}" required>
                </div>
                <div>
                    <label for="dog_regno">Registreringsnummer</label>
                    <input id="dog_regno" name="dog_regno" value="{{ $formData['dog_regno'] ?? '' }}">
                </div>
                <div>
                    <label for="dog_breed">Rase</label>
                    <input id="dog_breed" name="dog_breed" value="{{ $formData['dog_breed'] ?? '' }}">
                </div>
                <div>
                    <label for="dog_class">Registrert konkurranseklasse</label>
                    <input id="dog_class" name="dog_class" value="{{ $formData['dog_class'] ?? '' }}">
                </div>
                <div>
                    <label for="comment">Kommentar</label>
                    <textarea id="comment" name="comment">{{ $formData['comment'] ?? '' }}</textarea>
                </div>
                <button class="button primary" type="submit">Send påmelding</button>
            </form>
        </section>
    </div>
@endsection

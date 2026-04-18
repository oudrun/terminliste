@extends('layouts.app')

@section('content')
    <div class="grid">
        <section class="card">
            <h2>Ny prøve</h2>
            <form method="post" action="{{ route('admin') }}">
                @csrf
                <input type="hidden" name="action" value="addTrial">
                <div>
                    <label for="trial_number">Prøvenummer</label>
                    <input id="trial_number" name="trial_number" required>
                </div>
                <div>
                    <label for="title">Tittel</label>
                    <input id="title" name="title" required>
                </div>
                <div>
                    <label for="organizer">Arrangør</label>
                    <input id="organizer" name="organizer" required>
                </div>
                <div>
                    <label for="responsible_club">Ansvarlig klubb</label>
                    <input id="responsible_club" name="responsible_club">
                </div>
                <div>
                    <label for="location">Sted</label>
                    <input id="location" name="location" required>
                </div>
                <div>
                    <label for="trial_place">Prøveplass</label>
                    <input id="trial_place" name="trial_place">
                </div>
                <div>
                    <label for="date_start">Startdato</label>
                    <input id="date_start" type="date" name="date_start" required>
                </div>
                <div>
                    <label for="date_end">Sluttdato</label>
                    <input id="date_end" type="date" name="date_end" required>
                </div>
                <div>
                    <label for="registration_deadline">Påmeldingsfrist</label>
                    <input id="registration_deadline" type="date" name="registration_deadline" required>
                </div>
                <div>
                    <label for="contact_person">Kontaktperson</label>
                    <input id="contact_person" name="contact_person">
                </div>
                <div>
                    <label for="contact_phone">Kontakttelefon</label>
                    <input id="contact_phone" name="contact_phone">
                </div>
                <div>
                    <label for="contact_email">Kontakt e-post</label>
                    <input id="contact_email" type="email" name="contact_email">
                </div>
                <div>
                    <label for="description">Beskrivelse</label>
                    <textarea id="description" name="description" required></textarea>
                </div>
                <button class="button primary" type="submit">Lagre prøve</button>
            </form>
        </section>

        <section class="card">
            <h2>Ny klasse</h2>
            <form method="post" action="{{ route('admin') }}">
                @csrf
                <input type="hidden" name="action" value="addClass">
                <div>
                    <label for="trial_id">Prøve</label>
                    <select id="trial_id" name="trial_id" required>
                        <option value="">Velg prøve</option>
                        @foreach ($trials as $trial)
                            <option value="{{ (int) $trial['id'] }}">{{ $trial['title'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="name">Klassenavn</label>
                    <input id="name" name="name" placeholder="UK, AK, VK ..." required>
                </div>
                <div>
                    <label for="start_time">Starttid</label>
                    <input id="start_time" type="time" name="start_time">
                </div>
                <div>
                    <label for="judge">Dommer</label>
                    <input id="judge" name="judge">
                </div>
                <div>
                    <label for="price">Pris</label>
                    <input id="price" type="number" step="0.01" name="price" value="0">
                </div>
                <div>
                    <label for="max_participants">Maks deltakere</label>
                    <input id="max_participants" type="number" name="max_participants" value="10">
                </div>
                <div>
                    <label for="notes">Notat</label>
                    <textarea id="notes" name="notes"></textarea>
                </div>
                <button class="button primary" type="submit">Lagre klasse</button>
            </form>
        </section>
    </div>

    <section class="card">
        <h2>Registrerte prøver</h2>
        <table class="table">
            <thead>
            <tr>
                <th>Prøve</th>
                <th>Nummer / klubb</th>
                <th>Sted</th>
                <th>Dato</th>
                <th>Handling</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($trials as $trial)
                <tr>
                    <td><strong>{{ $trial['title'] }}</strong><br><small class="muted">{{ $trial['organizer'] }}</small></td>
                    <td><strong>{{ $trial['trial_number'] ?? '' }}</strong><br><small class="muted">{{ $trial['responsible_club'] ?? '' }}</small></td>
                    <td>{!! e($trial['location']) !!}@if(!empty($trial['trial_place']))<br><small class="muted">{{ $trial['trial_place'] }}</small>@endif</td>
                    <td>{{ formatDate($trial['date_start']) }} - {{ formatDate($trial['date_end']) }}</td>
                    <td>
                        <form method="post" action="{{ route('admin') }}" onsubmit="return confirm('Slette prøven med alle tilhørende klasser?');">
                            @csrf
                            <input type="hidden" name="action" value="deleteTrial">
                            <input type="hidden" name="trial_id" value="{{ (int) $trial['id'] }}">
                            <button class="button danger" type="submit">Slett</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </section>

    <section class="card">
        <h2>Klasser</h2>
        <table class="table">
            <thead>
            <tr>
                <th>Prøve</th>
                <th>Klasse</th>
                <th>Dommer</th>
                <th>Pris</th>
                <th>Handling</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($classes as $class)
                <tr>
                    <td>{{ $class['trial_title'] }}</td>
                    <td>{{ $class['name'] }}</td>
                    <td>{{ $class['judge'] }}</td>
                    <td>{{ number_format((float) $class['price'], 0, ',', ' ') }} kr</td>
                    <td>
                        <form method="post" action="{{ route('admin') }}" onsubmit="return confirm('Slette denne klassen?');">
                            @csrf
                            <input type="hidden" name="action" value="deleteClass">
                            <input type="hidden" name="class_id" value="{{ (int) $class['id'] }}">
                            <button class="button danger" type="submit">Slett</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </section>

    <section class="card">
        <h2>Påmeldinger</h2>
        <table class="table">
            <thead>
            <tr>
                <th>Deltaker</th>
                <th>Hund</th>
                <th>Prøve / klasse</th>
                <th>Status</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($registrations as $registration)
                <tr>
                    <td>
                        <strong>{{ $registration['owner_name'] }}</strong><br>
                        <small class="muted">{{ $registration['email'] }} / {{ $registration['phone'] }}</small>
                    </td>
                    <td>
                        <strong>{{ $registration['dog_name'] }}</strong><br>
                        <small class="muted">Reg.nr: {{ $registration['dog_regno'] ?? '' }}</small><br>
                        <small class="muted">{{ $registration['dog_breed'] }} {{ !empty($registration['dog_class']) ? '(' . $registration['dog_class'] . ')' : '' }}</small>
                    </td>
                    <td>{{ $registration['trial_title'] }} / {{ $registration['class_name'] }}</td>
                    <td>
                        <form method="post" action="{{ route('admin') }}">
                            @csrf
                            <input type="hidden" name="action" value="updateStatus">
                            <input type="hidden" name="registration_id" value="{{ (int) $registration['id'] }}">
                            <select name="status" onchange="this.form.submit()">
                                <option value="pending" @selected($registration['status'] === 'pending')>Avventer</option>
                                <option value="confirmed" @selected($registration['status'] === 'confirmed')>Bekreftet</option>
                                <option value="cancelled" @selected($registration['status'] === 'cancelled')>Avlyst</option>
                            </select>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </section>
@endsection

<section>
    <header>
        <h2 class="text-lg font-medium text-slate-900">Información del perfil</h2>
        <p class="mt-1 text-sm text-slate-600">Datos personales, EPS, contacto de emergencia y consentimientos.</p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">@csrf</form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" aria-label="Formulario de perfil">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" value="Nombre completo" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" aria-required="true" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <x-input-label for="document_type" value="Tipo de documento" />
                <select id="document_type" name="document_type" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-teal-500 focus:ring-teal-500" aria-label="Tipo de documento">
                    @foreach(['CC'=>'Cédula de ciudadanía','CE'=>'Cédula de extranjería','TI'=>'Tarjeta de identidad','PA'=>'Pasaporte','RC'=>'Registro civil','OTRO'=>'Otro'] as $v=>$l)
                        <option value="{{ $v }}" @selected(old('document_type', $user->document_type)===$v)>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <x-input-label for="document_number" value="Número de documento" />
                <x-text-input id="document_number" name="document_number" type="text" class="mt-1 block w-full" :value="old('document_number', $user->document_number)" autocomplete="off" />
                <x-input-error class="mt-2" :messages="$errors->get('document_number')" />
            </div>
        </div>

        <div>
            <x-input-label for="email" value="Correo electrónico" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" aria-required="true" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <p class="text-sm mt-2 text-slate-700">
                    Correo no verificado.
                    <button form="send-verification" class="underline text-teal-700">Reenviar verificación</button>
                </p>
            @endif
        </div>

        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <x-input-label for="phone" value="Teléfono" />
                <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $user->phone)" autocomplete="tel" />
            </div>
            <div>
                <x-input-label for="eps" value="EPS / Aseguradora" />
                <x-text-input id="eps" name="eps" type="text" class="mt-1 block w-full" :value="old('eps', $user->eps)" placeholder="Ej. Sanitas, Sura, Nueva EPS" />
            </div>
        </div>

        <fieldset class="border border-slate-200 rounded-lg p-4 space-y-3">
            <legend class="text-sm font-semibold text-slate-800 px-1">Contacto de emergencia</legend>
            <div>
                <x-input-label for="emergency_contact_name" value="Nombre" />
                <x-text-input id="emergency_contact_name" name="emergency_contact_name" type="text" class="mt-1 block w-full" :value="old('emergency_contact_name', $user->emergency_contact_name)" />
            </div>
            <div>
                <x-input-label for="emergency_contact_phone" value="Teléfono de emergencia" />
                <x-text-input id="emergency_contact_phone" name="emergency_contact_phone" type="text" class="mt-1 block w-full" :value="old('emergency_contact_phone', $user->emergency_contact_phone)" autocomplete="tel" />
            </div>
        </fieldset>

        <fieldset class="border border-slate-200 rounded-lg p-4 space-y-3">
            <legend class="text-sm font-semibold text-slate-800 px-1">Consentimientos</legend>
            <label class="flex items-start gap-2 text-sm text-slate-700">
                <input type="checkbox" name="consent_informed" value="1" class="mt-1 rounded border-slate-300 text-teal-600 focus:ring-teal-500"
                    @checked(old('consent_informed', (bool)$user->consent_informed_at))
                    @if($user->consent_informed_at) disabled @endif
                    aria-describedby="consent-informed-help">
                <span>
                    Acepto el <strong>consentimiento informado</strong> para la atención en salud mental.
                    @if($user->consent_informed_at)
                        <span class="block text-xs text-teal-700" id="consent-informed-help">Aceptado el {{ $user->consent_informed_at->format('d/m/Y H:i') }}</span>
                    @else
                        <span class="block text-xs text-amber-700" id="consent-informed-help">Obligatorio para agendar citas.</span>
                    @endif
                </span>
            </label>
            @if($user->consent_informed_at)
                <input type="hidden" name="consent_informed" value="1">
            @endif
            <label class="flex items-start gap-2 text-sm text-slate-700">
                <input type="checkbox" name="consent_data" value="1" class="mt-1 rounded border-slate-300 text-teal-600 focus:ring-teal-500"
                    @checked(old('consent_data', (bool)$user->consent_data_at))
                    @if($user->consent_data_at) disabled @endif>
                <span>
                    Autorizo el tratamiento de mis <strong>datos personales</strong> (Ley 1581 / Habeas Data).
                    @if($user->consent_data_at)
                        <span class="block text-xs text-teal-700">Aceptado el {{ $user->consent_data_at->format('d/m/Y H:i') }}</span>
                    @endif
                </span>
            </label>
            @if($user->consent_data_at)
                <input type="hidden" name="consent_data" value="1">
            @endif
        </fieldset>

        @if($user->isBookingBlocked())
            <div class="rounded-lg bg-amber-50 border border-amber-200 text-amber-900 text-sm p-3" role="alert">
                Agendamiento restringido hasta <strong>{{ $user->booking_blocked_until->format('d/m/Y H:i') }}</strong>
                (cancelaciones: {{ $user->cancellation_count }}, inasistencias: {{ $user->no_show_count }}).
            </div>
        @endif

        <div class="flex items-center gap-4">
            <x-primary-button>Guardar</x-primary-button>
            @if (session('status') === 'profile-updated')
                <p class="text-sm text-teal-700" role="status">Guardado.</p>
            @endif
        </div>
    </form>
</section>

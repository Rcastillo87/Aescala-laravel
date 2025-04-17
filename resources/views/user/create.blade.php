@extends('layouts.app')
@section('content')
<div class="w-full px-2">
    <form method="POST" action="{{ route('user.save') }}" autocomplete="off">
        @csrf
        <div class="flex flex-wrap -mx-3">
            <input type="hidden" id="id" name="id" value="{{$user?$user->id:''}}">
            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                <x-input-label for="nombre_completo" :value="__('Nombre Completo *')" />
                <x-text-input id="nombre_completo" class="block mt-1 w-full" type="text" name="nombre_completo" :value="old('nombre_completo', $user?$user->nombre_completo:'')" 
                required autofocus />
                <x-input-error :messages="$errors->get('nombre_completo')" class="mt-2" />
            </div>
            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                <x-input-label for="email" :value="__('Correo *')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $user?$user->email:'')" required/>
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>
            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                <x-input-label for="tipo_documento" :value="__('Tipo Documento *')" />
                <select name="tipo_documento" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 
                    dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full" required>
                    <option value="">-- Seleccione --</option>
                    @foreach ($tipoDocs as $key => $value)
                        <option value="{{$key}}" {{(old('tipo_documento', $user?$user->tipo_documento:'') == $key)?'selected':'' }}>
                            {{$value[1]}}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('tipo_documento')" class="mt-2" />
            </div>
            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                <x-input-label for="cedula" :value="__('Cedula *')" />
                <x-text-input id="cedula" class="block mt-1 w-full" type="number" name="cedula" :value="old('cedula', $user?$user->cedula:'')" required/>
                <x-input-error :messages="$errors->get('cedula')" class="mt-2" />
            </div>
            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                <x-input-label for="telefono" :value="__('Telefono *')" />
                <x-text-input id="telefono" class="block mt-1 w-full" type="number" name="telefono" :value="old('telefono', $user?$user->telefono:'')" required/>
                <x-input-error :messages="$errors->get('telefono')" class="mt-2" />
            </div>
            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                <x-input-label for="id_rol" :value="__('Perfil *')" />
                <select name="id_rol" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 
                    dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full" required>
                    <option value="">-- Seleccione --</option>
                    @foreach ($roles as $key => $rol)
                        <option value="{{$key}}" {{(old('id_rol', $user?$user->id_rol:'') == $key)?'selected':'' }}>
                            {{$rol}}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('id_rol')" class="mt-2" />
            </div>
            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                <x-input-label for="password" :value="__('Password *')" />
                <x-text-input id="password" class="block mt-1 w-full"
                                type="password"
                                name="password"
                                :required="$action == 'Crear'"/>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>
            <div class="w-full max-w-full p-3 shrink-0 md:w-6/12 lg:w-4/12 2xl:w-3/12 md:flex-0">
                <x-input-label for="password_confirmation" :value="__('Confirm Password *')" />

                <x-text-input id="password_confirmation" class="block mt-1 w-full"
                                type="password"
                                name="password_confirmation" 
                                :required="$action == 'Crear'"/>
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-secondary-button class="ms-4" href="{{ route('user.index') }}">
                Atras
            </x-secondary-button>
            <x-primary-button class="ms-4">
                {{ __($action) }}
            </x-primary-button>
        </div>
    </form>
</div>
@endsection

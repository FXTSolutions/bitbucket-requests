@extends('layouts.app')

@section('title', 'Pull Requests')

@section('header', 'Lista de Pull Requests')

@section('content')
<form method="POST" action="{{ route('pull-requests.show') }}" id="filterForm">
    @csrf
    <div class="mb-4">
        <label for="branch" class="block text-sm font-medium text-gray-700">Escolha a Branch:</label>
        <select name="branch" id="branch" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            <option value="master">master</option>
{{--            <option value="master-mx">master-mx</option>--}}
        </select>
    </div>

    <div class="mb-4">
        <input type="checkbox" name="show_merged_details" id="show_merged_details" class="mr-2">
        <label for="show_merged_details" class="text-sm font-medium text-gray-700">Mostrar detalhes de merge?</label>
    </div>

    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md flex items-center justify-center" id="submitButton" onclick="startLoading()">
        <span id="buttonText">Filtrar</span>
        <span id="loadingDots" class="hidden ml-2">
            <i class="fa-duotone fa-solid fa-spinner fa-spin-pulse"></i>
        </span>
    </button>
</form>

<script>
    function startLoading() {
        document.getElementById('buttonText').innerText = 'Carregando';
        document.getElementById('loadingDots').classList.remove('hidden');
        document.getElementById('submitButton').disabled = true;
        // document.getElementById('filterForm').submit();
    }
</script>
@endsection

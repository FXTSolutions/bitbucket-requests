@extends('layouts.app')

@section('title', 'Pull Requests')

@section('header', 'Lista de Pull Requests')

@section('content')
    <table class="table-auto border-collapse border border-gray-300 w-full text-left text-sm">
        <thead>
        <tr class="bg-gray-200">
            <th class="border border-gray-300 px-4 py-2">Branch Origin</th>
            <th class="border border-gray-300 px-4 py-2">Title</th>
            <th class="border border-gray-300 px-4 py-2">Server Dev</th>
            <th class="border border-gray-300 px-4 py-2">Server QA</th>
            <th class="border border-gray-300 px-4 py-2">Author</th>
            <th class="border border-gray-300 px-4 py-2">Created On</th>
            <th class="border border-gray-300 px-4 py-2">Updated On</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($formattedData as $data)
            <tr>
                <td class="border border-gray-300 px-4 py-2">{{ $data['branch_origin'] }}</td>
                <td class="border border-gray-300 px-4 py-2">{{ $data['title'] }}</td>
                <td class="border border-gray-300 px-4 py-2">
                <span class="px-2 py-1 rounded {{ $data['server_dev_status_color'] }}">
                    {{ $data['server_dev_status'] }}
                </span>
                </td>
                <td class="border border-gray-300 px-4 py-2">
                <span class="px-2 py-1 rounded {{ $data['server_qa_status_color'] }}">
                    {{ $data['server_qa_status'] }}
                </span>
                </td>
                <td class="border border-gray-300 px-4 py-2">{{ $data['author'] }}</td>
                <td class="border border-gray-300 px-4 py-2">{{ $data['created_on'] }}</td>
                <td class="border border-gray-300 px-4 py-2">{{ $data['updated_on'] }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="">
        Total: {{ count($formattedData) }}
    </div>
@endsection

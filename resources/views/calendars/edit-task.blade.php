@extends('layouts.base-interface')

@section('title', 'Modifier une Tâche')

@section('content')
<div class="relative z-10 bg-white/10 backdrop-blur-lg border-2 border-gray-500 rounded-[100px] shadow-xl px-10 py-12 w-[894px] h-[708px] text-center transform scale-75">
    <h1 class="text-[#E3EDEF] text-4xl sm:text-5xl font-semibold mb-8 leading-tight tracking-wide font-poppins">
        Update TASK?
    </h1>

    @if ($errors->any())
        <div class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4 font-poppins">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('calendars.tasks.update', [$formattedDate, $task['id']]) }}" class="flex flex-col items-center space-y-6">
        @csrf
        @method('PUT')


        <div class="w-[745px]">
            <input type="text" id="title" name="title" value="{{ old('title', $task['title']) }}" required
                   class="w-full h-[68px] px-4 rounded-[57px] bg-[#D9D9D9] opacity-100 text-gray-700 font-semibold font-poppins border-none focus:outline-none placeholder:text-xl"
                   placeholder="Task title:">
            <x-input-error :messages="$errors->get('title')" class="mt-1 text-red-200 font-poppins text-sm" />
        </div>


        <div class="w-[745px]">
            <textarea id="description" name="description" class="w-full h-[110px] px-4 py-2 rounded-[57px] bg-[#D9D9D9] opacity-100 text-gray-700 font-semibold font-poppins border-none focus:outline-none resize-none placeholder:text-xl" placeholder="Description:">{{ old('description', $task['description'] ?? '') }}</textarea>
            <x-input-error :messages="$errors->get('description')" class="mt-1 text-red-200 font-poppins text-sm" />
        </div>


        <div class="w-[745px]">
            <input type="datetime-local" id="start_date" name="start_date" value="{{ old('start_date', \Carbon\Carbon::parse($task['start_date'])->format('Y-m-d\TH:i')) }}" required
                   class="w-full h-[68px] px-4 rounded-[57px] bg-[#D9D9D9] opacity-100 text-gray-700 font-semibold font-poppins border-none focus:outline-none placeholder:text-xl">
            <x-input-error :messages="$errors->get('start_date')" class="mt-1 text-red-200 font-poppins text-sm" />
        </div>


        <div class="w-[745px]">
            <input type="datetime-local" id="deadline" name="deadline" value="{{ old('deadline', \Carbon\Carbon::parse($task['deadline'])->format('Y-m-d\TH:i')) }}" required
                   class="w-full h-[68px] px-4 rounded-[57px] bg-[#D9D9D9] opacity-100 text-gray-700 font-semibold font-poppins border-none focus:outline-none placeholder:text-xl">
            <x-input-error :messages="$errors->get('deadline')" class="mt-1 text-red-200 font-poppins text-sm" />
        </div>

        <br>

        <div class="flex space-x-4">
            <a href="{{ route('calendars.index') }}"
               class="h-[68px] w-[225px] rounded-[57px] bg-[#D9D9D9] opacity-100 text-black font-black text-[24px] font-inter hover:bg-[#319FBB] transition duration-200 flex items-center justify-center">
                Back
            </a>
            <button type="submit"
                    class="h-[68px] w-[225px] rounded-[57px] bg-[#D9D9D9] opacity-100 text-black font-black text-[24px] font-inter hover:bg-[#319FBB] transition duration-200">
                Update Task
            </button>

        </div>
    </form>


</div>
@endsection

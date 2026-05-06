@extends('layouts.app')
@section('title','سجل الألعاب')
@section('content')

<div class="min-h-screen py-8 bg-gray-50 dark:bg-gray-900">
  <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center mb-8">
      <h1 class="text-3xl font-bold text-gray-900 dark:text-white">سجل الألعاب</h1>
      <p class="mt-2 text-gray-600 dark:text-gray-400">تتبع جميع مبارياتك وتقدمك</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
      <div class="p-6">
        @if($games->count() > 0)
          <div class="space-y-4">
            @foreach($games as $game)
              <div class="flex items-center justify-between p-5 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-200">
                <div class="flex items-center space-x-4 rtl:space-x-reverse">
                  <div class="flex-shrink-0 w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                  </div>
                  <div>
                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ $game->created_at->diffForHumans() }}</span>
                    <p class="mt-1 font-medium text-gray-800 dark:text-gray-100">
                      ضد <span class="text-blue-600 dark:text-blue-400">{{ $game->opponentNameFor($user) }}</span>
                    </p>
                  </div>
                </div>
                <span @class([
                  'px-4 py-2 text-sm font-medium rounded-full shadow-sm transition-all duration-200',
                  $game->resultBadgeFor($user)['class']
                ])>
                  {{ $game->resultBadgeFor($user)['text'] }}
                </span>
              </div>
            @endforeach
          </div>
        @else
          <div class="text-center py-12">
            <svg class="mx-auto w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">لا توجد ألعاب حتى الآن</h3>
            <p class="mt-2 text-gray-500 dark:text-gray-400">ابدأ لعبتك الأولى وسيظهر سجل الألعاب هنا</p>
          </div>
        @endif
      </div>

      @if($games->count() > 0)
        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600">
          {{ $games->links() }}
        </div>
      @endif
    </div>
  </div>
</div>
@endsection
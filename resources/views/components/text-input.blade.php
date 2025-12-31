@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'w-full px-4 py-3 bg-[#1e2939] text-white text-base border-2 border-gray-500 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-[#1e2939] placeholder:text-gray-400 placeholder:opacity-70 transition-all duration-200']) }}>

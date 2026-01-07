@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'w-full px-4 py-3 bg-[#93a6c2] text-black text-base border-2 border-gray-500 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-[#93a6c2] placeholder:text-gray-400 placeholder:opacity-70 transition-all duration-200']) }}>

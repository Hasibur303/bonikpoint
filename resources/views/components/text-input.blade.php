@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-200 bg-[#f8f9fa] focus:border-primary focus:ring-primary rounded-md shadow-sm']) }}>

@props([
    'type' => 'button',
    'color' => 'blue',
    'text' => '',
])

<button type="{{ $type }}" @class([
    'inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white rounded-md transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-1',
    'bg-blue-500 hover:bg-blue-600 focus:ring-blue-500' => $color === 'blue',
    'bg-red-500 hover:bg-red-600 focus:ring-red-500' => $color === 'red',
    'bg-green-500 hover:bg-green-600 focus:ring-green-500' => $color === 'green',
    'bg-gray-500 hover:bg-gray-600 focus:ring-gray-500' => $color === 'gray',
])>
    {{ $text }}
</button>

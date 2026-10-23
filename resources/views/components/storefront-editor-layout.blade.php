<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Product | Bonik Point</title>
    <meta name="robots" content="noindex,nofollow">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#f3f5f4] p-4 font-sans text-gray-700 sm:p-6">
    @if(session('success'))
        <div role="status" class="mb-4 rounded-md border border-green-200 bg-green-50 p-3 text-sm text-green-800">{{ session('success') }}</div>
    @endif
    {{ $slot }}
    @if(session('success') && session('storefront_product_url'))
        <script>
            window.parent.postMessage({ type: 'storefront-product-saved', productUrl: @json(session('storefront_product_url')) }, window.location.origin);
        </script>
    @endif
</body>
</html>

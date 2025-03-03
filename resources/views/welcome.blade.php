<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>My App</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
  @include('components.header')
  <div class="container mt-4">
    @yield('content')
  </div>

  @yield('scripts')
</body>
</html>

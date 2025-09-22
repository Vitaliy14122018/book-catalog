<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Book Catalog</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- (опционально) Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
	
	<link href="css/style.css" rel="stylesheet">
	
	    <!-- jQuery -->
    <script src="js/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
	
    <!-- Добавляем CSRF Token для защиты от CSRF атак -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>

    <!-- Навигация -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ url('/') }}">Каталог книг</a>
        </div>
    </nav>

    <!-- Основной контент -->
    <div class="container mt-4">
        @yield('content')
		@yield('script')
    </div>
</body>
</html>